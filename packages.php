<?php require_once __DIR__ . '/inc/bootstrap.php';
$pdo = db();
$packages = $pdo->query('SELECT * FROM packages ORDER BY sort_order, id')->fetchAll();
$pageTitle = t('Our Hajj & Umrah Packages – Jama Jama Tour & Travels');
require __DIR__ . '/inc/header.php'; ?>
<main class="wrap">
    <h1 class="h"><?= e(t('Our Hajj & Umrah Packages')) ?></h1>
    <p class="muted"><?= e(t('Choose the perfect package for your spiritual journey')) ?></p>
    <div class="grid4"><?php foreach ($packages as $p): ?>
            <article class="card">
                <div class="img"><?php if ($image = package_image_url($p['image_path'] ?? null)): ?><img src="<?= e($image) ?>" alt="<?= e(localized_text($p, 'name')) ?>" loading="lazy"><?php endif; ?></div>
                <h3><?= e(localized_text($p, 'name')) ?></h3>
                <div class="cf">
                    <ul>
                        <li><?= e(localized_text($p, 'line1')) ?></li>
                        <li><?= e(localized_text($p, 'line2')) ?></li>
                        <li><?= e(localized_text($p, 'line3')) ?></li>
                    </ul>
                    <div class="price">
                        <small><?= e(t('Starting from')) ?></small><b>₹<?= number_format((int)$p['price']) ?></b><small><?= e(t('Per Person')) ?></small>
                    </div>
                </div>
                <a class="btn green" href="#"><?= e(t('View Details')) ?></a>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?></body>

</html>