<?php $nav = ['index.php' => 'Home', 'packages.php' => 'Packages', 'about.php' => 'About Us', 'gallery.php' => 'Gallery', 'index.php#contact' => 'Contact'];
$cur = basename($_SERVER['SCRIPT_NAME']);
$language = $_SESSION['language'] ?? 'en'; ?>
<!DOCTYPE html><html lang="<?= e($language) ?>"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'Jama Jama Tour & Travels') ?></title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<header class="top"><a class="brand" href="index.php"><span class="logo">☪</span><span><b>Jama Jama</b><small><?= e(t('Tour & Travels')) ?></small></span></a>
<nav><?php foreach ($nav as $u => $l): ?><a href="<?= e($u) ?>" <?= (($l === 'Home' && $cur === 'index.php') || ($l === 'Packages' && $cur === 'packages.php') || ($l === 'About Us' && $cur === 'about.php') || ($l === 'Gallery' && $cur === 'gallery.php')) ? 'class="on"' : '' ?>><?= e(t($l)) ?></a><?php endforeach; ?></nav>
<div class="tools"><form method="get" action="<?= e($cur) ?>"><select name="lang" aria-label="<?= e(t('Language')) ?>" onchange="this.form.submit()">
<option value="en" <?= $language === 'en' ? 'selected' : '' ?>>English</option><option value="bn" <?= $language === 'bn' ? 'selected' : '' ?>>বাংলা</option><option value="hi" <?= $language === 'hi' ? 'selected' : '' ?>>हिन्दी</option></select></form>
<a class="wa" href="https://wa.me/<?= e(WHATSAPP) ?>" target="_blank" rel="noopener"><?= e(t('WhatsApp Us')) ?></a>
<a class="user" href="admin/login.php" aria-label="<?= e(t('Admin login')) ?>">👤</a></div></header>
