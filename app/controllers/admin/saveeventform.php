<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../shared/functions.php';

$post = $_POST;

$isEdit      = !empty($post['edit']) && (int)$post['edit'] > 0;
$isRecurring = !$isEdit && isset($post['is_recurring']) && (int)$post['is_recurring'] === 1;
$startDateRaw = trim($post['start_date'] ?? '');
$endDateRaw   = trim($post['end_date'] ?? '');
$startTime    = trim($post['start_time'] ?? '00:00');
$endTime      = trim($post['end_time'] ?? '00:00');
$frequency    = trim($post['frequency'] ?? '');
$calendarDays = $post['calendar_days'] ?? [];

function buildSlug(string $title): string
{
    $base = strtolower(trim($title));
    $base = iconv('UTF-8', 'ASCII//TRANSLIT', $base);
    $base = preg_replace('/[^a-z0-9]+/', '-', $base);
    return trim($base, '-');
}

/**
 * Saves a single event occurrence using a prepared statement.
 * Returns the new row id on success, or null on failure (caller should
 * inspect $db->error if they need to log/report the failure reason).
 */
function saveOccurrence(array $data, bool $isCanonical, string $canonicalSlug): ?int
{
    global $db;

    $slug = $isCanonical
        ? $canonicalSlug
        : $canonicalSlug . '-' . date('d-m-Y', $data['start_date']);

    $sql = "INSERT INTO events
                (active, price, featured, created, slug, canonical_slug,
                 is_canonical, imagepath, title, summary, content, cat_id,
                 frequency, start_date, end_date, start_time, end_time, recurring, metad, metak)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return null;
    }

    $active     = (int)($data['active'] ?? 0);
    $price      = (float)($data['price'] ?? 0);
    $featured   = (int)($data['featured'] ?? 0);
    $created    = time();
    $isCanonicalInt = $isCanonical ? 1 : 0;
    $imagepath  = (string)($data['imagepath'] ?? '');
    $title      = (string)$data['title'];
    $summary    = (string)($data['summary'] ?? '');
    $content    = (string)($data['content'] ?? '');
    $catId      = (int)$data['cat_id'];
    $freq       = (string)($data['frequency'] ?? '');
    $startDate  = (int)$data['start_date'];
    $endDate    = (int)$data['end_date'];
    $startTimeV = (string)$data['start_time'];
    $endTimeV   = (string)$data['end_time'];
    $recurring  = 1;
    $metad      = (string)($data['metad'] ?? '');
    $metak      = (string)($data['metak'] ?? '');

    // Type string, one char per bound value, in column order:
    // active(i) price(d) featured(i) created(i) slug(s) canonical_slug(s)
    // is_canonical(i) imagepath(s) title(s) summary(s) content(s) cat_id(i)
    // frequency(s) start_date(i) end_date(i) start_time(s) end_time(s)
    // recurring(i) metad(s) metak(s)
    $types = 'idiississssisiississ';
    $stmt->bind_param(
        $types,
        $active, $price, $featured, $created, $slug, $canonicalSlug,
        $isCanonicalInt, $imagepath, $title, $summary, $content, $catId,
        $freq, $startDate, $endDate, $startTimeV, $endTimeV, $recurring, $metad, $metak
    );

    $ok = $stmt->execute();
    $insertId = $ok ? $stmt->insert_id : null;
    $stmt->close();

    return $insertId;
}

if ($isEdit) {
    // Pre-process date fields — both ends anchored to UTC so start_date and
    // end_date represent consistent absolute instants (previously end_date
    // was missing the UTC anchor, causing a mismatch against start_date).
    $_POST['start_date'] = (string)(int)strtotime($startDateRaw . ' ' . $startTime . ' UTC');
    $_POST['end_date']   = (string)(int)strtotime($endDateRaw   . ' ' . $endTime   . ' UTC');
    require __DIR__ . '/saveform.php';
    exit;
}

