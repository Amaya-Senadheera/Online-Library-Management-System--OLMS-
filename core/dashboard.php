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

$pending_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM transactions WHERE user_id = '$user_id' AND status = 'borrowed'");
$total_pending = mysqli_fetch_assoc($pending_query)['total'];

// --- 2. CURRENT READS & OVERDUE MATH ---
$current_reads_sql = "SELECT t.*, b.title, b.author, b.cover_image 
                      FROM transactions t 
                      JOIN books b ON t.book_id = b.id 
                      WHERE t.user_id = '$user_id' AND t.status = 'borrowed' 
                      ORDER BY t.borrow_date DESC";
$current_reads = mysqli_query($conn, $current_reads_sql);

$overdue_count = 0; // Replaced fine tracking with simple count
$current_books_data = [];
$has_overdue = false;

while($row = mysqli_fetch_assoc($current_reads)) {
    $due_date = new DateTime($row['due_date']);
    $today = new DateTime(); // Current date and time
    $row['is_overdue'] = false;
    
    // Check if today is past the due date
    if ($today > $due_date) {
        $days_late = $today->diff($due_date)->days;
        
        $row['is_overdue'] = true;
        $row['days_late'] = $days_late;
        
        $overdue_count++;
        $has_overdue = true;
    }
    $current_books_data[] = $row; // Store processed row into our array
}

// --- 3. READING HISTORY ---
$history_sql = "SELECT t.*, b.title, b.author, b.cover_image 
                FROM transactions t 
                JOIN books b ON t.book_id = b.id 
                WHERE t.user_id = '$user_id' AND t.status = 'returned' 
                ORDER BY t.returned_date DESC";
$history_reads = mysqli_query($conn, $history_sql);
?>

<div class="container my-5">
    <div class="p-4 mb-4 rounded-4 shadow-sm hero-faded text-white d-flex justify-content-between align-items-center" style="background-image: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('../assets/images/hero-bg.jpg'); background-size: cover;">
        <div>
            <h2 class="fw-bold">Welcome Back, <?php echo htmlspecialchars($username); ?>! 👋</h2>
            <p class="lead mb-0">Manage Your Current Reads And Track Your History.</p>
        </div>
        <div>
            <a href="profile.php" class="btn btn-outline-light rounded-pill px-4 shadow-sm fw-bold">
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
                <p class="text-muted mb-0 fw-semibold text-uppercase" style="font-size: 0.85rem;">Pending Returns</p>
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

    <?php if ($has_overdue): ?>
        <div class="alert alert-danger shadow-sm border-0 mb-4 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">Action Required: Overdue Books</h5>
                <p class="mb-0">You have books past their return date. Please return them as soon as possible.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm border-0">
                <a href="dashboard.php" class="list-group-item list-group-item-action active border-0"><i class="bi bi-grid-fill me-2"></i> My Dashboard</a>
                <a href="profile.php" class="list-group-item list-group-item-action border-0"><i class="bi bi-person-circle me-2"></i> My Profile</a>
                <a href="../operations/borrow_action.php" class="list-group-item list-group-item-action border-0"><i class="bi bi-person-circle me-2"></i> Borrow a Book</a>
                <a href="../operations/return_action.php" class="list-group-item list-group-item-action border-0"><i class="bi bi-person-circle me-2"></i> Return a Book</a>
                <a href="../auth/logout.php" class="list-group-item list-group-item-action text-danger border-0"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </div>
        </div>

        <div class="col-md-9">
            
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-book-half text-primary me-2"></i> Current Reads</h5>
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
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($current_books_data as $book): ?>
                                        <tr class="<?php echo $book['is_overdue'] ? 'table-danger' : ''; ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo $base_url; ?>assets/images/<?php echo $book['cover_image']; ?>" width="45" class="rounded shadow-sm me-3" style="height: 65px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($book['title']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($book['author']); ?></small>
                                                        <?php if ($book['is_overdue']): ?>
                                                            <div class="text-danger mt-1" style="font-size: 0.75rem; font-weight: bold;">
                                                                Overdue by <?php echo $book['days_late']; ?> days
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></td>
                                            <td class="fw-semibold <?php echo $book['is_overdue'] ? 'text-danger' : ''; ?>">
                                                <?php echo date('M d, Y', strtotime($book['due_date'])); ?>
                                            </td>
                                            <td class="text-end">
                                                <form action="../operations/return_action.php" method="POST">
                                                    <input type="hidden" name="transaction_id" value="<?php echo $book['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-primary px-3 rounded-pill">
                                                        Return Book
                                                    </button>
                                                </form>
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
                                    <?php while($row = mysqli_fetch_assoc($history_reads)): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></div>
                                                <small><?php echo htmlspecialchars($row['author']); ?></small>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($row['borrow_date'])); ?></td>
                                            <td class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> <?php echo date('M d, Y', strtotime($row['returned_date'])); ?></td>
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