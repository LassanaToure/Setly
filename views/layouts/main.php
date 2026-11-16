<?php use App\Core\View; ?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Setly - location de studios de tournage (podcast, vidéo, photo) à Paris avec réservation en ligne et matériel en option.">
    <title><?= e($title ?? 'Setly') ?></title>
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <a class="skip-link" href="#contenu">Aller au contenu principal</a>
    <?= View::partial('partials/header') ?>

    <main id="contenu" class="site-main">
        <?= View::partial('partials/flash') ?>
        <?= $content ?? '' ?>
    </main>

    <?= View::partial('partials/footer') ?>
    <script src="/assets/js/validation.js" defer></script>
    <script src="/assets/js/filters.js" defer></script>
    <script src="/assets/js/tarif.js" defer></script>
</body>
</html>
