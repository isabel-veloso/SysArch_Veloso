<?php
require_once 'includes/db.php';

$page_title = 'CCS - Register';
$error      = "";
$success    = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number       = trim($_POST['id_number']);
    $first_name      = trim($_POST['first_name']);
    $last_name       = trim($_POST['last_name']);
    $course          = trim($_POST['course'] ?? '');
    $year_level      = trim($_POST['year_level'] ?? '');
    $email           = trim($_POST['email']);
    $password        = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    // Basic validation
    if (empty($id_number) || empty($first_name) || empty($last_name) || empty($course) || empty($year_level) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $repeat_password) {
        $error = "Passwords do not match.";
    } elseif (!ctype_digit($id_number)) {
        $error = "ID number must contain numbers only.";
    } else {
        // Check if ID number already exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM students WHERE id_number = ?");
        mysqli_stmt_bind_param($stmt, "s", $id_number);
        mysqli_stmt_execute($stmt);
        $exists = mysqli_num_rows(mysqli_stmt_get_result($stmt));

        if ($exists > 0) {
            $error = "ID number is already registered.";
        } else {
            // Hash password and insert student
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt   = mysqli_prepare($conn, "INSERT INTO students (id_number, first_name, last_name, course, year_level, email, password, sessions_left) VALUES (?, ?, ?, ?, ?, ?, ?, 30)");
            mysqli_stmt_bind_param($stmt, "ssssiss", $id_number, $first_name, $last_name, $course, $year_level, $email, $hashed);


            if (mysqli_stmt_execute($stmt)) {
                // If admin added the student, go back to students page
                if (isset($_GET['from']) && $_GET['from'] == 'admin') {
                    header("Location: admin/students.php?success=registered");
                    exit();
                }
                $success = "Account created! You can now <a href='login.php' class='link-purple fw-bold'>login</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<?php require_once 'includes/navbar_public.php'; ?>

<div class="login-container">

    <!-- Left: Branding -->
    <div class="branding-side">
        <div class="logo-row">
            <img src="images/universityofcebulogo.png" alt="UC Logo" class="uni-logo">
            <div class="logo-divider"></div>
            <img src="images/ccslogo.png" alt="CCS Logo" class="dept-logo">
        </div>
        <h4 class="mt-4 mb-1">College of Computer Studies</h4>
        <p class="tagline mb-0">Inceptum &bull; Innovatio &bull; Muneris</p>
    </div>

    <!-- Right: Register Form -->
    <div class="form-side">
        <h2>Register</h2>
        <p class="text-muted mb-3">Create your student account.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form action="register.php<?= isset($_GET['from']) ? '?from=' . $_GET['from'] : '' ?>" method="POST">

            <div class="mb-3">
                <label class="form-label small fw-bold">ID NUMBER</label>
                <input type="text" name="id_number" class="form-control"
                        placeholder="e.g. 2312345678"
                        inputmode="numeric" pattern="[0-9]{1,10}"
                        maxlength="10"
                        value="<?= htmlspecialchars($_POST['id_number'] ?? '') ?>" required>
            </div>

            <div class="row g-2 mb-3">
                <div class="col">
                    <label class="form-label small fw-bold">FIRST NAME</label>
                    <input type="text" name="first_name" class="form-control"
                           placeholder="Juan"
                           value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                </div>
                <div class="col">
                    <label class="form-label small fw-bold">LAST NAME</label>
                    <input type="text" name="last_name" class="form-control"
                           placeholder="Dela Cruz"
                           value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col">
                    <label class="form-label small fw-bold">COURSE</label>
                    <select name="course" class="form-control" required>
                        <option value="" disabled selected>Select course</option>
                        <option value="BSIT" <?= ($_POST['course'] ?? '') == 'BSIT' ? 'selected' : '' ?>>BS Information Technology</option>
                        <option value="BSCS" <?= ($_POST['course'] ?? '') == 'BSCS' ? 'selected' : '' ?>>BS Computer Science</option>
                        <option value="BSCE" <?= ($_POST['course'] ?? '') == 'BSCE' ? 'selected' : '' ?>>BS Computer Engineering</option>
                        <option value="BSHM" <?= ($_POST['course'] ?? '') == 'BSHM' ? 'selected' : '' ?>>BS Hospitality Management</option>
                        <option value="BSA"  <?= ($_POST['course'] ?? '') == 'BSA'  ? 'selected' : '' ?>>BS Accountancy</option>
                    </select>
                </div>
                <div class="col">
                    <label class="form-label small fw-bold">YEAR LEVEL</label>
                    <select name="year_level" class="form-control" required>
                        <option value="" disabled selected>Select year</option>
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <option value="<?= $i ?>" <?= ($_POST['year_level'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">EMAIL</label>
                <input type="email" name="email" class="form-control"
                       placeholder="student@uc.edu.ph"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">PASSWORD</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Create a password" required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">REPEAT PASSWORD</label>
                <input type="password" name="repeat_password" class="form-control"
                       placeholder="Repeat your password" required>
            </div>

            <button type="submit" class="btn-primary-uc w-100 mb-3">Register Account</button>
        </form>

        <div class="text-center small text-muted">
            Already have an account? <a href="login.php" class="fw-bold link-purple">Sign In</a>
        </div>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>