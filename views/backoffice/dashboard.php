<?php
use App\Service\ReservationService;
$parStatut = [];
foreach ($reservations as $r) { $parStatut[$r['statut']] = ($parStatut[$r['statut']] ?? 0) + 1; }
$ca = 0;
foreach ($reservations as $r) { if ($r['statut'] !== 'annulee') { $ca += (int) $r['total_cents']; } }
?>
<div class="kpi-row">
    <div class="kpi">
        <p class="kpi__value"><?= count($reservations) ?></p>
        <p class="kpi__label">Réservations totales</p>
    </div>
    <div class="kpi">
        <p class="kpi__value"><?= $parStatut['en_attente'] ?? 0 ?></p>
        <p class="kpi__label">En attente</p>
    </div>
    <div class="kpi">
        <p class="kpi__value"><?= price($ca) ?></p>
        <p class="kpi__label">Chiffre d'affaires</p>
    </div>
    <div class="kpi">
        <p class="kpi__value"><?= (int) $avisEnAttente ?></p>
        <p class="kpi__label">Avis à modérer</p>
    </div>
</div>

<div class="card">
    <div class="card__head">
        <h2>Dernières réservations</h2>
        <a class="btn btn--ghost btn--sm" href="/back-office/reservations">Tout voir</a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Référence</th><th>Client</th><th>Studio</th><th>Date</th><th>Total</th><th>Statut</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($reservations, 0, 8) as $r): ?>
                    <tr>
                        <td><?= e($r['reference']) ?></td>
                        <td><?= e($r['client_prenom'] . ' ' . $r['client_nom']) ?></td>
                        <td><?= e($r['studio_nom']) ?></td>
                        <td><?= e(date('d/m/Y', strtotime($r['date_tournage']))) ?></td>
                        <td><?= price((int) $r['total_cents']) ?></td>
                        <td><span class="status status--<?= e($r['statut']) ?>"><?= e(ReservationService::statutLabel($r['statut'])) ?></span></td>
                        <td><a class="link-more" href="/back-office/reservation/<?= (int) $r['id_reservation'] ?>">Ouvrir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
