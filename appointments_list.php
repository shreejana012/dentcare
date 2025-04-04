<?php
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

// Fetch user's appointments with JOIN to doctors table
$query = "SELECT a.*, d.name as doctor_name 
          FROM appointments a 
          LEFT JOIN doctors d ON a.doctor_id = d.id 
          WHERE a.email = ? 
          ORDER BY a.appointment_date ASC, a.appointment_time ASC";
$stmt = $conn->prepare($query);

// Check if prepare() was successful
if ($stmt === false) {
    die("Error in SQL statement: " . $conn->error);
}

$email = $_SESSION['email'];
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="home-slider owl-carousel">
    <div class="slider-item bread-item" style="background-image: url('Assets/images/bg_1.jpg');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container" data-scrollax-parent="true">
            <div class="row slider-text align-items-end">
                <div class="col-md-7 col-sm-12 ftco-animate mb-5">
                    <p class="breadcrumbs" data-scrollax="properties: { translateY: '70%', opacity: 1.6 }"><span class="mr-2"><a href="index.php">Home</a></span> <span>My Appointments</span></p>
                    <h1 class="mb-3" data-scrollax="properties: { translateY: '70%', opacity: .9 }">Manage Your Dental Appointments</h1>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ftco-section py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center p-4 border rounded bg-light shadow-sm">
                    <div>
                        <h5 class="mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h5>
                        <p class="text-muted mb-0">Manage your upcoming dental appointments</p>
                    </div>
                    <div>
                        <a href="appointment.php" class="btn btn-primary me-2"><i class="fa fa-calendar-plus-o me-1"></i> Book Appointment</a>
                        <a href="logout.php" class="btn btn-outline-danger"><i class="fa fa-sign-out me-1"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-7 text-center heading-section ftco-animate">
                <h2 class="mb-3">My Appointments</h2>
                <p>View and manage all your scheduled dental appointments</p>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-12 text-end">
                <a href="appointment.php" class="btn btn-primary btn-lg"><i class="fa fa-plus-circle me-1"></i> Book New Appointment</a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                        <thead class="table-dark text-white">
                            <tr>
                                <th class="px-4 py-3" style="min-width: 120px;">Full Name</th>
                                <th class="px-4 py-3" style="min-width: 150px;">Email</th>
                                <th class="px-4 py-3" style="min-width: 120px;">Phone</th>
                                <th class="px-4 py-3" style="min-width: 150px;">Doctor</th>
                                <th class="px-4 py-3" style="min-width: 120px;">Date</th>
                                <th class="px-4 py-3" style="min-width: 120px;">Time</th>
                                <th class="px-4 py-3" style="min-width: 150px;">Booked On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php 
                                    // Format the date
                                    $formattedDate = date('M d, Y', strtotime($row['appointment_date']));
                                    
                                    // Format the time
                                    $formattedTime = date('h:i A', strtotime($row['appointment_time']));
                                    
                                    // Format the created_at timestamp
                                    $createdAt = isset($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : 'N/A';
                                    ?>
                                    <tr>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['doctor_name'] ?? $row['doctor'] ?? 'N/A'); ?></td>
                                        <td class="px-4 py-3"><?php echo $formattedDate; ?></td>
                                        <td class="px-4 py-3"><?php echo $formattedTime; ?></td>
                                        <td class="px-4 py-3"><?php echo $createdAt; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fa fa-calendar-times-o fa-4x text-muted mb-3"></i>
                                            <h5>No appointments found</h5>
                                            <p class="text-muted">You haven't booked any appointments yet.</p>
                                            <a href="appointment.php" class="btn btn-primary mt-2">Book Your First Appointment</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-hide alerts after 5 seconds -->
    <script>
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

<?php 
$stmt->close();
require('Assets/foot.php'); 
require('Assets/footer.php'); 
?>