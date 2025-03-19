<?php
session_start();
require('Assets/connection.php');
require('Assets/head.php');

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}
// Fetch doctors
$queryDoctors = "SELECT * FROM doctors";
$resultDoctors = $conn->query($queryDoctors);

// Delete doctor
if (isset($_GET['delete_doctor'])) {
    $id = $_GET['delete_doctor'];
    $deleteDoctorQuery = "DELETE FROM doctors WHERE id = ?";
    $stmt = $conn->prepare($deleteDoctorQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_appointments.php");
    exit;
}

include('admin_navbar.php'); 
?>
<section class="ftco-section">
<div class="container mt-5">
<!-- Manage Doctors Section -->
<h1 class="text-center mb-4">Manage Doctors</h1>
        <div class="d-flex justify-content-between mb-4">
            <a href="add_doctor.php" class="btn btn-success">Add New Doctor</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultDoctors->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td>
                                <a href="edit_doctor.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="manage_appointments.php?delete_doctor=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this doctor?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
</div>
</section>
<?php require('Assets/footer.php') ; ?>