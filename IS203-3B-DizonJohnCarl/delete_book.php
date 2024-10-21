<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'db.php'; 

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    $delete_loans_query = "DELETE FROM loans WHERE book_id = ?";
    $stmt_loans = $conn->prepare($delete_loans_query);
    $stmt_loans->bind_param("i", $book_id);
    $stmt_loans->execute();
    $stmt_loans->close();

    $delete_query = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $book_id);

    if ($stmt->execute()) {
        header("Location: manage_books.php?msg=Book deleted successfully!");
    } else {
        header("Location: manage_books.php?msg=Error deleting book: " . $stmt->error);
    }
    $stmt->close();
} else {
    header("Location: manage_books.php?msg=No book ID provided.");
}

$conn->close();
?>
