<?php
require_once dirname(__DIR__, 3) . '/app/bootstrap/app.php';
require_once APP_PATH . '/admin_core/auth.php';
header('Content-Type: application/json');

// --------------------------------------------------
// Resolve endpoint
// --------------------------------------------------

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$path = trim(str_replace($scriptDir, '', $uri), '/');
$path = preg_replace('#^api\.php/?#', '', $path);

$segments = $path ? explode('/', $path) : [];
$endpoint = $segments[0] ?? null;

if (!$endpoint) {
    http_response_code(404);
    echo json_encode(['error' => 'No endpoint provided']);
    exit;
}

// --------------------------------------------------
// White list of allowed endpoints
// --------------------------------------------------

$allowed = [
    'login',
    'dashboard',
    'navlist',
    'navform',
    'userform',
    'pagelist',
    'pageform',
    'eventlist',
    'eventform',
    'catform',
    'catlist',
    'emaillist',
    'emailform',
    'emailgroupform',
    'emailgrouplist',
    'emailgroupmembers',
    'memberlist',
    'memberform',
    'configform',
    'buildpagestable',
    'editfield',
    'extraimages',
    'flipfield',
    'getimages',
    'getlist',
    'delete',
    'imageModal',
    'imageform',
    'imageupload',
    'loadcontent',
    'loadmenu',
    'processsocialimages',
    'saveeventform',
    'saveform',
    'saveimage',
    'tinymceupload',
    'update_password',
    'savemassmail',
    'massmailform',
    'apievents',
    'savemassmail',
];

if (!in_array($endpoint, $allowed)) {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid endpoint']);
    exit;
}

if ($endpoint === 'login') {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
    $locked = ($_SESSION['failed_lock'] ?? null) ? 'Too many attempts, locked for 5 minutes.' : null;
    $_POST['login'] = 1;
    if (handleLogin()) {
        echo json_encode([
            'success' => true,
            'redirect' => '/admin/index.php'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $locked ?? 'Invalid login attempt: '.$_SESSION['failed_count'] 
        ]);
    }

    exit;
}

// --------------------------------------------------
// Everything else requires authentication
// --------------------------------------------------

$publicApiEndpoints = ['apievents'];

if (!in_array($endpoint, $publicApiEndpoints)) {
    requireAdmin();
}

// --------------------------------------------------
// Resolve controller or view
// --------------------------------------------------

$controllerPath = APP_PATH . "/controllers/admin/{$endpoint}.php";

if (file_exists($controllerPath)) {
    require $controllerPath;
    exit;
}

// --------------------------------------------------
// Nothing matched
// --------------------------------------------------

http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
exit;