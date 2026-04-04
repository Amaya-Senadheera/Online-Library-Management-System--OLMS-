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

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5"> 
            <h2 class="text-center mb-4 fw-bold" style="color: #1C110A;">Register New Account</h2>

            <?php if (isset($error)): ?>
                <div class="alert shadow-sm border-0 fw-bold" style="background-color: rgba(140, 58, 53, 0.1); color: #8C3A35; border-left: 4px solid #8C3A35 !important;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert text-center shadow-sm p-4 border-0" style="background-color: rgba(130, 168, 65, 0.15); color: #4a6322; border: 1px solid rgba(130, 168, 65, 0.3) !important;">
                    <h5 class="mb-3 fw-bold"><?php echo $success; ?></h5>
                    <div class="spinner-border" style="color: #82a841;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0 small" style="color: #757A45;">Redirecting to login page...</p>
                </div>

            <?php else: ?>

                <div class="card shadow-sm border-0 p-4 rounded-4" style="background-color: #FDFBF7;">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control border-0 shadow-sm" style="background-color: #ffffff;" placeholder="Choose a username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control border-0 shadow-sm" style="background-color: #ffffff;" placeholder="Enter your email" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Password</label>
                            <div class="input-group shadow-sm bg-white" style="border-radius: 12px; overflow: hidden;">
                                <input type="password" name="password" id="registerPassword" class="form-control border-0 bg-transparent" placeholder="Create a strong password" required>
                                <button class="btn border-0" type="button" id="togglePassword" style="color: #8C3A35;">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="register" class="btn w-100 fw-bold py-2 shadow-sm rounded-pill text-white" style="background-color: #82a841; border: none;">Register</button>
                    </form>
                </div>

                <p class="text-center mt-4 text-muted">
                    Already have an account? <a href="login.php" class="text-decoration-none fw-bold" style="color: #8C3A35;">Login here</a>
                </p>

            <?php endif; ?>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const togglePassword = document.getElementById("togglePassword");
    const passwordField = document.getElementById("registerPassword");
    const toggleIcon = document.getElementById("toggleIcon");

    if (togglePassword && passwordField) {
        togglePassword.addEventListener("click", function () {
            // Toggle the type attribute
            const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
            passwordField.setAttribute("type", type);
            
            // Toggle the eye / eye-slash icon
            toggleIcon.classList.toggle("bi-eye");
            toggleIcon.classList.toggle("bi-eye-slash");
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>