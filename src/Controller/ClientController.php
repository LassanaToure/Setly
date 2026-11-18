<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Model\Avis;
use App\Model\Creneau;
use App\Model\Materiel;
use App\Model\Reservation;
use App\Model\ReservationSuivi;
use App\Model\Studio;
use App\Model\Utilisateur;
use App\Service\ReservationService;
use App\Service\StatsService;
use App\Service\TarifService;

final class ClientController extends Controller
{

    public function reserverForm(Request $request, string $id): void
    {
        $studio = Studio::find((int) $id);
        if (!$studio) {
            $this->redirect('/studios');
        }

        $this->view('client/reserver', [
            'title'    => 'Réserver — ' . $studio['nom'],
            'studio'   => $studio,
            'creneaux' => Creneau::forStudio((int) $id),
            'materiel' => Materiel::all(),
            'user'     => Utilisateur::find(Auth::id()),
            'old'      => $this->oldInput(),
            'errors'   => $this->takeErrors(),
        ]);
    }

    public function tarif(Request $request): void
    {
        $studio = Studio::find((int) $request->query('studio', 0));
        if (!$studio) {
            Response::json(['error' => 'Studio inconnu'], 404);
        }

        $duree = max(0, (int) $request->query('duree', 0));
        $mode  = $request->query('mode', 'sur_place') === 'livraison' ? 'livraison' : 'sur_place';
        $km    = max(0, (int) $request->query('km', 0));
        $materiel = $this->parseMateriel($request->allQuery()['materiel'] ?? []);

        $tarif = TarifService::compute($studio, $duree, $materiel, $mode, $km);

        Response::json([
            'prix_studio'   => TarifService::format($tarif['prix_studio_cents']),
            'reduction'     => TarifService::format($tarif['reduction_cents']),
            'a_reduction'   => $tarif['reduction_cents'] > 0,
            'prix_materiel' => TarifService::format($tarif['prix_materiel_cents']),
            'livraison'     => TarifService::format($tarif['livraison_cents']),
            'total'         => TarifService::format($tarif['total_cents']),
        ]);
    }

    public function reserver(Request $request): void
    {
        Csrf::validate($request->all());

        $studio = Studio::find((int) $request->input('id_studio', 0));
        if (!$studio) {
            $this->redirect('/studios');
        }

        $creneau = Creneau::find((int) $request->input('id_creneau', 0));

        $v = new Validator($request->all());
        $v->required('id_creneau', 'Créneau')
          ->integer('duree_heures', 'Durée', (int) $studio['duree_min_heures'])
          ->integer('nb_personnes', 'Nombre de personnes', 1, (int) $studio['capacite'])
          ->in('mode_retrait', ['sur_place', 'livraison'], 'Mode de retrait');

        if (!$creneau || (int) $creneau['id_studio'] !== (int) $studio['id_studio']) {
            $v->addError('id_creneau', 'Ce créneau n\'est pas valide pour ce studio.');
        } else {
            $debut   = strtotime((string) $creneau['heure_debut']);
            $fin     = strtotime((string) $creneau['heure_fin']);
            $fenetre = ($debut !== false && $fin !== false && $fin > $debut)
                ? (int) floor(($fin - $debut) / 3600)
                : 0;
            $dureeDemandee = (int) $request->input('duree_heures', 0);
            if ($fenetre > 0 && $dureeDemandee > $fenetre) {
                $v->addError('duree_heures', "La durée demandée dépasse le créneau choisi ({$fenetre} h disponibles).");
            }
        }

        $mode = $request->input('mode_retrait') === 'livraison' ? 'livraison' : 'sur_place';
        $km   = $mode === 'livraison' ? max(0, (int) $request->input('livraison_km', 0)) : 0;
        if ($mode === 'livraison' && $km <= 0) {
            $v->addError('livraison_km', 'Indiquez la distance (km hors Paris) pour la livraison.');
        }

        if ($v->fails()) {
            $this->withInput($request->all(), $v->errors());
            $this->redirect('/reserver/' . (int) $studio['id_studio']);
        }

        $duree    = (int) $request->input('duree_heures');
        $materiel = $this->parseMateriel($request->input('materiel', []));

        $tarif = TarifService::compute($studio, $duree, $materiel, $mode, $km);

        $result = ReservationService::create(
            Auth::id(),
            $studio,
            (int) $creneau['id_creneau'],
            [
                'date_tournage' => $creneau['date'],
                'heure_debut'   => $creneau['heure_debut'],
                'duree_heures'  => $duree,
                'nb_personnes'  => (int) $request->input('nb_personnes'),
                'mode_retrait'  => $mode,
                'livraison_km'  => $km,
            ],
            $tarif
        );

        if (!$result['ok']) {
            $this->flash('error', $result['error']);
            $this->redirect('/reserver/' . (int) $studio['id_studio']);
        }

        $this->flash('success', 'Réservation ' . $result['reference'] . ' enregistrée. Un email de confirmation vous a été envoyé.');
        $this->redirect('/reservation/' . $result['id']);
    }

