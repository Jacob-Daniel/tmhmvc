<?php
declare(strict_types=1);

header('Content-Type: application/json');

// --------------------------------------------------
// Token Auth
// --------------------------------------------------
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (!preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Missing or malformed Authorization header']);
    exit;
}

$providedToken = trim($matches[1]);

$tokenRow = getListWhere(
    'api_tokens',
    'WHERE token = ? LIMIT 1',
    's',
    [$providedToken]
)->fetch_object();

if (!$tokenRow) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

// --------------------------------------------------
// Filters
// --------------------------------------------------

$fromTs = filter_input(INPUT_GET, 'from', FILTER_VALIDATE_INT);
$toTs   = filter_input(INPUT_GET, 'to',   FILTER_VALIDATE_INT);

// --------------------------------------------------
// Pagination
// --------------------------------------------------
$page     = max(1, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1);
$pageSize = min(50, max(1, filter_input(INPUT_GET, 'pageSize', FILTER_VALIDATE_INT) ?: 10));
$offset   = ($page - 1) * $pageSize;

// --------------------------------------------------
// Query — Sunday evenings (DAYOFWEEK=1 is Sunday)
// start_date and end_date are unix timestamps
// --------------------------------------------------
$where  = 'WHERE active = 1
           AND DAYOFWEEK(FROM_UNIXTIME(start_date)) = 1
           AND TIME(start_time) >= "17:00:00"';
$types  = '';
$params = [];
$total = $countResult->num_rows;

if ($fromTs) {
    $where   .= ' AND start_date >= ?';
    $types   .= 'i';
    $params[] = $fromTs;
}

if ($toTs) {
    $where   .= ' AND start_date <= ?';
    $types   .= 'i';
    $params[] = $toTs;
}

// --------------------------------------------------
// Total count — same WHERE but no LIMIT
// --------------------------------------------------
$countWhere = 'WHERE active = 1
               AND DAYOFWEEK(FROM_UNIXTIME(start_date)) = 1
               AND TIME(start_time) >= "17:00:00"';
$countTypes  = '';
$countParams = [];

if ($fromTs) {
    $countWhere   .= ' AND start_date >= ?';
    $countTypes   .= 'i';
    $countParams[] = $fromTs;
}
if ($toTs) {
    $countWhere   .= ' AND start_date <= ?';
    $countTypes   .= 'i';
    $countParams[] = $toTs;
}

$countResult = $countTypes
    ? getListWhere('events', $countWhere, $countTypes, $countParams)
    : getList('events', $countWhere);

$total = $countResult->num_rows;

$where .= " ORDER BY start_date DESC LIMIT ? OFFSET ?";
$types   .= 'ii';
$params[] = $pageSize;
$params[] = $offset;

try {
    $result = $types
        ? getListWhere('events', $where, $types, $params)
        : getList('events', $where);

    $events = [];
    while ($row = $result->fetch_assoc()) {
        // $row['blocks'] = htmlToBlocks($row['content'] ?? ''); // can not do this without PHP packages ..
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
exit;