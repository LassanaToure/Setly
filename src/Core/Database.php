<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = Config::get('db.host');
        $port = Config::get('db.port');
        $name = Config::get('db.name');
        $dsn  = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $ca = (string) Config::get('db.ssl_ca');
        if ($ca !== '') {
            $path = sys_get_temp_dir() . '/aiven-ca.pem';
            if (!is_file($path)) {
                file_put_contents($path, $ca);
            }
            $options[PDO::MYSQL_ATTR_SSL_CA] = $path;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
        }

        try {
            self::$pdo = new PDO($dsn, Config::get('db.user'), Config::get('db.pass'), $options);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Connexion à la base de données impossible.');
        }

        return self::$pdo;
    }
}
