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

// Fetch the 5 earliest upcoming appointments
$upcoming_appointments_query = "
    SELECT a.*, d.name as doctor_name 
    FROM appointments a 
    LEFT JOIN doctors d ON a.doctor_id = d.id
    WHERE a.status = 'pending' OR a.status IS NULL
    ORDER BY a.appointment_date ASC, a.appointment_time ASC
    LIMIT 5
";
$upcoming_appointments = $conn->query($upcoming_appointments_query);

// Handle appointment status updates
if (isset($_GET['appointment_id']) && isset($_GET['status'])) {
    $appointment_id = $_GET['appointment_id'];
    $status = $_GET['status'];
    
    if ($status == 'done' || $status == 'cancelled') {
        $update_query = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $status, $appointment_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Appointment status updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update appointment status.";
        }
        
        header("Location: admin_dashboard.php");
        exit;
    }
}


// Handle appointment updates and notifications
// if ($update_query->execute()) {
//     // Set the is_updated flag to 1 and reset is_notified to 0
//     $notifyQuery = "UPDATE appointments SET is_updated = 1, is_notified = 0 WHERE id = ?";
//     $notifyStmt = $conn->prepare($notifyQuery);
//     $notifyStmt->bind_param("i", $appointmentId);
//     $notifyStmt->execute();
    
//     $_SESSION['success'] = "Appointment updated successfully. Patient will be notified.";
//     header("Location: admin_appointments.php");
//     exit();
// } else {
//     $_SESSION['error'] = "Error updating appointment: " . $conn->error;
//     header("Location: admin_appointments.php");
//     exit();
// }

// Count completed and cancelled appointments
$completed_count_query = "SELECT COUNT(*) AS count FROM appointments WHERE status = 'done'";
$completed_count = $conn->query($completed_count_query)->fetch_assoc()['count'];

$cancelled_count_query = "SELECT COUNT(*) AS count FROM appointments WHERE status = 'cancelled'";
$cancelled_count = $conn->query($cancelled_count_query)->fetch_assoc()['count'];

require('admin_navbar.php'); 
?>

