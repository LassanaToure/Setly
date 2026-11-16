<?php use App\Core\Auth; ?>
<aside class="bo__sidebar">
    <a class="brand brand--light bo__brand" href="/"><span class="brand__dot" aria-hidden="true"></span>Setly</a>
    <nav class="bo__nav" aria-label="Navigation back-office">
        <a class="<?= active('/back-office') ?>" href="/back-office">Tableau de bord</a>
        <a class="<?= active('/back-office/reservations') ?>" href="/back-office/reservations">Réservations</a>
        <a class="<?= active('/back-office/studios') ?>" href="/back-office/studios">Studios</a>
        <a class="<?= active('/back-office/materiel') ?>" href="/back-office/materiel">Matériel</a>
        <a class="<?= active('/back-office/creneaux') ?>" href="/back-office/creneaux">Créneaux &amp; horaires</a>
        <a class="<?= active('/back-office/avis') ?>" href="/back-office/avis">Modération des avis</a>

        <?php if (Auth::isAdmin()): ?>
            <p class="bo__nav-section">Administration</p>
            <a class="<?= active('/admin/employes') ?>" href="/admin/employes">Comptes employés</a>
            <a class="<?= active('/admin/statistiques') ?>" href="/admin/statistiques">Statistiques</a>
        <?php endif; ?>
    </nav>
    <a class="bo__back" href="/">← Retour au site</a>
</aside>
