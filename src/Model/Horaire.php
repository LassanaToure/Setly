<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;

final class Horaire
{
    private const ORDER = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'];

    public static function all(): array
    {
        $rows = Database::connection()->query("SELECT * FROM horaire")->fetchAll();
        usort($rows, static fn ($a, $b) =>
            array_search($a['jour_semaine'], self::ORDER, true) <=> array_search($b['jour_semaine'], self::ORDER, true)
        );
        return $rows;
    }

    public static function update(int $id, ?string $ouverture, ?string $fermeture, bool $ferme): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE horaire SET heure_ouverture = :o, heure_fermeture = :f, ferme = :ferme
             WHERE id_horaire = :id"
        );
        $stmt->execute([
            ':o' => $ferme ? null : $ouverture,
            ':f' => $ferme ? null : $fermeture,
            ':ferme' => $ferme ? 1 : 0,
            ':id' => $id,
        ]);
    }
}
