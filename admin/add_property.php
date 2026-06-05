<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin_dashboard.php');
}

csrf_verify();

$title       = clean($_POST['title'] ?? '');
$description = clean($_POST['description'] ?? '');
$location    = clean($_POST['location'] ?? '');
$price       = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
$rooms       = filter_var($_POST['rooms'] ?? 1, FILTER_VALIDATE_INT);
$type        = in_array($_POST['type'] ?? '', ['sale', 'rent']) ? $_POST['type'] : 'sale';
$video_url   = filter_var(trim($_POST['video_url'] ?? ''), FILTER_VALIDATE_URL) ?: null;
$user_id     = (int)$_SESSION['user_id'];

if (!$title || !$description || $price === false || !$location) {
    flash('All required fields must be filled in.', 'danger');
    redirect('admin_dashboard.php');
}

// ── Image upload — absolute path to ROOT images/ folder ──────
$target_dir = dirname(__DIR__) . '/property_images/';
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

$allowed    = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
$imagePaths = [];

for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
    if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
    if ($_FILES['images']['size'][$i] > 5_000_000) continue;

    $mime = mime_content_type($_FILES['images']['tmp_name'][$i]);
    if (!array_key_exists($mime, $allowed)) continue;
    if (getimagesize($_FILES['images']['tmp_name'][$i]) === false) continue;

    $newName = uniqid('img_', true) . '.' . $allowed[$mime];
    if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_dir . $newName)) {
        $imagePaths[] = $newName;
    }
}

if (empty($imagePaths)) {
    flash('Please upload at least one valid JPG or PNG image.', 'danger');
    redirect('admin_dashboard.php');
}

$cover_image  = $imagePaths[0];
$other_images = implode(',', array_slice($imagePaths, 1));

$stmt = $conn->prepare(
    "INSERT INTO properties (user_id, title, description, price, location, cover_image, other_images, rooms, type, video_url)
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
    flash('Property added successfully!', 'success');
    redirect('../property.php?id=' . $newId);
} else {
    flash('Database error: ' . $stmt->error, 'danger');
    $stmt->close();
    redirect('admin_dashboard.php');
}
