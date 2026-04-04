<?php
session_start();
session_unset();
session_destroy();

// Define base URL for assets
$base_url = '/Online-Library-Management-System--OLMS-/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - OLMS</title>
    
    <link rel="icon" href="<?php echo $base_url; ?>assets/images/logo.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <meta http-equiv="refresh" content="2;url=../index.php">
    
    <style>
        body {
            /* 🟢 Reverted to the original deep slate/black cinematic background 🟢 */
            background-image: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), url('../assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .logout-card {
            max-width: 420px;
            width: 100%;
            border-radius: 1.25rem;
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="vh-100 d-flex align-items-center justify-content-center">

    <div class="card shadow-lg border-0 logout-card text-center p-5 mx-3" style="background-color: #FDFBF7;">
        <div class="mb-4">
            
            <img src="../assets/images/logo.png" alt="OLMS Logo" width="60" height="60" class="mb-3" onerror="this.style.display='none'">
            
            <h3 class="fw-bold mb-2" style="color: #1C110A;">See you soon! 👋</h3>
            <p class="text-muted">Safely logging you out of your account...</p>
        </div>
        
        <div class="d-flex justify-content-center align-items-center my-3">
            <div class="spinner-border" role="status" style="color: #82a841; width: 3rem; height: 3rem; border-width: 0.25rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        
        <p class="small text-muted mt-4 mb-0 fw-semibold">
            <i class="bi bi-shield-check me-1" style="color: #82a841;"></i> Redirecting to homepage
        </p>
    </div>

</body>
</html>