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

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5"> 
            <h2 class="text-center mb-4 fw-bold" style="color: #1C110A;">Login to OLMS</h2>

            <?php if (isset($error)): ?>
                <div class="alert shadow-sm border-0 fw-bold" style="background-color: rgba(140, 58, 53, 0.1); color: #8C3A35; border-left: 4px solid #8C3A35 !important;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert text-center shadow-sm p-4 border-0" style="background-color: rgba(130, 168, 65, 0.15); color: #4a6322; border: 1px solid rgba(130, 168, 65, 0.3) !important;">
                    <h5 class="mb-3 fw-bold"><?php echo $success_message; ?></h5>
                    <div class="spinner-border" style="color: #82a841;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0 small" style="color: #757A45;">Redirecting to your dashboard...</p>
                </div>

            <?php else: ?>
                
                <div class="card shadow-sm border-0 p-4 rounded-4" style="background-color: #FDFBF7;">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Username or Email</label>
                            <input type="text" name="input" class="form-control border-0 shadow-sm" style="background-color: #ffffff;" placeholder="Enter your username or email" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Password</label>
                            <div class="input-group shadow-sm bg-white" style="border-radius: 12px; overflow: hidden;">
                                <input type="password" name="password" id="loginPassword" class="form-control border-0 bg-transparent" placeholder="Enter your password" required>
                                <button class="btn border-0" type="button" id="togglePassword" style="color: #8C3A35;">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="login" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-pill">Login</button>
                    </form>
                </div>

                <p class="text-center mt-4 text-muted">
                    Don't have an account? <a href="register.php" class="text-decoration-none fw-bold" style="color: #8C3A35;">Register here</a>
                </p>

            <?php endif; ?>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const togglePassword = document.getElementById("togglePassword");
    const passwordField = document.getElementById("loginPassword");
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