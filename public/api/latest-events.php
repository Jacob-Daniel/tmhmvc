<?php
declare(strict_types=1);

$limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_SPECIAL_CHARS) ?? 8;

try {
    $sql = "WITH ranked_events AS (
                SELECT 
                    e.id, e.cat_id, e.title, e.slug, e.summary, e.imagepath, e.start_date,
                    ROW_NUMBER() OVER (PARTITION BY e.title ORDER BY e.start_date ASC) AS rn, c.slug as cat_slug
                FROM events e INNER JOIN categories c on e.cat_id = c.id
                WHERE e.end_date > UNIX_TIMESTAMP() AND e.active = 1
            )
            SELECT *
            FROM ranked_events
            WHERE rn = 1
            ORDER BY start_date ASC
            LIMIT $limit";
    global $db;
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $latest = [];
    while ($row = $result->fetch_assoc()) {
        $latest[] = $row;
    }

    echo json_encode($latest);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}