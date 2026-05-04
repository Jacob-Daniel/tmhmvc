<?php
/**
 * TinyMCE Image Upload Handler
 * Path: public/admin/api/tinymceUpload.php
 */

// Prevent direct access
if (!isset($_FILES) || empty($_FILES)) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

// Get domain from config
$domain = getValue('config', 'id', 1, 'domain');
$domain = !empty($domain) ? $domain : '';

// Allowed origins
$accepted_origins = [
    'http://localhost:3000',  // Vite dev server
    'http://amvc.uk',
    'https://amvc.uk',
    $domain
];

// Upload directory - relative to this file in public/admin/api/
$uploadDir = __DIR__ . '/../../../../public/graphics/uploads/';
$uploadUrl = '/graphics/uploads/'; // URL path for browser access

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200);
    exit;
}

// Check origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Origin denied']);
        exit;
    }
}

// Get uploaded file
reset($_FILES);
$file = current($_FILES);

if (!is_uploaded_file($file['tmp_name'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Upload failed']);
    exit;
}

// Validate file size (5MB max)
$maxFileSize = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $maxFileSize) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Maximum size is 5MB']);
    exit;
}

// Sanitize filename
$filename = basename($file['name']);
$filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

// Validate extension
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)]);
    exit;
}

// Validate MIME type (additional security)
$allowedMimes = [
    'image/jpeg',
    'image/jpg',
    'image/png',
    'image/gif',
    'image/webp',
    'image/svg+xml'
];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedMimes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file MIME type']);
    exit;
}

// Generate unique filename to prevent overwrites
$nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
$uniqueFilename = $nameWithoutExt . '_' . time() . '.' . $extension;
$uploadPath = $uploadDir . $uniqueFilename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    // Set proper permissions
    chmod($uploadPath, 0644);
    
    // Return JSON response with location
    echo json_encode([
        'location' => $uploadUrl . $uniqueFilename
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
}