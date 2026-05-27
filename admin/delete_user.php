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
    flash('No user specified.', 'danger');
    redirect('admin_dashboard.php');
}

// Prevent admin from deleting their own account
if ((int)$_SESSION['user_id'] === $id) {
    flash('You cannot delete your own account.', 'warning');
    redirect('admin_dashboard.php');
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    flash('User deleted successfully.', 'success');
} else {
    flash('Could not delete user. It may have already been removed.', 'warning');
}
$stmt->close();

redirect('admin_dashboard.php');
