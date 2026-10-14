<?php
// Edit these for your server (or set environment variables).
define('DB_HOST', getenv('DB_HOST') ?: 'jamajama-jamajama.i.aivencloud.com');
define('DB_NAME', getenv('DB_NAME') ?: 'jamajama');
define('DB_PORT', getenv('DB_PORT') ?: '16791');

define('DB_USER', getenv('DB_USER') ?: 'avnadmin');
define('DB_PASS', getenv('DB_PASS') ?: 'AVNS_eFFhyB3MMLlTc5qgdQB');
define('WHATSAPP', '919876543210');
define('PHONE', '+91 98765 43210');
define('EMAIL', 'info@jamajamatour.com');
define('MAX_VIDEO_MB', 200);   // app limit; the PHP limit below may be lower
define('MAX_IMAGE_MB', 5);
define('ROOT_DIR', __DIR__);
