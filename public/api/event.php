<?php
declare(strict_types=1);

$slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$slug) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing slug parameter']);
    exit;
}

try {
    $event = getRecord('events','slug',$slug);

    if (!$event) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        exit;
    }

    $seolink = getRecord('seo_links', 'entity_id', $event->id);
    $seo     = getRecord('seo', 'id', $seolink->target_id);

    $event->seo = $seo;

    echo json_encode($event);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}