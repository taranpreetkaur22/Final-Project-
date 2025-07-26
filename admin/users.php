<?php
session_start();
include 'includes/admin_header.php';
require_once '../db/conn.php';

// Handle user deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Check if user has any orders
    $orderCheck = mysqli_query($conn, "SELECT COUNT(*) as order_count FROM orders WHERE user_id = $id");
    $orderData = mysqli_fetch_assoc($orderCheck);

    if ($orderData['order_count'] > 0) {
        $_SESSION['error'] = "Cannot delete user - they have ".$orderData['order_count']." order(s).";
        header("Location: users.php");
        exit();
    }
    
    $query = "DELETE FROM users WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "User deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting user: " . mysqli_error($conn);
    }
    header("Location: users.php");
    exit();
}
// Fetch all users
$users = [];
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
if ($result) {
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

    <div class="container py-4">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['is_admin'] === '1' ? 'primary' : 'secondary' ?>">
                                        <?= htmlspecialchars($user['is_admin'] === '1' ? 'Admin' : 'User') ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="users.php?delete=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No users found</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/admin_footer.php'; ?>