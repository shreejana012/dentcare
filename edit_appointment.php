<?php
session_start();
require('Assets/connection.php');
require('Assets/head.php');

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

// Check if ID is set
if (!isset($_GET['id'])) {
    header("Location: manage_appointments.php");
    exit;
}

$id = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve only date and time
    $appointmentDate = htmlspecialchars($_POST['appointmentDate']);
    $appointmentTime = htmlspecialchars($_POST['appointmentTime']);

    // Update only date and time in database
    $updateQuery = "UPDATE appointments SET 
                    appointment_date = ?, 
                    appointment_time = ? 
                    WHERE id = ?";
    
    $stmt = $conn->prepare($updateQuery);
    
    if ($stmt === false) {
        die("Error in SQL statement: " . $conn->error);
    }
    
    $stmt->bind_param('ssi', $appointmentDate, $appointmentTime, $id);
    
    if ($stmt->execute()) {
        // Add notification code right here - after successful update
        $notifyQuery = "UPDATE appointments SET is_updated = 1, is_notified = 0 WHERE id = ?";
        $notifyStmt = $conn->prepare($notifyQuery);
        $notifyStmt->bind_param("i", $id);
        $notifyStmt->execute();
        
        $_SESSION['success'] = "Appointment updated successfully! Patient will be notified.";
        header("Location: manage_appointments.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update the appointment. Please try again.";
    }
}

// Fetch the appointment data
$query = "SELECT * FROM appointments WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_appointments.php");
    exit;
}

$appointment = $result->fetch_assoc();

include('admin_navbar.php');
?>

<section class="ftco-section">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Edit Appointment Date/Time</h4>
                    </div>
                    <div class="card-body">
                        <form action="edit_appointment.php?id=<?php echo $id; ?>" method="POST">
                            <div class="mb-3">
                                <p><strong>Patient:</strong> <?php echo htmlspecialchars($appointment['full_name']); ?></p>
                            </div>
                            <div class="mb-3">
                                <label for="appointmentDate" class="form-label">Appointment Date</label>
                                <input type="date" class="form-control" id="appointmentDate" name="appointmentDate" value="<?php echo htmlspecialchars($appointment['appointment_date']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="appointmentTime" class="form-label">Appointment Time</label>
                                <input type="time" class="form-control" id="appointmentTime" name="appointmentTime" value="<?php echo htmlspecialchars($appointment['appointment_time']); ?>" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update Appointment</button>
                                <a href="manage_appointments.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require('Assets/footer.php'); ?>