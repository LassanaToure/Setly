<?php use App\Core\Auth; ?>
<section class="container section">
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
        <a href="/studios">Studios</a> <span aria-hidden="true">/</span> <?= e($studio['nom']) ?>
    </nav>

    <div class="studio-detail">
        <div class="studio-detail__gallery">
            <?php if ($photos): ?>
                <img class="studio-detail__main" src="<?= e($photos[0]) ?>" alt="<?= e($studio['nom']) ?>" id="photo-principale">
                <?php if (count($photos) > 1): ?>
                    <div class="thumbs">
                        <?php foreach ($photos as $i => $url): ?>
                            <button type="button" class="thumb <?= $i === 0 ? 'is-active' : '' ?>" data-src="<?= e($url) ?>" aria-label="Photo <?= $i + 1 ?>">
                                <img src="<?= e($url) ?>" alt="">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <img class="studio-detail__main" src="/assets/img/studios/placeholder.svg" alt="<?= e($studio['nom']) ?>">
            <?php endif; ?>
        </div>

        <div class="studio-detail__info">
            <span class="badge badge--<?= e($studio['type_libelle']) ?>"><?= e(ucfirst($studio['type_libelle'])) ?></span>
            <h1><?= e($studio['nom']) ?></h1>
            <?php if ($studio['note_moyenne']): ?>
                <p class="rating">★ <?= e((string) $studio['note_moyenne']) ?> / 5</p>
            <?php endif; ?>

            <p class="price price--lg"><?= price((int) $studio['tarif_horaire_cents']) ?> <span>/ heure</span></p>
            <ul class="specs">
                <li><strong>Capacité</strong> <?= e($studio['capacite']) ?> personnes</li>
                <li><strong>Surface</strong> <?= e($studio['surface_m2']) ?> m²</li>
                <li><strong>Durée minimum</strong> <?= e($studio['duree_min_heures']) ?> h</li>
            </ul>

            <?php if (Auth::hasRole('client')): ?>
                <a class="btn btn--primary btn--lg btn--block" href="/reserver/<?= (int) $studio['id_studio'] ?>">Réserver ce studio</a>
            <?php elseif (Auth::check()): ?>
                <p class="muted">Les réservations sont réservées aux comptes clients.</p>
            <?php else: ?>
                <a class="btn btn--primary btn--lg btn--block" href="/connexion">Se connecter pour réserver</a>
                <p class="muted muted--center">Pas encore de compte ? <a href="/inscription">Inscrivez-vous</a>.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="studio-detail__body">
        <div class="prose">
            <h2>Description</h2>
            <p><?= nl2br(e($studio['description'])) ?></p>

            <h2>Équipement inclus</h2>
            <ul class="tags">
                <?php foreach ($equipements as $eq): ?>
                    <li class="tag"><?= e($eq) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <aside class="conditions-box" aria-label="Conditions du studio">
            <h2>Conditions</h2>
            <p><?= nl2br(e($studio['conditions'])) ?></p>
        </aside>
    </div>
</section>
