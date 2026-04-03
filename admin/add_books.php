<?php 
//include('../includes/db.php'); 
include('../includes/header.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $cover_image = $_POST['cover_image'];
    $total_qty = $_POST['total_qty'];

    $available_qty = $total_qty;

    $conn->query("INSERT INTO books 
    (title, author, category, cover_image, total_qty, available_qty)
    VALUES 
    ('$title','$author','$category','$cover_image','$total_qty','$available_qty')");

    header("Location: manage_books.php");
}
?>

<style>
/* Smooth animation */
.input-hover {
    transition: all 0.3s ease;
    border-radius: 8px;
}

/* Hover effect */
.input-hover:hover {
    transform: scale(1.02);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

/* Focus (when typing) */
.input-hover:focus {
    border-color: #4e73df;
    box-shadow: 0 0 10px rgba(78, 115, 223, 0.5);
    transform: scale(1.02);
}
</style>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h3 class="mb-4 text-center">Add New Book</h3>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label"><b>Book Title</b></label>
                <input type="text" name="title" class="form-control input-hover" required>
            </div>

            <div class="mb-3">
                <label class="form-label"><b>Author</b></label>
                <input type="text" name="author" class="form-control input-hover" required>
            </div>

            <div class="mb-3">
                <label class="form-label"><b>Category</b></label>
                <input type="text" name="category" class="form-control input-hover">
            </div>

            <div class="mb-3">
                <label class="form-label"><b>Cover Image URL</b></label>
                <input type="text" name="cover_image" class="form-control input-hover">
            </div>

            <div class="mb-3">
                <label class="form-label"><b>Total Quantity</b></label>
                <input type="number" name="total_qty" class="form-control input-hover" required>
            </div>

            <button type="submit" class="btn btn-success w-100">
                Add Book
            </button>

        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>