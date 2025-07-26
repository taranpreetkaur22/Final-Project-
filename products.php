<?php
session_start();
require_once 'db/conn.php';


// First, fetch all distinct categories from products
$categoryQuery = "SELECT * from categories";
$categoryResult = mysqli_query($conn, $categoryQuery);
$categories = mysqli_fetch_all($categoryResult, MYSQLI_ASSOC);

// Basic filtering
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';

// Build simple query
$query = "SELECT *, (created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS is_new FROM products WHERE stock > 0";

// Apply category filter if selected
// Apply category filter if selected
if ($category > 0) {
    $query .= " AND category_id = " . (int)$category;
}

// Apply simple sorting
switch ($sort) {
    case 'price_low':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY price DESC";
        break;
    default: // 'name'
        $query .= " ORDER BY name";
}

// Execute query
$result = mysqli_query($conn, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

include 'includes/header.php';
?>

    <div class="container py-4">
        <h1 class="mb-4">Our Products</h1>

        <!-- Simple Category Filter -->
        <div class="mb-4">
            <div class="btn-group" role="group">
                <a href="products.php" class="btn btn-outline-primary <?= empty($category) ? 'active' : '' ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="products.php?category=<?= $cat['id'] ?>"
                       class="btn btn-outline-primary <?= $category == $cat['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Simple Sort Dropdown -->
            <div class="mt-3">
                <label class="me-2">Sort by:</label>
                <select class="form-select d-inline-block w-auto" onchange="window.location=this.value">
                    <option value="products.php?sort=name" <?= $sort === 'name' ? 'selected' : '' ?>>Name (A-Z)</option>
                    <option value="products.php?sort=price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="products.php?sort=price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="row">
            <?php if (!empty($products)){ ?>
                <?php foreach ($products as $product){ ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                              <?php if ($product['price'] < 1500){ ?>
                                    <div class="badge bg-danger position-absolute mt-2 ms-2">Sale</div>
                                <?php } ?>
                                  <?php if ($product['is_new']): ?>
                                   <span class="badge bg-success position-absolute mt-2 ml-10" style="
        right: 0;
">New Release</span>
                                <?php endif; ?>
                            <img src="assets/images/<?= htmlspecialchars($product['image_url']) ?>"
                                 class="card-img-top"
                                 alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($product['brand']) ?></p>

                                <!-- Star Rating -->
                                <div class="mb-2 text-warning">
                                    <?php
                                    $rating = $product['rating'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="bi bi-star-fill"></i>';
                                        } elseif ($i - 0.5 <= $rating) {
                                            echo '<i class="bi bi-star-half"></i>';
                                        } else {
                                            echo '<i class="bi bi-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>

                                <p class="card-text">$<?= number_format($product['price'], 2) ?></p>

                                <div class="d-flex justify-content-between">
                                    <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary">
                                        Details
                                    </a>
                                    <form action="cart.php" method="post" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-cart-plus"></i> Add
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php }else{ ?>
                <div class="col-12">
                    <div class="alert alert-info">No products found.</div>
                </div>
            <?php } ?>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>