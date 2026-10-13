<?php
// Copy this file to config.php in production, or set DB_* variables in your environment.
// This repository version intentionally contains no secret credentials.

define('DB_HOST', getenv('DB_HOST') ?: 'jamajama-jamajama.i.aivencloud.com');
define('DB_PORT', getenv('DB_PORT') ?: '16791');
define('DB_NAME', getenv('DB_NAME') ?: 'defaultdb');
define('DB_PASS', getenv('DB_PASS') ?: 'AVNS_eFFhyB3MMLlTc5qgdQB');

define('DB_USER', getenv('DB_USER') ?: 'avnadmin');

$db_pass = getenv('DB_PASS');
if ($db_pass === false || $db_pass === '') {
    throw new RuntimeException('DB_PASS is not configured. Set it in your environment before running the app.');
}
define('DB_PASS', $db_pass);

define('WHATSAPP', getenv('WHATSAPP') ?: '919876543210');
define('PHONE', getenv('PHONE') ?: '+91 98765 43210');
define('EMAIL', getenv('EMAIL') ?: 'info@jamajamatour.com');
define('MAX_VIDEO_MB', 200);   // app limit; the PHP limit below may be lower
define('MAX_IMAGE_MB', 5);
define('ROOT_DIR', __DIR__);
