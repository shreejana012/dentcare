<?php
session_start();
require('Assets/connection.php');
require('Assets/head.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch website data
$user_count_query = "SELECT COUNT(*) AS count FROM users";
$user_count_result = $conn->query($user_count_query)->fetch_assoc()['count'];

$appointment_count_query = "SELECT COUNT(*) AS count FROM appointments";
$appointment_count_result = $conn->query($appointment_count_query)->fetch_assoc()['count'];

require('admin_navbar.php'); ?>

<section class="ftco-section">
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1 class="display-4 text-primary">Admin Dashboard</h1>
            <p class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_email']); ?></strong></p>
        </div>
        <div class="row g-4">
            <!-- User Count Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-primary rounded-lg">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h5 class="card-title text-uppercase text-primary">Total Users</h5>
                        <p class="display-6 text-success"><?php echo $user_count_result; ?></p>
                    </div>
                </div>
            </div>
            <!-- Appointment Count Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-warning rounded-lg">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-3x text-warning mb-3"></i>
                        <h5 class="card-title text-uppercase text-warning">Total Appointments</h5>
                        <p class="display-6 text-danger"><?php echo $appointment_count_result; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require('Assets/footer.php'); ?>
