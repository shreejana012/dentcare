<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="doctor_dashboard.php">DentCare Doctor</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="doctor_dashboard.php">Dashboard</a>
                </li>
                
                <!-- Notification Bell -->
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="notificationBtn" onclick="toggleNotifications()" role="button">
                        <i class="fas fa-bell"></i>
                        <?php if ($notification_count > 0): ?>
                            <span class="badge badge-danger rounded-circle position-absolute" style="top: 0; right: 0;"><?php echo $notification_count; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Notification Dropdown -->
                    <div id="notificationDropdown" class="dropdown-menu dropdown-menu-right shadow" style="width: 300px; padding: 0;">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="m-0">Notifications</h6>
                                <?php if ($notification_count > 0): ?>
                                    <span class="badge badge-light"><?php echo $notification_count; ?> new</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                                <?php if ($unread_appointments_result->num_rows > 0): ?>
                                    <div class="list-group list-group-flush">
                                        <?php while ($appt = $unread_appointments_result->fetch_assoc()): ?>
                                            <div class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">New appointment with <?php echo htmlspecialchars($appt['patient_name']); ?></h6>
                                                </div>
                                                <small>Date: <?php echo htmlspecialchars($appt['appointment_date']); ?> at <?php echo htmlspecialchars($appt['appointment_time']); ?></small>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3">
                                        <p class="text-muted mb-0">No new notifications</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($notification_count > 0): ?>
                                <div class="card-footer bg-light">
                                    <form method="post" action="">
                                        <button type="submit" name="mark_read" class="btn btn-sm btn-block btn-outline-primary">
                                            Mark all as read
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="logout_doctor.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Add Font Awesome for icons if not already included -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* Custom CSS for the notification dropdown */
#notificationDropdown {
    display: none;
    position: absolute;
    right: 0;
    z-index: 1000;
}

#notificationDropdown.show {
    display: block;
}

/* Badge positioning */
.position-relative {
    position: relative;
}

.position-absolute {
    position: absolute;
}
</style>