<?php 
//include '../includes/db.php'; 
include '../includes/header.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$success_message = "";

if (isset($_POST['login'])) {

    $input    = mysqli_real_escape_string($conn, $_POST['input']);   // Can be email or username
    $password = $_POST['password'];

    // Login with either username OR email
    $query = "SELECT * FROM users WHERE email = '$input' OR username = '$input'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];

            $success_message = "✅ Login successful! Welcome, " . htmlspecialchars($user['username']) . "!";

            // Auto redirect after 2 seconds
            $redirect_url = ($user['role'] == 'admin') ? "../admin/admin_index.php" : "../core/dashboard.php";
            echo "<meta http-equiv='refresh' content='2;url=$redirect_url'>";

        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "No account found with this username or email!";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5"> <h2 class="text-center mb-4 fw-bold">Login to OLMS</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger shadow-sm border-0"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                
                <div class="alert alert-success text-center shadow-sm p-4 border-0">
                    <h5 class="mb-3"><?php echo $success_message; ?></h5>
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0 text-muted small">Redirecting to your dashboard...</p>
                </div>

            <?php else: ?>
                
                <div class="card shadow-sm border-0 p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Username or Email</label>
                            <input type="text" name="input" class="form-control bg-light" placeholder="Enter your username or email" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control bg-light" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100 fw-bold">Login</button>
                    </form>
                </div>

                <p class="text-center mt-4 text-muted">
                    Don't have an account? <a href="register.php" class="text-decoration-none fw-bold">Register here</a>
                </p>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>