<?php
require_once __DIR__ . '/functions.php';

$file = basename($_GET['f'] ?? '');

if (!$file || !preg_match('/\.(jpg|jpeg|png)$/i', $file)) {
    http_response_code(404);
    exit;
}

$path = IMAGES_DIR . $file;

if (!is_file($path)) {
    http_response_code(404);
    exit;
}

$ext   = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$types = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];

header('Content-Type: ' . $types[$ext]);
header('Cache-Control: public, max-age=31536000, immutable');
header('Content-Length: ' . filesize($path));
readfile($path);
