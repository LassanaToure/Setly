<div class="card">
    <div class="card__head">
        <h2>Studios</h2>
        <button class="btn btn--primary btn--sm" type="button" data-toggle="#form-studio">+ Nouveau studio</button>
    </div>

    <div class="collapsible" id="form-studio" hidden>
        <form action="/back-office/studios" method="post" class="form form--grid" novalidate data-validate>
            <?= csrf_field() ?>
            <div class="field"><label for="s-nom">Nom *</label><input id="s-nom" name="nom" required data-rule="required"></div>
            <div class="field">
                <label for="s-type">Type *</label>
                <select id="s-type" name="id_type" required>
                    <?php foreach ($types as $t): ?><option value="<?= (int) $t['id_type'] ?>"><?= e(ucfirst($t['libelle'])) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="field"><label for="s-cap">Capacité *</label><input id="s-cap" name="capacite" type="number" min="1" required data-rule="required|min:1"></div>
            <div class="field"><label for="s-surf">Surface (m²)</label><input id="s-surf" name="surface_m2" type="number" min="0"></div>
            <div class="field"><label for="s-tarif">Tarif horaire (€) *</label><input id="s-tarif" name="tarif_horaire" type="number" step="0.01" min="0" required data-rule="required"></div>
            <div class="field"><label for="s-duree">Durée min. (h) *</label><input id="s-duree" name="duree_min_heures" type="number" min="1" value="2" required data-rule="required|min:1"></div>
            <div class="field field--full"><label for="s-desc">Description</label><textarea id="s-desc" name="description" rows="2"></textarea></div>
            <div class="field field--full"><label for="s-cond">Conditions</label><textarea id="s-cond" name="conditions" rows="2"></textarea></div>
            <div class="field field--full"><button class="btn btn--primary" type="submit">Créer le studio</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Nom</th><th>Type</th><th>Capacité</th><th>Tarif/h</th><th>État</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($studios as $s): ?>
                    <tr>
                        <td><?= e($s['nom']) ?></td>
                        <td><?= e(ucfirst($s['type_libelle'])) ?></td>
                        <td><?= (int) $s['capacite'] ?></td>
                        <td><?= price((int) $s['tarif_horaire_cents']) ?></td>
                        <td><?= (int) $s['actif'] === 1 ? '<span class="status status--confirmee">Actif</span>' : '<span class="status status--annulee">Archivé</span>' ?></td>
                        <td>
                            <form action="/back-office/studios/<?= (int) $s['id_studio'] ?>/archiver" method="post" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="actif" value="<?= (int) $s['actif'] === 1 ? '0' : '1' ?>">
                                <button class="btn btn--ghost btn--sm" type="submit"><?= (int) $s['actif'] === 1 ? 'Archiver' : 'Réactiver' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
