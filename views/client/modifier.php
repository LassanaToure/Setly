<section class="container section section--narrow">
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
        <a href="/reservation/<?= (int) $resa['id_reservation'] ?>"><?= e($resa['reference']) ?></a>
        <span aria-hidden="true">/</span> Modifier
    </nav>
    <h1>Modifier la réservation</h1>
    <p class="info-box">Le studio (<strong><?= e($studio['nom']) ?></strong>) ne peut pas être changé. Vous pouvez ajuster la date, l'horaire, la durée et le nombre de personnes tant que la réservation est en attente.</p>

    <form action="/reservation/<?= (int) $resa['id_reservation'] ?>/modifier" method="post" class="form" novalidate data-validate>
        <?= csrf_field() ?>
        <div class="field">
            <label for="date_tournage">Date <span class="req">*</span></label>
            <input type="date" id="date_tournage" name="date_tournage" required
                   value="<?= old($old, 'date_tournage', $resa['date_tournage']) ?>" data-rule="required">
            <p class="field__error" data-error-for="date_tournage"><?= e($errors['date_tournage'] ?? '') ?></p>
        </div>
        <div class="field">
            <label for="heure_debut">Horaire de début <span class="req">*</span></label>
            <input type="time" id="heure_debut" name="heure_debut" required
                   value="<?= old($old, 'heure_debut', substr((string) $resa['heure_debut'], 0, 5)) ?>" data-rule="required">
            <p class="field__error" data-error-for="heure_debut"></p>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="duree_heures">Durée (heures) <span class="req">*</span></label>
                <input type="number" id="duree_heures" name="duree_heures" min="<?= (int) $studio['duree_min_heures'] ?>"
                       value="<?= old($old, 'duree_heures', (string) $resa['duree_heures']) ?>"
                       required data-rule="required|min:<?= (int) $studio['duree_min_heures'] ?>">
                <p class="field__error" data-error-for="duree_heures"></p>
            </div>
            <div class="field">
                <label for="nb_personnes">Personnes <span class="req">*</span></label>
                <input type="number" id="nb_personnes" name="nb_personnes" min="1" max="<?= (int) $studio['capacite'] ?>"
                       value="<?= old($old, 'nb_personnes', (string) $resa['nb_personnes']) ?>"
                       required data-rule="required|min:1|max:<?= (int) $studio['capacite'] ?>">
                <p class="field__error" data-error-for="nb_personnes"></p>
            </div>
        </div>
        <div class="form__actions">
            <a class="btn btn--ghost" href="/reservation/<?= (int) $resa['id_reservation'] ?>">Annuler</a>
            <button type="submit" class="btn btn--primary">Enregistrer</button>
        </div>
    </form>
</section>
