<?php
session_start();
require_once 'db/conn.php';

// Unset all session variables
$_SESSION = array();

// Delete remember token from database if exists
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $query = "UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE remember_token = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);

    // Delete the cookie
    setcookie('remember_token', '', time() - 3600, '/');
}

// Destroy the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirect to home page
header("Location: index.php");
exit();
?>