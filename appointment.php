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

$doctorQuery = "SELECT id, name, specialization FROM doctors";
$doctorResult = $conn->query($doctorQuery);
?>

<section class="home-slider owl-carousel">
    <div class="slider-item bread-item" style="background-image: url('Assets/images/bg_1.jpg');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container" data-scrollax-parent="true">
            <div class="row slider-text align-items-end">
                <div class="col-md-7 col-sm-12 ftco-animate mb-5">
                    <p class="breadcrumbs"><span class="mr-2"><a href="index.php">Home</a></span> <span>Appointments</span></p>
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
                        <h4>Book an Appointment</h4>
                    </div>
                    <div class="card-body">
                        <form action="appointment.php" method="POST">
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
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Book Appointment</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <small>We will confirm your appointment via email or phone.</small>
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
