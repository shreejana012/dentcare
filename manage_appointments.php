<?php
session_start();
require('Assets/connection.php');
require('Assets/head.php');

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

// Check if the status column exists
$check_status_column = "SHOW COLUMNS FROM appointments LIKE 'status'";
$status_column_exists = $conn->query($check_status_column);
$has_status_column = $status_column_exists && $status_column_exists->num_rows > 0;

// Fetch active appointments with doctor names (exclude done/cancelled if status column exists)
$query = "SELECT a.*, d.name as doctor_name 
          FROM appointments a 
          LEFT JOIN doctors d ON a.doctor_id = d.id";

if ($has_status_column) {
    $query .= " WHERE (a.status IS NULL OR a.status = '' OR a.status = 'pending')";
}

$query .= " ORDER BY a.appointment_date ASC, a.appointment_time ASC";
$result = $conn->query($query);

// Delete appointment
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_query = "DELETE FROM appointments WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_appointments.php");
    exit;
}

// Update appointment status
if ($has_status_column && isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    
    if ($status == 'done' || $status == 'cancelled') {
        $update_query = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Appointment marked as " . ucfirst($status) . " successfully!";
        } else {
            $_SESSION['error'] = "Failed to update appointment status.";
        }
        
        header("Location: manage_appointments.php");
        exit;
    }
}

include('admin_navbar.php'); 
?>

<section class="ftco-section">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Appointments</h1>
            <?php if ($has_status_column): ?>
                <a href="appointment_history.php" class="btn btn-info">
                    <i class="fa fa-history"></i> View Appointment History
                </a>
            <?php endif; ?>
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

        <?php if (!$has_status_column): ?>
            <div class="alert alert-warning">
                <strong>Notice:</strong> The 'status' column is missing from your appointments table. 
                Some features will be limited. Please add this column to enable full functionality.
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Active Appointments</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Contact</th>
                                <th>Message</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php 
                                    // Format date and time
                                    $appointment_date = date('M d, Y', strtotime($row['appointment_date']));
                                    $appointment_time = date('h:i A', strtotime($row['appointment_time']));
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['doctor_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo $appointment_date; ?></td>
                                        <td><?php echo $appointment_time; ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($row['phone']); ?></div>
                                            <div class="text-muted small"><?php echo htmlspecialchars($row['email']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['message'] ?? 'No message'); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="edit_appointment.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <?php if ($has_status_column): ?>
                                                <a href="manage_appointments.php?id=<?php echo $row['id']; ?>&status=done" class="btn btn-success btn-sm" onclick="return confirm('Mark this appointment as completed?')">
                                                    <i class="fa fa-check"></i> Done
                                                </a>
                                                <a href="manage_appointments.php?id=<?php echo $row['id']; ?>&status=cancelled" class="btn btn-warning btn-sm" onclick="return confirm('Mark this appointment as cancelled?')">
                                                    <i class="fa fa-times"></i> Cancel
                                                </a>
                                                <?php endif; ?>
                                                <a href="manage_appointments.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this appointment?')">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <p class="text-muted mb-0">No active appointments found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                // Create fade-out effect
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = '0';
                // Remove the element after the fade completes
                setTimeout(function() {
                    alert.remove();
                }, 1000);
            }, 5000); // Wait 5 seconds before starting fade
        });
    });
    </script>
</section>

<?php require('Assets/footer.php'); ?>