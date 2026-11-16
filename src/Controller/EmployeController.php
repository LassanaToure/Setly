<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Model\Avis;
use App\Model\Creneau;
use App\Model\Horaire;
use App\Model\Materiel;
use App\Model\Reservation;
use App\Model\ReservationSuivi;
use App\Model\Studio;
use App\Service\ReservationService;

final class EmployeController extends Controller
{
    public function dashboard(Request $request): void
    {
        $this->view('backoffice/dashboard', [
            'title'         => 'Back-office — Setly',
            'reservations'  => Reservation::all(),
            'avisEnAttente' => count(Avis::pending()),
        ], 'layouts/backoffice');
    }

    public function reservations(Request $request): void
    {
        $filters = [
            'statut' => (string) $request->query('statut', ''),
            'client' => (string) $request->query('client', ''),
        ];
        $this->view('backoffice/reservations', [
            'title'        => 'Réservations — Back-office',
            'reservations' => Reservation::all($filters),
            'statuts'      => ReservationService::LABELS,
            'filters'      => $filters,
        ], 'layouts/backoffice');
    }

    public function filtrer(Request $request): void
    {
        $filters = [
            'statut' => (string) $request->query('statut', ''),
            'client' => (string) $request->query('client', ''),
        ];
        echo View::partial('backoffice/_reservations_rows', [
            'reservations' => Reservation::all($filters),
            'statuts'      => ReservationService::LABELS,
        ]);
    }

    public function reservationDetail(Request $request, string $id): void
    {
        $resa = Reservation::find((int) $id);
        if (!$resa) {
            Response::abort(404);
        }

        $from = $resa['statut'];
        $transitions = array_filter(
            array_keys(ReservationService::LABELS),
            static fn ($to) => ReservationService::canTransition($from, $to)
        );

        $this->view('backoffice/reservation_detail', [
            'title'       => 'Réservation ' . $resa['reference'],
            'resa'        => $resa,
            'materiels'   => Reservation::materiels((int) $id),
            'suivi'       => ReservationSuivi::forReservation((int) $id),
            'statuts'     => ReservationService::LABELS,
            'transitions' => $transitions,
        ], 'layouts/backoffice');
    }

    public function changeStatut(Request $request, string $id): void
    {
        Csrf::validate($request->all());
        $resa = Reservation::find((int) $id);
        if (!$resa) {
            Response::abort(404);
        }

        $to          = (string) $request->input('statut');
        $contactMode = trim((string) $request->input('contact_mode', '')) ?: null;
        $motif       = trim((string) $request->input('motif', '')) ?: null;

        $result = ReservationService::changeStatut($resa, $to, $contactMode, $motif);

        if (!$result['ok']) {
            $this->flash('error', $result['error']);
        } else {
            $this->flash('success', 'Statut mis à jour : ' . ReservationService::statutLabel($to) . '.');
        }
        $this->redirect('/back-office/reservation/' . $id);
    }

    public function studios(Request $request): void
    {
        $this->view('backoffice/studios', [
            'title'   => 'Studios — Back-office',
            'studios' => Studio::all(true),
            'types'   => Studio::types(),
        ], 'layouts/backoffice');
    }

    public function studioCreate(Request $request): void
    {
        Csrf::validate($request->all());
        $v = $this->validateStudio($request);
        if ($v->fails()) {
            $this->flash('error', implode(' ', $v->errors()));
            $this->redirect('/back-office/studios');
        }
        Studio::create($request->all());
        $this->flash('success', 'Studio créé.');
        $this->redirect('/back-office/studios');
    }

    public function studioUpdate(Request $request, string $id): void
    {
        Csrf::validate($request->all());
        $v = $this->validateStudio($request);
        if ($v->fails()) {
            $this->flash('error', implode(' ', $v->errors()));
            $this->redirect('/back-office/studios');
        }
        Studio::update((int) $id, $request->all());
        $this->flash('success', 'Studio mis à jour.');
        $this->redirect('/back-office/studios');
    }

    public function studioArchive(Request $request, string $id): void
    {
        Csrf::validate($request->all());
        Studio::setActive((int) $id, (bool) $request->input('actif', 0));
        $this->flash('success', 'Studio mis à jour.');
        $this->redirect('/back-office/studios');
    }

