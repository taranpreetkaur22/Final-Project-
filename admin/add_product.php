<?php
session_start();
include 'includes/admin_header.php';
require_once '../db/conn.php';

// Fetch categories from database
$categoryQuery = "SELECT id, name FROM categories ORDER BY name";
$categoryResult = mysqli_query($conn, $categoryQuery);
$categories = mysqli_fetch_all($categoryResult, MYSQLI_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category = mysqli_real_escape_string($conn, $_POST['category_id']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand'] ?? '');

    // Handle file upload
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/images/';

        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            // File uploaded successfully
        } else {
            $_SESSION['error'] = "Failed to upload image. Please check directory permissions.";
            header("Location: add_product.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Please select a product image";
        header("Location: add_product.php");
        exit();
    }

    $query = "INSERT INTO products (name, description, price, stock, category_id, brand, image_url) 
              VALUES ('$name', '$description', $price, $stock, '$category', '$brand', '$imageName')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Product added successfully!";
        header("Location: products.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding product: " . mysqli_error($conn);
        header("Location: add_product.php");
        exit();
    }
}
?>

    <div class="container py-4">
        <h1 class="mb-4"><i class="bi bi-plus-circle"></i> Add New Product</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Product Name*</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Brand</label>
                    <input type="text" class="form-control" name="brand">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Category*</label>
                    <select class="form-select" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Description*</label>
                    <textarea class="form-control" name="description" rows="4" required></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Price ($)*</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="price" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Stock Quantity*</label>
                    <input type="number" min="0" class="form-control" name="stock" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Product Image*</label>
                    <input type="file" class="form-control" name="image" accept="image/*" required>
                    <small class="text-muted">Allowed formats: JPG, PNG, GIF (Max 2MB)</small>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Add Product
                    </button>
                    <a href="products.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

<?php include 'includes/admin_footer.php'; ?>