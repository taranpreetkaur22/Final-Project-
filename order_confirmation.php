<?php
session_start();
include 'includes/header.php';
?>

    <div class="container py-5 text-center">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h2><i class="bi bi-check-circle"></i> Order Confirmed!</h2>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h3>Thank you for your purchase!</h3>
                    <p class="lead">Your order has been received.</p>
                </div>

                <div class="order-details bg-light p-4 rounded mb-4">
                    <h4>Order Details</h4>
                    <p><strong>Order Number:</strong> ORD-5A3BC9D2E1F4</p>
                    <p><strong>Date:</strong> June 15, 2023</p>
                    <p><strong>Estimated Delivery:</strong> June 18, 2023</p>
                </div>

                <a href="products.php" class="btn btn-primary">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>

<?php
include 'includes/footer.php';
?>