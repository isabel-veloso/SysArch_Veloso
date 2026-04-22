<?php
require_once '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

$page_title    = 'CCS - Edit Profile';
$student_id_pk = $_SESSION['student_id'];
$message       = "";

// Fetch student data
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $student_id_pk);
mysqli_stmt_execute($stmt);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);

    if (empty($first_name) || empty($last_name) || empty($email)) {
        $message = "<div class='alert alert-danger'>Please fill in all required fields.</div>";
    } else {

        // Handle profile picture upload
        $profile_picture = $student['profile_picture']; // keep existing by default

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext     = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $message = "<div class='alert alert-danger'>Only JPG, PNG, GIF or WEBP images allowed.</div>";
            } elseif ($_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
                $message = "<div class='alert alert-danger'>Image must be under 2MB.</div>";
            } else {
                $upload_dir = '../uploads/profiles/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                $filename = 'student_' . $student_id_pk . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $filename)) {
                    $profile_picture = 'uploads/profiles/' . $filename;
                }
            }
        }

        if (empty($message)) {
            $stmt = mysqli_prepare($conn, "UPDATE students SET first_name=?, last_name=?, email=?, profile_picture=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssssi", $first_name, $last_name, $email, $profile_picture, $student_id_pk);

            if (mysqli_stmt_execute($stmt)) {
                $message = "<div class='alert alert-success'>Profile updated successfully.</div>";
                $_SESSION['name'] = $first_name . ' ' . $last_name;
                // Refresh student data
                $stmt2 = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
                mysqli_stmt_bind_param($stmt2, "i", $student_id_pk);
                mysqli_stmt_execute($stmt2);
                $student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
            } else {
                $message = "<div class='alert alert-danger'>Failed to update profile.</div>";
            }
        }
    }
}

require_once '../includes/header.php';
?>

<?php require_once '../includes/navbar_user.php'; ?>

<div class="page-wrapper">
    <div class="container-fluid px-4">

        <?= $message ?>

        <h2 class="page-title">Edit Profile</h2>

        <div class="row g-4">

            <!-- Left: Profile picture -->
            <div class="col-md-3">
                <div class="card-uc text-center">
                    <div class="card-uc-header">Profile Picture</div>
                    <div class="card-uc-body">
                        <div class="student-avatar-wrapper">
                            <div class="student-avatar">
                                <?php if (!empty($student['profile_picture'])): ?>
                                    <img src="/VelosoProject/<?= htmlspecialchars($student['profile_picture']) ?>"
                                         alt="Profile Picture" id="pic-preview"
                                         style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <div class="student-avatar-placeholder" id="pic-placeholder">
                                        <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                    </div>
                                    <img src="" alt="" id="pic-preview"
                                         style="width:100%; height:100%; object-fit:cover; display:none;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="small text-muted mt-2">JPG, PNG, GIF or WEBP · Max 2MB</p>
                    </div>
                </div>
            </div>

            <!-- Right: Edit form -->
            <div class="col-md-9">
                <div class="card-uc">
                    <div class="card-uc-header">Personal Information</div>
                    <div class="card-uc-body">
                        <form method="POST" action="" enctype="multipart/form-data">

                            <!-- Profile picture file input -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold">PROFILE PICTURE</label>
                                <input type="file" name="profile_picture" class="form-control"
                                       accept="image/*" onchange="previewImage(this)">
                            </div>

                            <hr>

                            <!-- ID Number (read only) -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold">ID NUMBER</label>
                                <input type="text" class="form-control readonly-input"
                                       value="<?= htmlspecialchars($student['id_number']) ?>" readonly>
                            </div>

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

                            <div class="mb-3">
                                <label class="form-label small fw-bold">EMAIL</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= htmlspecialchars($student['email']) ?>" required>
                            </div>

                            <!-- Course and Year (read only) -->
                            <div class="row g-2 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">COURSE</label>
                                    <input type="text" class="form-control readonly-input"
                                           value="<?= htmlspecialchars($student['course']) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">YEAR LEVEL</label>
                                    <input type="text" class="form-control readonly-input"
                                           value="<?= htmlspecialchars($student['year_level']) ?>" readonly>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn-primary-uc">Save Changes</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var preview     = document.getElementById('pic-preview');
            var placeholder = document.getElementById('pic-placeholder');
            preview.src             = e.target.result;
            preview.style.display   = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>