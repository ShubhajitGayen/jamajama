<?php $nav = ['index.php' => 'Home', 'packages.php' => 'Packages', 'about.php' => 'About Us', 'gallery.php' => 'Gallery', 'index.php#contact' => 'Contact'];
$cur = basename($_SERVER['SCRIPT_NAME']);
$language = $_SESSION['language'] ?? 'en'; ?>
<!DOCTYPE html><html lang="<?= e($language) ?>"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Jama Jama Tour & Travels') ?></title>
<link rel="stylesheet" href="assets/style.css?v=<?= filemtime(__DIR__ . '/../assets/style.css') ?>"></head><body>
<header class="top"><a class="brand" href="index.php"><span class="logo">☪</span><span><b>Jama Jama</b><small><?= e(t('Tour & Travels')) ?></small></span></a>
<button class="menu-toggle" type="button" aria-label="<?= e(t('Open navigation')) ?>" data-open-label="<?= e(t('Open navigation')) ?>" data-close-label="<?= e(t('Close navigation')) ?>" aria-controls="site-nav" aria-expanded="false"><span></span><span></span><span></span></button>
<nav id="site-nav"><?php foreach ($nav as $u => $l): ?><a href="<?= e($u) ?>" <?= (($l === 'Home' && $cur === 'index.php') || ($l === 'Packages' && $cur === 'packages.php') || ($l === 'About Us' && $cur === 'about.php') || ($l === 'Gallery' && $cur === 'gallery.php')) ? 'class="on"' : '' ?>><?= e(t($l)) ?></a><?php endforeach; ?></nav>
<div class="tools"><form method="get" action="<?= e($cur) ?>"><select name="lang" aria-label="<?= e(t('Language')) ?>" onchange="this.form.submit()">
<option value="en" <?= $language === 'en' ? 'selected' : '' ?>>English</option><option value="bn" <?= $language === 'bn' ? 'selected' : '' ?>>বাংলা</option><option value="hi" <?= $language === 'hi' ? 'selected' : '' ?>>हिन्दी</option></select></form>
<a class="wa" href="https://wa.me/<?= e(WHATSAPP) ?>" target="_blank" rel="noopener"><?= e(t('WhatsApp Us')) ?></a>
<a class="user" href="admin/login.php" aria-label="<?= e(t('Admin login')) ?>">👤</a></div></header>
<script>
(() => {
    const menuToggle = document.querySelector('.menu-toggle');
    const siteNav = document.getElementById('site-nav');
    const header = document.querySelector('.top');
    if (!menuToggle || !siteNav || !header) return;

    const setMenuOpen = (open) => {
        menuToggle.setAttribute('aria-expanded', String(open));
        menuToggle.setAttribute('aria-label', open ? menuToggle.dataset.closeLabel : menuToggle.dataset.openLabel);
        header.classList.toggle('menu-open', open);
    };

    menuToggle.addEventListener('click', () => {
        setMenuOpen(menuToggle.getAttribute('aria-expanded') !== 'true');
    });
    siteNav.addEventListener('click', (event) => {
        if (event.target.closest('a')) window.setTimeout(() => setMenuOpen(false), 0);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setMenuOpen(false);
    });
    document.addEventListener('click', (event) => {
        if (!header.contains(event.target)) setMenuOpen(false);
    });
})();
</script>
