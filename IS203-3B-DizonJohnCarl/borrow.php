<?php
session_start();
include 'db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $user_id = $_SESSION['user_id'];
    $loan_date = date('Y-m-d');

    $query = "INSERT INTO loans (user_id, book_id, loan_date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("iis", $user_id, $book_id, $loan_date);

    if ($stmt->execute()) {
        $update_query = "UPDATE books SET copies_available = copies_available - 1 WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $book_id);
        $update_stmt->execute();

        echo "<div class='alert alert-success'>Book borrowed successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error borrowing book: " . $stmt->error . "</div>";
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>No book ID provided!</div>";
}

include 'includes/footer.php';
?>
