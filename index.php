<?php
session_start();
include 'db/conn.php';

// Initialize variables
$featuredProducts = [];
$categoryCounts = [];
$error = '';

// Get featured products from database
$query = "SELECT *, (created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS is_new  FROM products WHERE stock > 0 ORDER BY rating DESC LIMIT 4";
$result = mysqli_query($conn, $query);

if ($result) {
    $featuredProducts = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $error = "Error fetching featured products: " . mysqli_error($conn);
}


include 'includes/header.php';

// Display error if any
if (!empty($error)) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
}
?>

    <!-- Rest of your HTML remains the same until the featured products section -->

    <!-- Featured Products -->
    <section id="featured" class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2>Featured Products</h2>
                <a href="products.php" class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row g-4">
                <?php if (!empty($featuredProducts)){ ?>
                    <?php foreach ($featuredProducts as $product){ ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="card product-card h-100 border-0 shadow-sm">
                                <?php if ($product['price'] < 1500){ ?>
                                    <div class="badge bg-danger position-absolute mt-2 ms-2">Sale</div>
                                <?php } ?>
                                  <?php if ($product['is_new']): ?>
                                   <span class="badge bg-success position-absolute mt-2 ml-10" style="
        right: 0;
">New Release</span>
                                <?php endif; ?>
                                <img src="assets/images/<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="card-title mb-1"><?= htmlspecialchars($product['name']) ?></h5>
                                        <div class="text-warning">
                                            <?php
                                            $fullStars = floor($product['rating']);
                                            $halfStar = ($product['rating'] - $fullStars) >= 0.5;
                                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

                                            // Full stars
                                            for ($i = 0; $i < $fullStars; $i++) {
                                                echo '<i class="bi bi-star-fill"></i>';
                                            }

                                            // Half star
                                            if ($halfStar) {
                                                echo '<i class="bi bi-star-half"></i>';
                                            }

                                            // Empty stars
                                            for ($i = 0; $i < $emptyStars; $i++) {
                                                echo '<i class="bi bi-star"></i>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-2"><?= htmlspecialchars($product['brand']) ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary">$<?= number_format($product['price'], 2) ?></span>
                                        <form action="cart.php" method="post" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <input type="hidden" name="action" value="add">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary w-100">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php }else{ ?>
                    <div class="col-12">
                        <div class="alert alert-info">No featured products found.</div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Rest of your HTML remains the same -->

<?php include 'includes/footer.php'; ?>