<?php
// Usage (CLI only): php create_admin.php username password
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/config.php';
if ($argc < 3 || strlen($argv[2]) < 10) exit("Usage: php create_admin.php <username> <password (10+ chars)>\n");
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->prepare('INSERT INTO admins(username,password_hash) VALUES(?,?) ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash)')
    ->execute([$argv[1], password_hash($argv[2], PASSWORD_DEFAULT)]);
echo "Admin saved.\n";
