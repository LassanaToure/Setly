<?php use App\Core\View; ?>
<div class="card">
    <form class="filters filters--bo" id="filtres-bo-reservations" aria-label="Filtres des réservations">
        <div class="field">
            <label for="f-statut">Statut</label>
            <select id="f-statut" name="statut">
                <option value="">Tous</option>
                <?php foreach ($statuts as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= ($filters['statut'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field field--grow">
            <label for="f-client">Client</label>
            <input type="search" id="f-client" name="client" placeholder="Nom, prénom ou email" value="<?= e($filters['client'] ?? '') ?>">
        </div>
    </form>

    <div class="table-wrap" id="bo-reservations-table" aria-live="polite">
        <?= View::partial('backoffice/_reservations_rows', ['reservations' => $reservations, 'statuts' => $statuts]) ?>
    </div>
</div>
