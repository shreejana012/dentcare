<?php 
// Start the session to track user data
session_start();
ob_start();
require('Assets/connection.php');
require('Assets/head.php');
require('Assets/navbar.php');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Fetch doctors dynamically
$doctorQuery = "SELECT id, name, specialization FROM doctors";
$doctorResult = $conn->query($doctorQuery);
?>

<section class="home-slider owl-carousel">
    <div class="slider-item bread-item" style="background-image: url('Assets/images/bg_1.jpg');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container" data-scrollax-parent="true">
            <div class="row slider-text align-items-end">
                <div class="col-md-7 col-sm-12 ftco-animate mb-5">
                    <p class="breadcrumbs" data-scrollax="properties: { translateY: '70%', opacity: 1.6 }"><span class="mr-2"><a href="index.php">Home</a></span> <span>Book appointment</span></p>
                    <h1 class="mb-3" data-scrollax="properties: { translateY: '70%', opacity: .9 }">Our Service Keeps you Smile</h1>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
// Display a welcome message with the user's name
echo "<section class='ftco-section py-4'>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
                    <div class='d-flex justify-content-between align-items-center p-3 border rounded bg-light shadow-sm'>
                        <h5 class='mb-0'>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</h5>
                        <div>
                            <a href='logout.php' class='btn btn-danger me-2'>Logout</a>
                            <a href='appointments_list.php' class='btn btn-secondary'>View Appointments</a>
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
                                    <?php while ($row = $doctorResult->fetch_assoc()): ?>
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

<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $doctorId = (int)$_POST['doctor']; // Fetch doctor_id directly
    $appointmentDate = htmlspecialchars($_POST['appointmentDate']);
    $appointmentTime = htmlspecialchars($_POST['appointmentTime']);
    $message = htmlspecialchars($_POST['message']);
  
    


    // Insert data into the appointments table
    $query = "INSERT INTO appointments (full_name, email, phone, doctor_id, appointment_date, appointment_time, message) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    // Check if prepare() was successful
    if ($stmt === false) {
        die("Error in SQL statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param('sssisss', $fullName, $email, $phone, $doctorId, $appointmentDate, $appointmentTime, $message);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect with success message
        $_SESSION['success'] = "Appointment booked successfully!";
        header("Location: appointments_list.php");
        exit();
    } else {
        // Redirect with error message
        $_SESSION['error'] = "Failed to book the appointment. Please try again.";
        header("Location: appointments_list.php");
        exit();
    }
}
require('Assets/foot.php');
require('Assets/footer.php') ?>
