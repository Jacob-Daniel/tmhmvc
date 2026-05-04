<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../bootstrap/app.php';

// Security: Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Security: Validate and sanitize inputs
$id = filter_input(INPUT_POST, 'prod_id', FILTER_VALIDATE_INT);
$pcid = filter_input(INPUT_POST, 'pcid', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}

// Get record
$rec = getRecord("products", "id", $id);
if (!$rec) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit;
}

$iframe_url = filter_var($rec->iframe, FILTER_SANITIZE_URL);

$allowedPatterns = [
    '#^https://(www\.)?youtube(-nocookie)?\.com/embed/[A-Za-z0-9_-]+#',
    '#^https://player\.vimeo\.com/video/\d+#',
    '#^https://w\.soundcloud\.com/player/\?url=https%3A//api\.soundcloud\.com/(tracks|playlists)/\d+.*$#',
    '#^https://www\.bbc\.co\.uk/news/av/embed/[a-z0-9]+/\d+#',  
];

$isValid = false;
foreach ($allowedPatterns as $pattern) {
    if (preg_match($pattern, $iframe_url)) {
        $isValid = true;
        break;
    }
}

if (!$isValid) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid video URL']);
    exit;
}
$aspect = $rec->extracontent_type === 'audio' ? 'aspect-[12/3]' : 'aspect-video';
$res = [
    'id' => $id,
    'video' => sprintf(
        '<div id="film_%d" class="relative w-full '.$aspect.'"><iframe  class="absolute top-0 left-0 w-full h-full" allow="autoplay; encrypted-media" src="%s" frameBorder="0" allowfullscreen></iframe></div>',
        $id,
        htmlspecialchars($iframe_url, ENT_QUOTES, 'UTF-8')
    ),
    'h2' => sprintf(
       '<h2 class="text-xl md:text-2xl lg:text-5xl">%s</h2>',
        htmlspecialchars($rec->title, ENT_QUOTES, 'UTF-8')

    ),
    'desc' => strip_tags($rec->content, '<p><br><strong><em><ul><li><a>'),
    'button' => '<button class="cursor-pointer px-4 py-2 bg-gray-800 text-white hover:bg-gray-700 transition-colors" data-close><i class="fas fa-times mr-2"></i>Close</button>',
];

echo json_encode($res);
exit;
