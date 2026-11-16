<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Validator;
use App\Model\Utilisateur;

final class AuthController extends Controller
{
    public function registerForm(Request $request): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->view('auth/inscription', [
            'title'  => 'Inscription — Setly',
            'old'    => $this->oldInput(),
            'errors' => $this->takeErrors(),
        ]);
    }

    public function register(Request $request): void
    {
        Csrf::validate($request->all());

        $v = new Validator($request->all());
        $v->required('nom', 'Nom')
          ->required('prenom', 'Prénom')
          ->required('email', 'Email')->email('email')
          ->required('mot_de_passe', 'Mot de passe')->password('mot_de_passe')
          ->matches('mot_de_passe_confirm', 'mot_de_passe', 'Confirmation du mot de passe');

        if (!$v->fails() && Utilisateur::emailExists(trim((string) $request->input('email')))) {
            $v->addError('email', 'Un compte existe déjà avec cet email.');
        }

        if ($v->fails()) {
            $this->withInput($request->all(), $v->errors());
            $this->redirect('/inscription');
        }

        $id = Utilisateur::createClient([
            'nom'          => trim((string) $request->input('nom')),
            'prenom'       => trim((string) $request->input('prenom')),
            'email'        => trim((string) $request->input('email')),
            'mot_de_passe' => (string) $request->input('mot_de_passe'),
            'telephone'    => trim((string) $request->input('telephone', '')) ?: null,
        ]);

        $user = Utilisateur::find($id);
        Auth::login($user);
        $this->flash('success', 'Bienvenue ' . $user['prenom'] . ' ! Votre compte a été créé.');
        $this->redirect('/mes-reservations');
    }

    public function loginForm(Request $request): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->view('auth/connexion', [
            'title'  => 'Connexion — Setly',
            'old'    => $this->oldInput(),
            'errors' => $this->takeErrors(),
        ]);
    }

    public function login(Request $request): void
    {
        Csrf::validate($request->all());

        $email = trim((string) $request->input('email'));
        $pass  = (string) $request->input('mot_de_passe');

        $user = Utilisateur::findByEmail($email);

        if (!$user || (int) $user['actif'] !== 1 || !password_verify($pass, $user['mot_de_passe_hash'])) {
            $this->withInput(['email' => $email], ['email' => 'Identifiants invalides ou compte désactivé.']);
            $this->redirect('/connexion');
        }

        Auth::login($user);
        $this->flash('success', 'Connexion réussie.');

        if (Auth::isStaff()) {
            $this->redirect('/back-office');
        }
        $this->redirect('/mes-reservations');
    }

    public function logout(Request $request): void
    {
        Csrf::validate($request->all());
        Auth::logout();
        $this->flash('success', 'Vous êtes déconnecté.');
        $this->redirect('/');
    }

    public function forgotForm(Request $request): void
    {
        $this->view('auth/mot-de-passe-oublie', [
            'title'  => 'Mot de passe oublié — Setly',
            'old'    => $this->oldInput(),
            'errors' => $this->takeErrors(),
        ]);
    }

    public function forgot(Request $request): void
    {
        Csrf::validate($request->all());

        $v = new Validator($request->all());
        $v->required('email', 'Email')->email('email');

        if ($v->fails()) {
            $this->withInput($request->all(), $v->errors());
            $this->redirect('/mot-de-passe-oublie');
        }

        $this->flash('success', 'Si un compte existe pour cet email, un lien de réinitialisation vient d\'être envoyé.');
        $this->redirect('/connexion');
    }
}
