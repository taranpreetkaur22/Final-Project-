<?php
session_start();
require_once 'db/conn.php';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$productId = (int)$_GET['id'];

// Fetch product details
$product = [];
$query = "SELECT * FROM products WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);

    // Fetch basic reviews
    $query = "SELECT r.rating, r.comment, u.name as user_name 
              FROM product_reviews r 
              JOIN users u ON r.user_id = u.id 
              WHERE r.product_id = ? 
              ORDER BY r.created_at DESC 
              LIMIT 5";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);
    $reviews = mysqli_stmt_get_result($stmt);

    // Calculate average rating
    $avgRatingQuery = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                       FROM product_reviews 
                       WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $avgRatingQuery);
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);
    $ratingResult = mysqli_stmt_get_result($stmt);
    $ratingData = mysqli_fetch_assoc($ratingResult);

    $averageRating = round($ratingData['avg_rating'] ?? 0, 1);
    $reviewCount = $ratingData['review_count'] ?? 0;
} else {
    header("Location: products.php");
    exit();
}

include 'includes/header.php';
?>

    <div class="container py-4">
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6">
                <img src="assets/images/<?= htmlspecialchars($product['image_url']) ?>"
                     class="img-fluid rounded border"
                     alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

            <!-- Product Info -->
            <div class="col-md-6">
                <h2><?= htmlspecialchars($product['name']) ?></h2>

                <!-- Rating -->
                <div class="mb-3">
                    <div class="text-warning">
                        <?php
                        $fullStars = floor($averageRating);
                        $hasHalfStar = ($averageRating - $fullStars) >= 0.5;

                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $fullStars) {
                                echo '<i class="bi bi-star-fill"></i>';
                            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                                echo '<i class="bi bi-star-half"></i>';
                            } else {
                                echo '<i class="bi bi-star"></i>';
                            }
                        }
                        ?>
                        <span class="text-muted ms-2">(<?= $reviewCount ?> reviews)</span>
                    </div>
                </div>

                <!-- Price -->
                <h4 class="text-primary mb-3">$<?= number_format($product['price'], 2) ?></h4>

                <!-- Stock Status -->
                <p class="<?= $product['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                    <?= $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                </p>

                <!-- Description -->
                <p><?= htmlspecialchars($product['description']) ?></p>

                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <strong class="me-3">Quantity:</strong>
                        <div class="input-group" style="width: 120px;">
                            <button class="btn btn-outline-secondary" type="button" id="decrement">-</button>
                            <input type="text" class="form-control text-center" value="1" id="quantity" min="1" max="<?= $product['stock'] ?>">
                            <button class="btn btn-outline-secondary" type="button" id="increment">+</button>
                        </div>
                    </div>
                </div>


                <!-- Add to Cart -->
                <form action="cart.php" method="post" class="mt-4">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="action" value="add">
                    <button type="submit" class="btn btn-primary" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                </form>

                <div class="border-top pt-3">
                    <div class="d-flex mb-2">
                        <strong class="me-3" style="width: 100px;">Brand:</strong>
                        <span><?= htmlspecialchars($product['brand'] ?? 'N/A') ?></span>
                    </div>
                    <div class="d-flex mb-2">
                        <strong class="me-3" style="width: 100px;">Model:</strong>
                        <span><?= htmlspecialchars($product['model'] ?? 'N/A') ?></span>
                    </div>
                    <div class="d-flex mb-2">
                        <strong class="me-3" style="width: 100px;">Availability:</strong>
                        <span class="text-<?= $product['stock'] > 0 ? 'success' : 'danger' ?>">
                            <?= $product['stock'] > 0 ? "In Stock ({$product['stock']} items)" : 'Out of Stock' ?>
                        </span>
                    </div>
                    <div class="d-flex">
                        <strong class="me-3" style="width: 100px;">SKU:</strong>
                        <span><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="row mt-5">
            <div class="col-12">
                <h3>Customer Reviews</h3>

                <!-- Review Form (for logged in users) -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5>Write a Review</h5>
                            <form action="submit_review.php" method="post">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <select name="rating" class="form-select" required>
                                        <option value="5">5 Stars</option>
                                        <option value="4">4 Stars</option>
                                        <option value="3">3 Stars</option>
                                        <option value="2">2 Stars</option>
                                        <option value="1">1 Star</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Your Review</label>
                                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Please <a href="login.php">login</a> to write a review.
                    </div>
                <?php endif; ?>

                <!-- Display Reviews -->
                <?php if ($reviews && mysqli_num_rows($reviews) > 0): ?>
                    <div class="list-group">
                        <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                            <div class="list-group-item mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong><?= htmlspecialchars($review['user_name']) ?></strong>
                                    <div class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?= $i <= $review['rating'] ? '★' : '☆' ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="mb-0 mt-2"><?= htmlspecialchars($review['comment']) ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No reviews yet. Be the first to review!</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>

        // Quantity increment/decrement
        document.getElementById('increment').addEventListener('click', function() {
            var quantity = document.getElementById('quantity');
            var max = parseInt(quantity.max);
            var newValue = parseInt(quantity.value) + 1;
            quantity.value = newValue > max ? max : newValue;
        });

        document.getElementById('decrement').addEventListener('click', function() {
            var quantity = document.getElementById('quantity');
            if (parseInt(quantity.value) > 1) {
                quantity.value = parseInt(quantity.value) - 1;
            }
        });

    </script>

<?php include 'includes/footer.php'; ?>