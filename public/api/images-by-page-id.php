<?php
declare(strict_types=1);

$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$page) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing page parameter']);
    exit;
}

try {
    $res = getList('item_images', "where page_id = $page");

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