<?php
declare(strict_types=1);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing id parameter']);
    exit;
}

try {
    $page = getRecord('pages','id',$id);

    if (!$page) {
        http_response_code(404);
        echo json_encode(['error' => 'Page not found']);
        exit;
    }

    $seolink = getRecord('seo_links', 'entity_id', $page->id);
    $seo     = getRecord('seo', 'id', $seolink->target_id);

    $page->seo = $seo;

    echo json_encode($page);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}