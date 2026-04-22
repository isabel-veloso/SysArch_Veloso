<?php
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

$page_title = 'CCS - Admin Dashboard';

require_once '../includes/header.php';
?>

<?php require_once '../includes/navbar_admin.php'; ?>

<div class="page-wrapper">
    <div class="container-fluid px-4">
        <?php if (isset($_GET['success']) && $_GET['success'] == 'login'): ?>
            <div class="alert alert-success auto-dismiss">Login successful. Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?>!</div>
            <script>
                history.replaceState(null, '', '/VelosoProject/admin/admindashboard.php');
            </script>
        <?php endif; ?>
        <?php if (isset($_GET['success']) && $_GET['success'] == 'sitin'): ?>
            <div class="alert alert-success">Sit-in recorded successfully. Session deducted.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'no_sessions'): ?>
            <div class="alert alert-danger">This student has no remaining sessions.</div>
        <?php endif; ?>

        <h2 class="page-title">Dashboard</h2>

        <div class="row g-4">

            <!-- Statistics Card -->
            <div class="col-md-6">
                <div class="card-uc">
                    <div class="card-uc-header">📊 Statistics</div>
                    <div class="card-uc-body">
                        <?php
                        $total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM students"))['t'];
                        $active_sitins  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM sit_in_records WHERE time_out IS NULL"))['t'];
                        $today_sitins   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM sit_in_records WHERE DATE(time_in) = CURDATE()"))['t'];
                        ?>
                        <div class="row text-center g-3 mt-1">
                            <div class="col-4">
                                <div class="stat-number"><?= $total_students ?></div>
                                <div class="stat-label">Total Students</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-number" style="color:#198754;"><?= $active_sitins ?></div>
                                <div class="stat-label">Currently In</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-number" style="color:var(--ccs-purple);"><?= $today_sitins ?></div>
                                <div class="stat-label">Today's Sit-ins</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcements Card -->
            <div class="col-md-6">
                <div class="card-uc">
                    <div class="card-uc-header">📢 Announcements</div>
                    <div class="card-uc-body">

                        <!-- Post form -->
                        <form method="POST" action="/VelosoProject/admin/post_announcement.php" class="mb-3">
                            <div class="mb-2">
                                <input type="text" name="title" class="form-control form-control-sm"
                                       placeholder="Title" required>
                            </div>
                            <div class="mb-2">
                                <textarea name="content" class="form-control form-control-sm"
                                          rows="2" placeholder="Write announcement..." required></textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn-primary-uc" style="padding:6px 20px; font-size:0.85rem;">Post</button>
                            </div>
                        </form>

                        <!-- Announcements list -->
                        <div class="announcements-scroll-area">
                            <?php
                            $anns = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC");
                            if (mysqli_num_rows($anns) > 0):
                                while ($ann = mysqli_fetch_assoc($anns)):
                            ?>
                                <div class="announcement-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong class="small"><?= htmlspecialchars($ann['title']) ?></strong>
                                        <form method="POST" action="/VelosoProject/admin/post_announcement.php"
                                              onsubmit="return confirm('Delete this announcement?')" style="margin:0;">
                                            <input type="hidden" name="delete_id" value="<?= $ann['id'] ?>">
                                            <button type="submit" class="btn-delete-ann">✕</button>
                                        </form>
                                    </div>
                                    <p class="small text-muted mb-1"><?= nl2br(htmlspecialchars($ann['content'])) ?></p>
                                    <span class="announcement-date"><?= date('M d, Y h:i A', strtotime($ann['created_at'])) ?></span>
                                </div>
                            <?php endwhile; else: ?>
                                <p class="text-muted text-center mt-2">No announcements yet.</p>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>