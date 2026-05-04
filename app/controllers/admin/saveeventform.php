<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../shared/functions.php';

$post = $_POST;

$isEdit     = !empty($post['edit']) && (int)$post['edit'] > 0;
$isRecurring = !$isEdit && isset($post['is_recurring']) && (int)$post['is_recurring'] === 1;
$startDateRaw = trim($post['start_date'] ?? '');
$endDateRaw   = trim($post['end_date'] ?? '');
$startTime    = trim($post['start_time'] ?? '00:00');
$endTime      = trim($post['end_time'] ?? '00:00');
$frequency    = trim($post['frequency'] ?? '');
$calendarDays = $post['calendar_days'] ?? [];

function buildSlug(string $title, ?string $date = null): string
{
    $base = strtolower(trim($title));
    $base = iconv('UTF-8', 'ASCII//TRANSLIT', $base);
    $base = preg_replace('/[^a-z0-9]+/', '-', $base);
    $base = trim($base, '-');
    return $date ? $base . '-' . $date : $base;
}

function saveOccurrence(array $data, bool $isCanonical, string $canonicalSlug): bool
{
    global $db;

    $slug = $isCanonical
        ? $canonicalSlug
        : $canonicalSlug . '-' . date('d-m-Y', $data['start_date']);

    $sql = sprintf(
        "INSERT INTO events 
            (active, price, featured, author, created, slug, canonical_slug, 
             is_canonical, imagepath, title, summary, content, cat_id, 
             frequency, start_date, end_date, start_time, end_time, recurring, metad, metak)
         VALUES (%d,%0.2f,%d,%d,%d,'%s','%s',%d,'%s','%s','%s','%s',%d,'%s',%d,%d,'%s','%s',%d,'%s','%s')",
        (int)$data['active'],
        (float)($data['price'] ?? 0),
        (int)($data['featured'] ?? 0),
        (int)$data['author'],
        (int)$data['created'],
        $db->real_escape_string($slug),
        $db->real_escape_string($canonicalSlug),
        $isCanonical ? 1 : 0,
        $db->real_escape_string($data['imagepath'] ?? ''),
        $db->real_escape_string($data['title']),
        $db->real_escape_string($data['summary'] ?? ''),
        $db->real_escape_string($data['content'] ?? ''),
        (int)$data['cat_id'],
        $db->real_escape_string($data['frequency'] ?? ''),
        (int)$data['start_date'],
        (int)$data['end_date'],
        $db->real_escape_string($data['start_time']),
        $db->real_escape_string($data['end_time']),
        1,
        $db->real_escape_string($data['metad'] ?? ''),
        $db->real_escape_string($data['metak'] ?? '')
    );

    return (bool)$db->query($sql);
}

// --- EDIT existing record ---
if ($isEdit) {
    // Your existing saveform.php generic UPDATE handles this fine
    // Just ensure canonical_slug is preserved and not overwritten
    require __DIR__ . '/saveform.php';
    exit;
}

// --- NEW: random dates (comma-separated) ---
$startDates = array_filter(array_map('trim', explode(',', $startDateRaw)));
$canonicalSlug = buildSlug($post['title'] ?? 'event');

if (count($startDates) > 1) {
    $first = true;
    foreach ($startDates as $date) {
        $post['start_date'] = strtotime($date . ' ' . $startTime);
        $post['end_date']   = $endDateRaw
            ? strtotime($date . ' ' . $endTime)
            : $post['start_date'];
        saveOccurrence($post, $first, $canonicalSlug);
        $first = false;
    }
    echo json_encode(['type' => 'success', 'message' => 'Events created.']);
    exit;
}

// --- NEW: recurring ---
if ($isRecurring) {
    $post['recurring'] = 1;
    $intervals = [
        'Daily'     => 86400,
        'Weekly'    => 604800,
        'Bi-Weekly' => 1209600,
        'Monthly'   => 2592000,
    ];

    $loopDate = convertDate($startDateRaw);
    $endLoop  = convertDate($endDateRaw);

    if (!$loopDate || !$endLoop) {
        echo json_encode(['type' => 'error', 'message' => 'Invalid dates.']);
        exit;
    }

    $interval = $intervals[$frequency] ?? 604800;
    $first    = true;

    while ($loopDate <= $endLoop) {
        for ($day = 0; $day <= 6; $day++) {
            $candidate  = strtotime("+{$day} days", $loopDate);
            $dayOfWeek  = (int)date('w', $candidate);

            if (in_array((string)$dayOfWeek, $calendarDays, true)) {
                $post['start_date'] = strtotime(date('Y-m-d', $candidate) . ' ' . $startTime);
                $post['end_date']   = strtotime(date('Y-m-d', $candidate) . ' ' . $endTime);
                saveOccurrence($post, $first, $canonicalSlug);
                $first = false;
            }
        }
        $loopDate = strtotime("+{$interval} seconds", $loopDate);
    }

    echo json_encode(['type' => 'success', 'message' => 'Recurring events created.']);
    exit;
}

// --- NEW: single event ---
$post['start_date'] = strtotime($startDateRaw . ' ' . $startTime);
$post['end_date']   = strtotime($endDateRaw . ' ' . $endTime);
saveOccurrence($post, true, $canonicalSlug);
echo json_encode(['type' => 'success', 'message' => 'Event created.']);
exit;