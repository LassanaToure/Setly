<div class="stats"
     data-stats
     data-du="<?= e($filtres['du'] ?? '') ?>"
     data-au="<?= e($filtres['au'] ?? '') ?>"
     data-studio="<?= e($filtres['studio'] ?? '') ?>">

    <div class="card">
        <form class="filters filters--bo" id="filtres-stats">
            <div class="field"><label for="st-du">Du</label><input type="date" id="st-du" name="du" value="<?= e($filtres['du'] ?? '') ?>"></div>
            <div class="field"><label for="st-au">Au</label><input type="date" id="st-au" name="au" value="<?= e($filtres['au'] ?? '') ?>"></div>
            <div class="field">
                <label for="st-studio">Studio</label>
                <select id="st-studio" name="studio">
                    <option value="">Tous</option>
                    <?php foreach ($studios as $s): ?>
                        <option value="<?= e($s['nom']) ?>" <?= ($filtres['studio'] ?? '') === $s['nom'] ? 'selected' : '' ?>><?= e($s['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <div class="kpi-row" id="stats-kpi">
        <div class="kpi"><p class="kpi__value" data-kpi="total"><?= (int) $indicateurs['total_reservations'] ?></p><p class="kpi__label">Réservations</p></div>
        <div class="kpi"><p class="kpi__value" data-kpi="ca"><?= price((int) $indicateurs['ca_total_cents']) ?></p><p class="kpi__label">Chiffre d'affaires</p></div>
        <div class="kpi"><p class="kpi__value" data-kpi="top"><?= e($indicateurs['studio_top']) ?></p><p class="kpi__label">Studio le plus réservé</p></div>
    </div>

    <div class="stats-grid">
        <div class="card">
            <h2>Réservations par studio</h2>
            <canvas id="chart-reservations" height="220" role="img" aria-label="Graphique des réservations par studio"></canvas>
        </div>
        <div class="card">
            <h2>Chiffre d'affaires par studio (€)</h2>
            <canvas id="chart-ca" height="220" role="img" aria-label="Graphique du chiffre d'affaires par studio"></canvas>
        </div>
    </div>

    <div class="card">
        <h2>Détail</h2>
        <div class="table-wrap">
            <table class="table" id="stats-table">
                <thead><tr><th>Studio</th><th>Réservations</th><th>Chiffre d'affaires</th></tr></thead>
                <tbody>
                    <?php foreach ($parStudio as $row): ?>
                        <tr>
                            <td><?= e($row['studio']) ?></td>
                            <td><?= (int) $row['nb'] ?></td>
                            <td><?= price((int) $row['ca_cents']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$parStudio): ?>
                        <tr><td colspan="3" class="empty">Aucune donnée. Lancez <code>make sync-mongo</code> pour alimenter les statistiques.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="field__hint">Données issues de la base NoSQL MongoDB (collection <code>fait_reservation</code>), agrégées par studio.</p>
    </div>
</div>
