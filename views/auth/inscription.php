<section class="container section section--narrow">
    <div class="auth-card">
        <h1>Créer un compte</h1>
        <p class="muted">Rejoignez Setly pour réserver un studio.</p>

        <form action="/inscription" method="post" class="form" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="form__row">
                <div class="field">
                    <label for="prenom">Prénom <span class="req">*</span></label>
                    <input type="text" id="prenom" name="prenom" required value="<?= old($old, 'prenom') ?>" data-rule="required">
                    <p class="field__error" data-error-for="prenom"><?= e($errors['prenom'] ?? '') ?></p>
                </div>
                <div class="field">
                    <label for="nom">Nom <span class="req">*</span></label>
                    <input type="text" id="nom" name="nom" required value="<?= old($old, 'nom') ?>" data-rule="required">
                    <p class="field__error" data-error-for="nom"><?= e($errors['nom'] ?? '') ?></p>
                </div>
            </div>

            <div class="field">
                <label for="email">Email <span class="req">*</span></label>
                <input type="email" id="email" name="email" required value="<?= old($old, 'email') ?>" data-rule="required|email">
                <p class="field__error" data-error-for="email"><?= e($errors['email'] ?? '') ?></p>
            </div>

            <div class="field">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="<?= old($old, 'telephone') ?>" data-rule="phone">
                <p class="field__error" data-error-for="telephone"></p>
            </div>

            <div class="field">
                <label for="mot_de_passe">Mot de passe <span class="req">*</span></label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required data-rule="required|password">
                <p class="field__hint">Au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.</p>
                <p class="field__error" data-error-for="mot_de_passe"><?= e($errors['mot_de_passe'] ?? '') ?></p>
            </div>

            <div class="field">
                <label for="mot_de_passe_confirm">Confirmer le mot de passe <span class="req">*</span></label>
                <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required data-rule="required|match:mot_de_passe">
                <p class="field__error" data-error-for="mot_de_passe_confirm"><?= e($errors['mot_de_passe_confirm'] ?? '') ?></p>
            </div>

            <button type="submit" class="btn btn--primary btn--block">S'inscrire</button>
        </form>
        <p class="auth-card__alt">Déjà un compte ? <a href="/connexion">Se connecter</a></p>
    </div>
</section>
