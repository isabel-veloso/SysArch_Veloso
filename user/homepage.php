<?php
require_once '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

$page_title    = 'CCS - Home';
$student_id_pk = $_SESSION['student_id'];

// Fetch student data
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $student_id_pk);
mysqli_stmt_execute($stmt);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

require_once '../includes/header.php';
?>

<?php require_once '../includes/navbar_user.php'; ?>

<div class="page-wrapper">
    <div class="container-fluid px-4">

        <?php if (isset($_GET['success']) && $_GET['success'] == 'feedback'): ?>
            <div class="alert alert-success">Feedback submitted successfully.</div>
        <?php endif; ?>

        <div class="row g-4 align-items-stretch">

            <!-- Student Info Card -->
            <div class="col-md-4 d-flex">
                <div class="card-uc w-100 d-flex flex-column">
                    <div class="card-uc-header">Student Information</div>
                    <div class="card-uc-body flex-grow-1">

                        <!-- Profile picture -->
                        <div class="student-avatar-wrapper">
                            <div class="student-avatar">
                                <?php if (!empty($student['profile_picture'])): ?>
                                    <img src="/VelosoProject/<?= htmlspecialchars($student['profile_picture']) ?>"
                                         alt="Profile Picture"
                                         style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <div class="student-avatar-placeholder">
                                        <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Student details -->
                        <ul class="student-info-list mt-3">
                            <li><strong>ID:</strong> <?= htmlspecialchars($student['id_number']) ?></li>
                            <li><strong>Name:</strong> <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></li>
                            <li><strong>Course:</strong> <?= htmlspecialchars($student['course']) ?></li>
                            <li><strong>Year:</strong> <?= htmlspecialchars($student['year_level']) ?></li>
                            <li><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></li>
                            <li>
                                <strong>Sessions Left:</strong>
                                <span class="badge <?= $student['sessions_left'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $student['sessions_left'] ?>
                                </span>
                            </li>
                        </ul>

                    </div>
                </div>
            </div>

            <!-- Announcements Card -->
            <div class="col-md-4 d-flex">
                <div class="card-uc w-100 d-flex flex-column">
                    <div class="card-uc-header">📢 Announcements</div>
                    <div class="card-uc-body flex-grow-1">
                        <div class="announcements-scroll-area">
                            <?php
                            $announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC");
                            if (mysqli_num_rows($announcements) > 0):
                                while ($ann = mysqli_fetch_assoc($announcements)):
                            ?>
                                <div class="announcement-item">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= htmlspecialchars($ann['title']) ?></strong>
                                        <span class="announcement-date">
                                            <?= date('M d, Y', strtotime($ann['created_at'])) ?>
                                        </span>
                                    </div>
                                    <p class="small text-muted mb-0 mt-1">
                                        <?= nl2br(htmlspecialchars($ann['content'])) ?>
                                    </p>
                                </div>
                            <?php endwhile; else: ?>
                                <p class="text-muted text-center mt-2">No announcements yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rules and Regulations Card -->
            <div class="col-md-4 d-flex">
                <div class="card-uc w-100 d-flex flex-column">
                    <div class="card-uc-header">Rules and Regulations</div>
                    <div class="card-uc-body flex-grow-1">
                        <div class="rules-scroll-area">
                            <p class="text-center fw-bold mb-0" style="font-size:0.8rem; color:var(--uc-blue);">
                                COLLEGE OF INFORMATION &amp; COMPUTER STUDIES
                            </p>
                            <p class="text-center fw-bold mb-2" style="font-size:0.8rem; color:var(--uc-blue);">
                                LABORATORY RULES AND REGULATIONS
                            </p>
                            <p style="font-size:0.85rem;">
                                To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:
                            </p>
                            <ol class="rules-list">
                                <li>Maintain silence, proper decorum and discipline inside the laboratory. Mobile phones and other personal pieces of equipment must be switched off.</li>
                                <li>Games are not allowed inside the lab. This includes computer-related games, card games and other games that may disturb the operation of the lab.</li>
                                <li>Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing of software is strictly prohibited.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>