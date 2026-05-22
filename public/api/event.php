<?php
declare(strict_types=1);

$slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$slug) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing slug parameter']);
    exit;
}

try {
    $result = getListWhere(
        'events',
        'WHERE slug = ? AND active = 1 LIMIT 1',
        's',
        [$slug]
    );

    $event = $result->fetch_assoc();

    if (!$event) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        exit;
    }

    echo json_encode($event);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}