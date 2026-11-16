<?php
use App\Core\Session;
$flashes = Session::takeFlash();
if ($flashes):
?>
<div class="container">
    <?php foreach ($flashes as $f): ?>
        <div class="alert alert--<?= e($f['type']) ?>" role="alert">
            <?= e($f['message']) ?>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
