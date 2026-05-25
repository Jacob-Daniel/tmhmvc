<?php
declare(strict_types=1);

$slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$slug) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing slug parameter']);
    exit;
}

try {
    $page = getRecord('pages', 'slug', $slug);

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