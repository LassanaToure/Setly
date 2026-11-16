<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    public function __construct(private array $routes)
    {
    }

    public function dispatch(string $method, string $path): void
    {
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as [$routeMethod, $routePath, $handler, $access]) {
            if ($routeMethod !== $method) {
                continue;
            }

            $pattern = $this->toRegex($routePath);
            if (preg_match($pattern, $path, $matches)) {
                $this->guard($access);

                $params = array_filter(
                    $matches,
                    static fn ($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );

                [$class, $action] = explode('@', $handler);
                $fqcn = 'App\\Controller\\' . $class;

                $controller = new $fqcn();
                $controller->$action(new Request(), ...array_values($params));
                return;
            }
        }

        Response::abort(404, $this->notFound());
    }

    private function toRegex(string $path): string
    {
        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $regex . '$#';
    }

    private function guard(string $access): void
    {
        switch ($access) {
            case 'public':
                return;

            case 'auth':
                if (!Auth::check()) {
                    $this->denyToLogin();
                }
                return;

            case 'client':
                if (!Auth::hasRole('client')) {
                    Auth::check() ? Response::abort(403, 'Accès réservé aux clients.') : $this->denyToLogin();
                }
                return;

            case 'staff':
                if (!Auth::isStaff()) {
                    Auth::check() ? Response::abort(403, 'Accès réservé au personnel.') : $this->denyToLogin();
                }
                return;

            case 'employe':
            case 'administrateur':
                if (!Auth::hasRole($access) && !($access === 'employe' && Auth::isAdmin())) {
                    Auth::check() ? Response::abort(403, 'Accès non autorisé.') : $this->denyToLogin();
                }
                return;

            default:
                Response::abort(500, 'Niveau d\'accès inconnu.');
        }
    }

    private function denyToLogin(): never
    {
        Session::flash('error', 'Vous devez être connecté pour accéder à cette page.');
        Response::redirect('/connexion');
    }

    private function notFound(): string
    {
        return View::partial('errors/404');
    }
}
