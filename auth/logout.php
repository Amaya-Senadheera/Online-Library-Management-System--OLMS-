<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logging Out</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Auto redirect after 2 seconds -->
    <meta http-equiv="refresh" content="2;url=../index.php">
</head>

<body class="bg-light">

<div class="container text-center mt-5">
    <div class="card shadow p-4">
        <h3 class="text-danger">Logging out...</h3>
        <p class="mt-3">You are being redirected to the home page.</p>

        <!-- Optional spinner -->
        <div class="spinner-border text-primary mt-3"></div>
    </div>
</div>

</body>
</html>