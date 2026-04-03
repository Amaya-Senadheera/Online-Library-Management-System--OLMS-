<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../includes/db.php'); 
include('../includes/header.php');

// DELETE
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    // Delete the book
    $conn->query("DELETE FROM books WHERE id=$id");
    
    // Set a success message in the session before refreshing the page
    $_SESSION['success'] = "Book deleted successfully!";
    header("Location: manage_books.php");
    exit();
}

// SEARCH
$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $result = $conn->query("SELECT * FROM books 
        WHERE title LIKE '%$search%' OR author LIKE '%$search%'");
} else {
    $result = $conn->query("SELECT * FROM books");
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <?php if (isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({ 
                    title: 'Success!', 
                    text: '<?= htmlspecialchars($_SESSION['success']) ?>', 
                    icon: 'success', 
                    confirmButtonColor: '#198754' 
                });
            });
        </script>
        <?php unset($_SESSION['success']); // Clear it so it doesn't show again on refresh ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-center flex-grow-1 fw-bold" style="color: #4e73df;">
            Manage Books
        </h3>
    </div>

    <a href="add_books.php" class="btn btn-success btn-hover mb-3">
        Add Book
    </a>

    <?php if(!empty($search)): ?>
    <div class="mb-3">
        <a href="manage_books.php" class="btn btn-secondary btn-sm">
            🔙 Back to Full Book List
        </a>
    </div>
    <?php endif; ?>

    <form method="GET" class="mb-3">
        <div class="input-group shadow-sm">
            <input type="text" name="search" class="form-control" placeholder="🔍 Search by title or author..."
                value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary">Search</button>
        </div>
    </form>

    <?php if($result->num_rows == 0): ?>
        <div class="alert alert-warning text-center mt-3 shadow-sm border-0">
            No books found! This book is not in the library.
        </div>
    <?php else: ?>

    <div class="card shadow p-3 border-0 rounded-4">
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
                            <td class="fw-bold text-muted"><?= $row['id'] ?></td>

                            <td>
                                <?php
                                $cover = $row['cover_image'];
                                // If it's a valid web URL, use it directly
                                if (filter_var($cover, FILTER_VALIDATE_URL)) {
                                    $image_path = $cover;
                                } else {
                                    // Local filename pointing to the correct assets folder
                                    $image_path = "../assets/images/" . $cover;
                                }
                                ?>
                                <img src="<?= $image_path ?>" 
                                    width="50" height="70" 
                                    class="book-img shadow-sm rounded"
                                    style="object-fit: cover;"
                                    onerror="this.onerror=null; this.src='../assets/images/default-cover.jpg';">
                            </td>

                            <td class="fw-bold"><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['category']) ?></span></td>
                            <td><?= $row['total_qty'] ?></td>
                            <td class="fw-bold <?= ($row['available_qty'] > 0) ? 'text-success' : 'text-danger' ?>">
                                <?= $row['available_qty'] ?>
                            </td>

                            <td>
                                <a href="edit_book.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-hover fw-bold shadow-sm">
                                    ✏️ Edit
                                </a>

                                <form method="POST" id="deleteForm_<?= $row['id'] ?>" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button type="button" class="btn btn-danger btn-sm btn-hover fw-bold shadow-sm"
                                        onclick="confirmDelete(<?= $row['id'] ?>, '<?= addslashes(htmlspecialchars($row['title'])) ?>')">
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
    <?php endif; ?>

</div>

<script>
function confirmDelete(bookId, bookTitle) {
    Swal.fire({
        title: 'Delete ' + bookTitle + '?',
        text: "You won't be able to revert this! All associated data might be lost.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // If they click yes, submit the specific form for this book row
            document.getElementById('deleteForm_' + bookId).submit();
        }
    })
}
</script>

<?php include('../includes/footer.php'); ?>