<!-- Custom CSS for softer colors -->
<style>
    :root {
        --primary-color: #4a6da7;    /* Softer blue */
        --success-color: #5b9a68;    /* Muted green */
        --info-color: #5da3b2;       /* Softer teal */
        --warning-color: #d9b44a;    /* Muted gold */
        --danger-color: #b95d5d;     /* Muted red */
        --light-bg: #f8f9fa;         /* Light background */
        --card-bg: #ffffff;          /* Card background */
        --dark-text: #343a40;        /* Dark text */
        --muted-text: #6c757d;       /* Muted text */
        --border-color: #e9ecef;     /* Border color */
    }
    
    body {
        background-color: #f5f7fa;
        color: var(--dark-text);
    }
    
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
        background-color: var(--card-bg);
        margin-bottom: 20px;
    }
    
    .card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }
    
    .card-header {
        background-color: var(--card-bg);
        border-bottom: 1px solid var(--border-color);
        padding: 15px 20px;
        font-weight: 600;
    }
    
    .stat-card {
        padding: 20px;
        border-radius: 8px;
        color: white;
        height: 100%;
    }
    
    .stat-card.bg-primary {
        background-color: var(--primary-color) !important;
    }
    
    .stat-card.bg-success {
        background-color: var(--success-color) !important;
    }
    
    .stat-card.bg-info {
        background-color: var(--info-color) !important;
    }
    
    .stat-card.bg-warning {
        background-color: var(--warning-color) !important;
        color: #212529;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-success {
        background-color: var(--success-color);
        border-color: var(--success-color);
    }
    
    .btn-info {
        background-color: var(--info-color);
        border-color: var(--info-color);
    }
    
    .btn-danger {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }
    
    .btn {
        border-radius: 6px;
        font-weight: 500;
        padding: 0.375rem 1rem;
        transition: all 0.2s;
    }
    
    .btn-sm {
        padding: 0.25rem 0.7rem;
        font-size: 0.875rem;
    }
    
    .table thead th {
        background-color: var(--light-bg);
        border-top: none;
        border-bottom: 2px solid var(--border-color);
        color: var(--dark-text);
        font-weight: 600;
        padding: 12px 15px;
    }
    
    .table td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .icon-container {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    .alert {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
</style>

<section class="ftco-section">
    <div class="container-fluid mt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3 mb-2">Admin Dashboard</h1>
                <p class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_email']); ?></strong></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="appointment_history.php" class="btn btn-info me-2">
                    <i class="fa fa-history"></i> View Appointment History
                </a>
                <a href="manage_appointments.php" class="btn btn-primary">
                    <i class="fa fa-calendar"></i> Manage All Appointments
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row g-4">
            <!-- Left side - Statistics -->
            <div class="col-md-4">
                <div class="row g-4">
                    <!-- User Count Card -->
                    <div class="col-12 mb-4">
                        <div class="card h-100">
                            <div class="stat-card bg-primary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Total Users</h6>
                                        <h2 class="card-title mb-0"><?php echo $user_count_result; ?></h2>
                                    </div>
                                    <div class="icon-container">
                                        <i class="fa fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Appointment Count Card -->
                    <div class="col-12 mb-4">
                        <div class="card h-100">
                            <div class="stat-card bg-success">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2">Total Appointments</h6>
                                        <h2 class="card-title mb-0"><?php echo $appointment_count_result; ?></h2>
                                    </div>
                                    <div class="icon-container">
                                        <i class="fa fa-calendar-check-o fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Completed and Cancelled Cards Row -->
                    <div class="col-12">
                        <div class="row g-4">
                            <!-- Completed Appointments Card -->
                            <div class="col-6">
                                <div class="card h-100">
                                    <div class="stat-card bg-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-subtitle mb-1 small">Completed</h6>
                                                <h3 class="card-title mb-0"><?php echo $completed_count; ?></h3>
                                            </div>
                                            <div class="text-white">
                                                <i class="fa fa-check-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Cancelled Appointments Card -->
                            <div class="col-6">
                                <div class="card h-100">
                                    <div class="stat-card bg-warning">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-subtitle mb-1 small">Cancelled</h6>
                                                <h3 class="card-title mb-0"><?php echo $cancelled_count; ?></h3>
                                            </div>
                                            <div>
                                                <i class="fa fa-times-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Upcoming Appointments -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Upcoming Appointments</h5>
                        <a href="manage_appointments.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Date & Time</th>
                                        <th>Contact</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($upcoming_appointments->num_rows > 0): ?>
                                        <?php while ($appointment = $upcoming_appointments->fetch_assoc()): ?>
                                            <?php 
                                            // Format date and time
                                            $appointment_date = date('M d, Y', strtotime($appointment['appointment_date']));
                                            $appointment_time = date('h:i A', strtotime($appointment['appointment_time']));
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($appointment['full_name']); ?></div>
                                                </td>
                                                <td><?php echo htmlspecialchars($appointment['doctor_name'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <div class="fw-bold"><?php echo $appointment_date; ?></div>
                                                    <div class="text-muted small"><?php echo $appointment_time; ?></div>
                                                </td>
                                                <td>
                                                    <div><?php echo htmlspecialchars($appointment['phone']); ?></div>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($appointment['email']); ?></div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="admin_dashboard.php?appointment_id=<?php echo $appointment['id']; ?>&status=done" class="btn btn-sm btn-success" onclick="return confirm('Mark this appointment as completed?')">
                                                            <i class="fa fa-check"></i> Done
                                                        </a>
                                                        <a href="admin_dashboard.php?appointment_id=<?php echo $appointment['id']; ?>&status=cancelled" class="btn btn-sm btn-danger " onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                            <i class="fa fa-times"></i> Cancel
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <p class="text-muted mb-0">No upcoming appointments found.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-hide alerts script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 1s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 1000);
                }, 5000);
            });
        });
    </script>
</section>

<?php require('Assets/footer.php')?>