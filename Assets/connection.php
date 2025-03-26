<?php
// db_connection.php

// Database configuration
$host = getenv('AZURE_MYSQL_HOST') ?: 'localhost';
$username = getenv('AZURE_MYSQL_USERNAME') ?: 'root';
$password = getenv('AZURE_MYSQL_PASSWORD') ?: '';
$dbname = getenv('AZURE_MYSQL_DBNAME') ?: 'dentcare';
$ssl_ca = '/home/site/wwwroot/ssl/DigiCertGlobalRootCA.crt.pem';

// Create connection
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, $ssl_ca, NULL, NULL);

// Check connection
if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, 3306, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Display a success message (for testing only, remove in production)
//echo "Connected successfully";
?>
