<?php
declare(strict_types=1);

$featured = filter_input(INPUT_GET, 'featured', FILTER_VALIDATE_INT);

$where  = 'WHERE 1=1';
$types  = '';
$params = [];

if ($featured !== null && $featured !== false) {
    $where .= ' AND featured = ?';
    $types .= 'i';
    $params[] = $featured;
}

$where .= ' ORDER BY title ASC';

try {
    $result = $types
        ? getListWhere('categories', $where, $types, $params)
        : getList('categories', $where);

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    echo json_encode($categories);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}