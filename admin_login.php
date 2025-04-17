<?php
session_start();
ob_start();
require('Assets/connection.php');

// Track connection status
$connection_error = false;
if (!$conn) {
    $connection_error = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Loading spinner styles */
        .loader-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* Error message animation */
        .alert-danger {
            animation: shake 0.5s linear;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Loading spinner -->
    <div class="loader-container" id="loader">
        <div class="loader"></div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Admin Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($connection_error): ?>
                            <div class="alert alert-danger text-center">
                                <strong>System Error:</strong> Unable to connect to database. Please try again later or contact support.
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['admin_error'])): ?>
                            <div class="alert alert-danger text-center">
                                <?php echo htmlspecialchars($_SESSION['admin_error']); unset($_SESSION['admin_error']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['admin_info'])): ?>
                            <div class="alert alert-info text-center">
                                <?php echo htmlspecialchars($_SESSION['admin_info']); unset($_SESSION['admin_info']); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="Enter your admin email" autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           required placeholder="Enter your password" autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="loginButton">
                                    <span id="buttonText">Login</span>
                                    <span id="buttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted">
                        © <?php echo date("Y"); ?> Admin Panel
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form submission handling with loading indicators
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // Show loading spinner
            document.getElementById('buttonText').classList.add('d-none');
            document.getElementById('buttonSpinner').classList.remove('d-none');
            document.getElementById('loginButton').disabled = true;
            document.getElementById('loader').style.display = 'flex';
            
            // Continue with form submission
            return true;
        });

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                this.innerHTML = '<i class="bi bi-eye"></i>';
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 1000);
            });
        }, 5000);
    </script>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate inputs
        if (empty($_POST['email']) || empty($_POST['password'])) {
            throw new Exception("Email and password are required");
        }

        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        
        // Further validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        $password = $_POST['password'];

        // Check for connection before proceeding
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        $query = "SELECT email, password FROM admins WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // In production, use password_verify() instead of direct comparison
            if ($password === $admin['password']) {
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['last_activity'] = time(); // For session timeout
                header("Location: admin_dashboard.php");
                exit;
            } else {
                throw new Exception("Invalid email or password!");
            }
        } else {
            throw new Exception("Invalid email or password!");
        }
    } catch (Exception $e) {
        $_SESSION['admin_error'] = $e->getMessage();
        
        // Log the error (silently)
        error_log("Admin login error: " . $e->getMessage());
    }
    
    // Redirect back to login page
    header("Location: admin_login.php");
    exit;
}

// Clear output buffers and send the response
ob_end_flush();
?>