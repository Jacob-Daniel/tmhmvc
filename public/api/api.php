<?php
// public/api/api.php
require_once dirname(__DIR__, 2) . '/app/bootstrap/app.php';
header('Content-Type: application/json');

// --------------------------------------------------
// Bearer Token Auth
// --------------------------------------------------
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

// --------------------------------------------------
// Resolve endpoint
// --------------------------------------------------
$uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path     = preg_replace('#^/api/api\.php/?#', '', $uri);
$segments = $path ? explode('/', $path) : [];
$endpoint = $segments[0] ?? null;

// echo json_encode(['debug_endpoint' => $endpoint, 'debug_path' => $path, 'debug_uri' => $uri]);
// exit;

if (!$endpoint) {
    http_response_code(404);
    echo json_encode(['error' => 'No endpoint provided']);
    exit;
}

// --------------------------------------------------
// Whitelist
// --------------------------------------------------
$allowed = [
    'events',
    'events-news',
    'event-slugs',
];

if (!in_array($endpoint, $allowed)) {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid endpoint']);
    exit;
}

// --------------------------------------------------
// Resolve controller
// --------------------------------------------------
$controllerPath = __DIR__ . "/{$endpoint}.php";

if (file_exists($controllerPath)) {
    require $controllerPath;
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
exit;