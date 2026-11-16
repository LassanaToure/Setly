<?php use App\Core\View; ?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Back-office - Setly') ?></title>
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bo">
    <a class="skip-link" href="#bo-contenu">Aller au contenu</a>
    <div class="bo__layout">
        <?= View::partial('partials/bo_sidebar') ?>

        <div class="bo__main">
            <header class="bo__topbar">
                <h1 class="bo__page-title"><?= e($title ?? 'Back-office') ?></h1>
                <div class="bo__user">
                    <span><?= e(auth()['prenom'] ?? '') ?> - <?= e(auth()['role'] ?? '') ?></span>
                    <form action="/deconnexion" method="post" class="inline-form">
                        <?= csrf_field() ?>
                        <button class="btn btn--ghost btn--sm" type="submit">Déconnexion</button>
                    </form>
                </div>
            </header>

            <main id="bo-contenu" class="bo__content">
                <?= View::partial('partials/flash') ?>
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
    <script src="/assets/js/validation.js" defer></script>
    <script src="/assets/js/filters.js" defer></script>
    <script src="/assets/js/stats.js" defer></script>
</body>
</html>
