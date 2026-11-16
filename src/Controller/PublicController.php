<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Validator;
use App\Core\View;
use App\Model\Avis;
use App\Model\Horaire;
use App\Model\Materiel;
use App\Model\Studio;

final class PublicController extends Controller
{
    public function home(Request $request): void
    {
        $this->view('public/home', [
            'title'    => 'Setly — Réservez votre studio de tournage',
            'studios'  => Studio::highlighted(3),
            'avis'     => Avis::validatedForHome(3),
            'horaires' => Horaire::all(),
        ]);
    }

    public function catalogue(Request $request): void
    {
        $filters = $this->readFilters($request);
        $this->view('public/catalogue', [
            'title'   => 'Nos studios — Setly',
            'studios' => Studio::search($filters),
            'types'   => Studio::types(),
            'filters' => $filters,
        ]);
    }

    public function filtrer(Request $request): void
    {
        $studios = Studio::search($this->readFilters($request));
        echo View::partial('public/_studios_grid', ['studios' => $studios]);
    }

    public function studio(Request $request, string $id): void
    {
        $studio = Studio::find((int) $id);
        if (!$studio) {
            $this->view('errors/404', ['title' => 'Introuvable']);
            return;
        }

        $this->view('public/studio', [
            'title'       => $studio['nom'] . ' — Setly',
            'studio'      => $studio,
            'photos'      => Studio::photos((int) $id),
            'equipements' => Studio::equipements((int) $id),
        ]);
    }

    public function materiel(Request $request): void
    {
        $this->view('public/materiel', [
            'title'    => 'Matériel en location — Setly',
            'materiel' => Materiel::all(),
        ]);
    }

    public function contact(Request $request): void
    {
        $this->view('public/contact', [
            'title'    => 'Contact — Setly',
            'horaires' => Horaire::all(),
            'old'      => $this->oldInput(),
            'errors'   => $this->takeErrors(),
        ]);
    }

    public function contactSend(Request $request): void
    {
        Csrf::validate($request->all());

        $v = new Validator($request->all());
        $v->required('nom', 'Nom')
          ->required('email', 'Email')->email('email')
          ->required('message', 'Message');

        if ($v->fails()) {
            $this->withInput($request->all(), $v->errors());
            $this->redirect('/contact');
        }

        $this->flash('success', 'Votre message a bien été envoyé. Nous vous répondrons rapidement.');
        $this->redirect('/contact');
    }

    public function mentions(Request $request): void
    {
        $this->view('public/mentions', ['title' => 'Mentions légales — Setly']);
    }

    public function cgv(Request $request): void
    {
        $this->view('public/cgv', ['title' => 'CGV / CGU — Setly']);
    }

    private function readFilters(Request $request): array
    {
        return [
            'type'     => (string) $request->query('type', ''),
            'prix_max' => (string) $request->query('prix_max', ''),
            'capacite' => (string) $request->query('capacite', ''),
            'date'     => (string) $request->query('date', ''),
        ];
    }
}
