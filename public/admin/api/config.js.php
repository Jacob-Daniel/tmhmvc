<?php
// @ts-nocheck
require_once dirname(__DIR__, 3) . '/app/bootstrap/app.php';

header('Content-Type: application/javascript');
header('Cache-Control: public, max-age=3600');

$config = [
    'baseUrl'    => BASE_URL,
    'imgBaseUrl' => BASE_URL_IMG_DIR,
    'imgThumb150BaseUrl' => BASE_URL_IMG_THUMB_150_DIR,
];
echo 'window.CONFIG = ' . json_encode($config, JSON_UNESCAPED_SLASHES) . ';';