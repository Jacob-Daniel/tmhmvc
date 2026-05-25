<?php
declare(strict_types=1);


try {
    $pages = getRows('pages',['slug'],'active = 1');
    if (!$pages) {
        http_response_code(404);
        echo json_encode(['error' => 'Pages not found']);
        exit;
    }

    echo json_encode($pages);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}