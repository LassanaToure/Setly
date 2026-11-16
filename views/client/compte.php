<section class="container section section--narrow">
    <h1>Mon compte</h1>

    <div class="card detail-block">
        <h2>Mes informations</h2>
        <form action="/mon-compte" method="post" class="form" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="form__row">
                <div class="field">
                    <label for="prenom">Prénom <span class="req">*</span></label>
                    <input type="text" id="prenom" name="prenom" required value="<?= e($user['prenom']) ?>" data-rule="required">
                    <p class="field__error" data-error-for="prenom"><?= e($errors['prenom'] ?? '') ?></p>
                </div>
                <div class="field">
                    <label for="nom">Nom <span class="req">*</span></label>
                    <input type="text" id="nom" name="nom" required value="<?= e($user['nom']) ?>" data-rule="required">
                    <p class="field__error" data-error-for="nom"><?= e($errors['nom'] ?? '') ?></p>
                </div>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" value="<?= e($user['email']) ?>" disabled>
                <p class="field__hint">L'adresse email ne peut pas être modifiée.</p>
            </div>
            <div class="field">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="<?= e($user['telephone'] ?? '') ?>" data-rule="phone">
                <p class="field__error" data-error-for="telephone"></p>
            </div>
            <button type="submit" class="btn btn--primary">Mettre à jour</button>
        </form>
    </div>

    <div class="card detail-block">
        <h2>Changer mon mot de passe</h2>
        <form action="/mon-compte/mot-de-passe" method="post" class="form" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="field">
                <label for="mot_de_passe_actuel">Mot de passe actuel <span class="req">*</span></label>
                <input type="password" id="mot_de_passe_actuel" name="mot_de_passe_actuel" required data-rule="required">
                <p class="field__error" data-error-for="mot_de_passe_actuel"><?= e($errors['mot_de_passe_actuel'] ?? '') ?></p>
            </div>
            <div class="field">
                <label for="mot_de_passe">Nouveau mot de passe <span class="req">*</span></label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required data-rule="required|password">
                <p class="field__hint">Au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.</p>
                <p class="field__error" data-error-for="mot_de_passe"><?= e($errors['mot_de_passe'] ?? '') ?></p>
            </div>
            <div class="field">
                <label for="mot_de_passe_confirm">Confirmer <span class="req">*</span></label>
                <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required data-rule="required|match:mot_de_passe">
                <p class="field__error" data-error-for="mot_de_passe_confirm"></p>
            </div>
            <button type="submit" class="btn btn--primary">Changer le mot de passe</button>
        </form>
    </div>
</section>
