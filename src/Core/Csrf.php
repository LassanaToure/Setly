<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        if (!Session::has('_csrf')) {
            Session::set('_csrf', bin2hex(random_bytes(32)));
        }
        return (string) Session::get('_csrf');
    }

    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf" value="' . $token . '">';
    }

    public static function check(?string $token): bool
    {
        return is_string($token)
            && Session::has('_csrf')
            && hash_equals((string) Session::get('_csrf'), $token);
    }

    public static function validate(array $input): void
    {
        if (!self::check($input['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Jeton CSRF invalide ou expiré. Veuillez recharger la page.');
        }
    }
}
