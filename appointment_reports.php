<?php
session_start();
require('Assets/connection.php');
require('Assets/head.php');

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

include('admin_navbar.php');

// fetch total number of appointments
$totalQuery = "SELECT COUNT(*) AS total_appointments FROM appointments";
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
$totalResult = $conn->query($totalQuery);
if (!$totalResult) {
    die("Query failed: " . $conn->error);
}
$totalAppointments = $totalResult->fetch_assoc()['total_appointments'];

// fetch appointments per doctor
$doctorQuery = "
    SELECT d.name AS doctor_name, COUNT(a.id) AS total 
    FROM appointments a
    LEFT JOIN doctors d ON a.doctor_id = d.id
    GROUP BY d.name
    ORDER BY total DESC
";
$doctorResult = $conn->query($doctorQuery);

$doctorData = [];
while ($row = $doctorResult->fetch_assoc()) {
    $doctorData[$row['doctor_name']] = $row['total'];
}
// fetch appointments per user
$userQuery = "SELECT full_name, COUNT(*) AS total FROM appointments GROUP BY full_name ORDER BY total DESC LIMIT 5";
$userResult = $conn->query($userQuery);
$userData = [];
while ($row = $userResult->fetch_assoc()) {
    $userData[$row['full_name']] = $row['total'];
}

// fetch most booked time slots
$timeQuery = "SELECT appointment_time, COUNT(*) AS total FROM appointments GROUP BY appointment_time ORDER BY total DESC LIMIT 5";
$timeResult = $conn->query($timeQuery);
$timeData = [];
while ($row = $timeResult->fetch_assoc()) {
    $timeData[$row['appointment_time']] = $row['total'];
}

// fetch monthly appointment trends
$monthlyQuery = "SELECT DATE_FORMAT(appointment_date, '%Y-%m') AS month, COUNT(*) AS total FROM appointments GROUP BY month ORDER BY month ASC";
$monthlyResult = $conn->query($monthlyQuery);
$monthlyLabels = [];
$monthlyCounts = [];
while ($row = $monthlyResult->fetch_assoc()) {
    $monthlyLabels[] = $row['month'];
    $monthlyCounts[] = $row['total'];
}
?>

<section class="ftco-section">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Appointment Reports</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Appointments</h5>
                        <p class="card-text fs-3"><?php echo $totalAppointments; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Per Doctor -->
        <h3 class="mt-4">Appointments Per Doctor</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Appointments</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctorData as $doctor => $count): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doctor); ?></td>
                        <td><?php echo $count; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Appointments Per User -->
        <h3 class="mt-4">Top 5 Active Users</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Appointments</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userData as $user => $count): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user); ?></td>
                        <td><?php echo $count; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Most Booked Time Slots -->
        <h3 class="mt-4">Top 5 Most Booked Time Slots</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Appointments</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeData as $time => $count): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($time); ?></td>
                        <td><?php echo $count; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Monthly Trends Chart -->
        <h3 class="mt-4">Monthly Appointment Trends</h3>
        <canvas id="monthlyChart"></canvas>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Appointments Chart
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($monthlyLabels); ?>,
            datasets: [{
                label: 'Appointments',
                data: <?php echo json_encode($monthlyCounts); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php require('Assets/footer.php'); ?>
