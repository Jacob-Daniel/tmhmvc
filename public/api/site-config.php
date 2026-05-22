<?php
declare(strict_types=1);

try {
    $result = getList('config', 'WHERE id = 1 LIMIT 1');
    $config = $result->fetch_assoc();

    if (!$config) {
        http_response_code(404);
        echo json_encode(['error' => 'Config not found']);
        exit;
    }

    echo json_encode($config);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}