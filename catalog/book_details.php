<?php
// catalog/book_details.php
// Member 3 - Rakash MRM_244166J: Detailed view of a single book

include '../includes/header.php';

// Get book ID from URL
$book_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($book_id <= 0) {
    header("Location: books.php");
    exit();
}

// Fetch book details
$sql = "SELECT * FROM books WHERE id = $book_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: books.php");
    exit();
}

$book = $result->fetch_assoc();

// Fetch reviews for this book
$review_sql = "SELECT r.*, u.username 
               FROM reviews r 
               JOIN users u ON r.user_id = u.id 
               WHERE r.book_id = $book_id 
               ORDER BY r.created_at DESC";
$review_result = $conn->query($review_sql);

// Calculate average rating
$avg_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
            FROM reviews WHERE book_id = $book_id";
$avg_result = $conn->query($avg_sql);
$avg_data = $avg_result->fetch_assoc();
$avg_rating = round($avg_data['avg_rating'] ?? 0, 1);
$review_count = $avg_data['review_count'] ?? 0;
?>

<div class="container my-4">
    <!-- Back Button -->
    <a href="books.php" class="btn btn-outline-secondary mb-4">
        ← Back to Browse
    </a>

    <!-- Book Details Card -->
    <div class="card shadow-lg border-0 overflow-hidden">
        <div class="row g-0">
            <!-- Left: Book Cover -->
            <div class="col-md-4 bg-light text-center p-4">
                <?php
                $cover = $book['cover_image'];
                if (filter_var($cover, FILTER_VALIDATE_URL)) {
                    $image_path = $cover;
                } else {
                    $image_path = "../assets/images/" . $cover;
                }
                ?>
                <img src="<?php echo $image_path; ?>" class="img-fluid rounded shadow"
                    alt="<?php echo htmlspecialchars($book['title']); ?>"
                    onerror="this.onerror=null; this.src='../assets/images/default-cover.jpg'"
                    style="max-height: 450px; width: auto; object-fit: contain;">
            </div>

            <!-- Right: Book Info -->
            <div class="col-md-8">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($book['title']); ?></h1>

                    <div class="mb-4">
                        <span class="badge bg-info fs-6 me-2"><?php echo htmlspecialchars($book['category']); ?></span>
                        <span class="badge bg-secondary fs-6">📚 <?php echo $book['total_qty']; ?> copies total</span>
                    </div>

                    <p class="lead mb-4">
                        <i class="bi bi-person-circle"></i>
                        <strong><?php echo htmlspecialchars($book['author']); ?></strong>
                    </p>

                    <!-- Rating Display -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $avg_rating): ?>
                                        <i class="bi bi-star-fill text-warning fs-4"></i>
                                    <?php elseif ($i - 0.5 <= $avg_rating): ?>
                                        <i class="bi bi-star-half text-warning fs-4"></i>
                                    <?php else: ?>
                                        <i class="bi bi-star text-warning fs-4"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <span class="fs-5 fw-bold"><?php echo $avg_rating; ?></span>
                            <span class="text-muted">(<?php echo $review_count; ?> reviews)</span>
                        </div>
                    </div>

                    <!-- Availability Status -->
                    <div
                        class="alert <?php echo $book['available_qty'] > 0 ? 'alert-success' : 'alert-danger'; ?> mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>
                                <?php if ($book['available_qty'] > 0): ?>
                                    ✅ <strong>Available!</strong> <?php echo $book['available_qty']; ?> copy(s) ready to
                                    borrow
                                <?php else: ?>
                                    ❌ <strong>Currently Unavailable</strong> - All copies are borrowed
                                <?php endif; ?>
                            </span>
                            <span class="badge bg-dark">ISBN:
                                OLMS-<?php echo str_pad($book['id'], 6, '0', STR_PAD_LEFT); ?></span>
                        </div>
                    </div>

                    <!-- Borrow Button (only if user is logged in and book available) -->
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'member'): ?>
                        <?php if ($book['available_qty'] > 0): ?>
                            <button type="button" class="btn btn-success btn-lg px-5 me-2" data-bs-toggle="modal"
                                data-bs-target="#borrowConfirmModal">
                                📖 Borrow This Book
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg px-5" disabled>
                                ❌ Not Available
                            </button>
                        <?php endif ?>

                        <button type="button" class="btn btn-outline-primary btn-lg px-4" data-bs-toggle="modal"
                            data-bs-target="#reviewModal">
                            ⭐ Write a Review
                        </button>
                    <?php elseif (!isset($_SESSION['user_id'])): ?>
                        <div class="alert alert-info">
                            <a href="../auth/login.php" class="alert-link">Login</a> to borrow this book or leave a review.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal fade" id="borrowConfirmModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title fw-bold">📖 Confirm Borrow</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center p-4">
                            <i class="bi bi-question-circle text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Are you sure?</h4>
                            <p class="text-muted">Do you want to borrow
                                <strong>"<?php echo htmlspecialchars($book['title']); ?>"</strong>?</p>
                            <p class="small text-danger">Please note: You will have 14 days to return it.</p>
                        </div>
                        <div class="modal-footer justify-content-center bg-light">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">No,
                                Cancel</button>
                            <form action="../operations/borrow_action.php" method="POST" class="m-0">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                <button type="submit" class="btn btn-success px-4 fw-bold">Yes, Borrow Book</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="reviewModal" tabindex="-1">
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-5">
        <h3 class="fw-bold mb-4">
            📝 Reader Reviews
            <span class="fs-6 text-muted">(<?php echo $review_count; ?> reviews)</span>
        </h3>

        <?php if ($review_result->num_rows > 0): ?>
            <div class="row">
                <?php while ($review = $review_result->fetch_assoc()): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong class="fs-5"><?php echo htmlspecialchars($review['username']); ?></strong>
                                        <div class="rating-stars d-inline-block ms-3">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i
                                                    class="bi bi-star-fill text-warning <?php echo $i <= $review['rating'] ? 'opacity-100' : 'opacity-25'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                    </small>
                                </div>
                                <p class="card-text mt-2"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-light text-center p-5">
                <p class="mb-0">No reviews yet. Be the first to review this book!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../operations/submit_review.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">⭐ Write a Review for "<?php echo htmlspecialchars($book['title']); ?>"</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Your Rating</label>
                        <div class="rating-input">
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>"
                                        required>
                                    <label for="star<?php echo $i; ?>">★</label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Your Review</label>
                        <textarea name="comment" class="form-control" rows="5"
                            placeholder="Share your thoughts about this book..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Star Rating CSS for Modal -->
<style>
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 5px;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }

    .star-rating label:hover,
    .star-rating label:hover~label,
    .star-rating input:checked~label {
        color: #ffc107;
    }

    .rating-stars i {
        font-size: 1.2rem;
    }
</style>

<!-- Add Bootstrap Icons (if not already in header) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['success'])): ?>
    <script>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo $_SESSION['success']; ?>',
            icon: 'success',
            confirmButtonColor: '#198754' // Bootstrap Success Green
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <script>
        Swal.fire({
            title: 'Oops...',
            text: '<?php echo $_SESSION['error']; ?>',
            icon: 'error',
            confirmButtonColor: '#dc3545' // Bootstrap Danger Red
        });
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>