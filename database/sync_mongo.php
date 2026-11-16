<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Mongo;
use App\Model\Reservation;
use MongoDB\Driver\BulkWrite;

require __DIR__ . '/../src/bootstrap.php';

$reservations = Reservation::allForSync();

$bulk = new BulkWrite();

$bulk->delete([]);

foreach ($reservations as $r) {
    $bulk->insert([
        'reference'     => $r['reference'],
        'studio'        => $r['studio'],
        'type_studio'   => $r['type_studio'],
        'montant_cents' => (int) $r['total_cents'],
        'date'          => $r['date_tournage'],
        'statut'        => $r['statut'],
    ]);
}

$namespace = Config::get('mongo.db') . '.fait_reservation';
$result = Mongo::manager()->executeBulkWrite($namespace, $bulk);

printf(
    "Synchronisation terminée : %d réservations insérées dans %s.\n",
    count($reservations),
    $namespace
);
