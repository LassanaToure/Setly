<?php if (!$studios): ?>
    <p class="empty">Aucun studio ne correspond à ces critères. Essayez d'élargir votre recherche.</p>
<?php else: ?>
    <p class="results-count"><?= count($studios) ?> studio<?= count($studios) > 1 ? 's' : '' ?> disponible<?= count($studios) > 1 ? 's' : '' ?></p>
    <div class="grid grid--3">
        <?php foreach ($studios as $s): ?>
            <article class="card studio-card">
                <a class="studio-card__media" href="/studios/<?= (int) $s['id_studio'] ?>">
                    <img src="<?= e($s['photo'] ?? '/assets/img/studios/placeholder.svg') ?>" alt="Aperçu du <?= e($s['nom']) ?>" loading="lazy">
                    <span class="badge badge--<?= e($s['type_libelle']) ?>"><?= e(ucfirst($s['type_libelle'])) ?></span>
                </a>
                <div class="card__body">
                    <h3><a href="/studios/<?= (int) $s['id_studio'] ?>"><?= e($s['nom']) ?></a></h3>
                    <p class="muted"><?= e($s['capacite']) ?> pers. · <?= e($s['surface_m2']) ?> m²
                        <?php if ($s['note_moyenne']): ?>
                            · <span class="rating">★ <?= e((string) $s['note_moyenne']) ?></span> (<?= (int) $s['nb_avis'] ?>)
                        <?php endif; ?>
                    </p>
                    <div class="studio-card__foot">
                        <p class="price"><?= price((int) $s['tarif_horaire_cents']) ?> <span>/ h</span></p>
                        <a class="btn btn--primary btn--sm" href="/studios/<?= (int) $s['id_studio'] ?>">Détails</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
