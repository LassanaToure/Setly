<section class="container section section--narrow">
    <div class="auth-card">
        <h1>Mot de passe oublié</h1>
        <p class="muted">Indiquez votre email : nous vous enverrons un lien de réinitialisation.</p>

        <form action="/mot-de-passe-oublie" method="post" class="form" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="field">
                <label for="email">Email <span class="req">*</span></label>
                <input type="email" id="email" name="email" required value="<?= old($old, 'email') ?>" data-rule="required|email">
                <p class="field__error" data-error-for="email"><?= e($errors['email'] ?? '') ?></p>
            </div>
            <button type="submit" class="btn btn--primary btn--block">Envoyer le lien</button>
        </form>
        <p class="auth-card__alt"><a href="/connexion">Retour à la connexion</a></p>
    </div>
</section>
