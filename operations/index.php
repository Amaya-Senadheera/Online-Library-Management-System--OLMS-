<?php
// operations/index.php
// User dashboard showing borrowed books and account info

include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's active transactions
$borrowed_sql = "SELECT t.*, b.title, b.author, b.cover_image
                 FROM transactions t
                 JOIN books b ON t.book_id = b.id
                 WHERE t.user_id = $user_id AND t.status = 'active'
                 ORDER BY t.due_date ASC";
$borrowed_result = $conn->query($borrowed_sql);

// Fetch user's returned transactions (recent)
$returned_sql = "SELECT t.*, b.title, b.author
                 FROM transactions t
                 JOIN books b ON t.book_id = b.id
                 WHERE t.user_id = $user_id AND t.status = 'returned'
                 ORDER BY t.returned_date DESC LIMIT 5";
$returned_result = $conn->query($returned_sql);

// Fetch the absolute latest returned book to populate the Review Modal
$latest_return_sql = "SELECT b.id, b.title 
                      FROM transactions t 
                      JOIN books b ON t.book_id = b.id 
                      WHERE t.user_id = $user_id AND t.status = 'returned' 
                      ORDER BY t.returned_date DESC LIMIT 1";
$latest_return_result = $conn->query($latest_return_sql);
$just_returned_book = ($latest_return_result && $latest_return_result->num_rows > 0) ? $latest_return_result->fetch_assoc() : null;
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 5px; }
.star-rating input { display: none; }
.star-rating label { font-size: 2.5rem; color: #ddd; cursor: pointer; transition: color 0.2s, transform 0.2s; }
/* Antique Gold for Stars */
.star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #D4AF37; transform: scale(1.1); }
</style>

<div class="container my-4">
    <h1 class="display-5 fw-bold mb-5 text-dark">
        Borrow & Returns
    </h1>

    <?php if (isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: 'Book Returned!',
                    text: '<?php echo $_SESSION['success']; ?>',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: '⭐ Write a Review',
                    cancelButtonText: 'Close',
                    confirmButtonColor: '#D4AF37', 
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
                        reviewModal.show();
                    }
                });
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: 'Oops...',
                    text: '<?php echo $_SESSION['error']; ?>',
                    icon: 'error',
                    confirmButtonColor: '#8C3A35' 
                });
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-5 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-4">
            <h4 class="mb-0 fw-bold" style="color: #8C3A35;">
                <i class="bi bi-book-half me-2"></i> Currently Borrowed Books
            </h4>
        </div>
        <div class="card-body p-4" style="background-color: #FDFBF7;">
            <?php if ($borrowed_result->num_rows > 0): ?>
                <div class="row g-4">
                    <?php while ($transaction = $borrowed_result->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0" style="border-top: 4px solid #8C3A35 !important; background-color: #ffffff;">
                                <div class="card-body d-flex flex-column p-4">
                                    <div class="d-flex mb-4">
                                        <?php
                                        $cover = $transaction['cover_image'];
                                        $image_path = (filter_var($cover, FILTER_VALIDATE_URL)) ? $cover : "../assets/images/" . $cover;
                                        ?>
                                        <img src="<?php echo $image_path; ?>"
                                             class="me-3 rounded shadow-sm"
                                             alt="Cover"
                                             style="width: 70px; height: 100px; object-fit: cover;"
                                             onerror="this.onerror=null; this.src='../assets/images/default-cover.jpg'">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title fw-bold mb-1 text-dark"><?php echo htmlspecialchars($transaction['title']); ?></h6>
                                            <p class="card-text small text-muted mb-2">by <?php echo htmlspecialchars($transaction['author']); ?></p>
                                            <span class="badge rounded-pill px-3 py-2" style="background-color: rgba(140, 58, 53, 0.1); color: #8C3A35;">
                                                <i class="bi bi-calendar-event me-1"></i> Due: <?php echo date('M d', strtotime($transaction['due_date'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-sm w-100 mt-auto fw-bold text-white shadow-sm py-2 rounded-pill" style="background-color: #82a841; border: none;" data-bs-toggle="modal" data-bs-target="#returnModal<?php echo $transaction['id']; ?>">
                                        <i class="bi bi-arrow-return-left me-1"></i> Return Book
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="returnModal<?php echo $transaction['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-0 bg-white pt-4 pb-0">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center p-4 bg-white">
                                        <i class="bi bi-journal-arrow-down mb-3 d-block" style="font-size: 3.5rem; color: #82a841;"></i>
                                        <h4 class="fw-bold text-dark mb-3">Return this book?</h4>
                                        <p class="text-muted">Are you ready to return <br><strong style="color: #1C110A; font-size: 1.1rem;">"<?php echo htmlspecialchars($transaction['title']); ?>"</strong>?</p>
                                    </div>
                                    <div class="modal-footer justify-content-center border-0 bg-white pb-4">
                                        <button type="button" class="btn btn-light px-4 fw-bold rounded-pill text-muted border" data-bs-dismiss="modal">Cancel</button>
                                        <form action="../operations/return_action.php" method="POST" class="m-0">
                                            <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                            <button type="submit" class="btn px-4 fw-bold text-white rounded-pill shadow-sm" style="background-color: #82a841;">Yes, Return It</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-x" style="font-size: 3rem; color: #B08A5B;"></i>
                    <p class="text-muted mt-3 fs-5">You haven't borrowed any books yet.</p>
                    <a href="../catalog/books.php" class="btn px-4 py-2 rounded-pill fw-bold text-white shadow-sm mt-2" style="background-color: #8C3A35;">Browse Books</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-4">
            <h4 class="mb-0 fw-bold" style="color: #82a841;">
                <i class="bi bi-clock-history me-2"></i> Recently Returned Books
            </h4>
        </div>
        <div class="card-body p-4" style="background-color: #FDFBF7;">
            <?php if ($returned_result->num_rows > 0): ?>
                <div class="list-group list-group-flush gap-3">
                    <?php while ($transaction = $returned_result->fetch_assoc()): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 rounded-3 shadow-sm p-3" style="background-color: #ffffff; border-left: 5px solid #82a841 !important;">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($transaction['title']); ?></h6>
                                <p class="mb-0 small text-muted">
                                    <i class="bi bi-calendar-check me-1" style="color: #B08A5B;"></i> Returned on <?php echo date('M d, Y', strtotime($transaction['returned_date'])); ?>
                                </p>
                            </div>
                            <span class="badge rounded-pill px-3 py-2 text-white shadow-sm" style="background-color: #82a841;">
                                <i class="bi bi-check-circle me-1"></i> Returned
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0 text-center py-4">No returned books yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($just_returned_book): ?>
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <form action="../operations/submit_review.php" method="POST">
                <div class="modal-header border-0 pt-4 px-4 bg-white">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-star-fill me-2" style="color: #D4AF37;"></i> Review "<?php echo htmlspecialchars($just_returned_book['title']); ?>"</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <input type="hidden" name="book_id" value="<?php echo $just_returned_book['id']; ?>">
                    
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold text-dark mb-2">How was the book?</label>
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
                        <label class="form-label fw-bold text-dark">Your Thoughts</label>
                        <textarea name="comment" class="form-control bg-light border-0 shadow-sm" rows="4" 
                                  placeholder="What did you like or dislike about it?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 bg-white justify-content-end">
                    <button type="button" class="btn btn-light px-4 fw-bold rounded-pill text-muted border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn px-4 fw-bold text-white rounded-pill shadow-sm" style="background-color: #82a841; border: none;">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>