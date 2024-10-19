<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection file
include 'db_connect.php'; // Make sure you have this file with proper DB connection setup

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate the input fields
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $crime_type = htmlspecialchars(trim($_POST['crime_type']));
    $location = htmlspecialchars(trim($_POST['location']));
    $user_id = $_SESSION['user_id'];  // Get the logged-in user's ID
    
    // Check if the required fields are not empty
    if (empty($title) || empty($description) || empty($crime_type) || empty($location)) {
        $error_message = "Please fill in all required fields.";
        header("Location: submit_report.php?error=" . urlencode($error_message));
        exit();
    }

    // Handle evidence file upload (if provided)
    $evidence_file = '';
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
        // Define the upload directory
        $upload_dir = 'uploads/evidence/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
        }

        // Get the file info
        $file_name = basename($_FILES['evidence']['name']);
        $file_tmp = $_FILES['evidence']['tmp_name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        
        // Create a unique filename to avoid collisions
        $new_file_name = uniqid() . '.' . $file_ext;
        $file_path = $upload_dir . $new_file_name;
        
        // Move the uploaded file to the designated directory
        if (move_uploaded_file($file_tmp, $file_path)) {
            $evidence_file = $file_path;
        } else {
            $error_message = "Failed to upload evidence. Please try again.";
            header("Location: submit_report.php?error=" . urlencode($error_message));
            exit();
        }
    }

    // If no errors, insert the report into the database
    if (empty($error_message)) {
        // Prepare the SQL query
        $stmt = $conn->prepare("INSERT INTO reports (id,title,description,crime_type,location,evidence) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("isssss", $user_id, $title, $description, $crime_type, $location, $evidence_file);

        if ($stmt->execute()) {
            // Redirect with a success message
            $success_message = "Your crime report has been submitted successfully.";
            header("Location: submit_report.php?success=" . urlencode($success_message));
            exit();
        } else {
            $error_message = "There was an issue submitting your report. Please try again later.";
            header("Location: submit_report.php?error=" . urlencode($error_message));
            exit();
        }
    }
}
?>
