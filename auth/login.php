<?php 
include '../includes/db.php'; 
include '../includes/header.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$success_message = "";

if (isset($_POST['login'])) {

    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            // Set session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];

            // Success message with username
            $success_message = "✅ Login successful! Welcome, " . htmlspecialchars($user['username']) . "!";

            // Optional: Auto redirect after 2 seconds
            echo "<meta http-equiv='refresh' content='2;url=";
            if ($user['role'] == 'admin') {
                echo "../admin/admin_index.php";
            } else {
                echo "../core/dashboard.php";
            }
            echo "'>";

        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "No account found with this email!";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Login to OLMS</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center mt-3">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>