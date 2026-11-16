<section class="container section">
    <div class="section__head">
        <div>
            <h1>Matériel en location</h1>
            <p class="muted">À ajouter en option lors de votre réservation de studio.</p>
        </div>
    </div>

    <div class="grid grid--3">
        <?php foreach ($materiel as $m): ?>
            <article class="card materiel-card">
                <div class="card__body">
                    <h2><?= e($m['nom']) ?></h2>
                    <p class="muted"><?= e($m['description']) ?></p>
                    <p class="price"><?= price((int) $m['prix_unitaire_cents']) ?> <span>/ jour</span></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <p class="info-box">
        Livraison possible : <strong>5,00 € + 0,59 €/km</strong> hors Paris, ou retrait gratuit sur place.
    </p>
</section>
