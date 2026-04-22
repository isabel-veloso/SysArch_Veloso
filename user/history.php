<?php
require_once '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

$page_title = 'CCS - My Sit-in History';
$student_id = $_SESSION['student_id'];

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $record_id = intval($_POST['record_id']);
    $feedback  = trim($_POST['feedback']);

    if (!empty($feedback)) {
        $stmt = mysqli_prepare($conn,
            "UPDATE sit_in_records SET feedback = ? WHERE id = ? AND student_id = ? AND time_out IS NOT NULL AND feedback IS NULL"
        );
        mysqli_stmt_bind_param($stmt, 'sii', $feedback, $record_id, $student_id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: history.php?success=feedback");
    exit();
}

// Fetch all sit-in records for this student
$stmt = mysqli_prepare($conn,
    "SELECT r.id, r.purpose, r.lab, r.time_in, r.time_out, r.feedback
     FROM sit_in_records r
     WHERE r.student_id = ?
     ORDER BY r.time_in DESC"
);
mysqli_stmt_bind_param($stmt, 'i', $student_id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$records = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<?php require_once '../includes/navbar_user.php'; ?>

<div class="page-wrapper">
    <div class="container-fluid px-4">

        <?php if (isset($_GET['success']) && $_GET['success'] == 'feedback'): ?>
            <div class="alert alert-success">Feedback submitted successfully.</div>
        <?php endif; ?>

        <h2 class="page-title">My Sit-in History</h2>

        <div class="card-uc">
            <div class="card-uc-header">Sit-in Records</div>
            <div class="card-uc-body p-0">

                <?php if (empty($records)): ?>
                    <div class="text-center text-muted py-5">No sit-in records found.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table-uc mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purpose</th>
                                    <th>Lab</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Duration</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $i => $row): ?>
                                    <?php
                                        $duration = '—';
                                        if ($row['time_out']) {
                                            $in   = new DateTime($row['time_in']);
                                            $out  = new DateTime($row['time_out']);
                                            $diff = $in->diff($out);
                                            $duration = $diff->h > 0
                                                ? $diff->h . 'h ' . $diff->i . 'm'
                                                : $diff->i . 'm';
                                        }
                                        $has_feedback  = !empty($row['feedback']);
                                        $session_ended = !empty($row['time_out']);
                                    ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                                        <td><?= htmlspecialchars($row['lab']) ?></td>
                                        <td><?= date('M d, Y h:i A', strtotime($row['time_in'])) ?></td>
                                        <td>
                                            <?= $session_ended
                                                ? date('M d, Y h:i A', strtotime($row['time_out']))
                                                : '<span class="badge bg-success">Active</span>' ?>
                                        </td>
                                        <td><?= $duration ?></td>
                                        <td>
                                            <?php if ($has_feedback): ?>
                                                <button class="btn-outline-uc btn-sm"
                                                        onclick="openViewModal(<?= htmlspecialchars(json_encode($row['feedback'])) ?>)">
                                                    View Feedback
                                                </button>
                                            <?php elseif ($session_ended): ?>
                                                <button class="btn-primary-uc btn-sm"
                                                        onclick="openFeedbackModal(<?= $row['id'] ?>)">
                                                    Add Feedback
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small">Session ongoing</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>


<!-- ══ ADD FEEDBACK MODAL ══ -->
<div class="modal-overlay" id="feedbackOverlay">
    <div class="modal-box">
        <div class="modal-box-header">
            <span>Leave Feedback</span>
            <button class="modal-close" onclick="closeAll()">&#x2715;</button>
        </div>
        <div class="modal-box-body">
            <p class="text-muted small mb-3">Share your experience about this sit-in session.</p>
            <form method="POST" action="history.php">
                <input type="hidden" name="record_id" id="feedbackRecordId">
                <input type="hidden" name="submit_feedback" value="1">
                <div class="modal-field">
                    <label>Your Feedback</label>
                    <textarea name="feedback" id="feedbackText"
                              placeholder="Write your feedback here..." required></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-outline-uc" onclick="closeAll()">Cancel</button>
                    <button type="submit" class="btn-primary-uc">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ══ VIEW FEEDBACK MODAL ══ -->
<div class="modal-overlay" id="viewOverlay">
    <div class="modal-box">
        <div class="modal-box-header">
            <span>Feedback</span>
            <button class="modal-close" onclick="closeAll()">&#x2715;</button>
        </div>
        <div class="modal-box-body">
            <div class="modal-field">
                <label>Your Feedback</label>
                <textarea id="viewFeedbackText" class="readonly-input" readonly></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-primary-uc" onclick="closeAll()">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
function openFeedbackModal(recordId) {
    document.getElementById('feedbackRecordId').value = recordId;
    document.getElementById('feedbackText').value = '';
    document.getElementById('feedbackOverlay').classList.add('active');
}

function openViewModal(feedbackText) {
    document.getElementById('viewFeedbackText').value = feedbackText;
    document.getElementById('viewOverlay').classList.add('active');
}

function closeAll() {
    document.getElementById('feedbackOverlay').classList.remove('active');
    document.getElementById('viewOverlay').classList.remove('active');
}

// Close on overlay background click
document.getElementById('feedbackOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeAll();
});
document.getElementById('viewOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeAll();
});

// Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAll();
});
</script>

<?php require_once '../includes/footer.php'; ?>