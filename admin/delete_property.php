<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Auth
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

// POST-only — no GET-based deletions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('Invalid request method.', 'danger');
    redirect('admin_dashboard.php');
}

// CSRF
csrf_verify();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$id) {
    flash('No property specified.', 'danger');
    redirect('admin_dashboard.php');
}

// Optionally: delete associated image files from disk before removing the DB row
$imgStmt = $conn->prepare("SELECT cover_image, other_images FROM properties WHERE id = ?");
$imgStmt->bind_param("i", $id);
$imgStmt->execute();
$imgRow = $imgStmt->get_result()->fetch_assoc();
$imgStmt->close();

if ($imgRow) {
    $allImages = array_filter(array_map('trim', explode(',', $imgRow['cover_image'] . ',' . $imgRow['other_images'])));
    foreach ($allImages as $img) {
        $path = __DIR__ . '/../property_images/' . basename($img);
        if (file_exists($path)) @unlink($path);
    }
}

$stmt = $conn->prepare("DELETE FROM properties WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    flash('Property deleted successfully.', 'success');
} else {
    flash('Could not delete property. It may have already been removed.', 'warning');
}
$stmt->close();

redirect('admin_dashboard.php');
