<?php
$allowedOrigins = [
    'https://torriano.org',
    'https://www.torriano.org',
    'http://localhost:3001',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once dirname(__DIR__, 2) . '/app/bootstrap/app.php';
header('Content-Type: application/json');

// --------------------------------------------------
// Public endpoints (no token required)
// --------------------------------------------------
$uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path     = preg_replace('#^/api/api\.php/?#', '', $uri);
$segments = $path ? explode('/', $path) : [];
$endpoint = $segments[0] ?? null;

// --------------------------------------------------
// Whitelist
// --------------------------------------------------
$publicEndpoints = [
    'categories',
    'event',
    'events',
    'events-news',
    'events-slugs',
    'calendar-events',
    'page',
    'pages-slugs',
    'site-config',
    'page-by-slug',
    'nav',    
    'latest-events',    
    'banners',    
    'home-page',    
];

$isPublic = in_array($endpoint, $publicEndpoints, true);

// --------------------------------------------------
// Bearer Token Auth (skip for public endpoints)
// --------------------------------------------------
if (!$isPublic) {
    $headers    = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (!preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Missing or malformed Authorization header']);
        exit;
    }
    $tokenRow = getListWhere(
        'api_tokens', 'WHERE token = ? LIMIT 1', 's', [trim($matches[1])]
    )->fetch_object();
    if (!$tokenRow) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
}


if (!$endpoint) {
    http_response_code(404);
    echo json_encode(['error' => 'No endpoint provided']);
    exit;
}

$controllerPath = __DIR__ . "/{$endpoint}.php";

if (file_exists($controllerPath)) {
    require $controllerPath;
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
exit;