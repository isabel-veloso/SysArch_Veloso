<?php
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

// Get student ID from URL
if (!isset($_GET['id'])) {
    header("Location: students.php");
    exit();
}

$page_title = 'CCS - Edit Student';
$student_id = intval($_GET['id']);
$message    = "";

// Fetch student data
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Stop if student not found
if (!$student) {
    header("Location: students.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_student'])) {
    $first_name    = trim($_POST['first_name']);
    $last_name     = trim($_POST['last_name']);
    $course        = trim($_POST['course']);
    $year_level    = intval($_POST['year_level']);
    $email         = trim($_POST['email']);
    $sessions_left = intval($_POST['sessions_left']);

    if (empty($first_name) || empty($last_name) || empty($course) || empty($email)) {
        $message = "<div class='alert alert-danger'>Please fill in all required fields.</div>";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE students SET first_name=?, last_name=?, course=?, year_level=?, email=?, sessions_left=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssisii", $first_name, $last_name, $course, $year_level, $email, $sessions_left, $student_id);

        if (mysqli_stmt_execute($stmt)) {
            $message = "<div class='alert alert-success'>Student updated successfully.</div>";
            // Refresh student data
            $stmt2 = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
            mysqli_stmt_bind_param($stmt2, "i", $student_id);
            mysqli_stmt_execute($stmt2);
            $student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
        } else {
            $message = "<div class='alert alert-danger'>Failed to update student.</div>";
        }
    }
}

require_once '../includes/header.php';
?>

<?php require_once '../includes/navbar_admin.php'; ?>

<div class="page-wrapper">
    <div class="container-fluid px-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0">Edit Student</h2>
            <a href="students.php" class="btn-outline-uc">← Back to Students</a>
        </div>

        <?= $message ?>

        <div class="card-uc">
            <div class="card-uc-header">Student Information</div>
            <div class="card-uc-body">
                <form method="POST" action="">
                    <input type="hidden" name="student_id" value="<?= $student['id'] ?>">

                    <!-- ID Number (read only) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ID NUMBER</label>
                        <input type="text" class="form-control readonly-input"
                               value="<?= htmlspecialchars($student['id_number']) ?>" readonly>
                    </div>

                    <!-- Name -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">FIRST NAME</label>
                            <input type="text" name="first_name" class="form-control"
                                   value="<?= htmlspecialchars($student['first_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">LAST NAME</label>
                            <input type="text" name="last_name" class="form-control"
                                   value="<?= htmlspecialchars($student['last_name']) ?>" required>
                        </div>
                    </div>

                    <!-- Course and Year -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">COURSE</label>
                            <select name="course" class="form-control" required>
                                <option value="BSIT" <?= $student['course'] == 'BSIT' ? 'selected' : '' ?>>BS Information Technology</option>
                                <option value="BSCS" <?= $student['course'] == 'BSCS' ? 'selected' : '' ?>>BS Computer Science</option>
                                <option value="BSCE" <?= $student['course'] == 'BSCE' ? 'selected' : '' ?>>BS Computer Engineering</option>
                                <option value="BSHM" <?= $student['course'] == 'BSHM' ? 'selected' : '' ?>>BS Hospitality Management</option>
                                <option value="BSA"  <?= $student['course'] == 'BSA'  ? 'selected' : '' ?>>BS Accountancy</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">YEAR LEVEL</label>
                            <select name="year_level" class="form-control" required>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <option value="<?= $i ?>" <?= $student['year_level'] == $i ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">EMAIL</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($student['email']) ?>" required>
                    </div>

                    <!-- Sessions left -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold">SESSIONS LEFT</label>
                        <input type="number" name="sessions_left" class="form-control"
                               value="<?= $student['sessions_left'] ?>" min="0" max="30" required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" name="update_student" class="btn-primary-uc">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>