<?php 
include '../includes/db.php'; 
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
            $success = "Registration successful! Redirecting to login...";
            
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
        <div class="col-md-6">
            <h2 class="text-center mb-4">Register New Account</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" name="register" class="btn btn-success w-100">Register</button>
            </form>

            <p class="text-center mt-3">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>