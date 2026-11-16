<?php use App\Core\View; ?>
<section class="container section">
    <h1>Mes réservations</h1>

    <div class="tabs" id="filtre-reservations" role="tablist" aria-label="Filtrer mes réservations">
        <button class="tab <?= $filtre === '' ? 'is-active' : '' ?>" data-filtre="" role="tab">Toutes</button>
        <button class="tab <?= $filtre === 'en_cours' ? 'is-active' : '' ?>" data-filtre="en_cours" role="tab">En cours</button>
        <button class="tab <?= $filtre === 'terminees' ? 'is-active' : '' ?>" data-filtre="terminees" role="tab">Terminées</button>
    </div>

    <div id="reservations-liste" aria-live="polite">
        <?= View::partial('client/_reservations_list', ['reservations' => $reservations]) ?>
    </div>
</section>
