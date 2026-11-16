<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;

final class ReservationSuivi
{
    public static function add(int $idReservation, string $statut, ?string $commentaire = null, ?PDO $pdo = null): void
    {
        $pdo  = $pdo ?? Database::connection();
        $stmt = $pdo->prepare(
            "INSERT INTO reservation_suivi (id_reservation, statut, commentaire)
             VALUES (:r, :s, :c)"
        );
        $stmt->execute([':r' => $idReservation, ':s' => $statut, ':c' => $commentaire]);
    }

    public static function forReservation(int $idReservation): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM reservation_suivi WHERE id_reservation = :id ORDER BY date_changement"
        );
        $stmt->execute([':id' => $idReservation]);
        return $stmt->fetchAll();
    }
}
