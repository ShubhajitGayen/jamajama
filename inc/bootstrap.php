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
        $p = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
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
