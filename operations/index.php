<?php
// core/dashboard.php
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

// NEW: Fetch the absolute latest returned book to populate the Review Modal
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
.star-rating label { font-size: 2rem; color: #ddd; cursor: pointer; transition: color 0.2s; }
.star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #ffc107; }
</style>

<div class="container my-4">
    <h1 class="display-4 fw-bold mb-4">Borrow & Returns</h1>

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
                    confirmButtonColor: '#ffc107', // Gold color for review
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Trigger the Bootstrap Modal directly on this page!
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
                    confirmButtonColor: '#dc3545'
                });
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Currently Borrowed Books</h5>
        </div>
        <div class="card-body">
            <?php if ($borrowed_result->num_rows > 0): ?>
                <div class="row">
                    <?php while ($transaction = $borrowed_result->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex mb-3">
                                        <?php
                                        // SMART IMAGE LOGIC
                                        $cover = $transaction['cover_image'];
                                        $image_path = (filter_var($cover, FILTER_VALIDATE_URL)) ? $cover : "../assets/images/" . $cover;
                                        ?>
                                        <img src="<?php echo $image_path; ?>"
                                             class="me-3 rounded shadow-sm"
                                             alt="Cover"
                                             style="width: 60px; height: 80px; object-fit: cover;"
                                             onerror="this.onerror=null; this.src='../assets/images/default-cover.jpg'">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-1"><?php echo htmlspecialchars($transaction['title']); ?></h6>
                                            <p class="card-text small text-muted mb-1">by <?php echo htmlspecialchars($transaction['author']); ?></p>
                                            <p class="card-text small mb-0">
                                                <strong>Due:</strong> <?php echo date('M d, Y', strtotime($transaction['due_date'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-success btn-sm w-100 mt-auto" data-bs-toggle="modal" data-bs-target="#returnModal<?php echo $transaction['id']; ?>">
                                        <i class="bi bi-arrow-return-left"></i> Return Book
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="returnModal<?php echo $transaction['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title fw-bold">Confirm Return</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center p-4">
                                        <h4 class="mt-3">Return this book?</h4>
                                        <p class="text-muted">Are you ready to return <strong>"<?php echo htmlspecialchars($transaction['title']); ?>"</strong>?</p>
                                    </div>
                                    <div class="modal-footer justify-content-center bg-light">
                                        <form action="../operations/return_action.php" method="POST" class="m-0">
                                            <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                            <button type="submit" class="btn btn-success px-4 fw-bold">Yes, Return It</button>
                                        </form>
                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted mb-0">You haven't borrowed any books yet.</p>
                    <a href="../catalog/books.php" class="btn btn-primary mt-3">Browse Books</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Recently Returned Books</h5>
        </div>
        <div class="card-body">
            <?php if ($returned_result->num_rows > 0): ?>
                <div class="list-group list-group-flush">
                    <?php while ($transaction = $returned_result->fetch_assoc()): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 mb-2 bg-light rounded">
                            <div>
                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($transaction['title']); ?></h6>
                                <p class="mb-0 small text-muted">
                                    Returned on <?php echo date('M d, Y', strtotime($transaction['returned_date'])); ?>
                                </p>
                            </div>
                            <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i> Returned</span>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0 text-center py-3">No returned books yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($just_returned_book): ?>
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <form action="../operations/submit_review.php" method="POST">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">⭐ Review "<?php echo htmlspecialchars($just_returned_book['title']); ?>"</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="book_id" value="<?php echo $just_returned_book['id']; ?>">
                    
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold text-muted mb-2">How was the book?</label>
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
                        <label class="form-label fw-bold text-muted">Your Thoughts</label>
                        <textarea name="comment" class="form-control bg-light border-0" rows="4" 
                                  placeholder="What did you like or dislike about it?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>