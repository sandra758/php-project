<?php
session_start();
include 'db_connect.php'; // Ensure this connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];

    // File upload handling
    $target_dir = "uploads/";
    $file_name = basename($_FILES["evidence"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is a valid type (images, videos, documents)
    $allowed_types = array("jpg", "jpeg", "png", "gif", "mp4", "avi", "mov", "pdf", "doc", "docx");

    if (!in_array($fileType, $allowed_types)) {
        $uploadOk = 0;
        $error_message = "Sorry, only JPG, JPEG, PNG, GIF, MP4, AVI, MOV, PDF, DOC, and DOCX files are allowed.";
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $uploadOk = 0;
        $error_message = "Sorry, file already exists.";
    }

    // Check file size (5MB maximum)
    if ($_FILES["evidence"]["size"] > 5000000) {
        $uploadOk = 0;
        $error_message = "Sorry, your file is too large.";
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<div class='error-message'>$error_message</div>";
    } else {
        if (move_uploaded_file($_FILES["evidence"]["tmp_name"], $target_file)) {
            // Insert report into database including the file path
            $stmt = $conn->prepare("INSERT INTO reports (title, description, location, evidence) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $description, $location, $target_file);

            if ($stmt->execute()) {
                echo "<div class='success-message'>New report submitted successfully</div>";
            } else {
                echo "<div class='error-message'>Error: " . $stmt->error . "</div>";
            }
        } else {
            echo "<div class='error-message'>Sorry, there was an error uploading your file.</div>";
        }
    }
}
?>
