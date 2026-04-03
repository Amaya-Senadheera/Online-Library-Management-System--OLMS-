<?php
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// --- 1. QUICK STATS QUERIES ---
$read_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM transactions WHERE user_id = '$user_id' AND status = 'returned'");
$total_read = mysqli_fetch_assoc($read_query)['total'];

$pending_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM transactions WHERE user_id = '$user_id' AND status = 'active'");
$total_pending = mysqli_fetch_assoc($pending_query)['total'];

// --- 2. CURRENT READS & OVERDUE MATH ---
$current_reads_sql = "SELECT t.*, b.title, b.author, b.cover_image 
                      FROM transactions t 
                      JOIN books b ON t.book_id = b.id 
                      WHERE t.user_id = '$user_id' AND t.status = 'active' 
                      ORDER BY t.due_date ASC"; // Sorted by closest due date first
$current_reads = mysqli_query($conn, $current_reads_sql);

$overdue_count = 0;
$current_books_data = [];
$has_overdue = false;

while ($row = mysqli_fetch_assoc($current_reads)) {
    $due_date = new DateTime($row['due_date']);
    $today = new DateTime();
    $row['is_overdue'] = false;

    // Calculate difference
    $interval = $today->diff($due_date);
    $days_diff = (int) $interval->format("%r%a"); // Positive if in future, negative if past

    if ($days_diff < 0) {
        $row['is_overdue'] = true;
        $row['days_left'] = abs($days_diff);
        $overdue_count++;
        $has_overdue = true;
    } else {
        $row['days_left'] = $days_diff;
    }

    $current_books_data[] = $row;
}

// --- 3. READING HISTORY ---
$history_sql = "SELECT t.*, b.title, b.author, b.cover_image 
                FROM transactions t 
                JOIN books b ON t.book_id = b.id 
                WHERE t.user_id = '$user_id' AND t.status = 'returned' 
                ORDER BY t.returned_date DESC";
$history_reads = mysqli_query($conn, $history_sql);
?>

<style>
    .hover-link:hover {
        text-decoration: underline !important;
    }
</style>

<div class="container mt-2 mb-5">

    <div class="mb-3">
        <a href="../index.php" class="text-decoration-none text-muted fw-bold hover-link">
            <i class="bi bi-arrow-left me-2"></i>Back to Homepage
        </a>
    </div>

    <div class="p-4 mb-4 rounded-4 shadow-sm hero-faded text-white d-flex justify-content-between align-items-center"
        style="background-image: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('../assets/images/hero-bg.jpg'); background-size: cover;">
        <div>
            <h2 class="fw-bold text-white">Welcome Back, <?php echo htmlspecialchars($username); ?>! 👋</h2>
            <p class="lead mb-0 text-white">Track your reading time and history.</p>
        </div>
        <div>
            <a href="profile.php" class="btn btn-outline-light rounded-pill px-4 shadow-sm fw-bold text-white">
                <i class="bi bi-person-circle me-1"></i> Visit Profile
            </a>
        </div>
    </div>

    <div class="row text-center mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 py-3">
                <h3 class="display-6 fw-bold text-primary"><?php echo $total_read; ?></h3>
                <p class="text-muted mb-0 fw-semibold text-uppercase" style="font-size: 0.85rem;">Total Books Read</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 py-3">
                <h3 class="display-6 fw-bold text-success"><?php echo $total_pending; ?></h3>
                <p class="text-muted mb-0 fw-semibold text-uppercase" style="font-size: 0.85rem;">Currently Borrowed</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 py-3">
                <h3 class="display-6 fw-bold <?php echo ($overdue_count > 0) ? 'text-danger' : 'text-secondary'; ?>">
                    <?php echo $overdue_count; ?>
                </h3>
                <p class="text-muted mb-0 fw-semibold text-uppercase" style="font-size: 0.85rem;">Overdue Books</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm border-0">
                <a href="dashboard.php" class="list-group-item list-group-item-action active border-0"><i
                        class="bi bi-grid-fill me-2"></i> My Dashboard</a>
                <a href="profile.php" class="list-group-item list-group-item-action border-0"><i
                        class="bi bi-person-circle me-2"></i> My Profile</a>
                <a href="../catalog/books.php" class="list-group-item list-group-item-action border-0"><i
                        class="bi bi-search me-2"></i> Browse Books</a>
                <a href="../operations/index.php" class="list-group-item list-group-item-action border-0"><i
                        class="bi bi-arrow-left-right me-2"></i> Borrow & Return</a>
                <a href="../auth/logout.php" class="list-group-item list-group-item-action text-danger border-0"><i
                        class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-book-half text-primary me-2"></i> My Current Reads</h5>
                </div>
                <div class="card-body">
                    <?php if (count($current_books_data) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Book Details</th>
                                        <th>Borrowed On</th>
                                        <th>Due Date</th>
                                        <th class="text-center">Time Remaining</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($current_books_data as $book): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                    $cover = $book['cover_image'];
                                                    $image_path = (filter_var($cover, FILTER_VALIDATE_URL)) ? $cover : "../assets/images/" . $cover;
                                                    ?>
                                                    <img src="<?php echo $image_path; ?>" width="45"
                                                        class="rounded shadow-sm me-3" style="height: 60px; object-fit: cover;"
                                                        onerror="this.src='../assets/images/default-cover.jpg';">
                                                    <div>
                                                        <div class="fw-bold text-dark">
                                                            <?php echo htmlspecialchars($book['title']); ?></div>
                                                        <small
                                                            class="text-muted"><?php echo htmlspecialchars($book['author']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($book['due_date'])); ?></td>
                                            <td class="text-center">
                                                <?php if ($book['is_overdue']): ?>
                                                    <span class="badge bg-danger rounded-pill px-3">
                                                        Overdue (<?php echo $book['days_left']; ?>d)
                                                    </span>
                                                <?php elseif ($book['days_left'] <= 3): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3">
                                                        Only <?php echo $book['days_left']; ?> day(s) left!
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-success rounded-pill px-3">
                                                        <?php echo $book['days_left']; ?> days left
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-journal-x fs-1"></i>
                            <p class="mt-2">You don't have any books checked out right now.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-clock-history text-secondary me-2"></i> Reading History</h5>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($history_reads) > 0): ?>
                        <div class="table-responsive">
                            <table class="table align-middle text-muted">
                                <thead>
                                    <tr>
                                        <th>Book Details</th>
                                        <th>Borrowed On</th>
                                        <th>Returned On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($history_reads)): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?>
                                                </div>
                                                <small><?php echo htmlspecialchars($row['author']); ?></small>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($row['borrow_date'])); ?></td>
                                            <td class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>
                                                <?php echo date('M d, Y', strtotime($row['returned_date'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <p>Your reading history is empty.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>