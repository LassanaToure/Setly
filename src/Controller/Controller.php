<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Response;
use App\Core\Session;
use App\Core\View;

abstract class Controller
{
    protected function view(string $template, array $data = [], string $layout = 'layouts/main'): void
    {
        View::render($template, $data, $layout);
    }

    protected function redirect(string $path): never
    {
        Response::redirect($path);
    }

    protected function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }

    protected function withInput(array $input, array $errors): void
    {
        unset($input['_csrf'], $input['mot_de_passe'], $input['mot_de_passe_confirm']);
        Session::set('_old', $input);
        Session::set('_errors', $errors);
    }

    protected function oldInput(): array
    {
        $old = Session::get('_old', []);
        Session::remove('_old');
        return $old;
    }

    protected function takeErrors(): array
    {
        $errors = Session::get('_errors', []);
        Session::remove('_errors');
        return $errors;
    }
}
