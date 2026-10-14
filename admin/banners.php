<?php require_once __DIR__ . '/../inc/bootstrap.php'; require_admin(); $pdo = db();
function renumber(PDO $pdo, array $ids): void { $u = $pdo->prepare('UPDATE hero_banners SET display_order=? WHERE id=?'); foreach (array_values($ids) as $k => $id) $u->execute([$k + 1, $id]); }
function fields(): array {
    $language = $_SESSION['content_language'] ?? 'en';
    $suffix = $language === 'en' ? '' : '_' . $language;
    $text = [];
    foreach (['title' => 150, 'subtitle' => 200, 'description' => 1000, 'button_text' => 60] as $field => $limit) {
        $value = trim($_POST[$field] ?? '');
        if (mb_strlen($value) > $limit) throw new RuntimeException('One of the banner text fields is too long.');
        $text[$field . $suffix] = $value !== '' ? $value : ($field === 'title' && $language === 'en' ? '' : null);
    }
    $url = trim($_POST['button_url'] ?? '');
    if (!valid_url($url)) throw new RuntimeException('Button URL must be a page path, #section or full http(s) link.');
    return [$text, $url ?: null];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_check(); safe_run(function () use ($pdo) {
    $a = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    $ids = fn() => $pdo->query('SELECT id FROM hero_banners ORDER BY display_order, id')->fetchAll(PDO::FETCH_COLUMN);
    if ($a === 'add') {
        [$text, $bu] = fields();
        if (($_SESSION['content_language'] ?? 'en') !== 'en') throw new RuntimeException('Switch to English to create a banner. You can add translations after creating it.');
        if (!has_file('image')) throw new RuntimeException('Choose a banner image.');
        $img = save_upload($_FILES['image'], 'banner');
        $n = (int)$pdo->query('SELECT COALESCE(MAX(display_order),0)+1 FROM hero_banners')->fetchColumn();
        $base = ['image_path' => $img, 'button_url' => $bu, 'display_order' => $n, 'is_active' => empty($_POST['is_active']) ? 0 : 1];
        $values = array_merge($text, $base);
        $columns = implode(',', array_keys($values));
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $pdo->prepare("INSERT INTO hero_banners ($columns) VALUES ($placeholders)")->execute(array_values($values));
        flash('Banner added.');
    } elseif ($a === 'edit') {
        [$text, $bu] = fields();
        $q = $pdo->prepare('SELECT image_path FROM hero_banners WHERE id=?'); $q->execute([$id]); $old = $q->fetchColumn();
        if ($old === false) throw new RuntimeException('Banner not found.');
        $img = $old; if (has_file('image')) $img = save_upload($_FILES['image'], 'banner');
        $values = array_merge($text, ['button_url' => $bu, 'image_path' => $img, 'is_active' => empty($_POST['is_active']) ? 0 : 1]);
        $set = implode(',', array_map(fn($column) => "$column=?", array_keys($values)));
        $pdo->prepare("UPDATE hero_banners SET $set WHERE id=?")->execute([...array_values($values), $id]);
        if ($img !== $old) delete_file($old);
        $pos = max(1, (int)($_POST['position'] ?? 0));
        if ($pos) { $l = array_values(array_diff($ids(), [$id])); array_splice($l, min($pos, count($l) + 1) - 1, 0, [$id]); renumber($pdo, $l); }
        flash('Banner updated.');
    } elseif (in_array($a, ['up', 'down', 'first'], true)) {
        $pdo->beginTransaction();
        try { $l = $ids(); $k = array_search($id, $l);
          if ($k !== false) { array_splice($l, $k, 1); $n = $a === 'first' ? 0 : ($a === 'up' ? max(0, $k - 1) : min(count($l), $k + 1)); array_splice($l, $n, 0, [$id]); renumber($pdo, $l); }
          $pdo->commit(); } catch (Throwable $x) { $pdo->rollBack(); throw $x; }
    } elseif ($a === 'toggle') {
        $pdo->prepare('UPDATE hero_banners SET is_active=1-is_active WHERE id=?')->execute([$id]);
    } elseif ($a === 'delete') {
        $q = $pdo->prepare('SELECT image_path FROM hero_banners WHERE id=?'); $q->execute([$id]); $old = $q->fetchColumn();
        $pdo->prepare('DELETE FROM hero_banners WHERE id=?')->execute([$id]); delete_file($old ?: null);
        $pdo->beginTransaction(); renumber($pdo, $ids()); $pdo->commit(); flash('Banner deleted.');
    }
}); }
require __DIR__ . '/_head.php';
$list = $pdo->query('SELECT * FROM hero_banners ORDER BY display_order, id')->fetchAll();
function bform(?array $b, string $suffix, string $language): void { ?>
<p>Editing <?= e($language) ?> content. Blank translated fields use the English text on the public site.</p>
<?php foreach (['title' => 'Title (optional)', 'subtitle' => 'Subtitle', 'description' => 'Description', 'button_text' => 'Button text'] as $field => $label): ?>
<label><?= e($label) ?></label><?php if ($field === 'description'): ?><textarea name="<?= e($field) ?>" rows="2"><?= e($b[$field . $suffix] ?? '') ?></textarea><?php else: ?><input type="text" name="<?= e($field) ?>" maxlength="<?= $field === 'title' ? 150 : ($field === 'subtitle' ? 200 : 60) ?>" value="<?= e($b[$field . $suffix] ?? '') ?>"><?php endif; ?>
<?php endforeach; ?>
<label>Button destination</label><input type="text" name="button_url" list="dest" value="<?= e($b['button_url'] ?? '') ?>" placeholder="packages.php">
<datalist id="dest"><option value="packages.php">Packages</option><option value="index.php#contact">Contact</option><option value="gallery.php">Gallery</option><option value="about.php">About Us</option></datalist>
<label>Image (JPG, PNG or WebP, max <?= MAX_IMAGE_MB ?> MB)</label><input type="file" name="image" accept="image/jpeg,image/png,image/webp" <?= $b ? '' : 'required data-prev="bp"' ?>>
<?php if ($b): ?><label>Position</label><input type="number" name="position" min="1" value="<?= (int)$b['display_order'] ?>"><?php endif; ?>
<label><input type="checkbox" name="is_active" value="1" <?= !$b || $b['is_active'] ? 'checked' : '' ?>> Active (visible on homepage)</label><?php } ?>
<h1>Manage Hero Banners</h1>
<?php $contentLanguage = $_SESSION['content_language'] ?? 'en'; $suffix = $contentLanguage === 'en' ? '' : '_' . $contentLanguage; $languageLabel = ['en' => 'English', 'bn' => 'Bengali', 'hi' => 'Hindi'][$contentLanguage]; ?>
<?php if ($suffix === ''): ?><div class="box"><h2>Add banner</h2><form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="add"><?php bform(null, $suffix, $languageLabel); ?>
<p>Preview:</p><img id="bp" alt="" style="max-width:300px;display:none"><p><button>Add banner</button></p></form></div>
<?php else: ?><div class="box"><p>Switch the editing language to English to create a banner. Then select Bengali or Hindi to add its translation.</p></div><?php endif; ?>
<div class="box"><h2>Banners (shown in this order)</h2><?php if (!$list): ?><p>No banners yet. Add one above.</p><?php endif; ?>
<?php foreach ($list as $b): ?><div class="item"><img src="../<?= e($b['image_path']) ?>" alt="">
<div style="flex:1"><b>#<?= (int)$b['display_order'] ?> · <?= e($b['title'] ?: 'Untitled banner') ?></b> <span class="tag <?= $b['is_active'] ? 'on' : '' ?>"><?= $b['is_active'] ? 'Active' : 'Inactive' ?></span>
<p><?= e($b['subtitle']) ?> <?= $b['button_text'] ? '· Button: ' . e($b['button_text']) . ' → ' . e($b['button_url']) : '' ?></p>
<form class="inl acts" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
<button name="action" value="first" class="gray">Show first</button><button name="action" value="up" class="gray">↑ Up</button><button name="action" value="down" class="gray">↓ Down</button>
<button name="action" value="toggle"><?= $b['is_active'] ? 'Deactivate' : 'Activate' ?></button>
<button name="action" value="delete" class="red" onclick="return confirm('Delete this banner?')">Delete</button></form>
<details><summary>Edit <?= e($languageLabel) ?> content</summary><form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><?php bform($b, $suffix, $languageLabel); ?><p><button>Save changes</button></p></form></details></div></div>
<?php endforeach; ?></div>
<script>document.querySelectorAll('[data-prev]').forEach(function(i){i.onchange=function(){var t=document.getElementById(i.dataset.prev);if(i.files[0]){t.src=URL.createObjectURL(i.files[0]);t.style.display='block';}};});</script>
</div></body></html>
