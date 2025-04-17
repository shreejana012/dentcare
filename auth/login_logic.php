<?php
// Don't start session if in test mode
if (!defined('PHPUNIT_TEST') && session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Use the global connection
    $conn = $GLOBALS['conn'] ?? require_once __DIR__ . '/../Assets/connection.php';

    $stmt = $conn->prepare("SELECT id, email, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['username'] = $row['username'];

            if (!defined('PHPUNIT_TEST')) {
                header("Location: appointment.php");
                exit;
            }
        } else {
            $_SESSION['login_error'] = "Invalid password. Please try again.";
        }
    } else {
        $_SESSION['login_error'] = "No user found with this email.";
    }

    if (!defined('PHPUNIT_TEST')) {
        header("Location: login.php");
        exit;
    }
}