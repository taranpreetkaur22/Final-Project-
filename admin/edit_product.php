<?php
session_start();
include 'includes/admin_header.php';
require_once '../db/conn.php';

$productId = (int)$_GET['id'];
$product = null;

// Fetch categories from database
$categoryQuery = "SELECT id, name FROM categories ORDER BY name";
$categoryResult = mysqli_query($conn, $categoryQuery);
$categories = mysqli_fetch_all($categoryResult, MYSQLI_ASSOC);

// Fetch product details
$result = mysqli_query($conn, "SELECT * FROM products WHERE id = $productId");
if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
} else {
    $_SESSION['error'] = "Product not found";
    header("Location: products.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category = mysqli_real_escape_string($conn, $_POST['category_id']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand'] ?? '');

    // Handle file upload if new image is provided
    $imageName = $product['image_url'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/images/';
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            // Delete old image if it exists
            if (!empty($product['image_url'])) {
                $oldImagePath = $uploadDir . $product['image_url'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        } else {
            $_SESSION['error'] = "Failed to upload image";
            header("Location: edit_product.php?id=$productId");
            exit();
        }
    }

    $query = "UPDATE products SET 
              name = '$name',
              description = '$description',
              price = $price,
              stock = $stock,
              category_id = '$category',
              brand = '$brand',
              image_url = '$imageName'
              WHERE id = $productId";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Product updated successfully!";
        header("Location: products.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating product: " . mysqli_error($conn);
        header("Location: edit_product.php?id=$productId");
        exit();
    }
}
?>

    <div class="container py-4">
        <h1 class="mb-4">Edit Product</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Product Name*</label>
                    <input type="text" class="form-control" name="name"
                           value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Brand</label>
                    <input type="text" class="form-control" name="brand"
                           value="<?= htmlspecialchars($product['brand'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Category*</label>
                    <select class="form-select" name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= $product['category_id'] === $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Description*</label>
                    <textarea class="form-control" name="description" rows="4" required><?=
                        htmlspecialchars($product['description'])
                        ?></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Price ($)*</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="price"
                           value="<?= number_format($product['price'], 2) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Stock Quantity*</label>
                    <input type="number" min="0" class="form-control" name="stock"
                           value="<?= $product['stock'] ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Product Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <?php if (!empty($product['image_url'])): ?>
                        <div class="mt-2">
                            <img src="../assets/images/<?= htmlspecialchars($product['image_url']) ?>"
                                 width="80" class="img-thumbnail">
                            <small class="text-muted">Current image</small>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Product
                    </button>
                    <a href="products.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

<?php include 'includes/admin_footer.php'; ?>