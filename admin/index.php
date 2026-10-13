<?php require __DIR__ . '/_head.php'; $pdo = db();
$v = $pdo->query('SELECT title FROM hero_videos WHERE is_active=1')->fetchColumn();
$b = $pdo->query('SELECT COUNT(*) FROM hero_banners WHERE is_active=1')->fetchColumn(); ?>
<h1>Dashboard</h1><div class="box"><p>Active hero video: <b><?= $v ? e($v) : 'none' ?></b> · <a href="videos.php">manage</a></p>
<p>Active banners: <b><?= (int)$b ?></b> · <a href="banners.php">manage</a></p>
<p class="muted">Server upload limit: <?= server_limit_mb() ?> MB</p></div></div></body></html>
