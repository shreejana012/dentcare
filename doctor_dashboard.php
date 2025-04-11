<?php
session_start();
require('Assets/connection.php');
require('Assets/head.php');

if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor_login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];
$doctor_name = $_SESSION['doctor_name'];

// Connect to database
// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// Check for new notifications (unread appointments)
$notification_query = "SELECT COUNT(*) as count FROM appointments 
                      WHERE doctor_id = ? AND is_read = 0";
$stmt = $conn->prepare($notification_query);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$notification_result = $stmt->get_result();
$notification_data = $notification_result->fetch_assoc();
$notification_count = $notification_data['count'];

// Get notification details for dropdown
$unread_appointments_query = "SELECT id, appointment_date, appointment_time, full_name AS patient_name
                             FROM appointments 
                             WHERE doctor_id = ? AND is_read = 0
                             ORDER BY id DESC LIMIT 5";
$stmt = $conn->prepare($unread_appointments_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$unread_appointments_result = $stmt->get_result();

// Mark notifications as read if requested
if (isset($_POST['mark_read'])) {
    $mark_read_query = "UPDATE appointments SET is_read = 1 WHERE doctor_id = ? AND is_read = 0";
    $stmt = $conn->prepare($mark_read_query);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    
    // Redirect to refresh the page
    header("Location: doctor_dashboard.php?notifications_read=true");
    exit;
}

// Fetch all appointments for the logged-in doctor
$appointments_query = "SELECT id, appointment_date, appointment_time, message, 
                      full_name AS patient_name, email AS patient_email, is_read 
                      FROM appointments 
                      WHERE doctor_id = ?
                      ORDER BY is_read ASC, appointment_date DESC";
$stmt = $conn->prepare($appointments_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments_result = $stmt->get_result();
?>

<?php include('doctor_navbar.php'); ?>

<section class="ftco-section">
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center mb-4">Welcome, Dr. <?php echo htmlspecialchars($doctor_name); ?></h1>
                
                <?php if (isset($_GET['notifications_read'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    All notifications have been marked as read.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>
                
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h3 class="m-0 font-weight-bold text-primary">Your Appointments</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Patient Name</th>
                                        <th>Email</th>
                                        <th>Appointment Date</th>
                                        <th>Time</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($appointments_result->num_rows > 0): ?>
                                        <?php while ($row = $appointments_result->fetch_assoc()): ?>
                                            <tr <?php echo $row['is_read'] == 0 ? 'class="table-info font-weight-bold"' : ''; ?>>
                                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['patient_email']); ?></td>
                                                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                                <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                                                <td><?php echo htmlspecialchars($row['message']); ?></td>
                                                <td>
                                                    <?php if ($row['is_read'] == 0): ?>
                                                        <span class="badge badge-pill badge-info">New</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-pill badge-secondary">Read</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No appointments found</td>
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
</section>

<script>
// JavaScript to handle notification dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('notificationDropdown');
        const notificationBtn = document.getElementById('notificationBtn');
        
        if (dropdown && dropdown.classList.contains('show') && 
            !dropdown.contains(event.target) && 
            event.target !== notificationBtn) {
            dropdown.classList.remove('show');
        }
    });
});

function toggleNotifications() {
    document.getElementById('notificationDropdown').classList.toggle('show');
}
</script>

<?php require('Assets/footer.php'); ?>