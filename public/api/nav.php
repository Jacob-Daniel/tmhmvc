<?php
declare(strict_types=1);

$group = filter_input(INPUT_GET, 'group', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$group) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing group parameter']);
    exit;
}

try {
    $sql = "SELECT n.id, n.slug, n.label, n.target, p.slug AS page_slug
            FROM navigation n
            LEFT JOIN pages p ON p.id = n.page_id
            WHERE n.menu_group = ? AND n.active = 1
            ORDER BY n.sequence";

    global $db;
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $group);
    $stmt->execute();
    $result = $stmt->get_result();

    $nav = [];
    while ($row = $result->fetch_assoc()) {
        $nav[] = $row;
    }

    echo json_encode($nav);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}