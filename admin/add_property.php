<?php
session_start();
include 'db.php';

// Check if the user is logged in and admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $video_url = $_POST['video_url'];

    // Handle multiple image uploads
    $imageFiles = $_FILES['images'];
    $imagePaths = [];

    // Check if 'images' directory exists
    $target_dir = "images/";
    if (!is_dir($target_dir)) {
        echo "The 'images' directory does not exist. Please create it.";
        exit();
    }

    // Check if 'images' directory is writable
    if (!is_writable($target_dir)) {
        echo "The 'images' directory is not writable. Please check permissions.";
        exit();
    }

    for ($i = 0; $i < count($imageFiles['name']); $i++) {
        if ($imageFiles['error'][$i] == UPLOAD_ERR_OK) {
            $file_name = basename($imageFiles['name'][$i]);

            // Sanitize the filename by removing spaces and special characters
            $file_name = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $file_name);
            
            $target_file = $target_dir . $file_name;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is an actual image
            $check = getimagesize($imageFiles['tmp_name'][$i]);
            if ($check === false) {
                echo "File " . $file_name . " is not an image.";
                $uploadOk = 0;
            }

            // Check file size (limit to 5MB)
            if ($imageFiles['size'][$i] > 5000000) {
                echo "Sorry, " . $file_name . " is too large.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
                echo "Sorry, only JPG, JPEG & PNG files are allowed. (" . $file_name . ")";
                $uploadOk = 0;
            }

            // Upload image if everything is ok
            if ($uploadOk) {
                if (move_uploaded_file($imageFiles['tmp_name'][$i], $target_file)) {
                    $imagePaths[] = $file_name;
                } else {
                    echo "Sorry, there was an error uploading " . $file_name . ".";
                }
            }
        }
    }

    if (count($imagePaths) > 0) {
        $images = implode(',', $imagePaths);

        // Insert property details into the database
        $stmt = $conn->prepare("INSERT INTO properties (title, description, price, location, image, video_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $title, $description, $price, $location, $images, $video_url);

        if ($stmt->execute()) {
            echo "Property added successfully!";
        } else {
            echo "Error adding property.";
        }
        $stmt->close();
    } else {
        echo "No images uploaded.";
    }
}
?>
