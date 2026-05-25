<?php
declare(strict_types=1);
$slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_SPECIAL_CHARS);

// Temporary debug log
error_log("page-by-slug requested: " . ($slug ?? 'NO SLUG'));

if (!$slug) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing slug parameter']);
    exit;
}
try {
    $page = getRecord('pages', 'slug', $slug);

    // Debug log
    error_log("page-by-slug found: " . ($page ? 'YES id=' . $page->id : 'NO'));

    if (!$page) {
        http_response_code(404);
        echo json_encode(['error' => 'Page not found']);
        exit;
    }
    try {
        $seolink = getRecord('seo_links', 'entity_id', $page->id);
        $seo     = $seolink ? getRecord('seo', 'id', $seolink->target_id) : null;
        $page->seo = $seo ?? null;
    } catch (RuntimeException $e) {
        $page->seo = null;
    }
    echo json_encode($page);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}