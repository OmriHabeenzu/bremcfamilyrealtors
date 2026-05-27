<?php
// Include the database connection
include 'db.php';  // Ensure this path is correct

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input data from the form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL query
    $sql = "INSERT INTO admin (username, password) VALUES (?, ?)";

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Check if the statement preparation was successful
    if ($stmt === false) {
        die('Error in SQL query: ' . $conn->error);
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("ss", $username, $hashed_password);

    // Execute the query
    if ($stmt->execute()) {
        echo "Admin registered successfully!";
        // Redirect or further actions
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
