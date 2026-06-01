<?php
/**
 * ONE-TIME USE — DELETE THIS FILE IMMEDIATELY AFTER RUNNING
 * Promotes a user to admin role.
 */
require_once 'db.php';

// ── Change this to your actual username ──────────────────────
$target_username = 'omri'; // ← PUT YOUR USERNAME HERE

$stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE username = ?");
$stmt->bind_param("s", $target_username);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo "<h2 style='color:green;font-family:sans-serif'>✅ Done! \"" . htmlspecialchars($target_username) . "\" is now admin.</h2>";
    echo "<p style='font-family:sans-serif'>⚠️ <strong>Delete this file from the server immediately!</strong></p>";
} else {
    // Show all users so you can find the right username
    echo "<h2 style='color:red;font-family:sans-serif'>❌ User not found. Available usernames:</h2>";
    echo "<ul style='font-family:sans-serif'>";
    $all = $conn->query("SELECT id, username, role FROM users ORDER BY id");
    while ($row = $all->fetch_assoc()) {
        echo "<li>ID {$row['id']}: <strong>{$row['username']}</strong> — role: " . ($row['role'] ?: 'NULL') . "</li>";
    }
    echo "</ul>";
    echo "<p style='font-family:sans-serif'>Edit this file, set \$target_username to the correct value, and refresh.</p>";
}
$stmt->close();
?>
