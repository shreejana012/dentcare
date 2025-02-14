<?php
// Start the session
session_start();

// Destroy the session to log the user out
session_destroy();

// Optionally, clear all session variables
$_SESSION = array();

// Redirect to the login page or homepage after logout
header("Location: admin_login.php");
exit;
?>