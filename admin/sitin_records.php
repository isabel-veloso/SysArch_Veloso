<?php
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

$page_title = 'CCS - Sit-in Records';

require_once '../includes/header.php';
?>

<?php require_once '../includes/navbar_admin.php'; ?>

<div class="page-wrapper">
    <div class="container-fluid px-4">

        <h2 class="page-title">Sit-in Records</h2>

        <div class="card-uc">
            <div class="card-uc-header">Completed Sit-in Sessions</div>
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
                                <th>Time Out</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT r.id, s.id_number, s.first_name, s.last_name,
                                             s.course, s.year_level, r.purpose, r.lab,
                                             r.time_in, r.time_out
                                      FROM sit_in_records r
                                      JOIN students s ON r.student_id = s.id
                                      WHERE r.time_out IS NOT NULL
                                      ORDER BY r.time_out DESC";
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0):
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($result)):
                                    // Calculate duration
                                    $time_in   = strtotime($row['time_in']);
                                    $time_out  = strtotime($row['time_out']);
                                    $duration  = $time_out - $time_in;
                                    $hours     = floor($duration / 3600);
                                    $minutes   = floor(($duration % 3600) / 60);
                            ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['id_number']) ?></td>
                                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                    <td><?= htmlspecialchars($row['course'] . ' ' . $row['year_level']) ?></td>
                                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                                    <td><?= htmlspecialchars($row['lab']) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($row['time_in'])) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($row['time_out'])) ?></td>
                                    <td><?= $hours . 'h ' . $minutes . 'm' ?></td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No sit-in records yet.
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