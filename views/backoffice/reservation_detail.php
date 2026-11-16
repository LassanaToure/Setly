<?php use App\Service\ReservationService; ?>
<a class="link-back" href="/back-office/reservations">← Retour aux réservations</a>

<div class="detail-head">
    <div>
        <h2>Réservation <?= e($resa['reference']) ?></h2>
        <p class="muted"><?= e($resa['client_prenom'] . ' ' . $resa['client_nom']) ?> · <?= e($resa['client_email']) ?></p>
    </div>
    <span class="status status--<?= e($resa['statut']) ?>"><?= e(ReservationService::statutLabel($resa['statut'])) ?></span>
</div>

<div class="detail-grid">
    <div class="card detail-block">
        <h3>Prestation</h3>
        <dl class="data-list">
            <div><dt>Studio</dt><dd><?= e($resa['studio_nom']) ?></dd></div>
            <div><dt>Date</dt><dd><?= e(date('d/m/Y', strtotime($resa['date_tournage']))) ?> à <?= e(substr((string) $resa['heure_debut'], 0, 5)) ?></dd></div>
            <div><dt>Durée</dt><dd><?= (int) $resa['duree_heures'] ?> h</dd></div>
            <div><dt>Personnes</dt><dd><?= (int) $resa['nb_personnes'] ?></dd></div>
            <div><dt>Retrait</dt><dd><?= $resa['mode_retrait'] === 'livraison' ? 'Livraison (' . (int) $resa['livraison_km'] . ' km)' : 'Sur place' ?></dd></div>
            <div><dt>Total</dt><dd><strong><?= price((int) $resa['total_cents']) ?></strong></dd></div>
        </dl>
        <?php if ($materiels): ?>
            <h4>Matériel</h4>
            <ul class="list-mini">
                <?php foreach ($materiels as $m): ?>
                    <li><?= e($m['nom']) ?> × <?= (int) $m['quantite'] ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card detail-block">
        <h3>Changer le statut</h3>
        <?php if ($transitions): ?>
            <form action="/back-office/reservation/<?= (int) $resa['id_reservation'] ?>/statut" method="post" class="form" id="form-statut" data-validate novalidate>
                <?= csrf_field() ?>
                <div class="field">
                    <label for="statut">Nouveau statut</label>
                    <select id="statut" name="statut" data-statut-select>
                        <?php foreach ($transitions as $to): ?>
                            <option value="<?= e($to) ?>"><?= e($statuts[$to]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="contact-box" data-contact-box <?= in_array('annulee', $transitions, true) ? '' : 'hidden' ?>>
                    <p class="field__hint"><strong>Annulation :</strong> le client doit avoir été contacté au préalable.</p>
                    <div class="field">
                        <label for="contact_mode">Mode de contact</label>
                        <select id="contact_mode" name="contact_mode">
                            <option value="">-</option>
                            <option value="appel">Appel téléphonique</option>
                            <option value="email">Email</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="motif">Motif</label>
                        <textarea id="motif" name="motif" rows="2" placeholder="Motif communiqué au client"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary">Appliquer</button>
            </form>
        <?php else: ?>
            <p class="muted">Aucune transition possible depuis ce statut (<?= e(ReservationService::statutLabel($resa['statut'])) ?>).</p>
        <?php endif; ?>
    </div>
</div>

<div class="card detail-block">
    <h3>Historique</h3>
    <ol class="timeline">
        <?php foreach ($suivi as $etape): ?>
            <li class="timeline__item">
                <span class="timeline__dot" aria-hidden="true"></span>
                <div>
                    <p class="timeline__status"><?= e(ReservationService::statutLabel($etape['statut'])) ?></p>
                    <p class="timeline__date"><?= e(date('d/m/Y à H:i', strtotime($etape['date_changement']))) ?></p>
                    <?php if ($etape['commentaire']): ?><p class="timeline__comment"><?= e($etape['commentaire']) ?></p><?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
</div>
