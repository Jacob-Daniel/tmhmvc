<?php
declare(strict_types=1);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing id parameter']);
    exit;
}

try {
    $result = getListWhere('pages', 'WHERE id = ? LIMIT 1', 'i', [$id]);
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