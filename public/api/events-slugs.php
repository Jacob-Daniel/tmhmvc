<?php
declare(strict_types=1);

$catId = filter_input(INPUT_GET, 'cat_id', FILTER_VALIDATE_INT);

//tod check catId

$where  = 'WHERE e.active = 1 AND e.is_canonical = 1';
$types  = '';
$params = [];

if ($catId) {
    $where .= ' AND e.cat_id = ?';
    $types .= 'i';
    $params[] = $catId;
}

$where .= ' ORDER BY e.start_date DESC';

try {
    $sql = "SELECT e.slug, e.updated, e.created, e.title, c.slug AS cat, c.id AS catId 
            FROM events e
            JOIN categories c ON e.cat_id = c.id
            {$where}";

    global $db;
    if ($types) {
        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $db->query($sql);
    }

    $slugs = [];
    while ($row = $result->fetch_assoc()) {
        $slugs[] = [
            'slug' => $row['slug'],
            'cat'  => $row['cat'],
        ];
    }

    echo json_encode($slugs);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}