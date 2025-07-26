<?php
session_start();
require_once 'db/conn.php';
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout");
    exit();
}

$userId = (int)$_SESSION['user_id'];

// Process checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // 1. Get cart items and calculate total
        $cartQuery = "SELECT c.*, p.name, p.price, p.stock 
                     FROM cart c 
                     JOIN products p ON c.product_id = p.id 
                     WHERE c.user_id = $userId";
        $cartResult = mysqli_query($conn, $cartQuery);

        if (!$cartResult || mysqli_num_rows($cartResult) === 0) {
            throw new Exception("Your cart is empty");
        }

        $subtotal = 0;
        $orderItems = [];
        $productsToUpdate = [];

        while ($item = mysqli_fetch_assoc($cartResult)) {
            // Check stock availability
            if ($item['stock'] < $item['quantity']) {
                throw new Exception("Insufficient stock for {$item['name']}");
            }

            $itemTotal = $item['price'] * $item['quantity'];
            $subtotal += $itemTotal;

            $orderItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];

            $productsToUpdate[$item['product_id']] = $item['quantity'];
        }

        // 2. Create order record
        $fullName = mysqli_real_escape_string($conn, $_POST['full_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $paymentMethod = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? 'Credit Card');

        $orderQuery = "INSERT INTO orders 
                      (user_id, total_price, payment_method, shipping_address)
                      VALUES 
                      ($userId, $subtotal, '$paymentMethod', '$address')";

        if (!mysqli_query($conn, $orderQuery)) {
            throw new Exception("Error creating order: " . mysqli_error($conn));
        }

        $orderId = mysqli_insert_id($conn);

        // 3. Create order items and update product stock
        foreach ($orderItems as $item) {
            // Add to order_items (you'll need to create this table)
            $orderItemQuery = "INSERT INTO order_items 
                              (order_id, product_id, quantity, price)
                              VALUES
                              ($orderId, {$item['product_id']}, {$item['quantity']}, {$item['price']})";

            if (!mysqli_query($conn, $orderItemQuery)) {
                throw new Exception("Error saving order items: " . mysqli_error($conn));
            }

            // Update product stock
            $updateStockQuery = "UPDATE products 
                               SET stock = stock - {$item['quantity']} 
                               WHERE id = {$item['product_id']}";

            if (!mysqli_query($conn, $updateStockQuery)) {
                throw new Exception("Error updating inventory: " . mysqli_error($conn));
            }
        }

        // 4. Clear cart
        $clearCartQuery = "DELETE FROM cart WHERE user_id = $userId";
        if (!mysqli_query($conn, $clearCartQuery)) {
            throw new Exception("Error clearing cart: " . mysqli_error($conn));
        }

        // Commit transaction
        mysqli_commit($conn);

        $_SESSION['order_id'] = $orderId;
        header("Location: order_confirmation.php");
        exit();

    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        $_SESSION['error'] = $e->getMessage();
        header("Location: checkout.php");
        exit();
    }
}

// Get cart items for display
$query = "SELECT c.*, p.name, p.price, p.image_url 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = $userId";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: cart.php");
    exit();
}
?>

    <div class="container py-5">
        <h1 class="mb-4">Checkout</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Order Summary -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h3>Order Summary</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                    $subtotal = 0;
                    while ($item = mysqli_fetch_assoc($result)){
                        $itemTotal = $item['price'] * $item['quantity'];
                        $subtotal += $itemTotal;
                        ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <div>
                                <img src="assets/images/<?= htmlspecialchars($item['image_url']) ?>"
                                     width="40" class="me-3">
                                <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
                            </div>
                            <span>$<?= number_format($itemTotal, 2) ?></span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="card-footer text-end">
                <h4>Total: $<?= number_format($subtotal, 2) ?></h4>
            </div>
        </div>

        <!-- Checkout Form -->
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name*</label>
                    <input type="text" class="form-control" name="full_name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email*</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Shipping Address*</label>
                    <textarea class="form-control" name="address" rows="3" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Payment Method*</label>
                    <select class="form-select" name="payment_method" required>
                        <option value="Credit Card">Credit Card</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        Place Order
                    </button>
                </div>
            </div>
        </form>
    </div>

<?php
include 'includes/footer.php';
?>