<div class="bo-cols">
    <div class="card">
        <div class="card__head"><h2>Créneaux</h2></div>

        <form class="filters filters--bo" method="get" action="/back-office/creneaux">
            <div class="field">
                <label for="c-studio">Studio</label>
                <select id="c-studio" name="studio" onchange="this.form.submit()">
                    <?php foreach ($studios as $s): ?>
                        <option value="<?= (int) $s['id_studio'] ?>" <?= (int) $s['id_studio'] === $idStudio ? 'selected' : '' ?>><?= e($s['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <form action="/back-office/creneaux" method="post" class="form form--inline" novalidate data-validate>
            <?= csrf_field() ?>
            <input type="hidden" name="id_studio" value="<?= $idStudio ?>">
            <div class="field"><label for="cr-date">Date *</label><input id="cr-date" type="date" name="date" required data-rule="required"></div>
            <div class="field"><label for="cr-deb">Début *</label><input id="cr-deb" type="time" name="heure_debut" required data-rule="required"></div>
            <div class="field"><label for="cr-fin">Fin *</label><input id="cr-fin" type="time" name="heure_fin" required data-rule="required"></div>
            <button class="btn btn--primary btn--sm" type="submit">Ajouter</button>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Date</th><th>Horaire</th><th>Disponible</th></tr></thead>
                <tbody>
                    <?php foreach ($creneaux as $c): ?>
                        <tr>
                            <td><?= e(date('d/m/Y', strtotime($c['date']))) ?></td>
                            <td><?= e(substr((string) $c['heure_debut'], 0, 5)) ?>-<?= e(substr((string) $c['heure_fin'], 0, 5)) ?></td>
                            <td><?= (int) $c['disponible'] === 1 ? 'Oui' : 'Réservé' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$creneaux): ?><tr><td colspan="3" class="empty">Aucun créneau à venir.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card__head"><h2>Horaires d'ouverture</h2></div>
        <form action="/back-office/horaires" method="post" class="form">
            <?= csrf_field() ?>
            <table class="table table--horaires">
                <thead><tr><th>Jour</th><th>Ouverture</th><th>Fermeture</th><th>Fermé</th></tr></thead>
                <tbody>
                    <?php foreach ($horaires as $h): $id = (int) $h['id_horaire']; ?>
                        <tr>
                            <td><?= e(ucfirst($h['jour_semaine'])) ?></td>
                            <td><input type="time" name="ouverture_<?= $id ?>" value="<?= e(substr((string) $h['heure_ouverture'], 0, 5)) ?>"></td>
                            <td><input type="time" name="fermeture_<?= $id ?>" value="<?= e(substr((string) $h['heure_fermeture'], 0, 5)) ?>"></td>
                            <td><input type="checkbox" name="ferme_<?= $id ?>" value="1" <?= (int) $h['ferme'] === 1 ? 'checked' : '' ?>></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button class="btn btn--primary btn--sm" type="submit">Enregistrer les horaires</button>
        </form>
    </div>
</div>
