<section class="container section section--narrow">
    <h1>Donner un avis</h1>
    <p class="muted">Partagez votre expérience sur <strong><?= e($resa['studio_nom']) ?></strong>. Votre avis sera publié après modération.</p>

    <form action="/reservation/<?= (int) $resa['id_reservation'] ?>/avis" method="post" class="form" novalidate data-validate>
        <?= csrf_field() ?>
        <div class="field">
            <label>Note <span class="req">*</span></label>
            <div class="star-input" role="radiogroup" aria-label="Note de 1 à 5">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="note-<?= $i ?>" name="note" value="<?= $i ?>" data-rule="required" <?= (int) old($old, 'note') === $i ? 'checked' : '' ?>>
                    <label for="note-<?= $i ?>" title="<?= $i ?> étoile<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <p class="field__error" data-error-for="note"><?= e($errors['note'] ?? '') ?></p>
        </div>
        <div class="field">
            <label for="commentaire">Commentaire <span class="req">*</span></label>
            <textarea id="commentaire" name="commentaire" rows="5" required data-rule="required"><?= old($old, 'commentaire') ?></textarea>
            <p class="field__error" data-error-for="commentaire"><?= e($errors['commentaire'] ?? '') ?></p>
        </div>
        <div class="form__actions">
            <a class="btn btn--ghost" href="/reservation/<?= (int) $resa['id_reservation'] ?>">Retour</a>
            <button type="submit" class="btn btn--primary">Publier mon avis</button>
        </div>
    </form>
</section>
