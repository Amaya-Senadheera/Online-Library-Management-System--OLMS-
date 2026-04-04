<?php
// 1. Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Security headers (basic protection)
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");

// 3. Connect to the database safely
require_once __DIR__ . '/db.php';

// 4. Define the Base URL
$base_url = '/Online-Library-Management-System--OLMS-/';

// 5. Dynamic page title
$page_title = $page_title ?? "OLMS - Online Library";

// 6. Get current page (for active navbar)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $page_title; ?></title>

    <link rel="icon" href="<?php echo $base_url; ?>assets/images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="<?php echo $base_url; ?>assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
    <div class="container">

        <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo $base_url; ?>index.php">
            <img src="<?php echo $base_url; ?>assets/images/logo.png" alt="OLMS Logo" width="35" height="35" class="me-2 rounded-circle shadow-sm" style="object-fit: cover; border: 2px solid var(--vintage-ochre);">
            OLMS
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto">
                <?php if (isset($_SESSION['user_id'])): ?>

                    <?php if ($_SESSION['role'] === 'admin'): ?>

                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'admin_index.php') ? 'active fw-bold' : ''; ?>"
                               href="<?php echo $base_url; ?>admin/admin_index.php">
                                Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'manage_books.php') ? 'active fw-bold' : ''; ?>"
                               href="<?php echo $base_url; ?>admin/manage_books.php">
                                Manage Books
                            </a>
                        </li>

                    <?php else: ?>

                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active fw-bold' : ''; ?>"
                               href="<?php echo $base_url; ?>core/dashboard.php">
                                Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'books.php') ? 'active fw-bold' : ''; ?>"
                               href="<?php echo $base_url; ?>catalog/books.php">
                                Browse Books
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'operations') !== false) ? 'active fw-bold' : ''; ?>"
                               href="<?php echo $base_url; ?>operations/index.php">
                                Borrows & Returns
                            </a>
                        </li>

                    <?php endif; ?>

                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>

                    <span class="text-light me-3 d-flex align-items-center">
                        <i class="bi bi-person-circle me-2"></i>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>

                    <a href="<?php echo $base_url; ?>auth/logout.php" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>

                <?php else: ?>

                    <?php if (!in_array($current_page, ['login.php', 'register.php'])): ?>
                        <a href="<?php echo $base_url; ?>auth/login.php" class="btn btn-outline-light me-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>

                        <a href="<?php echo $base_url; ?>auth/register.php" class="btn btn-primary">
                            <i class="bi bi-person-plus me-1"></i> Sign Up
                        </a>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>

<div class="container mt-4">