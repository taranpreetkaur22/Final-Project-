<?php
session_start();
include 'includes/admin_header.php';
require_once '../db/conn.php';
?>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Products</h1>
            <a href="add_product.php" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add Product
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                while ($product = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td>
                            <img src="../assets/images/<?= htmlspecialchars($product['image_url']) ?>"
                                 width="50" height="50" class="img-thumbnail">
                        </td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                        <td><?= $product['stock'] ?></td>
                        <td>
                            <a href="edit_product.php?id=<?= $product['id'] ?>"
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="delete_product.php?id=<?= $product['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include 'includes/admin_footer.php'; ?>