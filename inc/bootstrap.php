<?php
declare(strict_types=1);
require __DIR__ . '/../config.php';
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
session_start();
$supportedLanguages = ['en', 'bn', 'hi'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $supportedLanguages, true)) {
    $_SESSION['language'] = $_GET['lang'];
}
if (!in_array($_SESSION['language'] ?? 'en', $supportedLanguages, true)) {
    $_SESSION['language'] = 'en';
}
if (isset($_GET['content_lang']) && in_array($_GET['content_lang'], $supportedLanguages, true)) {
    $_SESSION['content_language'] = $_GET['content_lang'];
}
if (!in_array($_SESSION['content_language'] ?? 'en', $supportedLanguages, true)) {
    $_SESSION['content_language'] = 'en';
}
require_once __DIR__ . '/i18n.php';

function db(): PDO {
    static $p = null;
    if (!$p) {
        // Build DSN with optional port
        $dsn = 'mysql:host=' . DB_HOST . (DB_PORT ? ';port=' . DB_PORT : '') . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        // If a CA file path is provided, pass it to PDO so SSL can be verified
        if (defined('PDO::MYSQL_ATTR_SSL_CA') && DB_SSL_CA) {
            $options[
                PDO::MYSQL_ATTR_SSL_CA
            ] = DB_SSL_CA;
        }
        $p = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $p;
}
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf()) . '">'; }
function flash(?string $m = null): string {
    if ($m !== null) { $_SESSION['flash'] = $m; return ''; }
    $f = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']); return $f;
}
function ini_bytes(string $k): int {
    $v = trim((string)ini_get($k)); $n = (int)$v;
    switch (strtolower(substr($v, -1))) { case 'g': $n *= 1024; case 'm': $n *= 1024; case 'k': $n *= 1024; }
    return $n;
}
function server_limit_mb(): float {
    return round(min(ini_bytes('upload_max_filesize'), ini_bytes('post_max_size')) / 1048576, 1);
}
function csrf_check(): void {
    // An oversized upload makes PHP drop the whole POST body.
    if (empty($_POST) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        flash('The file is larger than this server allows (' . server_limit_mb() . ' MB). Upload a smaller file or ask your host to raise upload_max_filesize and post_max_size.');
        header('Location: ' . basename($_SERVER['SCRIPT_NAME'])); exit;
    }
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(403); exit('Invalid or expired form token. Go back, refresh and try again.');
    }
}
function require_admin(): void {
    if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
}
function valid_url(string $u): bool {
    return $u === '' || (bool)preg_match('~^(https?://[^\s]+|#[\w-]*|[\w./-]+(\?[\w=&-]*)?(#[\w-]*)?)$~i', $u);
}

/** Validates (extension, MIME, real content, size) and stores an upload. Returns relative path. */
function save_upload(array $f, string $kind): string {
    $rules = [
        'video'  => ['dir' => 'hero-videos',   'exts' => ['mp4'], 'mimes' => ['video/mp4' => 'mp4', 'application/mp4' => 'mp4'], 'max' => MAX_VIDEO_MB],
        'gallery_video' => ['dir' => 'gallery', 'exts' => ['mp4'], 'mimes' => ['video/mp4' => 'mp4', 'application/mp4' => 'mp4'], 'max' => MAX_VIDEO_MB],
        'gallery_image' => ['dir' => 'gallery', 'exts' => ['jpg','jpeg','png','webp'], 'mimes' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'], 'max' => MAX_IMAGE_MB],
        'package_image' => ['dir' => 'packages', 'exts' => ['jpg','jpeg','png','webp'], 'mimes' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'], 'max' => MAX_IMAGE_MB],
        'poster' => ['dir' => 'video-posters', 'exts' => ['jpg','jpeg','png','webp'], 'mimes' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'], 'max' => MAX_IMAGE_MB],
        'banner' => ['dir' => 'hero-banners',  'exts' => ['jpg','jpeg','png','webp'], 'mimes' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'], 'max' => MAX_IMAGE_MB],
    ];
    $r = $rules[$kind];
    $err = $f['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE)
        throw new RuntimeException('File is too large. This server allows up to ' . server_limit_mb() . ' MB.');
    if ($err !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name']))
        throw new RuntimeException('Upload failed. Please try again.');
    if ($f['size'] > $r['max'] * 1048576)
        throw new RuntimeException("File exceeds the {$r['max']} MB limit for this type.");
    $ext = strtolower(pathinfo((string)$f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $r['exts'], true))
        throw new RuntimeException('Invalid file type. Allowed: ' . implode(', ', $r['exts']) . '.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!isset($r['mimes'][$mime])) throw new RuntimeException('File content does not match an allowed type.');
    if ($kind === 'package_image') {
        $extensionMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
        if (($extensionMimes[$ext] ?? null) !== $mime) throw new RuntimeException('File content does not match its extension.');
    }
    if ($kind === 'video' || $kind === 'gallery_video') {
        $h = file_get_contents($f['tmp_name'], false, null, 0, 12);
        if (strlen($h) < 12 || substr($h, 4, 4) !== 'ftyp') throw new RuntimeException('This is not a valid MP4 file.');
    } elseif (@getimagesize($f['tmp_name']) === false) {
        throw new RuntimeException('This is not a valid image.');
    }
    $dir = ROOT_DIR . '/uploads/' . $r['dir'];
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = bin2hex(random_bytes(16)) . '.' . $r['mimes'][$mime];   // never reuse client filename
    if (!move_uploaded_file($f['tmp_name'], "$dir/$name")) throw new RuntimeException('Could not save the file.');
    chmod("$dir/$name", 0644);
    return 'uploads/' . $r['dir'] . '/' . $name;
}
function delete_file(?string $rel): void {
    if (!$rel || strpos($rel, 'uploads/') !== 0) return;
    $real = realpath(ROOT_DIR . '/' . $rel); $base = realpath(ROOT_DIR . '/uploads');
    if ($real && $base && strpos($real, $base . DIRECTORY_SEPARATOR) === 0 && is_file($real)) @unlink($real);
}
function package_image_url(?string $path): ?string {
    if (!$path || !preg_match('~\Auploads/packages/[a-f0-9]{32}\.(?:jpg|png|webp)\z~D', $path)) return null;
    return is_file(ROOT_DIR . '/' . $path) ? $path : null;
}
function has_file(string $k): bool { return !empty($_FILES[$k]['name']); }
function gallery_items(PDO $pdo, ?int $limit = null): array {
    $items = $pdo->query('SELECT * FROM gallery_items ORDER BY display_order DESC, id DESC')->fetchAll();
    $knownPaths = array_fill_keys(array_column($items, 'media_path'), true);
    $legacyImages = glob(ROOT_DIR . '/uploads/gallery/*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
    foreach ($legacyImages as $path) {
        $relativePath = 'uploads/gallery/' . basename($path);
        if (!isset($knownPaths[$relativePath])) {
            $items[] = ['media_type' => 'image', 'title' => null, 'media_path' => $relativePath];
        }
    }
    return $limit === null ? $items : array_slice($items, 0, $limit);
}
function safe_run(callable $fn): void {
    try { $fn(); }
    catch (RuntimeException $x) { flash($x->getMessage()); }
    catch (Throwable $x) { error_log($x->getMessage()); flash('Something went wrong. Please try again.'); }
    header('Location: ' . basename($_SERVER['SCRIPT_NAME'])); exit;
}
