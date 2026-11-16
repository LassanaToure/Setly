<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;

final class Creneau
{

    public static function availableFor(int $idStudio, string $date): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM creneau
             WHERE id_studio = :s AND date = :d AND disponible = 1
             ORDER BY heure_debut"
        );
        $stmt->execute([':s' => $idStudio, ':d' => $date]);
        return $stmt->fetchAll();
    }

    public static function availableDates(int $idStudio): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT DISTINCT date FROM creneau
             WHERE id_studio = :s AND disponible = 1 AND date >= CURDATE()
             ORDER BY date LIMIT 60"
        );
        $stmt->execute([':s' => $idStudio]);
        return array_column($stmt->fetchAll(), 'date');
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare("SELECT * FROM creneau WHERE id_creneau = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function lockAndReserve(PDO $pdo, int $idCreneau): bool
    {
        $stmt = $pdo->prepare(
            "SELECT disponible FROM creneau WHERE id_creneau = :id FOR UPDATE"
        );
        $stmt->execute([':id' => $idCreneau]);
        $row = $stmt->fetch();

        if (!$row || (int) $row['disponible'] !== 1) {
            return false;
        }

        $upd = $pdo->prepare("UPDATE creneau SET disponible = 0 WHERE id_creneau = :id");
        $upd->execute([':id' => $idCreneau]);
        return true;
    }

    public static function release(int $idCreneau): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE creneau SET disponible = 1 WHERE id_creneau = :id"
        );
        $stmt->execute([':id' => $idCreneau]);
    }

    public static function create(int $idStudio, string $date, string $debut, string $fin): void
    {
        $stmt = Database::connection()->prepare(
            "INSERT INTO creneau (id_studio, date, heure_debut, heure_fin, disponible)
             VALUES (:s, :d, :hd, :hf, 1)"
        );
        $stmt->execute([':s' => $idStudio, ':d' => $date, ':hd' => $debut, ':hf' => $fin]);
    }

    public static function forStudio(int $idStudio): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM creneau WHERE id_studio = :s AND date >= CURDATE()
             ORDER BY date, heure_debut LIMIT 100"
        );
        $stmt->execute([':s' => $idStudio]);
        return $stmt->fetchAll();
    }
}
