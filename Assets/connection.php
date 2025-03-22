<?php
// db_connection.php

// Database configuration
$host = getenv('AZURE_MYSQL_HOST') ?: 'localhost';
$username = getenv('AZURE_MYSQL_USERNAME') ?: 'root';
$password = getenv('AZURE_MYSQL_PASSWORD') ?: '';
$dbname = getenv('AZURE_MYSQL_DBNAME') ?: 'dentcare';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Display a success message (for testing only, remove in production)
//echo "Connected successfully";
?>
