<section class="container section section--narrow">
    <h1>Contact</h1>
    <p class="muted">Une question sur nos studios ou votre réservation ? Écrivez-nous.</p>

    <form action="/contact" method="post" class="form" novalidate data-validate>
        <?= csrf_field() ?>
        <div class="field">
            <label for="nom">Nom <span class="req">*</span></label>
            <input type="text" id="nom" name="nom" required value="<?= old($old, 'nom') ?>" data-rule="required">
            <p class="field__error" data-error-for="nom"><?= e($errors['nom'] ?? '') ?></p>
        </div>
        <div class="field">
            <label for="email">Email <span class="req">*</span></label>
            <input type="email" id="email" name="email" required value="<?= old($old, 'email') ?>" data-rule="required|email">
            <p class="field__error" data-error-for="email"><?= e($errors['email'] ?? '') ?></p>
        </div>
        <div class="field">
            <label for="message">Message <span class="req">*</span></label>
            <textarea id="message" name="message" rows="5" required data-rule="required"><?= old($old, 'message') ?></textarea>
            <p class="field__error" data-error-for="message"><?= e($errors['message'] ?? '') ?></p>
        </div>
        <button type="submit" class="btn btn--primary">Envoyer</button>
    </form>
</section>
