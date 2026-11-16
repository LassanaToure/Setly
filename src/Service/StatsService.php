<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Mongo;
use MongoDB\Driver\Command;
use MongoDB\Driver\Query;

final class StatsService
{

    public static function parStudio(?string $du = null, ?string $au = null, ?string $studio = null): array
    {

        if (!extension_loaded('mongodb')) {
            return [];
        }

        $match = ['statut' => ['$ne' => 'annulee']];
        if ($du) {
            $match['date']['$gte'] = $du;
        }
        if ($au) {
            $match['date']['$lte'] = $au;
        }
        if ($studio) {
            $match['studio'] = $studio;
        }

        $pipeline = [
            ['$match' => $match],
            ['$group' => [
                '_id'           => '$studio',
                'nb'            => ['$sum' => 1],
                'ca_cents'      => ['$sum' => '$montant_cents'],
            ]],
            ['$sort' => ['ca_cents' => -1]],
        ];

        $cmd = new Command([
            'aggregate' => 'fait_reservation',
            'pipeline'  => $pipeline,
            'cursor'    => new \stdClass(),
        ]);

        try {
            $cursor = Mongo::manager()->executeCommand(
                \App\Core\Config::get('mongo.db'),
                $cmd
            );
            $rows = [];
            foreach ($cursor as $doc) {
                $rows[] = [
                    'studio'   => $doc->_id,
                    'nb'       => $doc->nb,
                    'ca_cents' => $doc->ca_cents,
                ];
            }
            return $rows;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function indicateurs(array $parStudio): array
    {
        $totalResa = 0;
        $caTotal   = 0;
        $top       = null;

        foreach ($parStudio as $row) {
            $totalResa += (int) $row['nb'];
            $caTotal   += (int) $row['ca_cents'];
            if ($top === null || $row['nb'] > $top['nb']) {
                $top = $row;
            }
        }

        return [
            'total_reservations' => $totalResa,
            'ca_total_cents'     => $caTotal,
            'studio_top'         => $top['studio'] ?? '—',
        ];
    }
}
