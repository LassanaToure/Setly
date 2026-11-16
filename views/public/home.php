<?php use App\Service\ReservationService; ?>
<section class="hero">
    <div class="container hero__inner">
        <div class="hero__text">
            <p class="eyebrow">Studios de tournage · Paris</p>
            <h1>Réservez votre studio de tournage en ligne</h1>
            <p class="lead">Podcast, vidéo ou photo : choisissez votre studio, votre créneau et votre matériel en quelques clics. Confirmation immédiate, suivi en temps réel.</p>
            <div class="hero__actions">
                <a class="btn btn--primary btn--lg" href="/studios">Voir les studios</a>
                <a class="btn btn--ghost btn--lg" href="/materiel">Louer du matériel</a>
            </div>
        </div>
        <div class="hero__media" aria-hidden="true">
            <div class="hero__card hero__card--1"></div>
            <div class="hero__card hero__card--2"></div>
            <div class="hero__card hero__card--3"></div>
        </div>
    </div>
</section>

<section class="container section">
    <div class="section__head">
        <h2>Nos studios</h2>
        <a class="link-more" href="/studios">Tout voir →</a>
    </div>
    <div class="grid grid--3">
        <?php foreach ($studios as $s): ?>
            <article class="card studio-card">
                <a class="studio-card__media" href="/studios/<?= (int) $s['id_studio'] ?>">
                    <img src="<?= e($s['photo'] ?? '/assets/img/studios/placeholder.svg') ?>" alt="Aperçu du <?= e($s['nom']) ?>" loading="lazy">
                    <span class="badge badge--<?= e($s['type_libelle']) ?>"><?= e(ucfirst($s['type_libelle'])) ?></span>
                </a>
                <div class="card__body">
                    <h3><a href="/studios/<?= (int) $s['id_studio'] ?>"><?= e($s['nom']) ?></a></h3>
                    <p class="muted"><?= e($s['capacite']) ?> personnes · <?= e($s['surface_m2']) ?> m²</p>
                    <p class="price"><?= price((int) $s['tarif_horaire_cents']) ?> <span>/ heure</span></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section--alt">
    <div class="container">
        <div class="section__head"><h2>Ils ont tourné chez Setly</h2></div>
        <div class="grid grid--3">
            <?php foreach ($avis as $a): ?>
                <blockquote class="card avis-card">
                    <div class="stars" aria-label="Note : <?= (int) $a['note'] ?> sur 5">
                        <?= str_repeat('★', (int) $a['note']) . str_repeat('☆', 5 - (int) $a['note']) ?>
                    </div>
                    <p>« <?= e($a['commentaire']) ?> »</p>
                    <footer><?= e($a['prenom']) ?> - <?= e($a['studio_nom']) ?></footer>
                </blockquote>
            <?php endforeach; ?>
            <?php if (!$avis): ?>
                <p class="muted">Les premiers avis clients s'afficheront ici.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="container section">
    <div class="steps">
        <div class="step"><span class="step__num">1</span><h3>Choisissez</h3><p>Filtrez les studios par type, capacité, prix et disponibilité.</p></div>
        <div class="step"><span class="step__num">2</span><h3>Réservez</h3><p>Sélectionnez un créneau, ajoutez du matériel, validez votre prix.</p></div>
        <div class="step"><span class="step__num">3</span><h3>Tournez</h3><p>Suivez votre réservation en temps réel jusqu'à la prestation.</p></div>
    </div>
</section>
