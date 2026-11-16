<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;

final class Reservation
{
    public static function create(PDO $pdo, array $d): int
    {
        $stmt = $pdo->prepare(
            "INSERT INTO reservation
             (id_utilisateur, id_studio, reference, date_tournage, heure_debut, duree_heures,
              nb_personnes, mode_retrait, livraison_km, prix_studio_cents, reduction_cents,
              prix_materiel_cents, livraison_cents, total_cents, statut)
             VALUES
             (:u, :s, :ref, :date, :hd, :duree, :nb, :mode, :km, :ps, :red, :pm, :liv, :total, 'en_attente')"
        );
        $stmt->execute([
            ':u' => $d['id_utilisateur'], ':s' => $d['id_studio'], ':ref' => $d['reference'],
            ':date' => $d['date_tournage'], ':hd' => $d['heure_debut'], ':duree' => $d['duree_heures'],
            ':nb' => $d['nb_personnes'], ':mode' => $d['mode_retrait'], ':km' => $d['livraison_km'],
            ':ps' => $d['prix_studio_cents'], ':red' => $d['reduction_cents'],
            ':pm' => $d['prix_materiel_cents'], ':liv' => $d['livraison_cents'], ':total' => $d['total_cents'],
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function addMateriel(PDO $pdo, int $idReservation, int $idMateriel, int $qte, int $prixCents): void
    {
        $stmt = $pdo->prepare(
            "INSERT INTO reservation_materiel (id_reservation, id_materiel, quantite, prix_unitaire_cents)
             VALUES (:r, :m, :q, :p)"
        );
        $stmt->execute([':r' => $idReservation, ':m' => $idMateriel, ':q' => $qte, ':p' => $prixCents]);
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT r.*, s.nom AS studio_nom, t.libelle AS type_libelle,
                    u.nom AS client_nom, u.prenom AS client_prenom, u.email AS client_email
             FROM reservation r
             JOIN studio s ON s.id_studio = r.id_studio
             JOIN type_studio t ON t.id_type = s.id_type
             JOIN utilisateur u ON u.id_utilisateur = r.id_utilisateur
             WHERE r.id_reservation = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function materiels(int $idReservation): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT rm.*, m.nom FROM reservation_materiel rm
             JOIN materiel m ON m.id_materiel = rm.id_materiel
             WHERE rm.id_reservation = :id"
        );
        $stmt->execute([':id' => $idReservation]);
        return $stmt->fetchAll();
    }

    public static function hasMateriel(int $idReservation): bool
    {
        $stmt = Database::connection()->prepare(
            "SELECT 1 FROM reservation_materiel WHERE id_reservation = :id LIMIT 1"
        );
        $stmt->execute([':id' => $idReservation]);
        return (bool) $stmt->fetchColumn();
    }

    public static function forClient(int $idUser, ?string $filtre = null): array
    {
        $sql = "SELECT r.*, s.nom AS studio_nom
                FROM reservation r JOIN studio s ON s.id_studio = r.id_studio
                WHERE r.id_utilisateur = :u";
        $params = [':u' => $idUser];

        if ($filtre === 'en_cours') {
            $sql .= " AND r.statut IN ('en_attente','confirmee','en_preparation','en_cours','attente_retour_materiel')";
        } elseif ($filtre === 'terminees') {
            $sql .= " AND r.statut IN ('terminee','annulee')";
        }
        $sql .= " ORDER BY r.date_creation DESC";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function all(array $filters = []): array
    {
        $sql = "SELECT r.*, s.nom AS studio_nom,
                       u.nom AS client_nom, u.prenom AS client_prenom
                FROM reservation r
                JOIN studio s ON s.id_studio = r.id_studio
                JOIN utilisateur u ON u.id_utilisateur = r.id_utilisateur
                WHERE 1=1";
        $params = [];

        if (!empty($filters['statut'])) {
            $sql .= " AND r.statut = :statut";
            $params[':statut'] = $filters['statut'];
        }
        if (!empty($filters['client'])) {
            $sql .= " AND (u.nom LIKE :client OR u.prenom LIKE :client OR u.email LIKE :client)";
            $params[':client'] = '%' . $filters['client'] . '%';
        }
        $sql .= " ORDER BY r.date_creation DESC";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function updateStatut(int $id, string $statut): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE reservation SET statut = :s WHERE id_reservation = :id"
        );
        $stmt->execute([':s' => $statut, ':id' => $id]);
    }

    public static function updateDetails(int $id, array $d): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE reservation SET date_tournage=:date, heure_debut=:hd, duree_heures=:duree,
                    nb_personnes=:nb, prix_studio_cents=:ps, reduction_cents=:red, total_cents=:total
             WHERE id_reservation=:id"
        );
        $stmt->execute([
            ':date' => $d['date_tournage'], ':hd' => $d['heure_debut'], ':duree' => $d['duree_heures'],
            ':nb' => $d['nb_personnes'], ':ps' => $d['prix_studio_cents'], ':red' => $d['reduction_cents'],
            ':total' => $d['total_cents'], ':id' => $id,
        ]);
    }

    public static function nextReference(): string
    {
        return 'SE-' . str_pad((string) random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
    }

    public static function allForSync(): array
    {
        return Database::connection()->query(
            "SELECT r.reference, r.total_cents, r.date_tournage, r.statut,
                    s.nom AS studio, t.libelle AS type_studio
             FROM reservation r
             JOIN studio s ON s.id_studio = r.id_studio
             JOIN type_studio t ON t.id_type = s.id_type"
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}
