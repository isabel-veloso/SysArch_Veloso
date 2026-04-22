<?php
require_once 'includes/db.php';

session_destroy();

header("Location: /VelosoProject/login.php?success=logout");
exit();
?>