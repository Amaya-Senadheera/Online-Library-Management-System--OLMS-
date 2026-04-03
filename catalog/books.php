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
    <!-- Hero / Welcome Section -->
    <div class="bg-primary text-white rounded-4 p-5 mb-5 shadow">
        <h1 class="display-4 fw-bold">Discover Your Next Read</h1>
        <p class="lead">Browse our collection of thousands of books. Find, borrow, and enjoy!</p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body p-4">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold">🔍 Search Books</label>
                    <input type="text" name="search" class="form-control form-control-lg" 
                           placeholder="Search by title or author..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">📂 Category</label>
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
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Count -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">📖 Books Collection</h3>
        <span class="badge bg-secondary fs-6 p-2">
            <?php echo $result->num_rows; ?> book(s) found
        </span>
    </div>

    <!-- Books Grid (Netflix-style cards) -->
    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($book = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <!-- Book Cover -->
                        <div class="book-cover-wrapper">
                            <img src="../assets/images/<?php echo htmlspecialchars($book['cover_image']); ?>" 
                                 class="card-img-top book-cover"
                                 alt="<?php echo htmlspecialchars($book['title']); ?>"
                                 onerror="this.src='../assets/images/default-cover.jpg'">
                            <?php if ($book['available_qty'] > 0): ?>
                                <span class="badge-available">Available</span>
                            <?php else: ?>
                                <span class="badge-unavailable">Borrowed</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="card-text text-muted">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($book['author']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-info"><?php echo htmlspecialchars($book['category']); ?></span>
                                <small class="text-success">
                                    📗 <?php echo $book['available_qty']; ?>/<?php echo $book['total_qty']; ?> left
                                </small>
                            </div>
                            <a href="book_details.php?id=<?php echo $book['id']; ?>" 
                               class="btn btn-primary w-100 mt-2">
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center p-5">
            <h4>😔 No books found</h4>
            <p>Try a different search term or browse all categories.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Add custom inline style for this page only -->
<style>
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 1rem 2rem rgba(0,0,0,0.15) !important;
}
.book-cover-wrapper {
    position: relative;
    overflow: hidden;
    background: #f0f0f0;
}
.book-cover {
    width: 100%;
    height: 280px;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.hover-card:hover .book-cover {
    transform: scale(1.05);
}
.badge-available {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #28a745;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}
.badge-unavailable {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #dc3545;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}
</style>

<?php include '../includes/footer.php'; ?>