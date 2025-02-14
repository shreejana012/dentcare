<?php
session_start();
require('Assets/connection.php');
require('Assets/head.php');

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

// Get doctor details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM doctors WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();
} else {
    header("Location: manage_appointments.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $updateQuery = "UPDATE doctors SET name = ?, specialization = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssi", $name, $specialization, $email, $phone, $id);
    $stmt->execute();
    header("Location: manage_doctors.php");
    exit;
}

include('admin_navbar.php'); 
?>

<section class="ftco-section">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Edit Doctor</h1>
        <form method="POST" class="w-50 mx-auto">
            <div class="mb-3">
                <label for="name" class="form-label">Doctor's Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($doctor['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="specialization" class="form-label">Specialization</label>
                <input type="text" class="form-control" id="specialization" name="specialization" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($doctor['phone']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</section>

<?php require('Assets/footer.php'); ?>
