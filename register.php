<?php
session_start();
require_once 'db/conn.php';

// Initialize variables
$errors = [];
$firstName = $lastName = $email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validate inputs
    if (empty($firstName)) {
        $errors['firstName'] = 'First name is required';
    } elseif (!preg_match('/^[a-zA-Z ]+$/', $firstName)) {
        $errors['firstName'] = 'First name can only contain letters and spaces';
    }

    if (empty($lastName)) {
        $errors['lastName'] = 'Last name is required';
    } elseif (!preg_match('/^[a-zA-Z ]+$/', $lastName)) {
        $errors['lastName'] = 'Last name can only contain letters and spaces';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    } else {
        // Check if email already exists
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $errors['email'] = 'Email is already registered';
        }
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }

    if (empty($confirmPassword)) {
        $errors['confirmPassword'] = 'Please confirm your password';
    } elseif ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Passwords do not match';
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $query = "INSERT INTO users (name, email, password, created_at) 
                  VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $firstName, $email, $passwordHash);

        if (mysqli_stmt_execute($stmt)) {
            // Get the new user ID
            $userId = mysqli_insert_id($conn);

            // Set session variables
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = "$firstName $lastName";
            $_SESSION['user_email'] = $email;
            $_SESSION['is_admin'] = 0; // Default to regular user

            // Redirect to welcome page
            header("Location: index.php");
            exit();
        } else {
            $errors['database'] = 'Registration failed. Please try again later.';
        }
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
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Create an Account</h2>
                            <p class="text-muted">Join us today and enjoy exclusive benefits</p>
                        </div>

                        <?php if (!empty($errors['database'])): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($errors['database']) ?></div>
                        <?php endif; ?>

                        <form action="register.php" method="post" novalidate>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control <?= isset($errors['firstName']) ? 'is-invalid' : '' ?>"
                                           id="firstName" name="firstName" placeholder="Enter your first name"
                                           value="<?= htmlspecialchars($firstName) ?>" required>
                                    <?php if (isset($errors['firstName'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['firstName']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control <?= isset($errors['lastName']) ? 'is-invalid' : '' ?>"
                                           id="lastName" name="lastName" placeholder="Enter your last name"
                                           value="<?= htmlspecialchars($lastName) ?>" required>
                                    <?php if (isset($errors['lastName'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['lastName']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group" style="max-width: 100% !important;">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                           id="email" name="email" placeholder="Enter your email"
                                           value="<?= htmlspecialchars($email) ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group" style="max-width: 100% !important;">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                           id="password" name="password" placeholder="Create a password" required>

                                    <?php if (isset($errors['password'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-text">Must be at least 8 characters</div>
                            </div>

                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <div class="input-group" style="max-width: 100% !important;">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control <?= isset($errors['confirmPassword']) ? 'is-invalid' : '' ?>"
                                           id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                                    <?php if (isset($errors['confirmPassword'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['confirmPassword']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Create Account</button>

                            <div class="text-center">
                                <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Sign in</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        // Client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            let valid = true;
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');

            // Check password length
            if (password.value.length < 8) {
                alert('Password must be at least 8 characters');
                valid = false;
            }

            // Check password match
            if (password.value !== confirmPassword.value) {
                alert('Passwords do not match');
                valid = false;
            }

            // Check terms agreement
            if (!document.getElementById('agreeTerms').checked) {
                alert('You must agree to the terms and conditions');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    </script>

<script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>