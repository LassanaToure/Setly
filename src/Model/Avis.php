<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;

final class Avis
{

    public static function validatedForHome(int $limit = 6): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT a.note, a.commentaire, a.date_creation,
                    u.prenom, s.nom AS studio_nom
             FROM avis a
             JOIN utilisateur u ON u.id_utilisateur = a.id_utilisateur
             JOIN studio s ON s.id_studio = a.id_studio
             WHERE a.statut_moderation = 'valide'
             ORDER BY a.date_creation DESC LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function existsForReservation(int $idReservation): bool
    {
        $stmt = Database::connection()->prepare(
            "SELECT 1 FROM avis WHERE id_reservation = :id"
        );
        $stmt->execute([':id' => $idReservation]);
        return (bool) $stmt->fetchColumn();
    }

    public static function create(int $idReservation, int $idUser, int $idStudio, int $note, string $commentaire): void
    {
        $stmt = Database::connection()->prepare(
            "INSERT INTO avis (id_reservation, id_utilisateur, id_studio, note, commentaire, statut_moderation)
             VALUES (:r, :u, :s, :note, :c, 'en_attente')"
        );
        $stmt->execute([
            ':r' => $idReservation, ':u' => $idUser, ':s' => $idStudio,
            ':note' => $note, ':c' => $commentaire,
        ]);
    }

    public static function pending(): array
    {
        return Database::connection()->query(
            "SELECT a.*, u.prenom, u.nom AS client_nom, s.nom AS studio_nom
             FROM avis a
             JOIN utilisateur u ON u.id_utilisateur = a.id_utilisateur
             JOIN studio s ON s.id_studio = a.id_studio
             WHERE a.statut_moderation = 'en_attente'
             ORDER BY a.date_creation"
        )->fetchAll();
    }

    public static function moderate(int $idAvis, string $decision): void
    {
        $statut = $decision === 'valide' ? 'valide' : 'refuse';
        $stmt = Database::connection()->prepare(
            "UPDATE avis SET statut_moderation = :s WHERE id_avis = :id"
        );
        $stmt->execute([':s' => $statut, ':id' => $idAvis]);
    }
}
