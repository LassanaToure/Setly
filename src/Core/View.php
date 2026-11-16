<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    private static string $base = __DIR__ . '/../../views/';

    public static function render(string $template, array $data = [], string $layout = 'layouts/main'): void
    {
        $content = self::partial($template, $data);

        $data['content'] = $content;
        echo self::partial($layout, $data);
    }

    public static function partial(string $template, array $data = []): string
    {
        $file = self::$base . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Vue introuvable : {$template}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }

    public static function e(?string $value): string
    {
        return Security::e($value);
    }
}
