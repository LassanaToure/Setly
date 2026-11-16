<?php use App\Core\Auth; ?>
<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="/">
            <span class="brand__dot" aria-hidden="true"></span>Setly
        </a>

        <button class="nav-toggle" aria-expanded="false" aria-controls="nav-principal" aria-label="Ouvrir le menu">
            <span></span><span></span><span></span>
        </button>

        <nav id="nav-principal" class="site-nav" aria-label="Navigation principale">
            <a class="<?= active('/studios') ?>" href="/studios">Studios</a>
            <a class="<?= active('/materiel') ?>" href="/materiel">Matériel</a>
            <a class="<?= active('/contact') ?>" href="/contact">Contact</a>

            <?php if (Auth::check()): ?>
                <?php if (Auth::hasRole('client')): ?>
                    <a class="<?= active('/mes-reservations') ?>" href="/mes-reservations">Mes réservations</a>
                    <a class="<?= active('/mon-compte') ?>" href="/mon-compte">Mon compte</a>
                <?php endif; ?>
                <?php if (Auth::isStaff()): ?>
                    <a class="btn btn--ghost" href="/back-office">Back-office</a>
                <?php endif; ?>
                <form action="/deconnexion" method="post" class="inline-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn--ghost">Déconnexion</button>
                </form>
            <?php else: ?>
                <a class="<?= active('/connexion') ?>" href="/connexion">Connexion</a>
                <a class="btn btn--primary" href="/inscription">Créer un compte</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
