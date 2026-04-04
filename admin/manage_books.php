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
    .hover-link:hover {
        text-decoration: underline !important;
    }

    /* Table row hover (Vintage Cream to slightly darker Beige) */
    .table-hover tbody tr:hover {
        background-color: #E9D9B2 !important;
        transition: 0.3s;
    }

    /* Button hover */
    .btn-hover {
        transition: 0.3s;
    }

    .btn-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(28, 17, 10, 0.2) !important;
    }

    /* Image styling */
    .book-img {
        border-radius: 6px;
        transition: 0.3s;
        border: 1px solid rgba(176, 138, 91, 0.3);
    }

    .book-img:hover {
        transform: scale(1.15);
        box-shadow: 0 5px 15px rgba(28, 17, 10, 0.3);
    }
    
    /* Custom Table Header Color */
    .vintage-thead th {
        background-color: #8C3A35 !important;
        color: white !important;
        border-bottom: 2px solid #6E2D29 !important;
    }
</style>

<div class="container mt-4 mb-5">

    <div class="mb-4">
        <a href="admin_index.php" class="text-decoration-none fw-bold hover-link" style="color: #8C3A35;">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({ 
                    title: 'Success!', 
                    text: '<?= htmlspecialchars($_SESSION['success']) ?>', 
                    icon: 'success', 
                    confirmButtonColor: '#82a841' // 🟢 Sage Green
                });
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h3 class="m-0 fw-bold" style="color: #1C110A;">
            <i class="bi bi-journals me-2" style="color: #8C3A35;"></i>Manage Books
        </h3>
        
        <a href="add_books.php" class="btn btn-hover fw-bold text-white shadow-sm rounded-pill px-4" style="background-color: #82a841; border: none;">
            <i class="bi bi-plus-circle me-1"></i> Add Book
        </a>
    </div>

    <form method="GET" class="mb-4">
        <div class="input-group shadow-sm rounded-pill overflow-hidden" style="border: 1px solid rgba(176, 138, 91, 0.4);">
            <input type="text" name="search" class="form-control border-0 px-4 bg-white" placeholder="🔍 Search by title or author..."
                value="<?= htmlspecialchars($search) ?>">
            <button class="btn fw-bold px-4 text-white" style="background-color: #8C3A35; border: none;">Search</button>
        </div>
    </form>

    <?php if(!empty($search)): ?>
    <div class="mb-3">
        <a href="manage_books.php" class="btn btn-light border fw-bold text-muted rounded-pill shadow-sm btn-hover">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Search
        </a>
    </div>
    <?php endif; ?>

    <?php if($result->num_rows == 0): ?>
        <div class="alert text-center mt-3 shadow-sm border-0 rounded-4 p-4" style="background-color: #FDFBF7;">
            <i class="bi bi-search" style="font-size: 2rem; color: #B08A5B;"></i>
            <h5 class="fw-bold mt-2 text-dark">No books found!</h5>
            <p class="text-muted mb-0">Try adjusting your search terms.</p>
        </div>
    <?php else: ?>

    <div class="card shadow-sm p-3 border-0 rounded-4" style="background-color: #FDFBF7;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="background-color: transparent;">
                <thead class="vintage-thead text-center">
                    <tr>
                        <th class="rounded-start">ID</th>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Available</th>
                        <th class="rounded-end">Actions</th>
                    </tr>
                </thead>

                <tbody class="text-center" style="border-top: none;">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= $row['id'] ?></td>

                            <td>
                                <?php
                                $cover = $row['cover_image'];
                                if (filter_var($cover, FILTER_VALIDATE_URL)) {
                                    $image_path = $cover;
                                } else {
                                    $image_path = "../assets/images/" . $cover;
                                }
                                ?>
                                <img src="<?= $image_path ?>" 
                                    width="50" height="70" 
                                    class="book-img shadow-sm"
                                    style="object-fit: cover;"
                                    onerror="this.onerror=null; this.src='../assets/images/default-cover.jpg';">
                            </td>

                            <td class="fw-bold text-dark text-start"><?= htmlspecialchars($row['title']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($row['author']) ?></td>
                            <td><span class="badge shadow-sm" style="background-color: rgba(176, 138, 91, 0.15); color: #8C3A35;"><?= htmlspecialchars($row['category']) ?></span></td>
                            <td class="fw-semibold"><?= $row['total_qty'] ?></td>
                            
                            <td class="fw-bold" style="color: <?= ($row['available_qty'] > 0) ? '#82a841' : '#8C3A35' ?>;">
                                <?= $row['available_qty'] ?>
                            </td>

                            <td>
                                <a href="edit_book.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-hover fw-bold shadow-sm text-white mb-1 mb-md-0" style="background-color: #82a841; border: none;">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                <form method="POST" id="deleteForm_<?= $row['id'] ?>" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button type="button" class="btn btn-danger btn-sm btn-hover fw-bold shadow-sm"
                                        onclick="confirmDelete(<?= $row['id'] ?>, '<?= addslashes(htmlspecialchars($row['title'])) ?>')">
                                        <i class="bi bi-trash3"></i> Delete
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
        confirmButtonColor: '#aa2734', // Standard Danger Red
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