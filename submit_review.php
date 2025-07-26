<?php
session_start();
require_once 'db/conn.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?error=Please login to review");
    exit();
}

// 2. Get form data
$productId = (int)$_POST['product_id'];
$userId = (int)$_SESSION['user_id'];
$rating = (int)$_POST['rating'];
$comment = trim($_POST['comment']);

// 3. Simple validation
if ($rating < 1 || $rating > 5) {
    header("Location: product.php?id=$productId&error=Invalid rating");
    exit();
}

if (strlen($comment) < 5) {
    header("Location: product.php?id=$productId&error=Comment too short");
    exit();
}

// 4. Save to database
$query = "INSERT INTO product_reviews 
          (user_id, product_id, rating, comment) 
          VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iiis", $userId, $productId, $rating, $comment);

if (mysqli_stmt_execute($stmt)) {
    header("Location: product.php?id=$productId&success=Review added");
} else {
    header("Location: product.php?id=$productId&error=Database error");
}

exit();
?>