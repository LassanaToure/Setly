<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set('user', [
            'id'     => (int) $user['id_utilisateur'],
            'nom'    => $user['nom'],
            'prenom' => $user['prenom'],
            'email'  => $user['email'],
            'role'   => $user['role'],
        ]);
    }

    public static function logout(): void
    {
        Session::remove('user');
        Session::regenerate();
    }

    public static function check(): bool
    {
        return Session::has('user');
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function role(): ?string
    {
        return self::user()['role'] ?? null;
    }

    public static function hasRole(string ...$roles): bool
    {
        return self::check() && in_array(self::role(), $roles, true);
    }

    public static function isStaff(): bool
    {
        return self::hasRole('employe', 'administrateur');
    }

    public static function isAdmin(): bool
    {
        return self::hasRole('administrateur');
    }
}
