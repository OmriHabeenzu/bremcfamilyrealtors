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

$username  = clean($_POST['username'] ?? '');
$password  = $_POST['password'] ?? '';
$confirm   = $_POST['confirm_password'] ?? '';
$role      = in_array($_POST['role'] ?? '', ['user', 'admin']) ? $_POST['role'] : 'user';

if (empty($username) || empty($password)) {
    flash('Username and password are required.', 'danger');
    redirect('admin_dashboard.php');
}

if ($password !== $confirm) {
    flash('Passwords do not match.', 'danger');
    redirect('admin_dashboard.php');
}

if (strlen($password) < 8) {
    flash('Password must be at least 8 characters.', 'danger');
    redirect('admin_dashboard.php');
}

// Check for duplicate username
$check = $conn->prepare("SELECT id FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    flash('Username already exists.', 'warning');
    $check->close();
    redirect('admin_dashboard.php');
}
$check->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt   = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashed, $role);

if ($stmt->execute()) {
    flash("User '{$username}' added successfully.", 'success');
} else {
    flash('Error adding user. Please try again.', 'danger');
}
$stmt->close();

redirect('admin_dashboard.php');
