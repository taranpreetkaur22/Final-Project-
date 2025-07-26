<?php
session_start();
require_once 'db/conn.php';

// Handle add to cart action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?redirect=cart");
        exit();
    }

    $productId = (int)$_POST['product_id'];
    $userId = (int)$_SESSION['user_id'];

    // Check if product exists
    $query = "SELECT id FROM products WHERE id = $productId";
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) === 0) {
        $_SESSION['error'] = "Product not found!";
        header("Location: index.php");
        exit();
    }
    // Check if product already in cart
    $query = "SELECT id, quantity FROM cart WHERE user_id = $userId AND product_id = $productId";
    $result = mysqli_query($conn, $query);
    $existingItem = mysqli_fetch_assoc($result);

    if ($existingItem) {
        // Update quantity
        $newQuantity = $existingItem['quantity'] + 1;
        $query = "UPDATE cart SET quantity = $newQuantity WHERE id = {$existingItem['id']}";
    } else {
        // Add new item
        $query = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($userId, $productId, 1)";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Product added to cart!";
    } else {
        $_SESSION['error'] = "Error adding to cart: " . mysqli_error($conn);
    }

    header("Location: cart.php");
    exit();
}

include 'includes/header.php';
?>

    <div class="container py-5">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <h1 class="mb-4">Your Shopping Cart</h1>

        <?php
        if (isset($_SESSION['user_id'])) {
            $userId = (int)$_SESSION['user_id'];
            $query = "SELECT c.*, p.name, p.price, p.image_url 
                 FROM cart c 
                 JOIN products p ON c.product_id = p.id 
                 WHERE c.user_id = $userId";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0){ ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $subtotal = 0;
                        while ($item = mysqli_fetch_assoc($result)){
                            $itemTotal = $item['price'] * $item['quantity'];
                            $subtotal += $itemTotal;
                            ?>
                            <tr>
                                <td>
                                    <img src="assets/images/<?= htmlspecialchars($item['image_url']) ?>" width="50" class="me-2">
                                    <?= htmlspecialchars($item['name']) ?>
                                </td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($itemTotal, 2) ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-4">
                    <h4>Subtotal: $<?= number_format($subtotal, 2) ?></h4>
                    <a href="checkout.php" class="btn btn-primary btn-lg mt-2">Proceed to Checkout</a>
                </div>
            <?php }else{ ?>
                <div class="alert alert-info">Your cart is empty.</div>
                <a href="products.php" class="btn btn-primary">Continue Shopping</a>
            <?php }
        } else {
            echo '<div class="alert alert-warning">Please <a href="login.php">login</a> to view your cart.</div>';
        }
        ?>
    </div>

<?php include 'includes/footer.php'; ?>