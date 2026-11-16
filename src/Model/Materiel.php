<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;

final class Materiel
{
    public static function all(bool $withInactive = false): array
    {
        $sql = "SELECT * FROM materiel";
        if (!$withInactive) {
            $sql .= " WHERE actif = 1";
        }
        $sql .= " ORDER BY nom";
        return Database::connection()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare("SELECT * FROM materiel WHERE id_materiel = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findMany(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if ($ids === []) {
            return [];
        }
        $in   = implode(',', array_fill(0, count($ids), '?'));
        $stmt = Database::connection()->prepare(
            "SELECT * FROM materiel WHERE actif = 1 AND id_materiel IN ($in)"
        );
        $stmt->execute($ids);
        $out = [];
        foreach ($stmt->fetchAll() as $row) {
            $out[(int) $row['id_materiel']] = $row;
        }
        return $out;
    }

    public static function create(array $d): int
    {
        $stmt = Database::connection()->prepare(
            "INSERT INTO materiel (nom, description, prix_unitaire_cents, actif)
             VALUES (:nom, :desc, :prix, 1)"
        );
        $stmt->execute([
            ':nom' => $d['nom'], ':desc' => $d['description'],
            ':prix' => (int) round(((float) $d['prix_unitaire']) * 100),
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function update(int $id, array $d): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE materiel SET nom=:nom, description=:desc, prix_unitaire_cents=:prix WHERE id_materiel=:id"
        );
        $stmt->execute([
            ':nom' => $d['nom'], ':desc' => $d['description'],
            ':prix' => (int) round(((float) $d['prix_unitaire']) * 100), ':id' => $id,
        ]);
    }

    public static function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare("UPDATE materiel SET actif = :a WHERE id_materiel = :id");
        $stmt->execute([':a' => $active ? 1 : 0, ':id' => $id]);
    }
}