    public function reservations(Request $request): void
    {
        $filtre = (string) $request->query('filtre', '');
        $this->view('client/reservations', [
            'title'        => 'Mes réservations — Setly',
            'reservations' => Reservation::forClient(Auth::id(), $filtre ?: null),
            'filtre'       => $filtre,
        ]);
    }

    public function filtrer(Request $request): void
    {
        $filtre = (string) $request->query('filtre', '');
        echo View::partial('client/_reservations_list', [
            'reservations' => Reservation::forClient(Auth::id(), $filtre ?: null),
        ]);
    }

    public function detail(Request $request, string $id): void
    {
        $resa = $this->ownedReservation((int) $id);

        $this->view('client/detail', [
            'title'     => 'Réservation ' . $resa['reference'],
            'resa'      => $resa,
            'materiels' => Reservation::materiels((int) $id),
            'suivi'     => ReservationSuivi::forReservation((int) $id),
            'suiviVisible' => ReservationService::suiviVisible($resa),
            'peutModifier' => ReservationService::clientCanEdit($resa),
            'peutAvis'  => $resa['statut'] === 'terminee' && !Avis::existsForReservation((int) $id),
        ]);
    }

    public function editForm(Request $request, string $id): void
    {
        $resa = $this->ownedReservation((int) $id);
        if (!ReservationService::clientCanEdit($resa)) {
            $this->flash('error', 'Cette réservation ne peut plus être modifiée.');
            $this->redirect('/reservation/' . $id);
        }

        $this->view('client/modifier', [
            'title'  => 'Modifier la réservation ' . $resa['reference'],
            'resa'   => $resa,
            'studio' => Studio::find((int) $resa['id_studio']),
            'old'    => $this->oldInput(),
            'errors' => $this->takeErrors(),
        ]);
    }

    public function update(Request $request, string $id): void
    {
        Csrf::validate($request->all());
        $resa = $this->ownedReservation((int) $id);

        if (!ReservationService::clientCanEdit($resa)) {
            $this->flash('error', 'Cette réservation ne peut plus être modifiée.');
            $this->redirect('/reservation/' . $id);
        }

        $studio = Studio::find((int) $resa['id_studio']);

        $v = new Validator($request->all());
        $v->required('date_tournage', 'Date')->date('date_tournage', 'Date')
          ->integer('duree_heures', 'Durée', (int) $studio['duree_min_heures'])
          ->integer('nb_personnes', 'Nombre de personnes', 1, (int) $studio['capacite']);

        if ($v->fails()) {
            $this->withInput($request->all(), $v->errors());
            $this->redirect('/reservation/' . $id . '/modifier');
        }

        $duree = (int) $request->input('duree_heures');

        $tarif = TarifService::compute($studio, $duree, [], 'sur_place', 0);
        $total = $tarif['prix_studio_net_cents'] + (int) $resa['prix_materiel_cents'] + (int) $resa['livraison_cents'];

        Reservation::updateDetails((int) $id, [
            'date_tournage'     => $request->input('date_tournage'),
            'heure_debut'       => $request->input('heure_debut', $resa['heure_debut']),
            'duree_heures'      => $duree,
            'nb_personnes'      => (int) $request->input('nb_personnes'),
            'prix_studio_cents' => $tarif['prix_studio_cents'],
            'reduction_cents'   => $tarif['reduction_cents'],
            'total_cents'       => $total,
        ]);
        ReservationSuivi::add((int) $id, $resa['statut'], 'Réservation modifiée par le client.');
        StatsService::upsertFait(Reservation::find((int) $id) ?? []);

        $this->flash('success', 'Votre réservation a été mise à jour.');
        $this->redirect('/reservation/' . $id);
    }

    public function cancel(Request $request, string $id): void
    {
        Csrf::validate($request->all());
        $resa = $this->ownedReservation((int) $id);

        if (!ReservationService::clientCanEdit($resa)) {
            $this->flash('error', 'Cette réservation ne peut plus être annulée en ligne. Contactez-nous.');
            $this->redirect('/reservation/' . $id);
        }

        Reservation::updateStatut((int) $id, 'annulee');
        ReservationSuivi::add((int) $id, 'annulee', 'Annulée par le client.');

        $this->releaseCreneau($resa);
        StatsService::upsertFait(Reservation::find((int) $id) ?? []);

        $this->flash('success', 'Votre réservation a été annulée.');
        $this->redirect('/mes-reservations');
    }

