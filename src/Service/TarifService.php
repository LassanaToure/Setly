<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Config;
use App\Model\Materiel;

final class TarifService
{

    public static function compute(
        array $studio,
        int $dureeHeures,
        array $materielQty,
        string $modeRetrait,
        int $km
    ): array {
        $seuil = (int) Config::get('metier.reduction_journee_seuil_h');
        $taux  = (float) Config::get('metier.reduction_journee_taux');

        $prixStudio = (int) $studio['tarif_horaire_cents'] * $dureeHeures;

        $reduction = 0;
        if ($dureeHeures >= $seuil) {
            $reduction = (int) round($prixStudio * $taux);
        }
        $prixStudioNet = $prixStudio - $reduction;

        $prixMateriel = 0;
        $lignesMateriel = [];
        if ($materielQty !== []) {
            $materiels = Materiel::findMany(array_keys($materielQty));
            foreach ($materielQty as $id => $qte) {
                $qte = max(0, (int) $qte);
                if ($qte === 0 || !isset($materiels[(int) $id])) {
                    continue;
                }
                $m = $materiels[(int) $id];
                $ligne = (int) $m['prix_unitaire_cents'] * $qte;
                $prixMateriel += $ligne;
                $lignesMateriel[] = [
                    'id'       => (int) $id,
                    'nom'      => $m['nom'],
                    'quantite' => $qte,
                    'prix_unitaire_cents' => (int) $m['prix_unitaire_cents'],
                    'total_cents' => $ligne,
                ];
            }
        }

        $livraison = 0;
        if ($modeRetrait === 'livraison') {
            $base = (int) Config::get('metier.livraison_base_cents');
            $parKm = (int) Config::get('metier.livraison_km_cents');
            $livraison = $base + ($parKm * max(0, $km));
        }

        $total = $prixStudioNet + $prixMateriel + $livraison;

        return [
            'prix_studio_cents'   => $prixStudio,
            'reduction_cents'     => $reduction,
            'prix_studio_net_cents' => $prixStudioNet,
            'prix_materiel_cents' => $prixMateriel,
            'lignes_materiel'     => $lignesMateriel,
            'livraison_cents'     => $livraison,
            'total_cents'         => $total,
        ];
    }

    public static function format(int $cents): string
    {
        return number_format($cents / 100, 2, ',', ' ') . ' €';
    }
}
