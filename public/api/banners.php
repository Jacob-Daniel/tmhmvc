<?php
declare(strict_types=1);

$page_id = filter_input(INPUT_GET, 'page_id', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$page_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing page_id parameter']);
    exit;
}

try {
    $res = getList('banners', "where page_id = $page_id");

    if (!$res) {
        http_response_code(404);
        echo json_encode(['error' => 'Image not found']);
        exit;
    }

	$images = [];

	while ($row = $res->fetch_assoc()) {
	    $images[] = $row;
	}

	echo json_encode($images);

} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}