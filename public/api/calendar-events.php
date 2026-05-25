<?php
declare(strict_types=1);

$catId = filter_input(INPUT_GET, 'cat_id', FILTER_VALIDATE_INT);

$where  = 'WHERE active = 1';
$types  = '';
$params = [];

if ($catId) {
    $where .= ' AND cat_id = ?';
    $types .= 'i';
    $params[] = $catId;
}

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
    echo json_encode(['error' => 'Query failed']);
}