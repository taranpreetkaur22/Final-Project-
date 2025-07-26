<?php
session_start();
require_once '../db/conn.php';
include 'includes/admin_header.php';

// Verify admin access
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit();
}

// Get all orders with user and item information
$query = "SELECT 
            o.*, 
            u.name, 
            u.email,
            GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ' × $', oi.price, ')') SEPARATOR '<br>') as items,
            COUNT(oi.id) as item_count
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN order_items oi ON o.id = oi.order_id
          JOIN products p ON oi.product_id = p.id
          GROUP BY o.id
          ORDER BY o.created_at DESC";
$orders = mysqli_query($conn, $query);
?>

    <div class="container-fluid">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-receipt"></i> Order Management</h1>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Shipping Address</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($order['name']) ?></strong><br>
                                    <?= htmlspecialchars($order['email']) ?>
                                </td>
                                <td>
                                    <small><?= $order['items'] ?></small><br>
                                    <span class="badge bg-secondary"><?= $order['item_count'] ?> items</span>
                                </td>
                                <td>$<?= number_format($order['total_price'], 2) ?></td>
                                <td>
                                <span class="badge bg-<?=
                                $order['status'] == 'pending' ? 'warning' :
                                    ($order['status'] == 'processing' ? 'info' :
                                        ($order['status'] == 'shipped' ? 'primary' :
                                            ($order['status'] == 'delivered' ? 'success' : 'danger')))
                                ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                                </td>
                                <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                <td><small><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></small></td>
                                <td>
                                    <?= date('M j', strtotime($order['created_at'])) ?><br>
                                    <small><?= date('g:i A', strtotime($order['created_at'])) ?></small>
                                </td>
                                <td>
                                    <?= $order['status'] ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/admin_footer.php'; ?>