<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Security;
use App\Service\TarifService;

if (!function_exists('e')) {

    function e(?string $value): string
    {
        return Security::e($value);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('price')) {

    function price(int $cents): string
    {
        return TarifService::format($cents);
    }
}

if (!function_exists('old')) {

    function old(array $source, string $key, string $default = ''): string
    {
        return Security::e((string) ($source[$key] ?? $default));
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return Auth::user();
    }
}

if (!function_exists('active')) {

    function active(string $path, string $class = 'is-active'): string
    {
        $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        return $current === $path ? $class : '';
    }
}
