<?php
declare(strict_types=1);

try {
    $config = getRecord('config','id',1);

    if (!$config) {
        http_response_code(404);
        echo json_encode(['error' => 'Config not found']);
        exit;
    }

    $link = getRecord('seo_links', 'entity_id', 1, "AND entity_type = 'organization'");
    $seo  = getRecord('seo', 'id', $link->target_id);

    $config->seo = $seo;    

    echo json_encode($config);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}