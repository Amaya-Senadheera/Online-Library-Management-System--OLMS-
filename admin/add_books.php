<?php 
// include('../includes/db.php'); // Assuming db is included in header or already managed
include('../includes/header.php');

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $total_qty = (int)$_POST['total_qty'];

    // Set available quantity = total initially
    $available_qty = $total_qty;

    $cover_image_path = "";

    // 🔥 1. FILE UPLOAD (PRIORITY)
    if(isset($_FILES['cover_file']) && $_FILES['cover_file']['name'] != ""){

        // Clean the filename to remove spaces
        $clean_name = preg_replace("/\s+/", "_", basename($_FILES['cover_file']['name']));
        $file_name = time() . "_" . $clean_name; 
        
        $tmp_name = $_FILES['cover_file']['tmp_name'];
        $target_dir = "../assets/images/";
        $target_file = $target_dir . $file_name;

        if(move_uploaded_file($tmp_name, $target_file)){
            // FIXED: Just save the filename, not the folder path!
            $cover_image_path = $file_name;
        }
    } 
    // 🔥 2. URL (fallback)
    else if(!empty($_POST['cover_image'])){
        $cover_image_path = mysqli_real_escape_string($conn, $_POST['cover_image']);
    }

    // Insert into database
    $sql = "INSERT INTO books (title, author, category, cover_image, total_qty, available_qty)
            VALUES ('$title', '$author', '$category', '$cover_image_path', $total_qty, $available_qty)";

    if($conn->query($sql)){
        header("Location: manage_books.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: ".$conn->error."</div>";
    }
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
    border: 1px solid rgba(176, 138, 91, 0.4); /* 🟢 Subtle Ochre Border */
}

/* 🟢 Replaced Blue glow with smooth Red-Brown glow 🟢 */
.input-hover:focus {
    border-color: #8C3A35;
    box-shadow: 0 0 0 0.25rem rgba(140, 58, 53, 0.25);
    outline: none;
}
</style>

<div class="container mt-4 mb-5">

    <div class="mb-4">
        <a href="admin_index.php" class="text-decoration-none fw-bold hover-link" style="color: #8C3A35;">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5 mx-auto" style="max-width: 800px; background-color: #FDFBF7;">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #1C110A;">
                <i class="bi bi-journal-plus me-2" style="color: #82a841;"></i>Add New Book
            </h3>
            <p class="text-muted">Fill in the details below to add a new book to the catalog.</p>
        </div>

        <form method="POST" enctype="multipart/form-data">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: #1C110A;">Book Title</label>
                    <input type="text" name="title" class="form-control input-hover p-2" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: #1C110A;">Author</label>
                    <input type="text" name="author" class="form-control input-hover p-2" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: #1C110A;">Category</label>
                    <input type="text" name="category" class="form-control input-hover p-2" placeholder="e.g., Fiction, Science, Biography">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: #1C110A;">Total Quantity</label>
                    <input type="number" name="total_qty" class="form-control input-hover p-2" min="1" value="1" required>
                </div>
            </div>

            <hr class="text-muted opacity-25 my-4">

            <div class="mb-4">
                <label class="form-label fw-bold" style="color: #1C110A;"><i class="bi bi-image me-2" style="color: #B08A5B;"></i>Cover Image</label>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Option 1: Upload a File</small>
                        <input type="file" name="cover_file" class="form-control input-hover">
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Option 2: Paste Image URL</small>
                        <input type="text" name="cover_image" class="form-control input-hover" placeholder="https://example.com/image.jpg">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn w-100 fw-bold py-3 mt-2 shadow-sm rounded-pill text-white" style="background-color: #82a841; border: none;">
                <i class="bi bi-plus-circle me-2"></i>Add Book to Catalog
            </button>

        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>