<?php
/**
 * ONE-TIME DIAGNOSTIC — DELETE AFTER USE
 * Shows users table structure + all users
 */
require_once 'db.php';

// Protect with a simple key so randos can't access it
if (($_GET['key'] ?? '') !== 'bremc2026') {
    die('No access.');
}

echo "<style>body{font-family:sans-serif;padding:20px} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ccc;padding:8px} th{background:#333;color:#fff}</style>";

// Show table columns
echo "<h2>users table columns:</h2><table><tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
$cols = $conn->query("DESCRIBE users");
while ($col = $cols->fetch_assoc()) {
    $nullIssue = ($col['Null'] === 'NO' && $col['Default'] === null && $col['Extra'] !== 'auto_increment') ? ' style="background:#ffe0e0"' : '';
    echo "<tr{$nullIssue}><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Default']}</td></tr>";
}
echo "</table>";

// Show all users
echo "<h2>All users:</h2><table><tr><th>ID</th><th>Username</th><th>Role</th><th>Created</th></tr>";
$users = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id");
while ($u = $users->fetch_assoc()) {
    echo "<tr><td>{$u['id']}</td><td>" . htmlspecialchars($u['username']) . "</td><td>{$u['role']}</td><td>{$u['created_at']}</td></tr>";
}
echo "</table>";
echo "<br><p style='color:red'><strong>DELETE THIS FILE AFTER READING THE OUTPUT!</strong></p>";
?>
