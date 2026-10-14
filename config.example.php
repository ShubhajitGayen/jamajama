<?php
// Example configuration template for deployment.
// Copy to config.php or set these values in your environment variables.

define('DB_HOST', getenv('DB_HOST') ?: 'jamajama-jamajama.i.aivencloud.com');
define('DB_PORT', getenv('DB_PORT') ?: '16791');
define('DB_NAME', getenv('DB_NAME') ?: 'jamajama');
define('DB_USER', getenv('DB_USER') ?: 'avnadmin');

$db_pass = getenv('DB_PASS');
if ($db_pass === false || $db_pass === '') {
    throw new RuntimeException('DB_PASS is not configured. Set it in your environment before running the app.');
}
define('DB_PASS', $db_pass);

define('WHATSAPP', getenv('WHATSAPP') ?: '919876543210');
define('PHONE', getenv('PHONE') ?: '+91 98765 43210');
define('EMAIL', getenv('EMAIL') ?: 'info@jamajamatour.com');
define('MAX_VIDEO_MB', 200);
define('MAX_IMAGE_MB', 5);
define('ROOT_DIR', __DIR__);
