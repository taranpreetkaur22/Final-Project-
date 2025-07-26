<?php
session_start();
require_once '../db/conn.php';
include 'includes/admin_header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $query = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
        mysqli_query($conn, $query);

        $_SESSION['success'] = "Category added successfully!";
    }
    elseif (isset($_POST['update_category'])) {
        $id = (int)$_POST['id'];
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $query = "UPDATE categories SET name='$name', description='$description' WHERE id=$id";
        mysqli_query($conn, $query);

        $_SESSION['success'] = "Category updated successfully!";
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $query = "DELETE FROM categories WHERE id=$id";
    mysqli_query($conn, $query);

    $_SESSION['success'] = "Category deleted successfully!";
}

// Fetch all categories
$query = "SELECT * FROM categories ORDER BY name";
$result = mysqli_query($conn, $query);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

    <div class="container py-4">
        <h1 class="mb-4">Manage Categories</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="row">


            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Existing Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($cat['name']) ?></td>
                                        <td><?= htmlspecialchars($cat['description']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-category"
                                                    data-id="<?= $cat['id'] ?>"
                                                    data-name="<?= htmlspecialchars($cat['name']) ?>"
                                                    data-desc="<?= htmlspecialchars($cat['description']) ?>">
                                                Edit
                                            </button>
                                            <a href="categories.php?delete=<?= $cat['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this category? Products will keep but lose category.')">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Add New Category</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Category Name*</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                            <button type="submit" name="add_category" class="btn btn-primary">
                                Add Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="id" id="edit_category_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category Name*</label>
                            <input type="text" class="form-control" name="name" id="edit_category_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_category_desc" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_category" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Handle edit button clicks
        document.querySelectorAll('.edit-category').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const desc = this.getAttribute('data-desc');

                document.getElementById('edit_category_id').value = id;
                document.getElementById('edit_category_name').value = name;
                document.getElementById('edit_category_desc').value = desc;

                var editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            });
        });
    </script>

<?php include 'includes/admin_footer.php'; ?>