    public function materiel(Request $request): void
    {
        $this->view('backoffice/materiel', [
            'title'    => 'Matériel — Back-office',
            'materiel' => Materiel::all(true),
        ], 'layouts/backoffice');
    }

    public function materielCreate(Request $request): void
    {
        Csrf::validate($request->all());
        $v = new Validator($request->all());
        $v->required('nom', 'Nom')->required('prix_unitaire', 'Prix');
        if ($v->fails()) {
            $this->flash('error', implode(' ', $v->errors()));
            $this->redirect('/back-office/materiel');
        }
        Materiel::create($request->all());
        $this->flash('success', 'Matériel créé.');
        $this->redirect('/back-office/materiel');
    }

    public function materielUpdate(Request $request, string $id): void
    {
        Csrf::validate($request->all());
        Materiel::update((int) $id, $request->all());
        $this->flash('success', 'Matériel mis à jour.');
        $this->redirect('/back-office/materiel');
    }

    public function materielArchive(Request $request, string $id): void
    {
        Csrf::validate($request->all());
        Materiel::setActive((int) $id, (bool) $request->input('actif', 0));
        $this->flash('success', 'Matériel mis à jour.');
        $this->redirect('/back-office/materiel');
    }

    public function creneaux(Request $request): void
    {
        $idStudio = (int) $request->query('studio', 0);
        $studios  = Studio::all(true);
        if ($idStudio === 0 && $studios) {
            $idStudio = (int) $studios[0]['id_studio'];
        }
        $this->view('backoffice/creneaux', [
            'title'    => 'Créneaux & horaires — Back-office',
            'studios'  => $studios,
            'idStudio' => $idStudio,
            'creneaux' => $idStudio ? Creneau::forStudio($idStudio) : [],
            'horaires' => Horaire::all(),
        ], 'layouts/backoffice');
    }

    public function creneauCreate(Request $request): void
    {
        Csrf::validate($request->all());
        $v = new Validator($request->all());
        $v->required('id_studio', 'Studio')
          ->required('date', 'Date')->date('date')
          ->required('heure_debut', 'Heure de début')
          ->required('heure_fin', 'Heure de fin');
        if ($v->fails()) {
            $this->flash('error', implode(' ', $v->errors()));
            $this->redirect('/back-office/creneaux');
        }
        Creneau::create(
            (int) $request->input('id_studio'),
            (string) $request->input('date'),
            (string) $request->input('heure_debut'),
            (string) $request->input('heure_fin')
        );
        $this->flash('success', 'Créneau ajouté.');
        $this->redirect('/back-office/creneaux?studio=' . (int) $request->input('id_studio'));
    }

    public function horaires(Request $request): void
    {
        $this->redirect('/back-office/creneaux');
    }

    public function horairesUpdate(Request $request): void
    {
        Csrf::validate($request->all());
        foreach (Horaire::all() as $h) {
            $id    = (int) $h['id_horaire'];
            $ferme = (bool) $request->input("ferme_$id", 0);
            Horaire::update(
                $id,
                (string) $request->input("ouverture_$id", '') ?: null,
                (string) $request->input("fermeture_$id", '') ?: null,
                $ferme
            );
        }
        $this->flash('success', 'Horaires mis à jour.');
        $this->redirect('/back-office/creneaux');
    }

    public function avis(Request $request): void
    {
        $this->view('backoffice/avis', [
            'title' => 'Modération des avis — Back-office',
            'avis'  => Avis::pending(),
        ], 'layouts/backoffice');
    }

    public function moderer(Request $request, string $id): void
    {
        Csrf::validate($request->all());
        $decision = $request->input('decision') === 'valide' ? 'valide' : 'refuse';
        Avis::moderate((int) $id, $decision);
        $this->flash('success', 'Avis ' . ($decision === 'valide' ? 'validé' : 'refusé') . '.');
        $this->redirect('/back-office/avis');
    }

    private function validateStudio(Request $request): Validator
    {
        $v = new Validator($request->all());
        $v->required('nom', 'Nom')
          ->required('id_type', 'Type')
          ->integer('capacite', 'Capacité', 1)
          ->required('tarif_horaire', 'Tarif horaire')
          ->integer('duree_min_heures', 'Durée minimum', 1);
        return $v;
    }
}
