<?php

declare(strict_types=1);

namespace App\Core;

use MongoDB\Driver\Manager;

final class Mongo
{
    private static ?Manager $manager = null;

    public static function manager(): Manager
    {
        if (self::$manager === null) {
            self::$manager = new Manager(Config::get('mongo.uri'));
        }

        return self::$manager;
    }

    public static function namespace(string $collection): string
    {
        return Config::get('mongo.db') . '.' . $collection;
    }
}
