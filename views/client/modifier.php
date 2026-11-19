<section class="container section section--narrow">
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
        <a href="/reservation/<?= (int) $resa['id_reservation'] ?>"><?= e($resa['reference']) ?></a>
        <span aria-hidden="true">/</span> Modifier
    </nav>
    <h1>Modifier la réservation</h1>
    <p class="info-box">Le studio (<strong><?= e($studio['nom']) ?></strong>) ne peut pas être changé. Vous pouvez choisir un autre créneau, ajuster la durée et le nombre de personnes tant que la réservation est en attente.</p>

    <form action="/reservation/<?= (int) $resa['id_reservation'] ?>/modifier" method="post" class="form" novalidate data-validate>
        <?= csrf_field() ?>
        <div class="field">
            <label for="id_creneau">Date et horaire <span class="req">*</span></label>
            <select id="id_creneau" name="id_creneau" required data-rule="required">
                <option value="">- Choisir un créneau -</option>
                <?php foreach ($creneaux as $c): ?>
                    <option value="<?= (int) $c['id_creneau'] ?>" <?= (old($old, 'id_creneau', (string) $currentCreneauId) == $c['id_creneau']) ? 'selected' : '' ?>>
                        <?= e(date('d/m/Y', strtotime($c['date']))) ?> ·
                        <?= e(substr((string) $c['heure_debut'], 0, 5)) ?>-<?= e(substr((string) $c['heure_fin'], 0, 5)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="field__error" data-error-for="id_creneau"><?= e($errors['id_creneau'] ?? '') ?></p>
            <?php if (!$creneaux): ?><p class="field__hint">Aucun créneau disponible pour le moment.</p><?php endif; ?>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="duree_heures">Durée (heures) <span class="req">*</span></label>
                <input type="number" id="duree_heures" name="duree_heures" min="<?= (int) $studio['duree_min_heures'] ?>"
                       value="<?= old($old, 'duree_heures', (string) $resa['duree_heures']) ?>"
                       required data-rule="required|min:<?= (int) $studio['duree_min_heures'] ?>">
                <p class="field__error" data-error-for="duree_heures"><?= e($errors['duree_heures'] ?? '') ?></p>
            </div>
            <div class="field">
                <label for="nb_personnes">Personnes <span class="req">*</span></label>
                <input type="number" id="nb_personnes" name="nb_personnes" min="1" max="<?= (int) $studio['capacite'] ?>"
                       value="<?= old($old, 'nb_personnes', (string) $resa['nb_personnes']) ?>"
                       required data-rule="required|min:1|max:<?= (int) $studio['capacite'] ?>">
                <p class="field__error" data-error-for="nb_personnes"><?= e($errors['nb_personnes'] ?? '') ?></p>
            </div>
        </div>
        <div class="form__actions">
            <a class="btn btn--ghost" href="/reservation/<?= (int) $resa['id_reservation'] ?>">Annuler</a>
            <button type="submit" class="btn btn--primary">Enregistrer</button>
        </div>
    </form>
</section>
