<section class="container section section--narrow">
    <div class="auth-card">
        <h1>Connexion</h1>
        <p class="muted">Accédez à votre espace Setly.</p>

        <form action="/connexion" method="post" class="form" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="field">
                <label for="email">Email <span class="req">*</span></label>
                <input type="email" id="email" name="email" required value="<?= old($old, 'email') ?>" data-rule="required|email">
                <p class="field__error" data-error-for="email"><?= e($errors['email'] ?? '') ?></p>
            </div>
            <div class="field">
                <label for="mot_de_passe">Mot de passe <span class="req">*</span></label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required data-rule="required">
                <p class="field__error" data-error-for="mot_de_passe"></p>
            </div>
            <button type="submit" class="btn btn--primary btn--block">Se connecter</button>
        </form>
        <p class="auth-card__alt">
            <a href="/mot-de-passe-oublie">Mot de passe oublié ?</a> ·
            Pas de compte ? <a href="/inscription">Créer un compte</a>
        </p>

        <div class="demo-note">
            <p><strong>Comptes de démonstration</strong> (mot de passe : <code>Setly@2025!</code>)</p>
            <ul>
                <li>Client : <code>lea@example.com</code></li>
                <li>Employé : <code>employe@setly.fr</code></li>
                <li>Admin : <code>admin@setly.fr</code></li>
            </ul>
        </div>
    </div>
</section>
