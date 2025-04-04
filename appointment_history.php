<?php
session_start();
require('Assets/connection.php');
require('Assets/head.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

// Get the filter parameter (default to 'all')
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build the query based on the filter
$query = "SELECT a.*, d.name as doctor_name 
          FROM appointments a 
          LEFT JOIN doctors d ON a.doctor_id = d.id
          WHERE 1=1";

if ($filter === 'done') {
    $query .= " AND a.status = 'done'";
} elseif ($filter === 'cancelled') {
    $query .= " AND a.status = 'cancelled'";
} elseif ($filter === 'all') {
    $query .= " AND (a.status = 'done' OR a.status = 'cancelled')";
}

$query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$result = $conn->query($query);

include('admin_navbar.php'); 
?>

<section class="ftco-section">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0">Appointment History</h1>
                <p class="text-muted">View completed and cancelled appointments</p>
            </div>
            <div>
                <a href="admin_dashboard.php" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
        <!-- Filter buttons -->
        <div class="mb-4">
            <div class="btn-group" role="group">
                <a href="appointment_history.php?filter=all" class="btn btn-outline-primary <?php echo $filter === 'all' ? 'active' : ''; ?>">
                    All History
                </a>
                <a href="appointment_history.php?filter=done" class="btn btn-outline-success <?php echo $filter === 'done' ? 'active' : ''; ?>">
                    Completed
                </a>
                <a href="appointment_history.php?filter=cancelled" class="btn btn-outline-danger <?php echo $filter === 'cancelled' ? 'active' : ''; ?>">
                    Cancelled
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Contact</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php 
                                    // Format date and time
                                    $appointment_date = date('M d, Y', strtotime($row['appointment_date']));
                                    $appointment_time = date('h:i A', strtotime($row['appointment_time']));
                                    
                                    // Set status badge color
                                    $status_class = $row['status'] === 'done' ? 'bg-success' : 'bg-danger';
                                    $status_text = $row['status'] === 'done' ? 'Completed' : 'Cancelled';
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['doctor_name'] ?? 'N/A'); ?></td>
                                        <td>
                                            <div><?php echo $appointment_date; ?></div>
                                            <div class="text-muted small"><?php echo $appointment_time; ?></div>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </td>
                                        <td>
                                            <div><?php echo htmlspecialchars($row['phone']); ?></div>
                                            <div class="text-muted small"><?php echo htmlspecialchars($row['email']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['message'] ?? 'No message'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">No appointment history found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require('Assets/footer.php'); ?>