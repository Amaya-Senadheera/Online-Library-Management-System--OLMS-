<?php
// core/profile.php
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ==========================================
// ⚙️ BACKEND LOGIC: HANDLE FORM SUBMISSIONS
// ==========================================

// 1. Handle Profile Update (Username & Email)
if (isset($_POST['update_profile'])) {
    $new_username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $new_email = mysqli_real_escape_string($conn, trim($_POST['email']));

    // Check if the username is already taken by someone ELSE
    $check_sql = "SELECT id FROM users WHERE username = ? AND id != ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("si", $new_username, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "That username is already taken. Please choose another.";
    } else {
        $update_sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $up_stmt = $conn->prepare($update_sql);
        $up_stmt->bind_param("ssi", $new_username, $new_email, $user_id);

        if ($up_stmt->execute()) {
            $_SESSION['username'] = $new_username; // Update session variable!
            $_SESSION['success'] = "Profile updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating profile.";
        }
    }
    header("Location: profile.php");
    exit();
}

// 2. Handle Password Update
if (isset($_POST['update_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters long.";
    } else {
        // Hash the new password securely
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $pass_sql = "UPDATE users SET password = ? WHERE id = ?";
        $pass_stmt = $conn->prepare($pass_sql);
        $pass_stmt->bind_param("si", $hashed_password, $user_id);

        if ($pass_stmt->execute()) {
            $_SESSION['success'] = "Password changed successfully!";
        } else {
            $_SESSION['error'] = "Error changing password.";
        }
    }
    header("Location: profile.php");
    exit();
}

// 3. Handle Review Deletion
if (isset($_POST['delete_review_id'])) {
    $review_id = (int) $_POST['delete_review_id'];

    // Ensure they can only delete THEIR OWN review
    $del_sql = "DELETE FROM reviews WHERE id = ? AND user_id = ?";
    $del_stmt = $conn->prepare($del_sql);
    $del_stmt->bind_param("ii", $review_id, $user_id);

    if ($del_stmt->execute()) {
        $_SESSION['success'] = "Review deleted successfully.";
    }
    header("Location: profile.php");
    exit();
}

// 4. Handle Review Edit
if (isset($_POST['edit_review_id'])) {
    $review_id = (int) $_POST['edit_review_id'];
    $new_rating = (int) $_POST['rating'];
    $new_comment = mysqli_real_escape_string($conn, trim($_POST['comment']));

    $edit_sql = "UPDATE reviews SET rating = ?, comment = ? WHERE id = ? AND user_id = ?";
    $edit_stmt = $conn->prepare($edit_sql);
    $edit_stmt->bind_param("isii", $new_rating, $new_comment, $review_id, $user_id);

    if ($edit_stmt->execute()) {
        $_SESSION['success'] = "Review updated successfully.";
    }
    header("Location: profile.php");
    exit();
}

// ==========================================
// 📊 FETCH CURRENT DATA FOR DISPLAY
// ==========================================

// Get user data
$user_sql = "SELECT username, email, created_at FROM users WHERE id = $user_id";
$user_data = $conn->query($user_sql)->fetch_assoc();

// Get user's reviews
$reviews_sql = "SELECT r.*, b.title, b.cover_image 
                FROM reviews r 
                JOIN books b ON r.book_id = b.id 
                WHERE r.user_id = $user_id 
                ORDER BY r.created_at DESC";
$reviews_result = $conn->query($reviews_sql);
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    /* 🟢 Changed star hover color to Antique Gold 🟢 */
    .star-rating label:hover,
    .star-rating label:hover~label,
    .star-rating input:checked~label {
        color: #D4AF37;
    }

    /* Link hover effect */
    .hover-link:hover {
        text-decoration: underline !important;
    }

    /* Sidebar Active State */
    .list-group-item.active {
        background-color: #8C3A35 !important;
        border-color: #8C3A35 !important;
        color: white !important;
    }
</style>

<div class="container mt-2 mb-5">

    <div class="mb-3">
        <a href="dashboard.php" class="text-decoration-none text-muted fw-bold hover-link">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({ title: 'Success!', text: '<?php echo $_SESSION['success']; ?>', icon: 'success', confirmButtonColor: '#82a841' });
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({ title: 'Oops...', text: '<?php echo $_SESSION['error']; ?>', icon: 'error', confirmButtonColor: '#8C3A35' });
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="p-4 mb-4 rounded-4 shadow-sm hero-faded text-white d-flex justify-content-between align-items-center"
        style="background-image: linear-gradient(rgba(28, 17, 10, 0.8), rgba(28, 17, 10, 0.8)), url('../assets/images/hero-bg.jpg'); background-size: cover; background-position: center;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                style="width: 70px; height: 70px; border: 3px solid rgba(255,255,255,0.2); background-color: #8C3A35;">
                <i class="bi bi-person text-white display-5"></i>
            </div>
            <div class="ms-3">
                <h2 class="fw-bold mb-0 text-white">My Profile</h2>
                <p class="mb-0 text-white-50">Member since
                    <?php echo date('F Y', strtotime($user_data['created_at'])); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm border-0 mb-4">
                <a href="dashboard.php" class="list-group-item list-group-item-action border-0"><i
                        class="bi bi-grid-fill me-2"></i> My Dashboard</a>
                <a href="profile.php" class="list-group-item list-group-item-action active border-0"><i
                        class="bi bi-person-circle me-2"></i> My Profile</a>
                <a href="../catalog/books.php" class="list-group-item list-group-item-action border-0"><i
                        class="bi bi-search me-2"></i> Browse Books</a>
                <a href="../operations/index.php" class="list-group-item list-group-item-action border-0"><i
                        class="bi bi-arrow-left-right me-2"></i> Borrow & Return</a>
                <a href="../auth/logout.php" class="list-group-item list-group-item-action text-danger border-0"><i
                        class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </div>

            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">
                        Current Details</h6>
                    <div class="mb-3">
                        <small class="text-muted d-block">Username</small>
                        <span class="fw-bold text-dark"><i class="bi bi-person-badge me-2" style="color: #8C3A35;"></i>
                            <?php echo htmlspecialchars($user_data['username']); ?></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Email Address</small>
                        <span class="fw-bold text-dark"><i class="bi bi-envelope-at me-2" style="color: #8C3A35;"></i>
                            <?php echo htmlspecialchars($user_data['email']); ?></span>
                    </div>

                    <hr class="text-muted opacity-25 my-3">

                    <div class="mb-3">
                        <small class="text-muted d-block">Security</small>
                        <a href="#" class="text-danger fw-bold text-decoration-none small hover-link"
                            data-bs-toggle="modal" data-bs-target="#passwordModal">
                            <i class="bi bi-shield-lock me-2"></i> Change Password
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-9">

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-person-gear me-2" style="color: #8C3A35;"></i> Edit Account
                        Details</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST" class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-muted small">Update Username</label>
                            <input type="text" name="username" class="form-control form-control-lg bg-light"
                                value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                        </div>

                        <div class="col-12 mb-4">
                            <label class="form-label fw-bold text-muted small">Update Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg bg-light"
                                value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        </div>

                        <hr class="text-muted opacity-25">

                        <div class="col-12 text-end mt-2">
                            <button type="submit" name="update_profile"
                                class="btn px-5 rounded-pill fw-bold shadow-sm w-100 w-md-auto text-white"
                                style="background-color: #82a841; border: none;">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-5">
                <div
                    class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold"><i class="bi bi-star-fill me-2" style="color: #D4AF37;"></i> My Reviews</h5>
                    <span class="badge bg-light text-dark border"><?php echo $reviews_result->num_rows; ?> total</span>
                </div>
                <div class="card-body">
                    <?php if ($reviews_result->num_rows > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while ($review = $reviews_result->fetch_assoc()): ?>
                                <div class="list-group-item py-3 px-0 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex">
                                            <?php
                                            $cover = $review['cover_image'];
                                            $image_path = (filter_var($cover, FILTER_VALIDATE_URL)) ? $cover : "../assets/images/" . $cover;
                                            ?>
                                            <img src="<?php echo $image_path; ?>" class="rounded shadow-sm me-3"
                                                style="width: 50px; height: 75px; object-fit: cover;"
                                                onerror="this.src='../assets/images/default-cover.jpg'">
                                            <div>
                                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($review['title']); ?></h6>
                                                <div class="mb-2">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="bi bi-star-fill"
                                                            style="<?php echo $i <= $review['rating'] ? 'color: #D4AF37;' : 'color: #ddd;'; ?>"></i>
                                                    <?php endfor; ?>
                                                    <small
                                                        class="text-muted ms-2"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                                                </div>
                                                <p class="mb-0 text-muted small">
                                                    <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="dropdown">
                                            <button
                                                class="btn btn-light border p-0 text-muted d-flex align-items-center justify-content-center"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                style="border-radius: 6px; width: 32px; height: 32px;">
                                                <i class="bi bi-chevron-down" style="font-size: 1rem;"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2">
                                                <li>
                                                    <button class="dropdown-item fw-bold small text-dark" type="button"
                                                        onclick="openEditModal(<?php echo $review['id']; ?>, <?php echo $review['rating']; ?>, '<?php echo addslashes(htmlspecialchars($review['comment'])); ?>', '<?php echo addslashes(htmlspecialchars($review['title'])); ?>')">
                                                        <i class="bi bi-pencil-square me-2" style="color: #8C3A35;"></i> Edit
                                                        Review
                                                    </button>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider opacity-50">
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger fw-bold small" type="button"
                                                        onclick="confirmDelete(<?php echo $review['id']; ?>)">
                                                        <i class="bi bi-trash3 me-2"></i> Delete Review
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-chat-square-text text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">You haven't written any reviews yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <form action="" method="POST">
                <div class="modal-header border-0" style="background-color: #E9D9B2;">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-shield-lock me-2"
                            style="color: #8C3A35;"></i>Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <p class="text-muted small mb-4">Ensure your new password is at least 6 characters long.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">New Password</label>
                        <input type="password" name="new_password" class="form-control bg-light border-0 shadow-sm"
                            placeholder="Enter new password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control bg-light border-0 shadow-sm"
                            placeholder="Confirm new password" required>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 pt-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_password"
                        class="btn px-4 rounded-pill fw-bold shadow-sm text-white"
                        style="background-color: #82a841; border: none;">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editReviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <form action="" method="POST">
                <div class="modal-header border-0" style="background-color: #E9D9B2;">
                    <h5 class="modal-title fw-bold text-dark" id="editModalTitle">Edit Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <input type="hidden" name="edit_review_id" id="edit_review_id">

                    <div class="mb-4 text-center">
                        <div class="rating-input d-flex justify-content-center">
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" value="<?php echo $i; ?>"
                                        id="edit_star<?php echo $i; ?>" required>
                                    <label for="edit_star<?php echo $i; ?>">★</label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <textarea name="comment" id="edit_comment" class="form-control bg-light border-0 shadow-sm"
                            rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 bg-white">
                    <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn px-4 fw-bold rounded-pill text-white shadow-sm"
                        style="background-color: #82a841; border: none;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="deleteReviewForm" action="" method="POST" style="display: none;">
    <input type="hidden" name="delete_review_id" id="delete_review_id">
</form>

<script>
    // Function to populate and open the Edit Modal
    function openEditModal(reviewId, rating, comment, bookTitle) {
        document.getElementById('edit_review_id').value = reviewId;
        document.getElementById('edit_comment').value = comment;
        document.getElementById('editModalTitle').innerText = 'Edit Review: ' + bookTitle;

        // Select the correct star rating
        document.getElementById('edit_star' + rating).checked = true;

        var editModal = new bootstrap.Modal(document.getElementById('editReviewModal'));
        editModal.show();
    }

    // Function to confirm deletion before submitting the hidden form
    function confirmDelete(reviewId) {
        Swal.fire({
            title: 'Delete this review?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', // Kept red for danger
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete_review_id').value = reviewId;
                document.getElementById('deleteReviewForm').submit();
            }
        })
    }
</script>

<?php include '../includes/footer.php'; ?>