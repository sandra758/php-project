<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection file
include 'db_connect.php';

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
    } else {
        // Handle evidence file upload (if provided)
        $evidence_file = '';
        if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
            // Define the upload directory
            $upload_dir = 'uploads/evidence/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Create the directory if not exists
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
            }
        }

        // If no errors, insert the report into the database
        if (empty($error_message)) {
            // Prepare the SQL query
            $stmt = $conn->prepare("INSERT INTO reports (user_id, title, description, crime_type, location, evidence, date_reported) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssss", $user_id, $title, $description, $crime_type, $location, $evidence_file);

            if ($stmt->execute()) {
                $success_message = "Your crime report has been submitted successfully.";
            } else {
                $error_message = "There was an issue submitting your report. Please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Reporter - Submit Crime Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            color: #555;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], textarea, select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            background: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #4cae4c;
        }
        .success-message, .error-message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Submit a Crime Report</h2>

            <!-- Display Success or Error Message -->
            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <?= $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?= $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Crime Reporting Form -->
            <form action="report_process2.php" method="post" enctype="multipart/form-data">
                <label for="title">Title:</label><br>
                <input type="text" id="title" name="title" required><br><br>
                
                <label for="description">Description:</label><br>
                <textarea id="description" name="description" rows="4" required></textarea><br><br>

                <label for="crime_type">Type of Crime:</label><br>
                <select id="crime_type" name="crime_type" required>
                    <option value="">Select a type of crime</option>
                    <option value="Murder">Murder</option>
                    <option value="Theft">Theft</option>
                    <option value="Theft">Raid</option>
                    <option value="Missing Person">Missing Person</option>
                    <option value="Missing Vehicle">Missing Vehicle</option>
                </select><br><br>

                <!-- List of Locations in Ernakulam District -->
                <label for="location">Location:</label><br>
                <select id="location" name="location" required>
                    <option value="">Select a location</option>
                    <option value="Aluva">Aluva</option>
                    <option value="Angamaly">Angamaly</option>
                    <option value="Kalamassery">Kalamassery</option>
                    <option value="Kakkanad">Kakkanad</option>
                    <option value="Kothamangalam">Kothamangalam</option>
                    <option value="Mattancherry">Mattancherry</option>
                    <option value="Muvattupuzha">Muvattupuzha</option>
                    <option value="North Paravur">North Paravur</option>
                    <option value="Perumbavoor">Perumbavoor</option>
                    <option value="Thrikkakara">Thrikkakara</option>
                    <option value="Tripunithura">Tripunithura</option>
                    <option value="Vazhakulam">Vazhakulam</option>
                    <option value="Vypeen">Vypeen</option>
                    <!-- Add more locations as needed -->
                </select><br><br>

                <label for="evidence">Upload Evidence (Images, Videos, etc.):</label><br>
                <input type="file" id="evidence" name="evidence"><br><br>

                <input type="submit" value="Submit Report">
            </form>
        </div>
    </div>
</body>
</html>
