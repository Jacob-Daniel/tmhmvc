<?php
// @ts-nocheck
require_once dirname(__DIR__, 3) . '/app/bootstrap/app.php';

header('Content-Type: application/javascript');
header('Cache-Control: public, max-age=3600');

$config = [
    'baseUrl'    => BASE_URL,
    'imgBaseUrl' => BASE_URL_IMG_DIR,
];
echo 'window.CONFIG = ' . json_encode($config, JSON_UNESCAPED_SLASHES) . ';';