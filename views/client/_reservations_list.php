<?php use App\Service\ReservationService; ?>
<?php if (!$reservations): ?>
    <p class="empty">Aucune réservation pour le moment. <a href="/studios">Réservez un studio</a>.</p>
<?php else: ?>
    <div class="reservation-cards">
        <?php foreach ($reservations as $r): ?>
            <article class="card resa-item">
                <div class="resa-item__main">
                    <p class="resa-item__ref"><?= e($r['reference']) ?></p>
                    <h2><?= e($r['studio_nom']) ?></h2>
                    <p class="muted">
                        <?= e(date('d/m/Y', strtotime($r['date_tournage']))) ?> ·
                        <?= e(substr((string) $r['heure_debut'], 0, 5)) ?> ·
                        <?= (int) $r['duree_heures'] ?> h · <?= (int) $r['nb_personnes'] ?> pers.
                    </p>
                </div>
                <div class="resa-item__side">
                    <span class="status status--<?= e($r['statut']) ?>"><?= e(ReservationService::statutLabel($r['statut'])) ?></span>
                    <p class="price"><?= price((int) $r['total_cents']) ?></p>
                    <a class="btn btn--ghost btn--sm" href="/reservation/<?= (int) $r['id_reservation'] ?>">Détails</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
