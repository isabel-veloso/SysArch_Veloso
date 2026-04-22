<?php
require_once 'includes/db.php';

$page_title = 'CCS - Login';
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number = trim($_POST['student_id']);
    $password  = $_POST['password'];

    if (empty($id_number) || empty($password)) {
        $error = "Please enter your ID and password.";
    } else {

        // Check admins table first
        $stmt = mysqli_prepare($conn, "SELECT * FROM admins WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $id_number);
        mysqli_stmt_execute($stmt);
        $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($admin && password_verify($password, $admin['password'])) {
            // Admin login successful
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            header("Location: admin/admindashboard.php?success=login");
            exit();
        } else {
            // Check students table
            $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id_number = ?");
            mysqli_stmt_bind_param($stmt, "s", $id_number);
            mysqli_stmt_execute($stmt);
            $student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            if ($student && password_verify($password, $student['password'])) {
                // Student login successful
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['id_number']  = $student['id_number'];
                $_SESSION['name']       = $student['first_name'] . ' ' . $student['last_name'];
                header("Location: user/homepage.php");
                exit();
            } else {
                $error = "Invalid ID or password.";
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

    <!-- Right: Login Form -->
    <div class="form-side">
        <h2>Login</h2>
        <p class="text-muted mb-4">Please login to your account.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'logout'): ?>
            <div class="alert alert-info">You have been logged out successfully.</div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">STUDENT ID</label>
                <input type="text" name="student_id" class="form-control"
                       placeholder="e.g. 23123456"
                       value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">PASSWORD</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control"
                           id="login-password" placeholder="••••••••">
                    <button class="btn btn-outline-secondary" type="button"
                            onclick="togglePassword('login-password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember_me" id="remember-me">
                    <label class="form-check-label small text-muted" for="remember-me">Remember me</label>
                </div>
                <a href="#" class="small link-purple fw-semibold">Forgot password?</a>
            </div>

            <button type="submit" class="btn-primary-uc w-100 mb-3">Login</button>
        </form>

        <div class="text-center small text-muted">
            New here? <a href="register.php" class="fw-bold link-purple">Create an Account</a>
        </div>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>