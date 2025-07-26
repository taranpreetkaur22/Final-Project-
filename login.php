<?php
session_start();
require_once 'db/conn.php';

// Initialize variables
$error = '';
$email = '';
$remember = false;

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        // Prepare SQL query
        $query = "SELECT id, name, email, password, is_admin FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['is_admin'] = $user['is_admin'];

                // Set remember me cookie if checked
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + 60 * 60 * 24 * 30; // 30 days

                    // Store token in database
                    $query = "UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    $expiryDate = date('Y-m-d H:i:s', $expiry);
                    mysqli_stmt_bind_param($stmt, "ssi", $token, $expiryDate, $user['id']);
                    mysqli_stmt_execute($stmt);

                    // Set cookie
                    setcookie('remember_token', $token, $expiry, '/');
                }

                // Redirect to appropriate page
                $redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : 'index.php';
                unset($_SESSION['redirect']);
                header("Location: $redirect");
                exit();
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
    }
}

// Check for remember me cookie
if (empty($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $query = "SELECT id, name, email, is_admin FROM users WHERE remember_token = ? AND token_expiry > NOW()";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];

        // Redirect to appropriate page
        $redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : 'index.php';
        unset($_SESSION['redirect']);
        header("Location: $redirect");
        exit();
    }
}

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Online Computer Store</title>
        <link href="./assets/css/bootstrap.css" rel="stylesheet">
        <link href="./assets/css/style.css" rel="stylesheet">
        <link href="./assets/css/custom.css" rel="stylesheet">
    </head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Welcome Back</h2>
                            <p class="text-muted">Sign in to your account to continue</p>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form action="login.php" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group" style="max-width: 100% !important;">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email"
                                           placeholder="Enter your email" value="<?= htmlspecialchars($email) ?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group" style="max-width: 100% !important;">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Enter your password" required>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember"
                                            <?= $remember ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                    <a href="#" class="text-decoration-none">Forgot password?</a>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Sign In</button>

                            <div class="text-center">
                                <p class="mb-0">Don't have an account? <a href="register.php" class="text-decoration-none">Sign up</a></p>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>