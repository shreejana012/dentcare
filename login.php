<?php 
session_start();
ob_start();
require('Assets/connection.php');

// Process login if POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require('auth/login_logic.php'); // This handles the login processing
    exit; // login_logic.php will handle redirects
}

// Display the login form
require('Assets/head.php');
require('Assets/navbar.php'); 
?>

<section class="home-slider owl-carousel">
    <div class="slider-item bread-item" style="background-image: url('Assets/images/bg_1.jpg');" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container" data-scrollax-parent="true">
            <div class="row slider-text align-items-end">
                <div class="col-md-7 col-sm-12 ftco-animate mb-5">
                    <p class="breadcrumbs" data-scrollax="properties: { translateY: '70%', opacity: 1.6}"><span class="mr-2"><a href="index.php">Home</a></span> <span>Login</span></p>
                    <h1 class="mb-3" data-scrollax="properties: { translateY: '70%', opacity: .9}">Secure & Easy Login</h1>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ftco-section">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['login_error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo $_SESSION['login_error']; 
                                unset($_SESSION['login_error']); 
                                ?>
                            </div>
                        <?php endif; ?>
                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        Don't have an account? <a href="signup.php">Sign up here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$conn->close();
require('Assets/foot.php');
require('Assets/footer.php');
?>