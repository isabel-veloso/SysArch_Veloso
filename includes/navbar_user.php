<?php
// Count unread notifications for this student
$notif_count = 0;
if (isset($_SESSION['student_id'])) {
    $sid = $_SESSION['student_id'];
    $nq  = mysqli_query($conn, "SELECT COUNT(*) as t FROM notifications WHERE student_id = $sid AND is_read = 0");
    $notif_count = mysqli_fetch_assoc($nq)['t'];
}
?>

<nav class="navbar navbar-expand-lg navbar-uc">
    <div class="container-fluid px-4">
        <span class="navbar-brand-text">College of Computer Studies Sit-in Monitoring System</span>
        <div class="navbar-links">
            <a href="/VelosoProject/user/homepage.php"
               class="nav-link-uc <?= basename($_SERVER['PHP_SELF']) == 'homepage.php' ? 'active' : '' ?>">
                Home
            </a>
            <a href="/VelosoProject/user/history.php"
               class="nav-link-uc <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">
                History
            </a>
            <a href="/VelosoProject/user/profile.php"
               class="nav-link-uc <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
                Edit Profile
            </a>

            <!-- Notification -->
            <a href="/VelosoProject/user/notifications.php"
               class="nav-link-uc position-relative <?= basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : '' ?>">
                Notifications
                <?php if ($notif_count > 0): ?>
                    <span class="notif-badge"><?= $notif_count ?></span>
                <?php endif; ?>
            </a>

            <a href="/VelosoProject/logout.php" class="nav-link-uc nav-link-logout">Logout</a>
        </div>
    </div>
</nav>