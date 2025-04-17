<?php
// view_notification.php - Displays a specific notification and marks it as read

session_start();
require('Assets/connection.php');
require('Assets/head.php');
require_once('notification_system.php');

// Check if the user is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor_login.php");
    exit;
}

// Check if notification ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: doctor_dashboard.php");
    exit;
}

$notification_id = $_GET['id'];
$doctor_id = $_SESSION['doctor_id'];
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize notification system
$notification_system = new NotificationSystem($conn);

// Get notification details
$query = "SELECT * FROM notifications WHERE id = ? AND user_id = ? AND user_type = 'doctor'";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $notification_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: doctor_dashboard.php");
    exit;
}

$notification = $result->fetch_assoc();

// Mark notification as read
$notification_system->markAsRead($notification_id);

// Get reference data (e.g., appointment details if it's an appointment notification)
$reference_data = null;
if ($notification['reference_id'] && $notification['type'] == 'appointment') {
    $query = "SELECT * FROM appointments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notification['reference_id']);
    $stmt->execute();
    $ref_result = $stmt->get_result();
    
    if ($ref_result->num_rows > 0) {
        $reference_data = $ref_result->fetch_assoc();
    }
}

include('doctor_navbar.php');
?>

<section class="ftco-section">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="doctor_dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="all_notifications.php">Notifications</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Notification</li>
                    </ol>
                </nav>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <?php echo getNotificationIcon($notification['type']); ?>
                            Notification Details
                        </h4>
                        <small class="text-muted"><?php echo date('F j, Y, g:i a', strtotime($notification['created_at'])); ?></small>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php 
                                switch($notification['type']) {
                                    case 'appointment':
                                        echo 'New Appointment';
                                        break;
                                    case 'reminder':
                                        echo 'Appointment Reminder';
                                        break;
                                    case 'message':
                                        echo 'New Message';
                                        break;
                                    case 'system':
                                        echo 'System Notification';
                                        break;
                                    default:
                                        echo 'Notification';
                                }
                            ?>
                        </h5>
                        <p class="card-text"><?php echo htmlspecialchars($notification['message']); ?></p>
                        
                        <?php if ($reference_data && $notification['type'] == 'appointment'): ?>
                            <hr>
                            <h6>Appointment Details</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Patient Name</th>
                                        <td><?php echo htmlspecialchars($reference_data['full_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <td><?php echo htmlspecialchars($reference_data['appointment_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Time</th>
                                        <td><?php echo htmlspecialchars($reference_data['appointment_time']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Message</th>
                                        <td><?php echo htmlspecialchars($reference_data['message']); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <a href="doctor_dashboard.php" class="btn btn-primary">View in Dashboard</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="all_notifications.php" class="btn btn-secondary">Back to All Notifications</a>
                        
                        <?php if ($notification['type'] == 'appointment' && $reference_data): ?>
                            <button type="button" class="btn btn-success" 
                                    data-toggle="modal" 
                                    data-target="#completeModal<?php echo $reference_data['id']; ?>">
                                Complete Appointment
                            </button>
                            
                            <!-- Complete Appointment Modal -->
                            <div class="modal fade" id="completeModal<?php echo $reference_data['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="completeModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="completeModalLabel">Complete Appointment</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post" action="doctor_dashboard.php">
                                            <div class="modal-body">
                                                <input type="hidden" name="appointment_id" value="<?php echo $reference_data['id']; ?>">
                                                <div class="form-group">
                                                    <label for="appointment_notes">Notes</label>
                                                    <textarea class="form-control" name="appointment_notes" rows="5" placeholder="Enter notes about this appointment..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" name="complete_appointment" class="btn btn-success">Complete Appointment</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Bootstrap JS and jQuery for modals -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php require('Assets/footer.php'); ?>