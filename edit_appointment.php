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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid appointment ID.";
    header("Location: appointments_list.php");
    exit;
}

$id = intval($_GET['id']);
$email = $_SESSION['email'];

// Fetch appointment details
$query = "SELECT * FROM appointments WHERE id = ? AND email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('is', $id, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Appointment not found.";
    header("Location: appointments_list.php");
    exit;
}

$appointment = $result->fetch_assoc();
$stmt->close();

$doctorQuery = "SELECT id, name, specialization FROM doctors";
$doctorResult = $conn->query($doctorQuery);

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
        header("Location: appointments_list.php");
        exit;
    } else {
        $_SESSION['error'] = "Error updating appointment.";
    }
    $stmt->close();
}
?>
<section class="home-slider owl-carousel">
    <div class="slider-item bread-item" style="background-image: url('Assets/images/bg_1.jpg');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container" data-scrollax-parent="true">
            <div class="row slider-text align-items-end">
                <div class="col-md-7 col-sm-12 ftco-animate mb-5">
                    <p class="breadcrumbs"><span class="mr-2"><a href="index.php">Home</a></span> <span>Edit Appointment</span></p>
                    <h1 class="mb-3">Your Healthcare, Simplified</h1>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="ftco-section py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center p-3 border rounded bg-light shadow-sm">
                    <h5 class='mb-0'>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h5>
                    <div>
                        <a href='logout.php' class='btn btn-danger'>Logout</a>
                        <a href='appointments_list.php' class='btn btn-secondary'>View Appointments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="ftco-section">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Edit Appointment</h4>
                    </div>
                    <div class="card-body">
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
                                <label for="doctor" class="form-label">Choose a Doctor</label>
                                <select class="form-control" id="doctor" name="doctor" required>
                                    <option value="">Select a doctor</option>
                                    <?php while ($row = $doctorResult->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo ($row['id'] == $appointment['doctor']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($row['name'] . " - " . $row['specialization']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
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
                            <a href="appointments_list.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
require('Assets/foot.php'); 
require('Assets/footer.php'); 
?>
