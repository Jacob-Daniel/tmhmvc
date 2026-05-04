<?php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/imageupload.php';

use App\Admin\Controllers\ImageUpload;

$controller = new ImageUpload();
$result = $controller->handle();

echo json_encode($result);