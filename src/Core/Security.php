<?php

declare(strict_types=1);

namespace App\Core;

final class Security
{
    public static function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        header("Content-Security-Policy: "
            . "default-src 'self'; "
            . "img-src 'self' data:; "
            . "style-src 'self' 'unsafe-inline'; "
            . "script-src 'self'; "
            . "form-action 'self'; "
            . "base-uri 'self'; "
            . "frame-ancestors 'none'");

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header_remove('X-Powered-By');
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
