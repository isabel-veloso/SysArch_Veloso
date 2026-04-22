<?php
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Delete an announcement
    if (isset($_POST['delete_id'])) {
        $id = intval($_POST['delete_id']);
        $stmt = mysqli_prepare($conn, "DELETE FROM announcements WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

    // Post a new announcement
    } elseif (!empty($_POST['title']) && !empty($_POST['content'])) {
        $title   = trim($_POST['title']);
        $content = trim($_POST['content']);

        $stmt = mysqli_prepare($conn, "INSERT INTO announcements (title, content) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $title, $content);
        mysqli_stmt_execute($stmt);

        // Notify all students about the new announcement
        if (mysqli_affected_rows($conn) > 0) {
            $message  = "New announcement: " . $title;
            $students = mysqli_query($conn, "SELECT id FROM students");
            $nstmt    = mysqli_prepare($conn, "INSERT INTO notifications (student_id, type, message) VALUES (?, 'announcement', ?)");
            while ($s = mysqli_fetch_assoc($students)) {
                mysqli_stmt_bind_param($nstmt, "is", $s['id'], $message);
                mysqli_stmt_execute($nstmt);
            }
        }
    }
}

header("Location: admindashboard.php");
exit();
?>