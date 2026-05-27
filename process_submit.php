<?php
require_once 'functions.php';
require_once 'db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// ── Auth ─────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    flash('Please log in to submit a property.', 'warning');
    redirect('login.php');
}

// ── CSRF ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('submit.php');
}
csrf_verify();

$user_id = (int)$_SESSION['user_id'];

// ── Text inputs ───────────────────────────────────────────────
$title     = clean($_POST['title'] ?? '');
$description = clean($_POST['description'] ?? '');
$price     = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
$location  = clean($_POST['location'] ?? '');
$rooms     = filter_var($_POST['rooms'] ?? '', FILTER_VALIDATE_INT);
$type      = in_array($_POST['type'] ?? '', ['sale', 'rent']) ? $_POST['type'] : '';
$video_url = filter_var(trim($_POST['video_link'] ?? ''), FILTER_VALIDATE_URL) ?: null;

if (!$title || !$description || $price === false || !$location || $rooms === false || !$type) {
    flash('All required fields must be filled in correctly.', 'danger');
    redirect('submit.php');
}

// ── Image upload ──────────────────────────────────────────────
$target_dir = __DIR__ . '/images/';
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

$allowed_types = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
$imagePaths    = [];
$imageFiles    = $_FILES['images'];
$errors        = [];

for ($i = 0; $i < count($imageFiles['name']); $i++) {
    if ($imageFiles['error'][$i] !== UPLOAD_ERR_OK) continue;

    // ── Size check ───────────────────────────────────────────
    if ($imageFiles['size'][$i] > 5_000_000) {
        $errors[] = basename($imageFiles['name'][$i]) . ' exceeds the 5 MB limit.';
        continue;
    }

    // ── MIME check (reads actual file content, not just extension) ──
    $mime = mime_content_type($imageFiles['tmp_name'][$i]);
    if (!array_key_exists($mime, $allowed_types)) {
        $errors[] = basename($imageFiles['name'][$i]) . ' is not a valid JPG or PNG image.';
        continue;
    }

    // ── Secondary sanity check with getimagesize ─────────────
    if (getimagesize($imageFiles['tmp_name'][$i]) === false) {
        $errors[] = basename($imageFiles['name'][$i]) . ' does not appear to be a valid image.';
        continue;
    }

    // ── Generate unique filename (never use the user-supplied name) ──
    $ext      = $allowed_types[$mime];
    $newName  = uniqid('img_', true) . '.' . $ext;
    $destPath = $target_dir . $newName;

    if (move_uploaded_file($imageFiles['tmp_name'][$i], $destPath)) {
        $imagePaths[] = $newName;
    } else {
        $errors[] = 'Could not save ' . basename($imageFiles['name'][$i]) . '.';
    }
}

if ($errors) {
    flash(implode(' ', $errors), 'warning');
}

if (empty($imagePaths)) {
    flash('Please upload at least one valid JPG or PNG image.', 'danger');
    redirect('submit.php');
}

$cover_image  = $imagePaths[0];
$other_images = implode(',', array_slice($imagePaths, 1));

// ── Insert ────────────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO properties
        (user_id, title, description, price, location, cover_image, other_images, rooms, type, video_url)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("ississssss",
    $user_id, $title, $description, $price,
    $location, $cover_image, $other_images,
    $rooms, $type, $video_url
);

if ($stmt->execute()) {
    $newId = $stmt->insert_id;
    $stmt->close();
    flash('Property submitted successfully! It is now live.', 'success');
    redirect('property.php?id=' . $newId);
} else {
    $stmt->close();
    flash('Database error while saving the property. Please try again.', 'danger');
    redirect('submit.php');
}
