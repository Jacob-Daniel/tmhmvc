<?php
declare(strict_types=1);

$category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
$midnight = strtotime('midnight');
$where  = 'WHERE active = 1';
$types  = '';
$params = [];

if ($category) {
    $where .= ' AND cat_id = (SELECT id FROM categories WHERE slug = ?)';
    $types .= 's';
    $params[] = $category;
}

$midnightUTC = strtotime('today midnight UTC');
$where  .= ' AND start_date >= ?';
$types  .= 'i';
$params[] = $midnightUTC;

$where .= ' ORDER BY start_date ASC';
try {
    $result = $types
        ? getListWhere('events', $where, $types, $params)
        : getList('events', $where);

    if (!$result) {
        http_response_code(404);
        echo json_encode(['error' => 'Events not found']);
        exit;
    }    

    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = ['start_date' => $row['start_date']];
    }

    echo json_encode($events);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed', 'message' => $e->getMessage()]);
}