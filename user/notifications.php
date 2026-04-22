<?php
require_once '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

$page_title = 'CCS - Notifications';
$student_id = $_SESSION['student_id'];

// Mark all this student's notifications as read
mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE student_id = $student_id");

// Fetch all notifications for this student
$result = mysqli_query($conn,
    "SELECT * FROM notifications
     WHERE student_id = $student_id
     ORDER BY created_at DESC
     LIMIT 100"
);

require_once '../includes/header.php';
?>

<?php require_once '../includes/navbar_user.php'; ?>

<div class="page-wrapper">
    <div class="container-fluid px-4">

        <h2 class="page-title mb-4">Notifications</h2>

        <div class="card-uc">
            <div class="card-uc-header">Your Activity</div>
            <div class="card-uc-body" style="padding: 0;">

                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($notif = mysqli_fetch_assoc($result)): ?>
                        <div class="notif-item <?= $notif['is_read'] ? '' : 'notif-unread' ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="notif-icon me-2">
                                        <?= $notif['type'] === 'sitin' ? '🖥️' : ($notif['type'] === 'announcement' ? '📢' : '🚪') ?>
                                    </span>
                                    <strong class="small">
                                        <?= htmlspecialchars($notif['message']) ?>
                                    </strong>
                                </div>
                                <span class="announcement-date text-nowrap ms-3">
                                    <?= date('M d, Y h:i A', strtotime($notif['created_at'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No notifications yet.</p>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>