<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Database;
use App\Model\Creneau;
use App\Model\Reservation;
use App\Model\ReservationSuivi;
use App\Model\Studio;

final class ReservationService
{

    private const TRANSITIONS = [
        'en_attente'     => ['confirmee', 'annulee'],
        'confirmee'      => ['en_preparation', 'annulee'],
        'en_preparation' => ['en_cours', 'annulee'],
        'en_cours'       => ['terminee', 'attente_retour_materiel'],
        'attente_retour_materiel' => ['terminee'],
        'terminee'       => [],
        'annulee'        => [],
    ];

    public const LABELS = [
        'en_attente'     => 'En attente',
        'confirmee'      => 'Confirmée',
        'en_preparation' => 'En préparation',
        'en_cours'       => 'En cours',
        'attente_retour_materiel' => 'En attente du retour de matériel',
        'terminee'       => 'Terminée',
        'annulee'        => 'Annulée',
    ];

    public static function create(int $idUser, array $studio, int $idCreneau, array $details, array $tarif): array
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {

            if (!Creneau::lockAndReserve($pdo, $idCreneau)) {
                $pdo->rollBack();
                return ['ok' => false, 'error' => 'Ce créneau vient d\'être réservé. Merci d\'en choisir un autre.'];
            }

            $reference = Reservation::nextReference();

            $idResa = Reservation::create($pdo, [
                'id_utilisateur'    => $idUser,
                'id_studio'         => (int) $studio['id_studio'],
                'reference'         => $reference,
                'date_tournage'     => $details['date_tournage'],
                'heure_debut'       => $details['heure_debut'],
                'duree_heures'      => $details['duree_heures'],
                'nb_personnes'      => $details['nb_personnes'],
                'mode_retrait'      => $details['mode_retrait'],
                'livraison_km'      => $details['livraison_km'],
                'prix_studio_cents' => $tarif['prix_studio_cents'],
                'reduction_cents'   => $tarif['reduction_cents'],
                'prix_materiel_cents' => $tarif['prix_materiel_cents'],
                'livraison_cents'   => $tarif['livraison_cents'],
                'total_cents'       => $tarif['total_cents'],
            ]);

            foreach ($tarif['lignes_materiel'] as $ligne) {
                Reservation::addMateriel($pdo, $idResa, $ligne['id'], $ligne['quantite'], $ligne['prix_unitaire_cents']);
            }

            ReservationSuivi::add($idResa, 'en_attente', 'Réservation créée par le client.', $pdo);

            $pdo->commit();
            return ['ok' => true, 'id' => $idResa, 'reference' => $reference];
        } catch (\Throwable $e) {
            $pdo->rollBack();
            return ['ok' => false, 'error' => 'Une erreur est survenue lors de la réservation.'];
        }
    }

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    public static function changeStatut(array $resa, string $to, ?string $contactMode, ?string $motif): array
    {
        $from = $resa['statut'];

        if (!self::canTransition($from, $to)) {
            return ['ok' => false, 'error' => 'Transition de statut non autorisée.'];
        }

        if ($to === 'annulee') {
            if (!$contactMode || !$motif) {
                return ['ok' => false, 'error' => 'L\'annulation nécessite d\'avoir contacté le client (mode + motif).'];
            }
        }

        Reservation::updateStatut((int) $resa['id_reservation'], $to);

        $commentaire = self::LABELS[$to];
        if ($to === 'annulee') {
            $commentaire .= " — Client contacté par {$contactMode}. Motif : {$motif}";
        }
        ReservationSuivi::add((int) $resa['id_reservation'], $to, $commentaire);

        return ['ok' => true];
    }

    public static function clientCanEdit(array $resa): bool
    {
        return $resa['statut'] === 'en_attente';
    }

    public static function suiviVisible(array $resa): bool
    {
        return !in_array($resa['statut'], ['en_attente'], true);
    }

    public static function statutLabel(string $statut): string
    {
        return self::LABELS[$statut] ?? $statut;
    }
}
