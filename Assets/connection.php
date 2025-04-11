

<?php
// db_connection.php

// Database configuration
$host = 'localhost'; // Change to your database host
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password
$dbname = 'dentcare'; // Change to your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Display a success message (for testing only, remove in production)
//echo "Connected successfully";
?>
