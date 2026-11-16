<div class="card">
    <div class="card__head">
        <h2>Matériel</h2>
        <button class="btn btn--primary btn--sm" type="button" data-toggle="#form-materiel">+ Nouveau matériel</button>
    </div>

    <div class="collapsible" id="form-materiel" hidden>
        <form action="/back-office/materiel" method="post" class="form form--grid" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="field"><label for="m-nom">Nom *</label><input id="m-nom" name="nom" required data-rule="required"></div>
            <div class="field"><label for="m-prix">Prix / jour (€) *</label><input id="m-prix" name="prix_unitaire" type="number" step="0.01" min="0" required data-rule="required"></div>
            <div class="field field--full"><label for="m-desc">Description</label><textarea id="m-desc" name="description" rows="2"></textarea></div>
            <div class="field field--full"><button class="btn btn--primary" type="submit">Créer le matériel</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Nom</th><th>Prix/jour</th><th>État</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($materiel as $m): ?>
                    <tr>
                        <td><?= e($m['nom']) ?></td>
                        <td><?= price((int) $m['prix_unitaire_cents']) ?></td>
                        <td><?= (int) $m['actif'] === 1 ? '<span class="status status--confirmee">Actif</span>' : '<span class="status status--annulee">Archivé</span>' ?></td>
                        <td>
                            <form action="/back-office/materiel/<?= (int) $m['id_materiel'] ?>/archiver" method="post" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="actif" value="<?= (int) $m['actif'] === 1 ? '0' : '1' ?>">
                                <button class="btn btn--ghost btn--sm" type="submit"><?= (int) $m['actif'] === 1 ? 'Archiver' : 'Réactiver' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
