<?php require_once __DIR__ . '/inc/bootstrap.php';
$items = gallery_items(db());
$pageTitle = t('Gallery – Zam Zam Tour & Travels');
require __DIR__ . '/inc/header.php'; ?>
<main class="wrap"><h1 class="h"><?= e(t('Gallery')) ?></h1>
<?php if (!$items): ?><p class="muted"><?= e(t('Gallery items will appear here soon.')) ?></p><?php endif; ?>
<div class="ggrid"><?php foreach ($items as $item): ?><?php if ($item['media_type'] === 'video'): ?><video controls preload="metadata" src="<?= e($item['media_path']) ?>" aria-label="<?= e($item['title'] ?: t('Gallery video')) ?>"></video><?php else: ?><img loading="lazy" src="<?= e($item['media_path']) ?>" alt="<?= e($item['title'] ?: t('Travel photograph')) ?>"><?php endif; ?><?php endforeach; ?></div></main>
<?php require __DIR__ . '/inc/footer.php'; ?></body></html>
