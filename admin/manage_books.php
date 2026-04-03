<?php
//include('../includes/db.php'); 
include('../includes/header.php');

// DELETE
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $conn->query("DELETE FROM books WHERE id=$id");
    header("Location: manage_books.php");
}

// SEARCH
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $result = $conn->query("SELECT * FROM books 
        WHERE title LIKE '%$search%' OR author LIKE '%$search%'");
} else {
    $result = $conn->query("SELECT * FROM books");
}
?>

<style>
    /* Table row hover */
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transition: 0.3s;
    }

    /* Button hover */
    .btn-hover {
        transition: 0.3s;
    }

    .btn-hover:hover {
        transform: scale(1.05);
    }

    /* Image styling */
    .book-img {
        border-radius: 5px;
        transition: 0.3s;
    }

    .book-img:hover {
        transform: scale(1.1);
    }
</style>

<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <!-- Centered Title -->
        <h3 class="text-center flex-grow-1 fw-bold" style="color: #4e73df;">
            Manage Books
        </h3>

    </div>

    <!-- Add Book Button -->
    <a href="add_books.php" class="btn btn-success btn-hover mb-3">
        Add Book
    </a>

    <!-- Search Bar -->
    <form method="GET" class="mb-3">
        <div class="input-group shadow-sm">
            <input type="text" name="search" class="form-control" placeholder="🔍 Search by title or author..."
                value="<?= $search ?>">
            <button class="btn btn-primary">Search</button>
        </div>
    </form>




    <!-- Table -->
    <div class="card shadow p-3">
        <div class="table-responsive">

            <table class="table table-hover table-bordered text-center align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>

                            <td>
                                <?php
                                $cover = $row['cover_image'];
                                // Check if the string starts with http:// or https://
                                if (strpos($cover, 'http') === 0) {
                                    // It's a full URL, use it exactly as it is
                                    $image_path = $cover;
                                } else {
                                    // It's a local filename, look in our assets folder
                                    // We use $base_url to make sure it works from any subfolder
                                    $image_path = $base_url . "assets/images/" . $cover;
                                }
                                ?>
                                <img src="<?= $image_path ?>" width="50" height="70" class="book-img shadow-sm rounded"
                                    style="object-fit: cover;"
                                    onerror="this.src='<?= $base_url ?>assets/images/default-cover.jpg'">
                            </td>

                            <td><?= $row['title'] ?></td>
                            <td><?= $row['author'] ?></td>
                            <td><?= $row['category'] ?></td>
                            <td><?= $row['total_qty'] ?></td>
                            <td><?= $row['available_qty'] ?></td>

                            <td>
                                <!-- EDIT -->
                                <a href="edit_book.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-hover">
                                    ✏️ Edit
                                </a>

                                <!-- DELETE -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button class="btn btn-danger btn-sm btn-hover"
                                        onclick="return confirm('Delete this book?')">
                                        🗑 Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

<?php include('../includes/footer.php'); ?>