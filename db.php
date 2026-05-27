<?php
// Load credentials from config file stored outside the web root
$config = dirname(__DIR__) . '/config.php';
if (!file_exists($config)) {
    die('Site configuration file not found. Please contact the administrator.');
}
require_once $config;

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // In production, never reveal the actual error to the browser
    error_log('DB Connection failed: ' . $conn->connect_error);
    die('Database connection error. Please try again later.');
}

// Use UTF-8 throughout
$conn->set_charset('utf8mb4');
