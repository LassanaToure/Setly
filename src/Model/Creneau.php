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

    /**
     * Créneaux proposables à la modification : les créneaux disponibles du studio,
     * plus celui actuellement occupé par la réservation (pour permettre de le garder).
     */
    public static function availableForEdit(int $idStudio, int $currentCreneauId): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM creneau
             WHERE id_studio = :s AND date >= CURDATE()
               AND (disponible = 1 OR id_creneau = :cur)
             ORDER BY date, heure_debut LIMIT 100"
        );
        $stmt->execute([':s' => $idStudio, ':cur' => $currentCreneauId]);
        return $stmt->fetchAll();
    }

    /** Identifiant du créneau occupé par une réservation (par studio + date + heure), ou 0. */
    public static function idForReservation(array $resa): int
    {
        $stmt = Database::connection()->prepare(
            "SELECT id_creneau FROM creneau
             WHERE id_studio = :s AND date = :d AND heure_debut = :h LIMIT 1"
        );
        $stmt->execute([':s' => $resa['id_studio'], ':d' => $resa['date_tournage'], ':h' => $resa['heure_debut']]);
        $cid = $stmt->fetchColumn();
        return $cid ? (int) $cid : 0;
    }

    /** Remet à disponible le créneau occupé par une réservation (annulation). */
    public static function releaseForReservation(array $resa): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE creneau SET disponible = 1
             WHERE id_studio = :s AND date = :d AND heure_debut = :h"
        );
        $stmt->execute([':s' => $resa['id_studio'], ':d' => $resa['date_tournage'], ':h' => $resa['heure_debut']]);
    }
}
