<?php require_once __DIR__ . '/../inc/bootstrap.php'; require_admin(); $pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_check(); safe_run(function () use ($pdo) {
    $a = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($a === 'add' || $a === 'edit') {
        $title = trim($_POST['title'] ?? ''); $desc = trim($_POST['description'] ?? '');
        if ($title === '' || mb_strlen($title) > 150) throw new RuntimeException('Title is required (max 150 characters).');
        if (mb_strlen($desc) > 1000) throw new RuntimeException('Description is too long (max 1000 characters).');
        if ($a === 'add') {
            if (!has_file('video')) throw new RuntimeException('Choose an MP4 video to upload.');
            $vp = save_upload($_FILES['video'], 'video'); $pp = null;
            try { if (has_file('poster')) $pp = save_upload($_FILES['poster'], 'poster'); } catch (Throwable $x) { delete_file($vp); throw $x; }
            $pdo->prepare('INSERT INTO hero_videos(title,description,video_path,poster_path,is_active) VALUES(?,?,?,?,0)')->execute([$title, $desc ?: null, $vp, $pp]);
            flash('Video uploaded as inactive. Preview it below, then click Activate to publish.');
        } else {
            $s = $pdo->prepare('SELECT * FROM hero_videos WHERE id=?'); $s->execute([$id]); $o = $s->fetch();
            if (!$o) throw new RuntimeException('Video not found.');
            $vp = $o['video_path']; $pp = $o['poster_path']; $oldV = $oldP = null;
            if (has_file('video')) { $vp = save_upload($_FILES['video'], 'video'); $oldV = $o['video_path']; }
            try { if (has_file('poster')) { $pp = save_upload($_FILES['poster'], 'poster'); $oldP = $o['poster_path']; } }
            catch (Throwable $x) { if ($oldV) delete_file($vp); throw $x; }
            $pdo->prepare('UPDATE hero_videos SET title=?,description=?,video_path=?,poster_path=? WHERE id=?')->execute([$title, $desc ?: null, $vp, $pp, $id]);
            delete_file($oldV); delete_file($oldP); flash('Video updated.');
        }
    } elseif ($a === 'activate') {
        $pdo->beginTransaction();
        try { $pdo->exec('UPDATE hero_videos SET is_active=0 WHERE is_active=1');
              $pdo->prepare('UPDATE hero_videos SET is_active=1 WHERE id=?')->execute([$id]); $pdo->commit(); }
        catch (Throwable $x) { $pdo->rollBack(); throw $x; }
        flash('Video is now live on the homepage. The previous video was deactivated.');
    } elseif ($a === 'deactivate') {
        $pdo->prepare('UPDATE hero_videos SET is_active=0 WHERE id=?')->execute([$id]); flash('Video deactivated. The homepage will show a fallback until you activate one.');
    } elseif ($a === 'delete') {
        $s = $pdo->prepare('SELECT * FROM hero_videos WHERE id=?'); $s->execute([$id]); $o = $s->fetch();
        if ($o) { $pdo->prepare('DELETE FROM hero_videos WHERE id=?')->execute([$id]); delete_file($o['video_path']); delete_file($o['poster_path']); flash('Video deleted.'); }
    }
}); }
require __DIR__ . '/_head.php';
$list = $pdo->query('SELECT * FROM hero_videos ORDER BY is_active DESC, id DESC')->fetchAll(); ?>
<h1>Manage Hero Video</h1>
<div class="box"><h2>Upload new video</h2><p>MP4 only. Server limit: <b><?= server_limit_mb() ?> MB</b>.</p>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="add">
<label>Title</label><input type="text" name="title" maxlength="150" required>
<label>Description (optional)</label><textarea name="description" rows="2" maxlength="1000"></textarea>
<label>MP4 video</label><input type="file" name="video" accept="video/mp4" required data-prev="pv">
<label>Poster image (JPG, PNG or WebP)</label><input type="file" name="poster" accept="image/jpeg,image/png,image/webp" data-prev="pp">
<p>Preview:</p><video id="pv" controls style="max-width:320px;display:none"></video> <img id="pp" alt="" style="max-width:200px;display:none"><p><button>Upload video</button></p></form></div>
<div class="box"><h2>Uploaded videos</h2><?php if (!$list): ?><p>No videos yet. Upload one above.</p><?php endif; ?>
<?php foreach ($list as $v): ?><div class="item">
 <video controls preload="none" <?= $v['poster_path'] ? 'poster="../' . e($v['poster_path']) . '"' : '' ?> src="../<?= e($v['video_path']) ?>"></video>
 <div style="flex:1"><b><?= e($v['title']) ?></b> <span class="tag <?= $v['is_active'] ? 'on' : '' ?>"><?= $v['is_active'] ? 'Active on homepage' : 'Inactive' ?></span>
 <p><?= e($v['description']) ?></p>
 <div class="acts"><form class="inl" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
  <?php if ($v['is_active']): ?><button name="action" value="deactivate" class="gray">Deactivate</button><?php else: ?><button name="action" value="activate">Activate</button><?php endif; ?>
  <button name="action" value="delete" class="red" onclick="return confirm('Delete this video and its files?')">Delete</button></form></div>
 <details><summary>Edit / replace files</summary><form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
  <label>Title</label><input type="text" name="title" value="<?= e($v['title']) ?>" required maxlength="150"><label>Description</label><textarea name="description" rows="2"><?= e($v['description']) ?></textarea>
  <label>Replace video (optional)</label><input type="file" name="video" accept="video/mp4"><label>Replace poster (optional)</label><input type="file" name="poster" accept="image/jpeg,image/png,image/webp"><p><button>Save changes</button></p></form></details></div></div>
<?php endforeach; ?></div>
<script>document.querySelectorAll('[data-prev]').forEach(function(i){i.onchange=function(){var t=document.getElementById(i.dataset.prev);if(i.files[0]){t.src=URL.createObjectURL(i.files[0]);t.style.display='block';}};});</script>
</div></body></html>
