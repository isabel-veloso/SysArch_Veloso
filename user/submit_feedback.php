<?php
require_once '../includes/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: /VelosoProject/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_SESSION['student_id'];
    $feedback   = trim($_POST['feedback']);

    if (!empty($feedback)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO feedback (student_id, feedback, submitted_at) VALUES (?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, "is", $student_id, $feedback);
        mysqli_stmt_execute($stmt);
    }
}

header("Location: homepage.php?success=feedback");
exit();
?>