<?php 
include('../includes/db.php'); 
include('../includes/header.php'); 

if (!isset($_GET['id'])) {
    header("Location: manage_books.php");
    exit;
}

$id = $_GET['id'];

// Fetch book
$result = $conn->query("SELECT * FROM books WHERE id=$id");
$book = $result->fetch_assoc();

// Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Added mysqli_real_escape_string to prevent SQL injection errors on update
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $cover_image = mysqli_real_escape_string($conn, $_POST['cover_image']);
    $total_qty = (int)$_POST['total_qty'];
    $available_qty = (int)$_POST['available_qty'];

    $conn->query("UPDATE books SET 
        title='$title',
        author='$author',
        category='$category',
        cover_image='$cover_image',
        total_qty='$total_qty',
        available_qty='$available_qty'
        WHERE id=$id");

    header("Location: manage_books.php");
    exit;
}
?>

<style>
.hover-link:hover {
    text-decoration: underline !important;
}

.input-hover {
    transition: all 0.3s ease;
    border-radius: 8px;
    background-color: #ffffff;
    border: 1px solid rgba(176, 138, 91, 0.4); /*  Subtle Ochre Border */
}

/*  Smooth Red-Brown focus glow instead of jumpy blue zoom  */
.input-hover:focus {
    border-color: #8C3A35;
    box-shadow: 0 0 0 0.25rem rgba(140, 58, 53, 0.25);
    outline: none;
}
</style>

<div class="container mt-4 mb-5">

    <div class="mb-4">
        <a href="manage_books.php" class="text-decoration-none fw-bold hover-link" style="color: #8C3A35;">
            <i class="bi bi-arrow-left me-2"></i>Back to Manage Books
        </a>
    </div>

    <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5 mx-auto" style="max-width: 800px; background-color: #FDFBF7;">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #1C110A;">
                <i class="bi bi-pencil-square me-2" style="color: #8C3A35;"></i>Edit Book
            </h3>
            <p class="text-muted">Update the details for <strong>"<?php echo htmlspecialchars($book['title']); ?>"</strong>.</p>
        </div>

        <form method="POST">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: #1C110A;">Book Title</label>
                    <input type="text" name="title" class="form-control input-hover p-2" 
                           value="<?php echo htmlspecialchars($book['title']); ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: #1C110A;">Author</label>
                    <input type="text" name="author" class="form-control input-hover p-2" 
                           value="<?php echo htmlspecialchars($book['author']); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: #1C110A;">Category</label>
                    <input type="text" name="category" class="form-control input-hover p-2" 
                           value="<?php echo htmlspecialchars($book['category']); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: #1C110A;"><i class="bi bi-link-45deg me-1" style="color: #B08A5B;"></i>Cover Image URL</label>
                    <input type="text" name="cover_image" class="form-control input-hover p-2" 
                           value="<?php echo htmlspecialchars($book['cover_image']); ?>">
                </div>
            </div>

            <hr class="text-muted opacity-25 my-4">

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold" style="color: #1C110A;">Total Quantity</label>
                    <input type="number" name="total_qty" class="form-control input-hover p-2" 
                           value="<?php echo $book['total_qty']; ?>" required>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold" style="color: #1C110A;">Available Quantity</label>
                    <input type="number" name="available_qty" class="form-control input-hover p-2" 
                           value="<?php echo $book['available_qty']; ?>" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2">
                <a href="manage_books.php" class="btn btn-light border px-4 fw-bold rounded-pill text-muted shadow-sm">Cancel</a>
                
                <button type="submit" class="btn px-5 fw-bold text-white rounded-pill shadow-sm" style="background-color: #82a841; border: none;">
                    <i class="bi bi-save me-2"></i>Update Book
                </button>
            </div>

        </form>
    </div>

</div>

<?php include('../includes/footer.php'); ?>
