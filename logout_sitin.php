<?php
require_once 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

if (isset($_POST['record_id'])) {
    $record_id = intval($_POST['record_id']);

    // Get the record to find student_id
    $stmt = mysqli_prepare($conn, "SELECT * FROM sit_in_records WHERE id = ? AND time_out IS NULL");
    mysqli_stmt_bind_param($stmt, "i", $record_id);
    mysqli_stmt_execute($stmt);
    $record = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($record) {
        // Set logout time
        $upd = mysqli_prepare($conn, "UPDATE sit_in_records SET time_out = NOW() WHERE id = ?");
        mysqli_stmt_bind_param($upd, "i", $record_id);
        mysqli_stmt_execute($upd);

        // Insert logout notification for student
        $student_id = $record['student_id'];
        $msg = "You have been logged out from Lab " . $record['lab'] . " at " . date('M d, Y h:i A') . ".";
        $nstmt = mysqli_prepare($conn, "INSERT INTO notifications (student_id, type, message) VALUES (?, 'logout', ?)");
        mysqli_stmt_bind_param($nstmt, "is", $student_id, $msg);
        mysqli_stmt_execute($nstmt);
    }
}

header("Location: admin/current_sitin.php?success=logout");
exit();
?>