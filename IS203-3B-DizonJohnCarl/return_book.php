<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['loan_id'])) {
    $loan_id = $_GET['loan_id'];

    $query = "SELECT book_id FROM loans WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $loan = $result->fetch_assoc();
        $book_id = $loan['book_id'];

        $update_loan_query = "UPDATE loans SET return_date = NOW(), status = 'returned' WHERE id = ?";
        $update_loan_stmt = $conn->prepare($update_loan_query);
        $update_loan_stmt->bind_param("i", $loan_id);
        $update_loan_stmt->execute();

        $update_book_query = "UPDATE books SET copies_available = copies_available + 1 WHERE id = ?";
        $update_book_stmt = $conn->prepare($update_book_query);
        $update_book_stmt->bind_param("i", $book_id);
        $update_book_stmt->execute();

        header("Location: view_borrowed_books.php?msg=Book returned successfully!");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Invalid loan ID!</div>";
    }

    $update_loan_stmt->close();
    $update_book_stmt->close();
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>No loan ID provided!</div>";
}
?>
