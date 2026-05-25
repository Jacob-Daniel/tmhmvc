<?php
declare(strict_types=1);

// --------------------------------------------------
// Filters
// --------------------------------------------------
$dateParam = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
$fromTs    = filter_input(INPUT_GET, 'from',   FILTER_VALIDATE_INT);
$toTs      = filter_input(INPUT_GET, 'to',     FILTER_VALIDATE_INT);
$catId     = filter_input(INPUT_GET, 'cat_id', FILTER_VALIDATE_INT);

if (!$dateParam || !$fromTs || !$toTs || !$catId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameter']);
    exit;
}

// --------------------------------------------------
// Pagination
// --------------------------------------------------
$page     = max(1, filter_input(INPUT_GET, 'page',     FILTER_VALIDATE_INT) ?: 1);
$pageSize = min(50, max(1, filter_input(INPUT_GET, 'pageSize', FILTER_VALIDATE_INT) ?: 10));
$offset   = ($page - 1) * $pageSize;

// --------------------------------------------------
// Base query
// --------------------------------------------------
$baseWhere  = 'WHERE active = 1';
$baseTypes  = '';
$baseParams = [];

if ($catId) {
    $baseWhere  .= ' AND cat_id = ?';
    $baseTypes  .= 'i';
    $baseParams[] = $catId;
}

if ($dateParam) {
    // exact date match
    $midnight = strtotime($dateParam . ' 00:00:00');
    $baseWhere  .= ' AND DATE(FROM_UNIXTIME(start_date)) = DATE(FROM_UNIXTIME(?))';
    $baseTypes  .= 'i';
    $baseParams[] = $midnight;
} elseif ($fromTs) {
    $baseWhere  .= ' AND start_date >= ?';
    $baseTypes  .= 'i';
    $baseParams[] = $fromTs;
} else {
    // default — from today midnight
    $midnight     = strtotime('today midnight');
    $baseWhere  .= ' AND start_date >= ?';
    $baseTypes  .= 'i';
    $baseParams[] = $midnight;
}

if ($toTs) {
    $baseWhere  .= ' AND start_date <= ?';
    $baseTypes  .= 'i';
    $baseParams[] = $toTs;
}

// --------------------------------------------------
// Count
// --------------------------------------------------
$countResult = getListWhere('events', $baseWhere, $baseTypes, $baseParams);
$total       = $countResult->num_rows;

// --------------------------------------------------
// Paginated query
// --------------------------------------------------
$dataWhere  = $baseWhere . ' GROUP BY title ORDER BY MIN(start_date) ASC LIMIT ? OFFSET ?';
$dataTypes  = $baseTypes . 'ii';
$dataParams = [...$baseParams, $pageSize, $offset];

try {
    $result = getListWhere('events', $dataWhere, $dataTypes, $dataParams);
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    echo json_encode([
        'data'     => $events,
        'page'     => $page,
        'pageSize' => $pageSize,
        'total'    => $total,
        'pages'    => (int) ceil($total / $pageSize),
    ]);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}