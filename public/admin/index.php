<?php
require_once __DIR__ . '/../../app/bootstrap/app.php';
require_once __DIR__ . '/../../app/admin_core/auth.php';

handleLogin();
requireAdmin();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
    require __DIR__ . '/../../app/admin_core/head.php';
    require __DIR__ . '/../../app/admin_core/header.php';
    require __DIR__ . '/../../app/admin_core/main.php';
    require __DIR__ . '/../../app/admin_core/footer.php';
    exit;
}

$action = $_GET['action'] ?? null;

$allowedActions = [
    'dashboard',
    'pagelist',
    'pageform',
    'userform',
    'catform',
    'gallery',
    'imageform',
    'configform',
    'catlist',
    'eventlist',
    'emailform',
    'emailgroupmembers',
    'massmailform',
    'emailgroupform',
    'emailgrouplist',
    'memberlist',
    'memberform',
    'emaillist',
    'navlist',
    'navform',
    'eventform',
    'eventlist',
    'flipfield',
    'menu',
    'loadcontent',
    'apitokenform',
];

if (!in_array($action, $allowedActions)) {
    http_response_code(404);
    exit('Invalid action');
}
$viewFile = __DIR__ . "/../../app/controllers/admin/{$action}.php";

if (!file_exists($viewFile)) {
    http_response_code(404);
    exit('Controller not found');
}

require $viewFile;