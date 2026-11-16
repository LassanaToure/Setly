<div class="card">
    <div class="card__head"><h2>Avis en attente de modération</h2></div>

    <?php if (!$avis): ?>
        <p class="empty">Aucun avis à modérer pour le moment.</p>
    <?php else: ?>
        <div class="moderation-list">
            <?php foreach ($avis as $a): ?>
                <article class="moderation-item">
                    <div class="moderation-item__head">
                        <div>
                            <span class="stars" aria-label="Note <?= (int) $a['note'] ?> sur 5"><?= str_repeat('★', (int) $a['note']) . str_repeat('☆', 5 - (int) $a['note']) ?></span>
                            <strong><?= e($a['prenom'] . ' ' . $a['client_nom']) ?></strong> · <?= e($a['studio_nom']) ?>
                        </div>
                        <span class="muted"><?= e(date('d/m/Y', strtotime($a['date_creation']))) ?></span>
                    </div>
                    <p>« <?= e($a['commentaire']) ?> »</p>
                    <div class="moderation-item__actions">
                        <form action="/back-office/avis/<?= (int) $a['id_avis'] ?>/moderer" method="post" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="decision" value="valide">
                            <button class="btn btn--primary btn--sm" type="submit">Valider</button>
                        </form>
                        <form action="/back-office/avis/<?= (int) $a['id_avis'] ?>/moderer" method="post" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="decision" value="refuse">
                            <button class="btn btn--danger btn--sm" type="submit">Refuser</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