// --- random dates (comma-separated) ---
$startDates = array_filter(array_map('trim', explode(',', $startDateRaw)));
$canonicalSlug = buildSlug($post['title'] ?? 'event');

if (count($startDates) > 1) {
    $first = true;
    $failures = [];

    foreach ($startDates as $date) {
        $post['start_date'] = strtotime($date . ' ' . $startTime . ' UTC');
        $post['end_date']   = $endDateRaw
            ? strtotime($date . ' ' . $endTime . ' UTC')
            : $post['start_date'];

        $id = saveOccurrence($post, $first, $canonicalSlug);
        if ($id === null) {
            $failures[] = $date;
        }
        $first = false;
    }

    if ($failures) {
        echo json_encode([
            'type'    => 'error',
            'message' => 'Some occurrences failed to save: ' . implode(', ', $failures),
        ]);
    } else {
        echo json_encode(['type' => 'success', 'message' => 'Events created.']);
    }
    exit;
}

// --- recurring ---
if ($isRecurring) {
    $post['recurring'] = 1;

    // NOTE: Monthly uses a flat 30-day offset, so it will drift against
    // actual calendar months over time (e.g. Jan 31 -> Mar 2 -> Apr 1).
    // Leaving as-is per existing behaviour; flag if calendar-accurate
    // monthly recurrence is wanted instead.
    $intervals = [
        'Daily'     => 86400,
        'Weekly'    => 604800,
        'Bi-Weekly' => 1209600,
        'Monthly'   => 2592000,
    ];

    // NOTE: convertDate() lives in shared/functions.php and its UTC-safety
    // is unverified from this file alone — if it uses strtotime() without
    // a UTC anchor, these loop boundaries are in server-local time even
    // though the per-occurrence timestamps below are UTC-anchored.
    $loopDate = convertDate($startDateRaw, '-');
    $endLoop  = convertDate($endDateRaw, '-');
    if (!$loopDate || !$endLoop) {
        echo json_encode(['type' => 'error', 'message' => 'Invalid dates.']);
        exit;
    }

    $interval = $intervals[$frequency] ?? 604800;
    $first    = true;
    $failures = [];

    while ($loopDate <= $endLoop) {
        for ($day = 0; $day <= 6; $day++) {
            $candidate = strtotime("+{$day} days", $loopDate);
            $dayOfWeek = (int)date('w', $candidate);

            // NOTE: calendar_days is compared as strings with strict
            // in_array(). If the frontend ever sends ints (not "0".."6"
            // strings), this silently matches nothing.
            if (in_array((string)$dayOfWeek, $calendarDays, true)) {
                $dateStr = date('Y-m-d', $candidate);
                $post['start_date'] = strtotime($dateStr . ' ' . $startTime . ' UTC');
                $post['end_date']   = strtotime($dateStr . ' ' . $endTime   . ' UTC');

                $id = saveOccurrence($post, $first, $canonicalSlug);
                if ($id === null) {
                    $failures[] = $dateStr;
                }
                $first = false;
            }
        }
        $loopDate = strtotime("+{$interval} seconds", $loopDate);
    }

    if ($failures) {
        echo json_encode([
            'type'    => 'error',
            'message' => 'Some occurrences failed to save: ' . implode(', ', $failures),
        ]);
    } else {
        echo json_encode(['type' => 'success', 'message' => 'Recurring events created.']);
    }
    exit;
}

// --- single event ---
$post['start_date'] = strtotime($startDateRaw . ' ' . $startTime . ' UTC');
$post['end_date']   = strtotime($endDateRaw   . ' ' . $endTime   . ' UTC');

$id = saveOccurrence($post, true, $canonicalSlug);
if ($id === null) {
    echo json_encode(['type' => 'error', 'message' => 'Event failed to save.']);
} else {
    echo json_encode(['type' => 'success', 'message' => 'Event created.']);
}
exit;