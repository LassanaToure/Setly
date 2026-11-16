<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;

final class Studio
{

    public static function search(array $filters = []): array
    {
        $sql = "SELECT s.*, t.libelle AS type_libelle,
                       (SELECT url FROM studio_photo p WHERE p.id_studio = s.id_studio ORDER BY position LIMIT 1) AS photo,
                       (SELECT ROUND(AVG(a.note),1) FROM avis a WHERE a.id_studio = s.id_studio AND a.statut_moderation = 'valide') AS note_moyenne,
                       (SELECT COUNT(*) FROM avis a WHERE a.id_studio = s.id_studio AND a.statut_moderation = 'valide') AS nb_avis
                FROM studio s
                JOIN type_studio t ON t.id_type = s.id_type
                WHERE s.actif = 1";
        $params = [];

        if (!empty($filters['type'])) {
            $sql .= " AND t.libelle = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['prix_max'])) {
            $sql .= " AND s.tarif_horaire_cents <= :prix_max";
            $params[':prix_max'] = (int) $filters['prix_max'] * 100;
        }
        if (!empty($filters['capacite'])) {
            $sql .= " AND s.capacite >= :capacite";
            $params[':capacite'] = (int) $filters['capacite'];
        }
        if (!empty($filters['date'])) {
            $sql .= " AND EXISTS (SELECT 1 FROM creneau c
                                  WHERE c.id_studio = s.id_studio
                                    AND c.date = :date AND c.disponible = 1)";
            $params[':date'] = $filters['date'];
        }

        $sql .= " ORDER BY s.nom";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT s.*, t.libelle AS type_libelle,
                    (SELECT ROUND(AVG(a.note),1) FROM avis a WHERE a.id_studio = s.id_studio AND a.statut_moderation='valide') AS note_moyenne
             FROM studio s JOIN type_studio t ON t.id_type = s.id_type
             WHERE s.id_studio = :id"
        );
        $stmt->execute([':id' => $id]);
        $studio = $stmt->fetch();
        return $studio ?: null;
    }

    public static function photos(int $idStudio): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT url FROM studio_photo WHERE id_studio = :id ORDER BY position"
        );
        $stmt->execute([':id' => $idStudio]);
        return array_column($stmt->fetchAll(), 'url');
    }

    public static function equipements(int $idStudio): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT libelle FROM equipement_inclus WHERE id_studio = :id ORDER BY libelle"
        );
        $stmt->execute([':id' => $idStudio]);
        return array_column($stmt->fetchAll(), 'libelle');
    }

    public static function highlighted(int $limit = 3): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT s.*, t.libelle AS type_libelle,
                    (SELECT url FROM studio_photo p WHERE p.id_studio = s.id_studio ORDER BY position LIMIT 1) AS photo
             FROM studio s JOIN type_studio t ON t.id_type = s.id_type
             WHERE s.actif = 1 ORDER BY s.id_studio LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function all(bool $withInactive = false): array
    {
        $sql = "SELECT s.*, t.libelle AS type_libelle
                FROM studio s JOIN type_studio t ON t.id_type = s.id_type";
        if (!$withInactive) {
            $sql .= " WHERE s.actif = 1";
        }
        $sql .= " ORDER BY s.nom";
        return Database::connection()->query($sql)->fetchAll();
    }

    public static function create(array $d): int
    {
        $stmt = Database::connection()->prepare(
            "INSERT INTO studio (id_type, nom, description, capacite, surface_m2, tarif_horaire_cents, duree_min_heures, conditions, actif)
             VALUES (:type, :nom, :desc, :cap, :surf, :tarif, :duree, :cond, 1)"
        );
        $stmt->execute([
            ':type' => (int) $d['id_type'], ':nom' => $d['nom'], ':desc' => $d['description'],
            ':cap' => (int) $d['capacite'], ':surf' => (int) ($d['surface_m2'] ?? 0),
            ':tarif' => (int) round(((float) $d['tarif_horaire']) * 100),
            ':duree' => (int) $d['duree_min_heures'], ':cond' => $d['conditions'],
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function update(int $id, array $d): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE studio SET id_type=:type, nom=:nom, description=:desc, capacite=:cap,
                    surface_m2=:surf, tarif_horaire_cents=:tarif, duree_min_heures=:duree, conditions=:cond
             WHERE id_studio=:id"
        );
        $stmt->execute([
            ':type' => (int) $d['id_type'], ':nom' => $d['nom'], ':desc' => $d['description'],
            ':cap' => (int) $d['capacite'], ':surf' => (int) ($d['surface_m2'] ?? 0),
            ':tarif' => (int) round(((float) $d['tarif_horaire']) * 100),
            ':duree' => (int) $d['duree_min_heures'], ':cond' => $d['conditions'], ':id' => $id,
        ]);
    }

    public static function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare("UPDATE studio SET actif = :a WHERE id_studio = :id");
        $stmt->execute([':a' => $active ? 1 : 0, ':id' => $id]);
    }

    public static function types(): array
    {
        return Database::connection()->query("SELECT * FROM type_studio ORDER BY libelle")->fetchAll();
    }
}