    public function avisForm(Request $request, string $id): void
    {
        $resa = $this->ownedReservation((int) $id);
        if ($resa['statut'] !== 'terminee' || Avis::existsForReservation((int) $id)) {
            $this->flash('error', 'Vous ne pouvez pas déposer d\'avis pour cette réservation.');
            $this->redirect('/reservation/' . $id);
        }

        $this->view('client/avis', [
            'title'  => 'Donner un avis',
            'resa'   => $resa,
            'old'    => $this->oldInput(),
            'errors' => $this->takeErrors(),
        ]);
    }

    public function avis(Request $request, string $id): void
    {
        Csrf::validate($request->all());
        $resa = $this->ownedReservation((int) $id);

        if ($resa['statut'] !== 'terminee' || Avis::existsForReservation((int) $id)) {
            $this->redirect('/reservation/' . $id);
        }

        $v = new Validator($request->all());
        $v->integer('note', 'Note', 1, 5)
          ->required('commentaire', 'Commentaire');

        if ($v->fails()) {
            $this->withInput($request->all(), $v->errors());
            $this->redirect('/reservation/' . $id . '/avis');
        }

        Avis::create(
            (int) $id,
            Auth::id(),
            (int) $resa['id_studio'],
            (int) $request->input('note'),
            trim((string) $request->input('commentaire'))
        );

        $this->flash('success', 'Merci ! Votre avis sera publié après validation par notre équipe.');
        $this->redirect('/reservation/' . $id);
    }

    public function compte(Request $request): void
    {
        $this->view('client/compte', [
            'title'  => 'Mon compte — Setly',
            'user'   => Utilisateur::find(Auth::id()),
            'errors' => $this->takeErrors(),
        ]);
    }

    public function updateCompte(Request $request): void
    {
        Csrf::validate($request->all());

        $v = new Validator($request->all());
        $v->required('nom', 'Nom')->required('prenom', 'Prénom');

        if ($v->fails()) {
            $this->withInput($request->all(), $v->errors());
            $this->redirect('/mon-compte');
        }

        Utilisateur::updateProfil(Auth::id(), [
            'nom'       => trim((string) $request->input('nom')),
            'prenom'    => trim((string) $request->input('prenom')),
            'telephone' => trim((string) $request->input('telephone', '')) ?: null,
        ]);
        $this->flash('success', 'Vos informations ont été mises à jour.');
        $this->redirect('/mon-compte');
    }

    public function updatePassword(Request $request): void
    {
        Csrf::validate($request->all());

        $user = Utilisateur::find(Auth::id());
        $v = new Validator($request->all());
        $v->required('mot_de_passe_actuel', 'Mot de passe actuel')
          ->required('mot_de_passe', 'Nouveau mot de passe')->password('mot_de_passe')
          ->matches('mot_de_passe_confirm', 'mot_de_passe', 'Confirmation');

        if (!password_verify((string) $request->input('mot_de_passe_actuel'), $user['mot_de_passe_hash'])) {
            $v->addError('mot_de_passe_actuel', 'Le mot de passe actuel est incorrect.');
        }

        if ($v->fails()) {
            $this->withInput([], $v->errors());
            $this->redirect('/mon-compte');
        }

        Utilisateur::updatePassword(Auth::id(), (string) $request->input('mot_de_passe'));
        $this->flash('success', 'Votre mot de passe a été modifié.');
        $this->redirect('/mon-compte');
    }

    private function ownedReservation(int $id): array
    {
        $resa = Reservation::find($id);
        if (!$resa || (int) $resa['id_utilisateur'] !== Auth::id()) {
            Response::abort(403, 'Cette réservation ne vous appartient pas.');
        }
        return $resa;
    }

    private function parseMateriel(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $id => $qte) {
            $q = (int) $qte;
            if ($q > 0) {
                $out[(int) $id] = $q;
            }
        }
        return $out;
    }

    private function releaseCreneau(array $resa): void
    {
        $creneaux = Creneau::availableFor((int) $resa['id_studio'], $resa['date_tournage']);

        $all = \App\Core\Database::connection()->prepare(
            "SELECT id_creneau FROM creneau WHERE id_studio = :s AND date = :d AND heure_debut = :h LIMIT 1"
        );
        $all->execute([':s' => $resa['id_studio'], ':d' => $resa['date_tournage'], ':h' => $resa['heure_debut']]);
        $cid = $all->fetchColumn();
        if ($cid) {
            Creneau::release((int) $cid);
        }
    }
}
