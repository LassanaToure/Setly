<?php use App\Service\ReservationService; ?>
<section class="container section">
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
        <a href="/mes-reservations">Mes réservations</a> <span aria-hidden="true">/</span> <?= e($resa['reference']) ?>
    </nav>

    <div class="detail-head">
        <div>
            <h1>Réservation <?= e($resa['reference']) ?></h1>
            <p class="muted"><?= e($resa['studio_nom']) ?> · <?= e(ucfirst($resa['type_libelle'])) ?></p>
        </div>
        <span class="status status--<?= e($resa['statut']) ?>"><?= e(ReservationService::statutLabel($resa['statut'])) ?></span>
    </div>

    <div class="detail-grid">
        <div class="card detail-block">
            <h2>Détails de la prestation</h2>
            <dl class="data-list">
                <div><dt>Date</dt><dd><?= e(date('d/m/Y', strtotime($resa['date_tournage']))) ?></dd></div>
                <div><dt>Horaire</dt><dd><?= e(substr((string) $resa['heure_debut'], 0, 5)) ?></dd></div>
                <div><dt>Durée</dt><dd><?= (int) $resa['duree_heures'] ?> h</dd></div>
                <div><dt>Personnes</dt><dd><?= (int) $resa['nb_personnes'] ?></dd></div>
                <div><dt>Retrait</dt><dd><?= $resa['mode_retrait'] === 'livraison' ? 'Livraison (' . (int) $resa['livraison_km'] . ' km)' : 'Sur place' ?></dd></div>
            </dl>

            <?php if ($materiels): ?>
                <h3>Matériel loué</h3>
                <ul class="list-mini">
                    <?php foreach ($materiels as $m): ?>
                        <li><?= e($m['nom']) ?> × <?= (int) $m['quantite'] ?> - <?= price((int) $m['prix_unitaire_cents'] * (int) $m['quantite']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="card detail-block">
            <h2>Prix</h2>
            <dl class="recap">
                <div><dt>Prix studio</dt><dd><?= price((int) $resa['prix_studio_cents']) ?></dd></div>
                <?php if ((int) $resa['reduction_cents'] > 0): ?>
                    <div class="recap__reduction"><dt>Réduction journée</dt><dd>−<?= price((int) $resa['reduction_cents']) ?></dd></div>
                <?php endif; ?>
                <div><dt>Matériel</dt><dd><?= price((int) $resa['prix_materiel_cents']) ?></dd></div>
                <div><dt>Livraison</dt><dd><?= price((int) $resa['livraison_cents']) ?></dd></div>
                <div class="recap__total"><dt>Total</dt><dd><?= price((int) $resa['total_cents']) ?></dd></div>
            </dl>

            <?php if ($peutModifier): ?>
                <div class="detail-actions">
                    <a class="btn btn--ghost btn--sm" href="/reservation/<?= (int) $resa['id_reservation'] ?>/modifier">Modifier</a>
                    <form action="/reservation/<?= (int) $resa['id_reservation'] ?>/annuler" method="post"
                          onsubmit="return confirm('Confirmer l\'annulation de cette réservation ?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn--danger btn--sm">Annuler</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($peutAvis): ?>
                <a class="btn btn--primary btn--sm" href="/reservation/<?= (int) $resa['id_reservation'] ?>/avis">Donner mon avis</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($suiviVisible): ?>
        <div class="card detail-block">
            <h2>Suivi de la réservation</h2>
            <ol class="timeline">
                <?php foreach ($suivi as $etape): ?>
                    <li class="timeline__item">
                        <span class="timeline__dot" aria-hidden="true"></span>
                        <div>
                            <p class="timeline__status"><?= e(ReservationService::statutLabel($etape['statut'])) ?></p>
                            <p class="timeline__date"><?= e(date('d/m/Y à H:i', strtotime($etape['date_changement']))) ?></p>
                            <?php if ($etape['commentaire']): ?>
                                <p class="timeline__comment"><?= e($etape['commentaire']) ?></p>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    <?php else: ?>
        <p class="info-box">Le suivi détaillé sera accessible une fois votre réservation confirmée par notre équipe.</p>
    <?php endif; ?>
</section>
