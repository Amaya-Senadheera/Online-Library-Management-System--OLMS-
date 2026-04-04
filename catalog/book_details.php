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
    <a href="books.php" class="btn btn-outline-secondary mb-4 shadow-sm fw-bold border-0" style="background-color: #E9D9B2; color: #1C110A;">
        <i class="bi bi-arrow-left me-1"></i> Back to Browse
    </a>

    <div class="card shadow-lg border-0 overflow-hidden rounded-4" style="background-color: #FDFBF7;">
        <div class="row g-0">
            <div class="col-md-4 text-center p-4 d-flex align-items-center justify-content-center" style="background-color: rgba(176, 138, 91, 0.1);">
                <?php
                $cover = $book['cover_image'];
                if (filter_var($cover, FILTER_VALIDATE_URL)) {
                    $image_path = $cover;
                } else {
                    $image_path = "../assets/images/" . $cover;
                }
                ?>
                <img src="<?php echo $image_path; ?>" class="img-fluid rounded shadow-lg"
                    alt="<?php echo htmlspecialchars($book['title']); ?>"
                    onerror="this.onerror=null; this.src='../assets/images/default-cover.jpg'"
                    style="max-height: 450px; width: auto; object-fit: contain; border: 1px solid rgba(176, 138, 91, 0.3);">
            </div>

            <div class="col-md-8">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="display-5 fw-bold mb-3 text-dark"><?php echo htmlspecialchars($book['title']); ?></h1>

                    <div class="mb-4">
                        <span class="badge bg-info fs-6 me-2 shadow-sm text-white"><?php echo htmlspecialchars($book['category']); ?></span>
                        <span class="badge fs-6 shadow-sm" style="background-color: #8C3A35;"> <?php echo $book['total_qty']; ?> copies total</span>
                    </div>

                    <p class="lead mb-4 text-muted">
                        <i class="bi bi-person-circle me-1" style="color: #8C3A35;"></i>
                        <strong><?php echo htmlspecialchars($book['author']); ?></strong>
                    </p>

                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $avg_rating): ?>
                                        <i class="bi bi-star-fill fs-4" style="color: #D4AF37;"></i>
                                    <?php elseif ($i - 0.5 <= $avg_rating): ?>
                                        <i class="bi bi-star-half fs-4" style="color: #D4AF37;"></i>
                                    <?php else: ?>
                                        <i class="bi bi-star fs-4" style="color: #D4AF37; opacity: 0.3;"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <span class="fs-5 fw-bold text-dark"><?php echo $avg_rating; ?></span>
                            <span class="text-muted">(<?php echo $review_count; ?> reviews)</span>
                        </div>
                    </div>

                    <?php if ($book['available_qty'] > 0): ?>
                        <div class="alert mb-4 border-0 shadow-sm" style="background-color: rgba(130, 168, 65, 0.15); border-left: 5px solid #82a841 !important;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span style="color: #4a6322;">
                                    <i class="bi bi-check-circle-fill me-2" style="color: #82a841;"></i>
                                    <strong>Available!</strong> <?php echo $book['available_qty']; ?> copy(s) ready to borrow
                                </span>
                                <span class="badge" style="background-color: #1C110A;">ISBN: OLMS-<?php echo str_pad($book['id'], 6, '0', STR_PAD_LEFT); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert mb-4 border-0 shadow-sm" style="background-color: rgba(140, 58, 53, 0.1); border-left: 5px solid #8C3A35 !important;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span style="color: #6E2D29;">
                                    <i class="bi bi-x-circle-fill me-2" style="color: #8C3A35;"></i>
                                    <strong>Currently Unavailable</strong> - All copies are borrowed
                                </span>
                                <span class="badge" style="background-color: #1C110A;">ISBN: OLMS-<?php echo str_pad($book['id'], 6, '0', STR_PAD_LEFT); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-2">
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'member'): ?>
                            <?php if ($book['available_qty'] > 0): ?>
                                <button type="button" class="btn btn-lg px-5 fw-bold text-white shadow-sm" style="background-color: #82a841; border: none;" data-bs-toggle="modal"
                                    data-bs-target="#borrowConfirmModal">
                                    <i class="bi bi-book me-1"></i> Borrow This Book
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-lg px-5 fw-bold" disabled>
                                    <i class="bi bi-x-circle me-1"></i> Not Available
                                </button>
                            <?php endif ?>

                            <button type="button" class="btn btn-outline-primary btn-lg px-4 fw-bold" data-bs-toggle="modal"
                                data-bs-target="#reviewModal">
                                <i class="bi bi-star-fill me-1" style="color: #D4AF37;"></i> Write a Review
                            </button>
                        <?php elseif (!isset($_SESSION['user_id'])): ?>
                            <div class="alert w-100 border-0" style="background-color: #E9D9B2; color: #1C110A;">
                                <a href="../auth/login.php" class="alert-link" style="color: #8C3A35;">Login</a> to borrow this book or leave a review.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            </div>
    </div>

    <div class="mt-5">
        <h3 class="fw-bold mb-4 text-dark">
             Reader Reviews
            <span class="fs-6 text-muted fw-normal">(<?php echo $review_count; ?> reviews)</span>
        </h3>

        <?php if ($review_result->num_rows > 0): ?>
            <div class="row">
                <?php while ($review = $review_result->fetch_assoc()): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="background-color: #FDFBF7;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong class="fs-5" style="color: #8C3A35;"><i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($review['username']); ?></strong>
                                        <div class="rating-stars d-inline-block ms-3">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star-fill <?php echo $i <= $review['rating'] ? 'opacity-100' : 'opacity-25'; ?>" style="color: #D4AF37;"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                    </small>
                                </div>
                                <p class="card-text mt-2 text-dark"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert text-center p-5 border-0 shadow-sm rounded-4" style="background-color: #FDFBF7;">
                <i class="bi bi-chat-left-dots" style="font-size: 3rem; color: #D4AF37;"></i>
                <p class="mb-0 mt-3 text-muted">No reviews yet. Be the first to review this book!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="borrowConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: #82a841;">
                <h5 class="modal-title fw-bold"><i class="bi bi-journal-check me-2"></i> Confirm Borrow</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <i class="bi bi-question-circle" style="font-size: 4rem; color: #82a841;"></i>
                <h4 class="mt-3 fw-bold text-dark">Are you sure?</h4>
                <p class="text-muted">Do you want to borrow <br>
                    <strong style="color: #1C110A; font-size: 1.1rem;">"<?php echo htmlspecialchars($book['title']); ?>"</strong>?</p>
                <p class="small fw-bold px-3 py-2 rounded" style="background-color: rgba(140, 58, 53, 0.1); color: #8C3A35;">
                    <i class="bi bi-info-circle me-1"></i> Please note: You will have 14 days to return it.
                </p>
            </div>
            <div class="modal-footer justify-content-center bg-light border-0">
                <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <form action="../operations/borrow_action.php" method="POST" class="m-0">
                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                    <button type="submit" class="btn px-4 fw-bold text-white rounded-pill shadow-sm" style="background-color: #82a841;">Yes, Borrow Book</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="../operations/submit_review.php" method="POST">
                <div class="modal-header border-0" style="background-color: #E9D9B2;">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2" style="color: #8C3A35;"></i> Write a Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                    
                    <p class="text-center text-muted small mb-4">Sharing your thoughts on <strong>"<?php echo htmlspecialchars($book['title']); ?>"</strong></p>

                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold text-dark mb-2">Your Rating</label>
                        <div class="rating-input d-flex justify-content-center">
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" required>
                                    <label for="star<?php echo $i; ?>">★</label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Your Review</label>
                        <textarea name="comment" class="form-control bg-light border-0 shadow-sm" rows="5"
                            placeholder="Share your thoughts about this book..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 bg-white">
                    <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
        font-size: 2.5rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s, transform 0.2s;
    }

    /* 🟢 Changed modal star hover color to Vintage Ochre 🟢 */
    .star-rating label:hover,
    .star-rating label:hover~label,
    .star-rating input:checked~label {
        color: #D4AF37; 
        transform: scale(1.1);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['success'])): ?>
    <script>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo $_SESSION['success']; ?>',
            icon: 'success',
            confirmButtonColor: '#82a841' // 🟢 Theme Sage Green
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
            confirmButtonColor: '#8C3A35' // 🟢 Theme Red-Brown
        });
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>