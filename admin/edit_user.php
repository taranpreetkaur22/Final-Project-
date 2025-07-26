<?php
session_start();
include 'includes/admin_header.php';
require_once '../db/conn.php';

// Check if user ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "User ID not provided";
    header("Location: users.php");
    exit();
}

$user_id = (int)$_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Initialize the base query
    $query = "UPDATE users SET 
              name = '$name', 
              email = '$email'";

    // Only update password if a new one was provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query .= ", password = '$password'";
    }

    // Complete the query
    $query .= " WHERE id = $user_id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "User updated successfully!";
        header("Location: users.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating user: " . mysqli_error($conn);
        header("Location: edit_user.php?id=$user_id");
        exit();
    }
}

// Fetch user data
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $_SESSION['error'] = "User not found";
    header("Location: users.php");
    exit();
}
?>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-pencil-square"></i> Edit User</h1>
            <a href="users.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name*</label>
                            <input type="text" class="form-control" name="name"
                                   value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email*</label>
                            <input type="email" class="form-control" name="email"
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password">
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>


                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include 'includes/admin_footer.php'; ?>