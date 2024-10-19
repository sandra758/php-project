         <?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include 'db_connect.php';

// Fetch the latest five crime reports from the database
$sql = "SELECT id, title, description, crime_type, location, evidence, date_reported FROM reports ORDER BY date_reported DESC LIMIT 5";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Reporter - Official Website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .logo {
            font-size: 36px; /* Larger font size */
            font-weight: bold; /* Bold text */
            text-transform: uppercase;
            letter-spacing: 3px; /* Increase spacing between letters */
            margin-bottom: 10px;
            background: linear-gradient(90deg, #5cb85c, #2c3e50); /* Gradient background for text */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent; /* Makes gradient visible */
        }
        .navbar {
            display: flex;
            justify-content: center;
            background-color: #444;
            padding: 10px;
        }
        .navbar a {
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }
        .navbar a:hover {
            background-color: #5cb85c;
            color: #fff;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            position: relative; /* Make the container the positioning reference for the side image */
        }
        .marquee-container {
            background-color: #5cb85c;
            color: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
            height: 50px;
        }
        .marquee {
            display: inline-block;
            white-space: nowrap;
            position: absolute;
            animation: marquee 10s linear infinite;
            font-size: 36px;
            font-weight: bold;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        @keyframes marquee {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(-100%);
            }
        }
        .heading {
            font-size: 48px; /* Increase the font size */
            font-weight: bold;
            color: #5cb85c; /* Green color */
            text-transform: uppercase; /* Make text uppercase */
            letter-spacing: 5px; /* Increase spacing between letters */
            margin: 20px 0; /* Margin above and below */
            text-align: center; /* Center the text */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Add shadow for better visibility */
        }
        .info-section {
            background: #fff;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .info-section h2 {
            color: #5cb85c;
        }
        .info-section p {
            font-size: 18px;
            line-height: 1.6;
        }
        .report-container {
            margin-top: 50px;
        }
        .report {
            background: #fff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .report h3 {
            color: #5cb85c;
        }
        .report p {
            margin: 10px 0;
        }
        .side-image {
            position: absolute; /* Allows positioning relative to the nearest positioned ancestor */
            right: 10px; /* Distance from the right side */
            top: 150px; /* Distance from the top */
            width: 300px; /* Width of the image */
            height: auto; /* Maintain aspect ratio */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Optional shadow for better visibility */
            z-index: 10; /* Ensure it is on top of other elements */
        }
        footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .footer-links a {
            color: #5cb85c;
            text-decoration: none;
            margin: 0 10px;
        }
    </style>
</head>
<body>
        <!-- Navigation Bar -->
    <div class="navbar">
       
        <a href="report_crime.php">Report Crime</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
        <a href="application.php">Applications</a>
        


    </div>

    <div class="container">
        <div class="heading">CRIME REPORTER</div> <!-- Beautiful heading added -->
        <div class="marquee-container">
            <div class="marquee">
                Official Crime Reporting Platform
            </div>
        </div>

        <!-- Information About Crime Reporter -->
        <div class="info-section">
            <h2>About</h2>
            <p>
               <b> Welcome to the official Crime Reporter platform, a user-friendly tool designed to ensure transparency 
                and accountability when reporting crimes in our community. Whether it’s a minor incident or a major crime, 
                we provide a platform for the public to file reports, share details, and upload evidence securely.</b>
            </p>
            <p>
               <b> We work together with local authorities to help create a safer environment for everyone. Feel free to browse 
                the latest crime reports or submit your own reports using the dedicated reporting page. Together, we can make a difference.</b>
            </p>
        </div>

                        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='report'>";
                echo "<h3>" . $row["title"] . "</h3>";
                echo "<p>" . $row["description"] . "</p>";
                echo "<p><strong>Type of Crime:</strong> " . $row["crime_type"] . "</p>";
                echo "<p><strong>Location:</strong> " . $row["location"] . "</p>";
                if (!empty($row["evidence"])) {
                    echo "<p><strong>Evidence:</strong> <a href='" . $row["evidence"] . "' target='_blank'>View File</a></p>";
                }
                echo "<p><small><em>Reported on: " . $row["date_reported"] . "</em></small></p>";
                echo "</div>";
            }
        }        ?>
    </div>

    <footer>
        <p>&copy; 2024 Crime Reporter. All rights reserved.</p>
        <div class="footer-links">
            <a href="terms.php">Terms of Service</a>
            <a href="privacy.php">Privacy Policy</a>
            <a href="contact.php">Contact Us</a>
        </div>
    </footer>
</body>
</html> 