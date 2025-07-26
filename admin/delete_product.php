<?php
session_start();
include 'includes/admin_header.php';
require_once '../db/conn.php';

$productId = (int)$_GET['id'];

// Check if product exists in any orders
$orderCheck = mysqli_query($conn, "SELECT COUNT(*) as order_count FROM order_items WHERE product_id = $productId");
$orderData = mysqli_fetch_assoc($orderCheck);

if ($orderData['order_count'] > 0) {
    $_SESSION['error'] = "Cannot delete product - it exists in ".$orderData['order_count']." order(s).";
    header("Location: products.php");
    exit();
}


// Get product info to delete image
$result = mysqli_query($conn, "SELECT image_url FROM products WHERE id = $productId");
if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);

    // Delete the image file
    if (!empty($product['image_url'])) {
        $imagePath = "../assets/images/products/" . $product['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}

// Delete from database
$query = "DELETE FROM products WHERE id = $productId";
if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Product deleted successfully!";
} else {
    $_SESSION['error'] = "Error deleting product: " . mysqli_error($conn);
}

header("Location: products.php");
exit();
?>