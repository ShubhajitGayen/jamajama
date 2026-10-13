<?php require_once __DIR__ . '/../inc/bootstrap.php'; require_admin(); $pdo = db();
function package_fields(): array {
    $language = $_SESSION['content_language'] ?? 'en';
    $suffix = $language === 'en' ? '' : '_' . $language;
    $fields = [];
    foreach (['name', 'line1', 'line2', 'line3'] as $field) {
        $key = $field . $suffix;
        $value = trim($_POST[$field] ?? '');
        if (mb_strlen($value) > 100) throw new RuntimeException('One of the package text fields is too long (max 100 characters).');
        $fields[$key] = $value !== '' ? $value : null;
    }
    if ($language === 'en' && !$fields['name']) throw new RuntimeException('Package name is required.');
    if ($language === 'en') {
        $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_INT);
        if ($price === false || $price < 1 || $price > 4294967295) throw new RuntimeException('Enter a valid package price.');
        $order = filter_var($_POST['sort_order'] ?? '0', FILTER_VALIDATE_INT);
        if ($order === false || $order < 0 || $order > 2147483647) throw new RuntimeException('Enter a valid package order.');
        $fields['price'] = $price;
        $fields['sort_order'] = $order;
    }
    return $fields;
}
function delete_package_image_if_unreferenced(PDO $pdo, ?string $path): void {
    if (!$path || !preg_match('~\Auploads/packages/[a-f0-9]{32}\.(?:jpg|png|webp)\z~D', $path)) return;
    $used = $pdo->prepare('SELECT COUNT(*) FROM packages WHERE image_path=?');
    $used->execute([$path]);
    if (!(int)$used->fetchColumn()) delete_file($path);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'edit') {
        $allowed = ['action', 'id', 'name', 'line1', 'line2', 'line3', 'price', 'sort_order'];
        $_SESSION['package_form_input'] = array_filter(
            array_intersect_key($_POST, array_flip($allowed)),
            'is_scalar'
        );
    } else {
        unset($_SESSION['package_form_input']);
    }
    safe_run(function () use ($pdo) {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($action === 'add' || $action === 'edit') {
        $fields = package_fields();
        if ($action === 'add') {
            if (($_SESSION['content_language'] ?? 'en') !== 'en') throw new RuntimeException('Switch to English to create a package. You can add translations after creating it.');
            $imagePath = has_file('image') ? save_upload($_FILES['image'], 'package_image') : null;
            $fields['image_path'] = $imagePath;
            $columns = implode(',', array_keys($fields));
            $values = implode(',', array_fill(0, count($fields), '?'));
            try {
                $pdo->prepare("INSERT INTO packages ($columns) VALUES ($values)")->execute(array_values($fields));
            } catch (Throwable $x) {
                delete_file($imagePath);
                throw $x;
            }
            flash('Package added.');
        } else {
            $exists = $pdo->prepare('SELECT image_path FROM packages WHERE id=?');
            $exists->execute([$id]);
            $oldImage = $exists->fetchColumn();
            if ($oldImage === false) throw new RuntimeException('Package not found.');
            $imagePath = $oldImage;
            if (has_file('image')) {
                $imagePath = save_upload($_FILES['image'], 'package_image');
            } elseif (!empty($_POST['remove_image'])) {
                $imagePath = null;
            }
            $fields['image_path'] = $imagePath;
            $set = implode(',', array_map(fn($column) => "$column=?", array_keys($fields)));
            try {
                $pdo->prepare("UPDATE packages SET $set WHERE id=?")->execute([...array_values($fields), $id]);
            } catch (Throwable $x) {
                if ($imagePath !== $oldImage) delete_file($imagePath);
                throw $x;
            }
            if ($imagePath !== $oldImage) delete_package_image_if_unreferenced($pdo, $oldImage);
            flash('Package updated.');
        }
    } elseif ($action === 'delete') {
        $image = $pdo->prepare('SELECT image_path FROM packages WHERE id=?');
        $image->execute([$id]);
        $oldImage = $image->fetchColumn();
        if ($oldImage === false) throw new RuntimeException('Package not found.');
        $delete = $pdo->prepare('DELETE FROM packages WHERE id=?'); $delete->execute([$id]);
        delete_package_image_if_unreferenced($pdo, $oldImage);
        flash('Package deleted.');
    } else {
        throw new RuntimeException('Invalid package action.');
    }
    unset($_SESSION['package_form_input']);
}); }
require __DIR__ . '/_head.php';
$packages = $pdo->query('SELECT * FROM packages ORDER BY sort_order, id')->fetchAll();
$submitted = $_SESSION['package_form_input'] ?? [];
unset($_SESSION['package_form_input']);
$contentLanguage = $_SESSION['content_language'] ?? 'en';
$suffix = $contentLanguage === 'en' ? '' : '_' . $contentLanguage;
$languageLabel = ['en' => 'English', 'bn' => 'Bengali', 'hi' => 'Hindi'][$contentLanguage];
$textFields = [
    'name' => 'Package name',
    'line1' => 'Detail 1',
    'line2' => 'Detail 2',
    'line3' => 'Detail 3',
];
function package_form(?array $package, array $textFields, string $suffix, string $language, array $submitted = [], bool $isEdit = false): void {
    $inputValue = static function (string $field) use ($package, $suffix, $submitted): string {
        return (string)($submitted[$field] ?? $package[$field . $suffix] ?? '');
    };
    $image = package_image_url($package['image_path'] ?? null);
?>
<p>Editing <?= e($language) ?> content. Blank translated fields use the English text on the public site.</p>
<?php foreach ($textFields as $field => $label): ?><label><?= e($label) ?></label><input type="text" name="<?= e($field) ?>" maxlength="100" value="<?= e($inputValue($field)) ?>" <?= $field === 'name' && $suffix === '' ? 'required' : '' ?>><?php endforeach; ?>
<?php if ($suffix === ''): ?>
<label>Price (₹)</label><input type="number" name="price" min="1" max="4294967295" required value="<?= e($submitted['price'] ?? $package['price'] ?? '') ?>">
<label>Display order</label><input type="number" name="sort_order" min="0" value="<?= e($submitted['sort_order'] ?? $package['sort_order'] ?? 0) ?>">
<?php endif; ?>
<label>Package Image</label>
<p>JPG, JPEG, PNG or WebP; max <?= MAX_IMAGE_MB ?> MB.</p>
<p data-package-image-message><?= $isEdit ? 'No new image selected. The current image will remain unless you replace or remove it.' : 'No image selected.' ?></p>
<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-package-image-file>
<?php if ($isEdit): ?>
<label><input type="checkbox" name="remove_image" value="1" data-remove-package-image> Remove current image</label>
<?php endif; ?>
<div class="package-image-preview" data-has-image="<?= $image ? '1' : '0' ?>">
    <img data-package-image-preview <?= $image ? 'src="../' . e($image) . '"' : 'hidden' ?> alt="<?= $image ? 'Current package image' : '' ?>" style="max-width:240px;max-height:160px;object-fit:cover">
    <p data-package-image-empty <?= $image ? 'hidden' : '' ?>><?= $isEdit ? 'No package image is currently set.' : 'No image selected.' ?></p>
</div>
<?php } ?>
<h1>Manage Packages</h1>
<?php if ($suffix === ''): ?><div class="box"><h2>Add package</h2><form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="add"><?php package_form(null, $textFields, $suffix, $languageLabel, ($submitted['action'] ?? '') === 'add' ? $submitted : []); ?><p><button>Add package</button></p></form></div>
<?php else: ?><div class="box"><p>Switch the editing language to English to create a package. Then select Bengali or Hindi to add its translation.</p></div><?php endif; ?>
<div class="box"><h2>Packages</h2><?php if (!$packages): ?><p>No packages yet. Add one above.</p><?php endif; ?>
<?php foreach ($packages as $package): ?><div class="item"><div style="flex:1"><b><?= e($package['name']) ?></b> · ₹<?= number_format((int)$package['price']) ?>
<details><summary>Edit <?= e($languageLabel) ?> content</summary><form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= (int)$package['id'] ?>"><?php package_form($package, $textFields, $suffix, $languageLabel, (($submitted['action'] ?? '') === 'edit' && (int)($submitted['id'] ?? 0) === (int)$package['id']) ? $submitted : [], true); ?><p><button>Save changes</button></p></form></details>
<form class="inl" method="post" onsubmit="return confirm('Delete this package?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$package['id'] ?>"><button class="red">Delete</button></form>
</div></div><?php endforeach; ?></div>
</div>
<script>
document.querySelectorAll('form[enctype="multipart/form-data"]').forEach(function(form) {
    const file = form.querySelector('[data-package-image-file]');
    const preview = form.querySelector('[data-package-image-preview]');
    const empty = form.querySelector('[data-package-image-empty]');
    const message = form.querySelector('[data-package-image-message]');
    const remove = form.querySelector('[data-remove-package-image]');
    if (!file || !preview || !empty || !message) return;
    const hasImage = form.querySelector('.package-image-preview').dataset.hasImage === '1';
    file.addEventListener('change', function() {
        if (file.files[0]) {
            preview.src = URL.createObjectURL(file.files[0]);
            preview.hidden = false;
            empty.hidden = true;
            message.textContent = 'New image selected; preview shown below.';
            if (remove) remove.checked = false;
        }
    });
    if (remove) remove.addEventListener('change', function() {
        if (remove.checked && !file.files.length) {
            preview.hidden = true;
            empty.hidden = false;
            empty.textContent = 'The current image will be removed when you save.';
            message.textContent = 'The current image will be removed when you save.';
        } else if (!remove.checked && !file.files.length) {
            preview.hidden = !hasImage;
            empty.hidden = hasImage;
            empty.textContent = hasImage ? 'Current image remains in place.' : 'No package image is currently set.';
            message.textContent = hasImage
                ? 'No new image selected. The current image will remain unless you replace or remove it.'
                : 'No image selected.';
        }
    });
});
</script>
</body></html>
