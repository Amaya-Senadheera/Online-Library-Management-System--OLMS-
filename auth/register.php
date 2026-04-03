<?php 
//include '../includes/db.php'; 
include '../includes/header.php'; 

if (isset($_POST['register'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email or username already exists
    $check = "SELECT * FROM users WHERE email='$email' OR username='$username'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        $error = "Username or Email already exists!";
    } else {
        $query = "INSERT INTO users (username, email, password, role) 
                  VALUES ('$username', '$email', '$password', 'member')";

        if (mysqli_query($conn, $query)) {
            // Updated success message to match login style
            $success = "✅ Registration successful! Welcome, " . htmlspecialchars($username) . "!";
            
            // Redirect to login page after 2 seconds
            echo "<meta http-equiv='refresh' content='2;url=login.php'>";
        } else {
            $error = "Registration failed! Please try again.";
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5"> <h2 class="text-center mb-4 fw-bold">Register New Account</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger shadow-sm border-0"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                
                <div class="alert alert-success text-center shadow-sm p-4 border-0">
                    <h5 class="mb-3"><?php echo $success; ?></h5>
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0 text-muted small">Redirecting to login page...</p>
                </div>

            <?php else: ?>

                <div class="card shadow-sm border-0 p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control bg-light" placeholder="Choose a username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control bg-light" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control bg-light" placeholder="Create a strong password" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-success w-100 fw-bold">Register</button>
                    </form>
                </div>

                <p class="text-center mt-4 text-muted">
                    Already have an account? <a href="login.php" class="text-decoration-none fw-bold text-success">Login here</a>
                </p>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>