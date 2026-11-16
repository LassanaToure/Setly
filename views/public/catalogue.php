<?php use App\Core\View; ?>
<section class="container section">
    <div class="section__head section__head--catalogue">
        <div>
            <h1>Nos studios</h1>
            <p class="muted">Filtrez sans rechargement de page pour trouver le studio idéal.</p>
        </div>
    </div>

    <div class="catalogue">
        <form class="filters" id="filtres-studios" aria-label="Filtres du catalogue">
            <div class="field">
                <label for="f-type">Type</label>
                <select id="f-type" name="type">
                    <option value="">Tous les types</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= e($t['libelle']) ?>" <?= ($filters['type'] ?? '') === $t['libelle'] ? 'selected' : '' ?>>
                            <?= e(ucfirst($t['libelle'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="f-prix">Prix max (€/h)</label>
                <input type="number" id="f-prix" name="prix_max" min="0" step="5" value="<?= e($filters['prix_max'] ?? '') ?>" placeholder="ex. 90">
            </div>
            <div class="field">
                <label for="f-cap">Capacité min.</label>
                <input type="number" id="f-cap" name="capacite" min="1" value="<?= e($filters['capacite'] ?? '') ?>" placeholder="ex. 4">
            </div>
            <div class="field">
                <label for="f-date">Disponible le</label>
                <input type="date" id="f-date" name="date" value="<?= e($filters['date'] ?? '') ?>">
            </div>
            <button type="reset" class="btn btn--ghost btn--sm" id="reset-filtres">Réinitialiser</button>
        </form>

        <div id="studios-resultats" aria-live="polite">
            <?= View::partial('public/_studios_grid', ['studios' => $studios]) ?>
        </div>
    </div>
</section>
