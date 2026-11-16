<?php use App\Model\Horaire; $horairesFooter = Horaire::all(); ?>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div>
            <p class="brand brand--light"><span class="brand__dot" aria-hidden="true"></span>Setly</p>
            <p class="muted">Location de studios de tournage à Paris : podcast, vidéo, photo. Réservation en ligne et matériel en option.</p>
        </div>

        <nav aria-label="Liens de pied de page">
            <h2 class="site-footer__title">Navigation</h2>
            <ul class="site-footer__links">
                <li><a href="/studios">Nos studios</a></li>
                <li><a href="/materiel">Matériel</a></li>
                <li><a href="/contact">Contact</a></li>
                <li><a href="/mentions-legales">Mentions légales</a></li>
                <li><a href="/cgv">CGV / CGU</a></li>
            </ul>
        </nav>

        <div>
            <h2 class="site-footer__title">Horaires d'ouverture</h2>
            <ul class="site-footer__hours">
                <?php foreach ($horairesFooter as $h): ?>
                    <li>
                        <span><?= e(ucfirst($h['jour_semaine'])) ?></span>
                        <span><?= (int) $h['ferme'] === 1
                            ? 'Fermé'
                            : e(substr((string) $h['heure_ouverture'], 0, 5)) . ' - ' . e(substr((string) $h['heure_fermeture'], 0, 5)) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="site-footer__bottom">
        <p>© <?= date('Y') ?> Setly - Projet DWWM. Tous droits réservés.</p>
    </div>
</footer>
