<?php
$page_title = 'CCS - Feedback Reports';
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

$result = mysqli_query($conn, "
    SELECT 
        s.id_number,
        s.first_name,
        s.last_name,
        s.course,
        s.year_level,
        r.purpose,
        r.lab,
        r.time_in,
        r.feedback
    FROM sit_in_records r
    JOIN students s ON r.student_id = s.id
    WHERE r.feedback IS NOT NULL AND r.feedback != ''
    ORDER BY r.time_in DESC
");

require_once '../includes/header.php';
require_once '../includes/navbar_admin.php';
?>

<div class="page-wrapper">
    <div class="container-fluid px-4">

        <h2 class="page-title mb-4">Feedback Reports</h2>

        <div class="card-uc">
            <div class="card-uc-header">Student Feedback</div>
            <div class="card-uc-body">

                <?php if (mysqli_num_rows($result) === 0): ?>
                    <p class="text-muted text-center mt-2">No feedback submitted yet.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table-uc">
                        <thead>
                            <tr>
                                <th>ID Number</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Purpose</th>
                                <th>Lab</th>
                                <th>Date</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id_number']) ?></td>
                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['course']) ?></td>
                                <td><?= htmlspecialchars($row['year_level']) ?></td>
                                <td><?= htmlspecialchars($row['purpose']) ?></td>
                                <td><?= htmlspecialchars($row['lab']) ?></td>
                                <td><?= date('M d, Y', strtotime($row['time_in'])) ?></td>
                                <td>
                                    <?php
                                    $feedback = htmlspecialchars($row['feedback']);
                                    $preview  = mb_strlen($row['feedback']) > 60
                                                ? htmlspecialchars(mb_substr($row['feedback'], 0, 60)) . '…'
                                                : $feedback;
                                    ?>
                                    <span class="text-truncate-preview"><?= $preview ?></span>
                                    <?php if (mb_strlen($row['feedback']) > 60): ?>
                                        <br>
                                        <button class="btn-view-feedback"
                                            onclick="openFeedbackModal(this)"
                                            data-name="<?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>"
                                            data-feedback="<?= htmlspecialchars($row['feedback']) ?>">
                                            View More
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<!-- Feedback Modal -->
<div class="modal-overlay" id="feedback-overlay">
    <div class="modal-box">
        <div class="modal-box-header">
            <span id="feedback-modal-title">Feedback</span>
            <button class="modal-close" onclick="closeFeedbackModal()">&#x2715;</button>
        </div>
        <div class="modal-box-body">
            <p id="feedback-modal-text" style="white-space: pre-wrap; font-size: 0.9rem; color: #333;"></p>
        </div>
    </div>
</div>

<style>
    .btn-view-feedback {
        background: none;
        border: none;
        color: var(--ccs-purple);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
        text-decoration: underline;
    }
    .btn-view-feedback:hover {
        color: var(--uc-blue);
    }
</style>

<script>
function openFeedbackModal(btn) {
    document.getElementById('feedback-modal-title').textContent = btn.dataset.name + ' — Feedback';
    document.getElementById('feedback-modal-text').textContent  = btn.dataset.feedback;
    document.getElementById('feedback-overlay').classList.add('active');
}

function closeFeedbackModal() {
    document.getElementById('feedback-overlay').classList.remove('active');
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('feedback-overlay').addEventListener('click', function (e) {
        if (e.target === this) closeFeedbackModal();
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>