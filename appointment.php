<?php 
// Start the session to track user data
session_start();
ob_start();

// DevOps PR Change - Minor update for issue #12

require('Assets/connection.php');
require('Assets/head.php');
// require('Assets/navbar.php'); // Use the original navbar

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Fetch doctors dynamically
$doctorQuery = "SELECT id, name, specialization FROM doctors";
$doctorResult = $conn->query($doctorQuery);

// Check for appointment updates (notification count)
$notification_query = "SELECT COUNT(*) as count FROM appointments 
                      WHERE email = ? AND is_updated = 1 AND is_notified = 0";
$stmt = $conn->prepare($notification_query);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$notification_result = $stmt->get_result();
$notification_data = $notification_result->fetch_assoc();
$notification_count = $notification_data['count'];

// Get details of updated appointments
$updated_appointments_query = "SELECT id, appointment_date, appointment_time, doctor_id
                             FROM appointments 
                             WHERE email = ? AND is_updated = 1 AND is_notified = 0
                             ORDER BY id DESC";
$stmt = $conn->prepare($updated_appointments_query);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$updated_appointments_result = $stmt->get_result();

// Mark notifications as read if requested
if (isset($_POST['mark_notified'])) {
    $mark_read_query = "UPDATE appointments SET is_notified = 1 
                       WHERE email = ? AND is_updated = 1 AND is_notified = 0";
    $stmt = $conn->prepare($mark_read_query);
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    header("Location: appointment.php?notifications_read=true");
    exit;
}
?>

<section class="home-slider owl-carousel">
    <div class="slider-item bread-item" style="background-image: url('Assets/images/bg_1.jpg');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container" data-scrollax-parent="true">
            <div class="row slider-text align-items-end">
                <div class="col-md-7 col-sm-12 ftco-animate mb-5">
                    <p class="breadcrumbs"><span class="mr-2"><a href="appointment.php">Home</a></span></p>
                    <h1 class="mb-3">Our Service Keeps you Smile</h1>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
echo "<section class='ftco-section py-4'>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
                    <div class='d-flex justify-content-between align-items-center p-3 border rounded bg-light shadow-sm'>
                        <h5 class='mb-0'>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</h5>
                        <div class='d-flex align-items-center'>
                            <a href='appointments_list.php' class='btn btn-secondary mr-2'>View Appointments</a>
                            <div class='dropdown mr-2 position-relative'>
                                <button class='btn btn-info position-relative' id='notificationBtn' onclick='toggleNotifications()'>
                                    <i class='fas fa-bell'></i> Notifications";

if ($notification_count > 0) {
    echo "<span class='badge badge-danger rounded-circle position-absolute' style='top: -5px; right: -5px;'>" . $notification_count . "</span>";
}

echo "                          </button>
                                <div id='notificationDropdown' class='dropdown-menu dropdown-menu-right shadow' style='width: 300px; padding: 0; display: none;'>
                                    <div class='card'>
                                        <div class='card-header bg-primary text-white d-flex justify-content-between align-items-center'>
                                            <h6 class='m-0'>Appointment Updates</h6>";

if ($notification_count > 0) {
    echo "<span class='badge badge-light'>" . $notification_count . " new</span>";
}

echo "                              </div>
                                        <div class='card-body p-0' style='max-height: 250px; overflow-y: auto;'>";

if ($updated_appointments_result->num_rows > 0) {
    echo "<div class='list-group list-group-flush'>";
    while ($appt = $updated_appointments_result->fetch_assoc()) {
        $doctor_query = "SELECT name FROM doctors WHERE id = ?";
        $stmt = $conn->prepare($doctor_query);
        $stmt->bind_param("i", $appt['doctor_id']);
        $stmt->execute();
        $doctor_result = $stmt->get_result();
        $doctor_data = $doctor_result->fetch_assoc();
        $doctor_name = $doctor_data ? $doctor_data['name'] : 'Unknown Doctor';

        echo "<div class='list-group-item list-group-item-action'>
                <div class='d-flex w-100 justify-content-between'>
                    <h6 class='mb-1'>Appointment updated!</h6>
                </div>
                <p class='mb-1'>Doctor: " . htmlspecialchars($doctor_name) . "</p>
                <small>New Date: " . htmlspecialchars($appt['appointment_date']) . " at " . htmlspecialchars($appt['appointment_time']) . "</small>
              </div>";
    }
    echo "</div>";
} else {
    echo "<div class='text-center py-3'>
            <p class='text-muted mb-0'>No appointment updates</p>
          </div>";
}

echo "                              </div>";

if ($notification_count > 0) {
    echo "<div class='card-footer bg-light'>
            <form method='post'>
                <button type='submit' name='mark_notified' class='btn btn-sm btn-block btn-outline-primary'>
                    Mark all as read
                </button>
            </form>
          </div>";
}

echo "                          </div>
                            </div>
                            <a href='logout.php' class='btn btn-danger'>Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </section>";
?>

<section class="ftco-section">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Book an Appointment</h4>
                    </div>
                    <div class="card-body">
                        <form action="appointment.php" method="POST">
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
                            </div>
                            <div class="mb-3">
                                <label for="doctor" class="form-label">Choose a Doctor</label>
                                <select class="form-control" id="doctor" name="doctor" required>
                                    <option value="">Select a doctor</option>
                                    <?php 
                                    $doctorResult->data_seek(0);
                                    while ($row = $doctorResult->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo htmlspecialchars($row['id']); ?>">
                                            <?php echo htmlspecialchars($row['name'] . " - " . $row['specialization']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="appointmentDate" class="form-label">Appointment Date</label>
                                <input type="date" class="form-control" id="appointmentDate" name="appointmentDate" required>
                            </div>
                            <div class="mb-3">
                                <label for="appointmentTime" class="form-label">Appointment Time</label>
                                <input type="time" class="form-control" id="appointmentTime" name="appointmentTime" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Additional Message</label>
                                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Enter any additional information"></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Book Appointment</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <small class="text-muted">We will confirm your appointment via email or phone.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
#notificationDropdown { position: absolute; right: 0; z-index: 1000; }
#notificationDropdown.show { display: block !important; }
.position-relative { position: relative; }
.position-absolute { position: absolute; }
.mr-2 { margin-right: 0.5rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $doctorId = (int)$_POST['doctor'];
    $appointmentDate = htmlspecialchars($_POST['appointmentDate']);
    $appointmentTime = htmlspecialchars($_POST['appointmentTime']);
    $message = htmlspecialchars($_POST['message']);

    $query = "INSERT INTO appointments (full_name, email, phone, doctor_id, appointment_date, appointment_time, message, is_updated, is_notified) 
              VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Error in SQL statement: " . $conn->error);
    }

    $stmt->bind_param('sssisssi', $fullName, $email, $phone, $doctorId, $appointmentDate, $appointmentTime, $message, $is_updated);
    $is_updated = 0;

    if ($stmt->execute()) {
        $_SESSION['success'] = "Appointment booked successfully!";
        header("Location: appointments_list.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to book the appointment. Please try again.";
        header("Location: appointments_list.php");
        exit();
    }
}

require('Assets/foot.php');
require('Assets/footer.php');
?>
