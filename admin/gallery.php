<?php require_once __DIR__ . '/../inc/bootstrap.php'; require_admin(); $pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_check(); safe_run(function () use ($pdo) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $type = $_POST['media_type'] ?? '';
        if (!in_array($type, ['image', 'video'], true)) throw new RuntimeException('Choose an image or video.');
        if (!has_file('media')) throw new RuntimeException('Choose a gallery file to upload.');
        $title = trim($_POST['title'] ?? '');
        if (mb_strlen($title) > 150) throw new RuntimeException('Caption must be 150 characters or fewer.');
        $path = save_upload($_FILES['media'], $type === 'video' ? 'gallery_video' : 'gallery_image');
        try {
            $order = (int)$pdo->query('SELECT COALESCE(MAX(display_order),0)+1 FROM gallery_items')->fetchColumn();
            $pdo->prepare('INSERT INTO gallery_items(media_type,title,media_path,display_order) VALUES(?,?,?,?)')
                ->execute([$type, $title ?: null, $path, $order]);
        } catch (Throwable $x) {
            delete_file($path);
            throw $x;
        }
        flash('Gallery item uploaded.');
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $query = $pdo->prepare('SELECT media_path FROM gallery_items WHERE id=?');
        $query->execute([$id]);
        $path = $query->fetchColumn();
        if ($path === false) throw new RuntimeException('Gallery item not found.');
        $pdo->prepare('DELETE FROM gallery_items WHERE id=?')->execute([$id]);
        delete_file($path);
        flash('Gallery item deleted.');
    } else {
        throw new RuntimeException('Invalid gallery action.');
    }
}); }
require __DIR__ . '/_head.php';
$items = $pdo->query('SELECT * FROM gallery_items ORDER BY display_order DESC, id DESC')->fetchAll(); ?>
<h1>Manage Gallery</h1>
<div class="box"><h2>Upload image or video</h2><p>Images: JPG, PNG or WebP (max <?= MAX_IMAGE_MB ?> MB). Videos: MP4 (max <?= MAX_VIDEO_MB ?> MB; actual server upload limit <?= server_limit_mb() ?> MB).</p>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="add">
<label>Media type</label><select name="media_type" id="media-type"><option value="image">Image</option><option value="video">Video (MP4)</option></select>
<label>Caption (optional)</label><input type="text" name="title" maxlength="150">
<label>File</label><input type="file" name="media" id="media-file" accept="image/jpeg,image/png,image/webp" required>
<p><button>Upload to gallery</button></p></form></div>
<div class="box"><h2>Gallery items</h2><?php if (!$items): ?><p>No gallery items have been uploaded.</p><?php endif; ?>
<div class="ggrid"><?php foreach ($items as $item): ?><div class="item">
<?php if ($item['media_type'] === 'video'): ?><video controls preload="metadata" src="../<?= e($item['media_path']) ?>"></video><?php else: ?><img src="../<?= e($item['media_path']) ?>" alt=""><?php endif; ?>
<div style="flex:1"><b><?= e($item['title'] ?: ucfirst($item['media_type'])) ?></b>
<form method="post" onsubmit="return confirm('Delete this gallery item?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="red">Delete</button></form></div></div><?php endforeach; ?></div></div>
</div>
<script>
const mediaType = document.getElementById('media-type');
const mediaFile = document.getElementById('media-file');
mediaType.addEventListener('change', () => {
  mediaFile.accept = mediaType.value === 'video' ? 'video/mp4' : 'image/jpeg,image/png,image/webp';
});
</script></body></html>
