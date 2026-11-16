<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Model\Utilisateur;
use App\Service\StatsService;
use App\Service\TarifService;

final class AdminController extends Controller
{

    public function employes(Request $request): void
    {
        $this->view('admin/employes', [
            'title'    => 'Comptes employés — Administration',
            'employes' => Utilisateur::employes(),
            'old'      => $this->oldInput(),
            'errors'   => $this->takeErrors(),
        ], 'layouts/backoffice');
    }

    public function createEmploye(Request $request): void
    {
        Csrf::validate($request->all());

        $v = new Validator($request->all());
        $v->required('nom', 'Nom')
          ->required('prenom', 'Prénom')
          ->required('email', 'Email')->email('email')
          ->required('mot_de_passe', 'Mot de passe')->password('mot_de_passe');

        if (!$v->fails() && Utilisateur::emailExists(trim((string) $request->input('email')))) {
            $v->addError('email', 'Un compte existe déjà avec cet email.');
        }

        if ($v->fails()) {
            $this->withInput($request->all(), $v->errors());
            $this->redirect('/admin/employes');
        }

        Utilisateur::createEmploye([
            'nom'          => trim((string) $request->input('nom')),
            'prenom'       => trim((string) $request->input('prenom')),
            'email'        => trim((string) $request->input('email')),
            'mot_de_passe' => (string) $request->input('mot_de_passe'),
        ]);

        $this->flash('success', 'Compte employé créé. Un email d\'information lui a été envoyé (sans le mot de passe).');
        $this->redirect('/admin/employes');
    }

    public function toggleEmploye(Request $request, string $id): void
    {
        Csrf::validate($request->all());
        $employe = Utilisateur::find((int) $id);
        if (!$employe || $employe['role'] !== 'employe') {
            Response::abort(404);
        }
        $nouvelEtat = (int) $employe['actif'] !== 1;
        Utilisateur::setActive((int) $id, $nouvelEtat);
        $this->flash('success', 'Compte ' . ($nouvelEtat ? 'réactivé' : 'désactivé') . '.');
        $this->redirect('/admin/employes');
    }

    public function stats(Request $request): void
    {
        $du     = (string) $request->query('du', '') ?: null;
        $au     = (string) $request->query('au', '') ?: null;
        $studio = (string) $request->query('studio', '') ?: null;

        $parStudio   = StatsService::parStudio($du, $au, $studio);
        $indicateurs = StatsService::indicateurs($parStudio);

        $this->view('admin/statistiques', [
            'title'       => 'Statistiques — Administration',
            'parStudio'   => $parStudio,
            'indicateurs' => $indicateurs,
            'filtres'     => ['du' => $du, 'au' => $au, 'studio' => $studio],
            'studios'     => \App\Model\Studio::all(true),
        ], 'layouts/backoffice');
    }

    public function statsData(Request $request): void
    {
        $du     = (string) $request->query('du', '') ?: null;
        $au     = (string) $request->query('au', '') ?: null;
        $studio = (string) $request->query('studio', '') ?: null;

        $parStudio = StatsService::parStudio($du, $au, $studio);

        Response::json([
            'labels'      => array_column($parStudio, 'studio'),
            'reservations'=> array_map('intval', array_column($parStudio, 'nb')),
            'ca'          => array_map(static fn ($r) => round($r['ca_cents'] / 100, 2), $parStudio),
            'indicateurs' => StatsService::indicateurs($parStudio),
        ]);
    }
}
