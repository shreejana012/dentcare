<?php
session_start();
require('Assets/connection.php');
require('Assets/head.php');

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $insertQuery = "INSERT INTO doctors (name, specialization, email, phone) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $name, $specialization, $email, $phone);
    $stmt->execute();
    header("Location: manage_doctors.php");
    exit;
}

include('admin_navbar.php'); 
?>

<section class="ftco-section">
    <div class="container mt-5 bordered">
        <h1 class="text-center mb-4">Add New Doctor</h1>
        <form method="POST" class="w-50 mx-auto">
            <div class="mb-3">
                <label for="name" class="form-label">Doctor's Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="specialization" class="form-label">Specialization</label>
                <input type="text" class="form-control" id="specialization" name="specialization" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div class="mb-3">
            <button type="submit" class="btn btn-success">Add Doctor</button>
        </form>
    </div>
</section>

<?php require('Assets/footer.php'); ?>
