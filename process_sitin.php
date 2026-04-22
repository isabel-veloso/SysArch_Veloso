<?php
require_once 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id  = intval($_POST['student_id']);
    $purpose     = trim($_POST['purpose'] ?? '');
    $lab         = trim($_POST['lab'] ?? '');
    $redirect_to = $_POST['redirect_to'] ?? '/VelosoProject/admin/admindashboard.php';

    // Whitelist allowed redirects for security
    $allowed = [
        '/VelosoProject/admin/admindashboard.php',
        '/VelosoProject/admin/students.php',
        '/VelosoProject/admin/current_sitin.php',
        '/VelosoProject/admin/sitin_records.php',
    ];
    if (!in_array($redirect_to, $allowed)) {
        $redirect_to = '/VelosoProject/admin/admindashboard.php';
    }

    if (empty($purpose) || empty($lab)) {
        header("Location: " . $redirect_to . "?error=missing_fields");
        exit();
    }

    // Get student session count
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$student) {
        header("Location: " . $redirect_to);
        exit();
    }

    if ($student['sessions_left'] <= 0) {
        header("Location: " . $redirect_to . "?error=no_sessions");
        exit();
    }

    // Deduct 1 session
    $new_sessions = $student['sessions_left'] - 1;
    $upd = mysqli_prepare($conn, "UPDATE students SET sessions_left = ? WHERE id = ?");
    mysqli_stmt_bind_param($upd, "ii", $new_sessions, $student_id);
    mysqli_stmt_execute($upd);

    // Record sit-in
    $ins = mysqli_prepare($conn, "INSERT INTO sit_in_records (student_id, purpose, lab, time_in) VALUES (?, ?, ?, NOW())");
    mysqli_stmt_bind_param($ins, "iss", $student_id, $purpose, $lab);
    mysqli_stmt_execute($ins);

    // Insert notification for student
    $msg = "You have been recorded for sit-in at Lab " . $lab . " for " . $purpose . ".";
    $nstmt = mysqli_prepare($conn, "INSERT INTO notifications (student_id, type, message) VALUES (?, 'sitin', ?)");
    mysqli_stmt_bind_param($nstmt, "is", $student_id, $msg);
    mysqli_stmt_execute($nstmt);

    header("Location: " . $redirect_to . "?success=sitin");
    exit();
}

header("Location: admin/admindashboard.php");
exit();
?>