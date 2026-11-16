<?php

declare(strict_types=1);

return [
    'app' => [
        'name'   => 'Setly',
        'env'    => getenv('APP_ENV') ?: 'local',
        'url'    => getenv('APP_URL') ?: 'http://localhost:8080',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'setly',
        'user' => getenv('DB_USER') ?: 'setly',
        'pass' => getenv('DB_PASS') ?: 'setly_pass',
    ],
    'mongo' => [
        'uri' => getenv('MONGO_URI') ?: 'mongodb://127.0.0.1:27017',
        'db'  => getenv('MONGO_DB') ?: 'setly_stats',
    ],

    'metier' => [
        'reduction_journee_seuil_h' => 8,
        'reduction_journee_taux'    => 0.10,
        'livraison_base_cents'      => 500,
        'livraison_km_cents'        => 59,
        'frais_retard_materiel_cents' => 60000,
        'delai_retour_jours_ouvres' => 10,
    ],
];
