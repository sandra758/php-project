<?php
// Database connection parameters
$servername = "localhost"; // Use "127.0.0.1" if "localhost" doesn't work
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password (leave empty if no password is set)
$dbname = "crime_reporter"; // Name of the database you created

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
