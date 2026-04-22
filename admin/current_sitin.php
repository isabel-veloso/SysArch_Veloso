<?php
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

$page_title = 'CCS - Current Sit-in';

require_once '../includes/header.php';
?>

<?php require_once '../includes/navbar_admin.php'; ?>

<div class="page-wrapper">
    <div class="container-fluid px-4">

        <?php if (isset($_GET['success']) && $_GET['success'] == 'logout'): ?>
            <div class="alert alert-success">Student logged out successfully.</div>
        <?php endif; ?>
        <?php if (isset($_GET['success']) && $_GET['success'] == 'sitin'): ?>
            <div class="alert alert-success">Sit-in recorded successfully.</div>
        <?php endif; ?>

        <h2 class="page-title">Current Sit-in</h2>

        <div class="card-uc">
            <div class="card-uc-header">Students Currently in the Lab</div>
            <div class="card-uc-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table table-uc mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Number</th>
                                <th>Name</th>
                                <th>Course & Year</th>
                                <th>Purpose</th>
                                <th>Lab</th>
                                <th>Time In</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT r.id, s.id_number, s.first_name, s.last_name,
                                             s.course, s.year_level, r.purpose, r.lab, r.time_in
                                      FROM sit_in_records r
                                      JOIN students s ON r.student_id = s.id
                                      WHERE r.time_out IS NULL
                                      ORDER BY r.time_in DESC";
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0):
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($result)):
                            ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['id_number']) ?></td>
                                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                    <td><?= htmlspecialchars($row['course'] . ' ' . $row['year_level']) ?></td>
                                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                                    <td><?= htmlspecialchars($row['lab']) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($row['time_in'])) ?></td>
                                    <td>
                                        <form method="POST" action="/VelosoProject/logout_sitin.php"
                                              onsubmit="return confirm('Log out this student?')">
                                            <input type="hidden" name="record_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn-danger-uc">Logout</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No students currently sitting in.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>