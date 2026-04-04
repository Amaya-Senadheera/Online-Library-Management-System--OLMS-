<?php 
// catalog/books.php
// Member 3 - Rakash MRM_244166J: Book Search & Discovery Page (Netflix-style homepage)

include '../includes/header.php';

// Get search & filter parameters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

// Build the SQL query
$sql = "SELECT * FROM books WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (title LIKE '%$search%' OR author LIKE '%$search%')";
}

if (!empty($category)) {
    $sql .= " AND category = '$category'";
}

$sql .= " ORDER BY title ASC";
$result = $conn->query($sql);

// Get unique categories for filter dropdown
$cat_sql = "SELECT DISTINCT category FROM books WHERE category IS NOT NULL ORDER BY category";
$cat_result = $conn->query($cat_sql);
?>

<div class="container my-4">
    <div class="p-5 mb-5 rounded-4 shadow-lg text-white text-center" 
         style="background-image: linear-gradient(rgba(28, 17, 10, 0.7), rgba(28, 17, 10, 0.4)), url('../assets/images/hero-bg.jpg'); background-size: cover; background-position: center;">
        <h1 class="display-4 fw-bold mb-3">Discover Your Next Read</h1>
        <p class="lead mb-0 text-white-50">Browse our collection of thousands of books. Find, borrow, and enjoy!</p>
    </div>

    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body p-4">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold"> Search Books</label>
                    <input type="text" name="search" class="form-control form-control-lg" 
                           placeholder="Search by title or author..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"> Category</label>
                    <select name="category" class="form-select form-select-lg">
                        <option value="">All Categories</option>
                        <?php while ($cat = $cat_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                                <?php echo ($category == $cat['category']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">Books Collection</h3>
        <span class="badge bg-secondary fs-6 p-2 shadow-sm">
            <?php echo $result->num_rows; ?> book(s) found
        </span>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($book = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <div class="book-cover-wrapper">
                            <?php
                            $cover = $book['cover_image'];
                            if (filter_var($cover, FILTER_VALIDATE_URL)) {
                                $image_path = $cover;
                            } else {
                                $image_path = "../assets/images/" . $cover;
                            }
                            ?>
                            <img src="<?php echo $image_path; ?>" 
                                 class="card-img-top book-cover"
                                 alt="<?php echo htmlspecialchars($book['title']); ?>"
                                 onerror="this.onerror=null; this.src='../assets/images/default-cover.jpg'">
                            <?php if ($book['available_qty'] > 0): ?>
                                <span class="badge-available shadow-sm">Available</span>
                            <?php else: ?>
                                <span class="badge-unavailable shadow-sm">Borrowed</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="card-text text-muted small mb-3">
                                <i class="bi bi-person me-1" style="color: #8C3A35;"></i> <?php echo htmlspecialchars($book['author']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3 mt-auto">
                                <span class="badge bg-info shadow-sm"><?php echo htmlspecialchars($book['category']); ?></span>
                                
                                <small class="fw-bold" style="color: #82a841;">
                                    <?php echo $book['available_qty']; ?>/<?php echo $book['total_qty']; ?> left
                                </small>
                            </div>
                            <a href="book_details.php?id=<?php echo $book['id']; ?>" 
                               class="btn btn-outline-primary w-100 fw-bold">
                                View Details <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center p-5 shadow-sm border-0 rounded-4" style="background-color: #FDFBF7; border: 1px solid rgba(176, 138, 91, 0.3) !important;">
            <h4 class="fw-bold mb-3" style="color: #8C3A35;">😔 No books found</h4>
            <p class="text-muted mb-0">Try a different search term or browse all categories.</p>
        </div>
    <?php endif; ?>
</div>

<style>
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-8px);
    /* 🟢 Changed shadow to match the warm dark theme instead of stark black 🟢 */
    box-shadow: 0 1rem 2rem rgba(28, 17, 10, 0.2) !important;
}
.book-cover-wrapper {
    position: relative;
    overflow: hidden;
    background: #E9D9B2; /* Changed fallback background to theme beige */
}
.book-cover {
    width: 100%;
    height: 320px;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.hover-card:hover .book-cover {
    transform: scale(1.05);
}
.badge-available {
    position: absolute;
    top: 12px;
    right: 12px;
    background-color: #82a841; /* 🟢 New Sage Green 🟢 */
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}
.badge-unavailable {
    position: absolute;
    top: 12px;
    right: 12px;
    background-color: #8C3A35; /* 🟢 Vintage Red-Brown 🟢 */
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}
</style>

<?php include '../includes/footer.php'; ?>