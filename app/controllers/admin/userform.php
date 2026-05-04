<?php
declare(strict_types=1);

// Start session if not already started
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current logged-in user ID
$adminUserId = $_SESSION['admin_user_id'] ?? null;

if (!$adminUserId) {
    die('Unauthorized');
}

// Fetch user record from DB
$sql = sprintf("SELECT id, username, email FROM adminusers WHERE id=%d", (int)$adminUserId);
$res = $db->query($sql);

if (!$res || $res->num_rows === 0) {
    die('User not found');
}

$user = $res->fetch_object();

// Map DB fields to form variables
$id               = $user->id;
$username         = $user->username;
$email            = $user->email;

// Optional title for the form
$title = 'Your Profile';

// Render the Tailwind form
render('userform', [
    'id'               => $id,
    'username'         => $username,
    'email'            => $email
]);
