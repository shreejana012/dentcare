<?php
session_start();
ob_start();
require('Assets/connection.php');
require('Assets/head.php');
require('Assets/navbar.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Invalid appointment ID.";
    header("Location: appointments.php");
    exit;
}

$id = $_GET['id'];
$email = $_SESSION['email'];

// Fetch appointment details
$query = "SELECT * FROM appointments WHERE id = ? AND email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('is', $id, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Appointment not found.";
    header("Location: appointments.php");
    exit;
}

$appointment = $result->fetch_assoc();

// Update appointment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $doctor = $_POST['doctor'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $message = $_POST['message'];

    $update_query = "UPDATE appointments SET full_name=?, phone=?, doctor=?, appointment_date=?, appointment_time=?, message=? WHERE id=? AND email=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('ssssssis', $full_name, $phone, $doctor, $appointment_date, $appointment_time, $message, $id, $email);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Appointment updated successfully!";
        header("Location: appointments.php");
        exit;
    } else {
        $_SESSION['error'] = "Error updating appointment.";
    }
}

?>

<div class="container mt-5">
    <h2>Edit Appointment</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($appointment['full_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($appointment['phone']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="doctor" class="form-label">Doctor</label>
            <input type="text" class="form-control" name="doctor" value="<?php echo htmlspecialchars($appointment['doctor']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="appointment_date" class="form-label">Appointment Date</label>
            <input type="date" class="form-control" name="appointment_date" value="<?php echo htmlspecialchars($appointment['appointment_date']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="appointment_time" class="form-label">Appointment Time</label>
            <input type="time" class="form-control" name="appointment_time" value="<?php echo htmlspecialchars($appointment['appointment_time']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" name="message"><?php echo htmlspecialchars($appointment['message']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Appointment</button>
        <a href="appointments.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php 
$stmt->close();
require('Assets/foot.php'); 
require('Assets/footer.php'); 
?>
