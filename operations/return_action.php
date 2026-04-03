<?php
// operations/return_action.php
// Handle book return logic

include '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'])) {
    $transaction_id = (int) $_POST['transaction_id'];
    $user_id = $_SESSION['user_id'];

    // Check if transaction exists and belongs to user and is active
    $trans_sql = "SELECT * FROM transactions WHERE id = $transaction_id AND user_id = $user_id AND status = 'active'";
    $trans_result = $conn->query($trans_sql);

    if ($trans_result->num_rows > 0) {
        $transaction = $trans_result->fetch_assoc();
        $book_id = $transaction['book_id'];

        // Update transaction
        $update_trans_sql = "UPDATE transactions SET returned_date = NOW(), status = 'returned' WHERE id = $transaction_id";

        if ($conn->query($update_trans_sql) === TRUE) {
            // Update book availability
            $update_book_sql = "UPDATE books SET available_qty = available_qty + 1 WHERE id = $book_id";
            $conn->query($update_book_sql);

            $_SESSION['success'] = "Book returned successfully!";
        } else {
            $_SESSION['error'] = "Error returning book: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Invalid return request.";
    }

    // Redirect back to dashboard
    // --- PASTE THIS AT THE BOTTOM OF return_action.php ---

    // Check where the user came from (the Referer)
    $back_to = $_SERVER['HTTP_REFERER'] ?? '../index.php';

    // Redirect them back to EXACTLY where they clicked the button
    header("Location: " . $back_to);
    exit();
} else {
    // Invalid request
    header("Location: ../index.php");
    exit();
}
?>