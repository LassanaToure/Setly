<div class="card">
    <div class="card__head">
        <h2>Créer un compte employé</h2>
    </div>
    <p class="field__hint">L'employé est informé par email de la création de son compte. Le mot de passe ne lui est pas transmis : il le réinitialise lui-même. Aucun compte administrateur ne peut être créé depuis l'application.</p>

    <form action="/admin/employes" method="post" class="form form--grid" novalidate data-validate>
        <?= csrf_field() ?>
        <div class="field"><label for="e-prenom">Prénom *</label><input id="e-prenom" name="prenom" required value="<?= old($old, 'prenom') ?>" data-rule="required"><p class="field__error" data-error-for="prenom"><?= e($errors['prenom'] ?? '') ?></p></div>
        <div class="field"><label for="e-nom">Nom *</label><input id="e-nom" name="nom" required value="<?= old($old, 'nom') ?>" data-rule="required"><p class="field__error" data-error-for="nom"><?= e($errors['nom'] ?? '') ?></p></div>
        <div class="field field--full"><label for="e-email">Email *</label><input id="e-email" type="email" name="email" required value="<?= old($old, 'email') ?>" data-rule="required|email"><p class="field__error" data-error-for="email"><?= e($errors['email'] ?? '') ?></p></div>
        <div class="field field--full">
            <label for="e-pass">Mot de passe initial *</label>
            <input id="e-pass" type="password" name="mot_de_passe" required data-rule="required|password">
            <p class="field__hint">Au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.</p>
            <p class="field__error" data-error-for="mot_de_passe"><?= e($errors['mot_de_passe'] ?? '') ?></p>
        </div>
        <div class="field field--full"><button class="btn btn--primary" type="submit">Créer le compte</button></div>
    </form>
</div>

<div class="card">
    <div class="card__head"><h2>Comptes employés</h2></div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Nom</th><th>Email</th><th>État</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($employes as $emp): ?>
                    <tr>
                        <td><?= e($emp['prenom'] . ' ' . $emp['nom']) ?></td>
                        <td><?= e($emp['email']) ?></td>
                        <td><?= (int) $emp['actif'] === 1 ? '<span class="status status--confirmee">Actif</span>' : '<span class="status status--annulee">Désactivé</span>' ?></td>
                        <td>
                            <form action="/admin/employes/<?= (int) $emp['id_utilisateur'] ?>/toggle" method="post" class="inline-form">
                                <?= csrf_field() ?>
                                <button class="btn btn--ghost btn--sm" type="submit"><?= (int) $emp['actif'] === 1 ? 'Désactiver' : 'Réactiver' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$employes): ?><tr><td colspan="4" class="empty">Aucun employé enregistré.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
