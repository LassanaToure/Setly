<?php use App\Service\ReservationService; ?>
<section class="container section">
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
        <a href="/studios/<?= (int) $studio['id_studio'] ?>"><?= e($studio['nom']) ?></a>
        <span aria-hidden="true">/</span> Réserver
    </nav>
    <h1>Réserver - <?= e($studio['nom']) ?></h1>

    <form action="/reserver" method="post" class="reservation"
          novalidate data-validate
          data-studio="<?= (int) $studio['id_studio'] ?>"
          data-tarif-horaire="<?= (int) $studio['tarif_horaire_cents'] ?>"
          data-duree-min="<?= (int) $studio['duree_min_heures'] ?>"
          data-capacite="<?= (int) $studio['capacite'] ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id_studio" value="<?= (int) $studio['id_studio'] ?>">

        <div class="reservation__grid">
            <div class="reservation__form">
                <fieldset>
                    <legend>1. Créneau</legend>
                    <div class="field">
                        <label for="id_creneau">Date et horaire <span class="req">*</span></label>
                        <select id="id_creneau" name="id_creneau" required data-rule="required">
                            <option value="">- Choisir un créneau -</option>
                            <?php foreach ($creneaux as $c): ?>
                                <option value="<?= (int) $c['id_creneau'] ?>">
                                    <?= e(date('d/m/Y', strtotime($c['date']))) ?> ·
                                    <?= e(substr((string) $c['heure_debut'], 0, 5)) ?>-<?= e(substr((string) $c['heure_fin'], 0, 5)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="field__error" data-error-for="id_creneau"><?= e($errors['id_creneau'] ?? '') ?></p>
                        <?php if (!$creneaux): ?><p class="field__hint">Aucun créneau disponible pour le moment.</p><?php endif; ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>2. Prestation</legend>
                    <div class="form__row">
                        <div class="field">
                            <label for="duree_heures">Durée (heures) <span class="req">*</span></label>
                            <input type="number" id="duree_heures" name="duree_heures" min="<?= (int) $studio['duree_min_heures'] ?>"
                                   value="<?= old($old, 'duree_heures', (string) $studio['duree_min_heures']) ?>"
                                   required data-rule="required|min:<?= (int) $studio['duree_min_heures'] ?>">
                            <p class="field__hint">Minimum <?= (int) $studio['duree_min_heures'] ?> h. Réduction de 10 % dès 8 h (journée).</p>
                            <p class="field__error" data-error-for="duree_heures"></p>
                        </div>
                        <div class="field">
                            <label for="nb_personnes">Nombre de personnes <span class="req">*</span></label>
                            <input type="number" id="nb_personnes" name="nb_personnes" min="1" max="<?= (int) $studio['capacite'] ?>"
                                   value="<?= old($old, 'nb_personnes', '1') ?>"
                                   required data-rule="required|min:1|max:<?= (int) $studio['capacite'] ?>">
                            <p class="field__hint">Capacité maximum : <?= (int) $studio['capacite'] ?> personnes.</p>
                            <p class="field__error" data-error-for="nb_personnes"></p>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>3. Matériel (optionnel)</legend>
                    <div class="materiel-list">
                        <?php foreach ($materiel as $m): ?>
                            <div class="materiel-row">
                                <div>
                                    <span class="materiel-row__name"><?= e($m['nom']) ?></span>
                                    <span class="muted"><?= price((int) $m['prix_unitaire_cents']) ?> / jour</span>
                                </div>
                                <input type="number" class="materiel-qty" name="materiel[<?= (int) $m['id_materiel'] ?>]"
                                       min="0" value="0" aria-label="Quantité <?= e($m['nom']) ?>"
                                       data-prix="<?= (int) $m['prix_unitaire_cents'] ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>4. Retrait du matériel</legend>
                    <div class="radio-group">
                        <label class="radio"><input type="radio" name="mode_retrait" value="sur_place" checked data-mode> Retrait sur place (gratuit)</label>
                        <label class="radio"><input type="radio" name="mode_retrait" value="livraison" data-mode> Livraison (5,00 € + 0,59 €/km hors Paris)</label>
                    </div>
                    <div class="field" id="champ-km" hidden>
                        <label for="livraison_km">Distance hors Paris (km)</label>
                        <input type="number" id="livraison_km" name="livraison_km" min="0" value="0" data-rule="min:0">
                        <p class="field__error" data-error-for="livraison_km"><?= e($errors['livraison_km'] ?? '') ?></p>
                    </div>
                </fieldset>
            </div>

            <aside class="reservation__summary" aria-label="Récapitulatif du prix">
                <h2>Récapitulatif</h2>
                <dl class="recap" id="recap-prix">
                    <div><dt>Prix studio</dt><dd data-recap="prix_studio">-</dd></div>
                    <div class="recap__reduction" data-recap-reduction hidden><dt>Réduction journée (−10 %)</dt><dd data-recap="reduction">-</dd></div>
                    <div><dt>Matériel</dt><dd data-recap="prix_materiel">-</dd></div>
                    <div><dt>Livraison</dt><dd data-recap="livraison">-</dd></div>
                    <div class="recap__total"><dt>Total</dt><dd data-recap="total">-</dd></div>
                </dl>
                <p class="recap__note">Montant calculé et vérifié côté serveur avant validation.</p>

                <div class="conditions-mini">
                    <h3>Conditions du studio</h3>
                    <p><?= nl2br(e($studio['conditions'])) ?></p>
                </div>

                <button type="submit" class="btn btn--primary btn--block btn--lg">Valider la réservation</button>
            </aside>
        </div>
    </form>
</section>
