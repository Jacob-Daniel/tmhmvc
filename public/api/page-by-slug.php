<?php
declare(strict_types=1);

$slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$slug) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing slug parameter']);
    exit;
}

try {
    $result = getListWhere('pages', 'WHERE slug = ? LIMIT 1', 's', [$slug]);
    $page = $result->fetch_assoc();

    if (!$page) {
        http_response_code(404);
        echo json_encode(['error' => 'Page not found']);
        exit;
    }

    echo json_encode($page);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}