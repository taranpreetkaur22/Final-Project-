<?php
// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verify admin role exists
if ($_SESSION['is_admin'] !== 1) {
    header("Location: ../index.php");
    exit();
}

?>