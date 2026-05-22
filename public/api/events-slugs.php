<?php
declare(strict_types=1);

// --------------------------------------------------
// Filters
// --------------------------------------------------
$catId = filter_input(INPUT_GET, 'cat_id', FILTER_VALIDATE_INT);

// --------------------------------------------------
// Query
// --------------------------------------------------
$where  = 'WHERE active = 1';
$types  = '';
$params = [];

if ($catId) { $where .= ' AND cat_id = ?'; $types .= 'i'; $params[] = $catId; }

$where .= ' ORDER BY start_date DESC';

try {
    $result = $types
        ? getListWhere('events', $where, $types, $params)
        : getList('events', $where);

    $slugs = [];
    while ($row = $result->fetch_assoc()) {
        $slugs[] = ['slug' => $row['slug']];
    }

    echo json_encode($slugs);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}