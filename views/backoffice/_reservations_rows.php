<?php use App\Service\ReservationService; ?>
<table class="table">
    <thead>
        <tr><th>Référence</th><th>Client</th><th>Studio</th><th>Date</th><th>Total</th><th>Statut</th><th></th></tr>
    </thead>
    <tbody>
        <?php if (!$reservations): ?>
            <tr><td colspan="7" class="empty">Aucune réservation ne correspond à ces filtres.</td></tr>
        <?php else: ?>
            <?php foreach ($reservations as $r): ?>
                <tr>
                    <td><?= e($r['reference']) ?></td>
                    <td><?= e($r['client_prenom'] . ' ' . $r['client_nom']) ?></td>
                    <td><?= e($r['studio_nom']) ?></td>
                    <td><?= e(date('d/m/Y', strtotime($r['date_tournage']))) ?></td>
                    <td><?= price((int) $r['total_cents']) ?></td>
                    <td><span class="status status--<?= e($r['statut']) ?>"><?= e(ReservationService::statutLabel($r['statut'])) ?></span></td>
                    <td><a class="link-more" href="/back-office/reservation/<?= (int) $r['id_reservation'] ?>">Gérer</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
