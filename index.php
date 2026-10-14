<?php require_once __DIR__ . '/inc/bootstrap.php';
$pdo = db();
$video = $pdo->query('SELECT * FROM hero_videos WHERE is_active=1 LIMIT 1')->fetch();
$banners = $pdo->query('SELECT * FROM hero_banners WHERE is_active=1 ORDER BY display_order, id')->fetchAll();
$packages = $pdo->query('SELECT * FROM packages ORDER BY sort_order LIMIT 4')->fetchAll();
$gallery = gallery_items($pdo, 5);
$pageTitle = t('Zam Zam Tour & Travels – Hajj & Umrah Packages');
require __DIR__ . '/inc/header.php'; ?>
<section class="hero">
    <div class="hero-video">
        <?php if ($video): ?>
            <video autoplay loop controls preload="auto" playsinline
                <?= $video['poster_path'] ? 'poster="' . e($video['poster_path']) . '"' : '' ?>
                aria-label="<?= e($video['title']) ?>">
                <source src="<?= e($video['video_path']) ?>" type="video/mp4">
                <?= e(t('Your browser does not support video.')) ?>
            </video>
            <button type="button" class="sound-toggle" hidden><?= e(t('Play with sound')) ?></button>
        <?php else: ?><div class="ph">
                <p><?= e(t('Promotional video coming soon')) ?></p>
            </div><?php endif; ?>
    </div>
    <div class="hero-slider" id="slider" tabindex="0" role="region" aria-roledescription="carousel"
        aria-label="<?= e(t('Promotions')) ?>">
        <?php if ($banners): foreach ($banners as $i => $b): ?>
                <article class="slide<?= $i === 0 ? ' active' : '' ?>" aria-hidden="<?= $i === 0 ? 'false' : 'true' ?>"
                    style="background-image:url('<?= e($b['image_path']) ?>')">
                    <div class="slide-in"><?php if (localized_text($b, 'subtitle')): ?><p class="sub">
                                <?= e(localized_text($b, 'subtitle')) ?></p><?php endif; ?>
                        <?php if (localized_text($b, 'title')): ?><h2><?= e(localized_text($b, 'title')) ?></h2><?php endif; ?>
                        <?php if (localized_text($b, 'description')): ?><p><?= e(localized_text($b, 'description')) ?></p>
                        <?php endif; ?>
                        <?php if (localized_text($b, 'button_text')): ?><a class="btn gold"
                                href="<?= e($b['button_url'] ?: '#') ?>"><?= e(localized_text($b, 'button_text')) ?></a><?php endif; ?>
                    </div>
                </article>
            <?php endforeach;
        else: ?>
            <article class="slide active">
                <div class="slide-in">
                    <p class="sub"><?= e(t('Your journey to the holy lands')) ?></p>
                    <h2><?= e(t('Hajj & Umrah With Zam Zam')) ?></h2>
                    <p><?= e(t('Trusted, comfortable, blessed.')) ?></p><a class="btn gold"
                        href="packages.php"><?= e(t('Explore Packages')) ?></a>
                </div>
            </article>
        <?php endif; ?>
        <?php if (count($banners) > 1): ?>
            <button class="arrow prev" aria-label="<?= e(t('Previous slide')) ?>">‹</button><button class="arrow next"
                aria-label="<?= e(t('Next slide')) ?>">›</button>
            <div class="dots"><?php foreach ($banners as $i => $b): ?><button
                        aria-label="<?= e(sprintf(t('Go to slide %d'), $i + 1)) ?>"
                        class="<?= $i === 0 ? 'on' : '' ?>"></button><?php endforeach; ?></div>
        <?php endif; ?>
    </div>
</section>
<section class="trust">
    <div>
        <b><?= e(t('Trusted Agency')) ?></b>
    </div>
    <div>
        <b><?= e(t('Best Accommodation')) ?></b>
        <small><?= e(t('Near Haram')) ?></small>
    </div>
    <div>
        <b><?= e(t('Experienced')) ?></b>
        <small><?= e(t('Guides')) ?></small>
    </div>
    <div>
        <b><?= e(t('24/7')) ?></b>
        <small><?= e(t('Support')) ?></small>
    </div>
</section>
<section id="packages" class="wrap">
    <div class="row">
        <div>
            <h2 class="h"><?= e(t('Our Hajj & Umrah Packages')) ?></h2>
            <p class="muted"><?= e(t('Choose the perfect package for your spiritual journey')) ?></p>
        </div><a class="btn line" href="packages.php"><?= e(t('View All Packages')) ?></a>
    </div>
    <div class="grid4"><?php foreach ($packages as $p): ?>
            <div class="card">
                <div class="img"><?php if ($image = package_image_url($p['image_path'] ?? null)): ?><img
                            src="<?= e($image) ?>" alt="<?= e(localized_text($p, 'name')) ?>" loading="lazy"><?php endif; ?>
                </div>
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
            </div>
        <?php endforeach; ?>
    </div>
</section>
<section id="about" class="wrap">
    <h2 class="h"><?= e(t('About Us')) ?></h2>
    <p class="muted">
        <?= e(t('Zam Zam Tour & Travels guides pilgrims to Makkah and Madinah with care and comfort.')) ?></p><a
        class="btn line" href="about.php"><?= e(t('Read More')) ?></a>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
<script src="assets/slider.js"></script>
<script>
    const heroVideo = document.querySelector('.hero-video video');
    const soundToggle = document.querySelector('.sound-toggle');
    if (heroVideo && soundToggle) {
        const startWithSound = () => {
            heroVideo.muted = false;
            heroVideo.play().then(() => {
                    soundToggle.hidden = true;
                })
                .catch(() => {
                    soundToggle.hidden = false;
                });
        };
        heroVideo.muted = false;
        heroVideo.play().then(() => {
                soundToggle.hidden = true;
            })
            .catch(() => {
                soundToggle.hidden = false;
            });
        soundToggle.addEventListener('click', startWithSound);
        heroVideo.addEventListener('playing', () => {
            soundToggle.hidden = true;
        });
    }
</script>
</body>

</html>