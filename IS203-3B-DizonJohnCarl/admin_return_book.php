<?php
session_start();
include 'db.php'; 
include 'includes/header.php'; 


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}


$borrowed_query = "SELECT loans.id AS loan_id, books.title, books.author, users.username, loans.loan_date, loans.due_date, loans.return_date 
                    FROM loans 
                    JOIN books ON loans.book_id = books.id 
                    JOIN users ON loans.user_id = users.id";
$borrowed_result = $conn->query($borrowed_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Borrowed Books</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="table-responsive">
        <h2>Books Currently Borrowed</h2>

        <?php if ($borrowed_result->num_rows > 0): ?>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Loan Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Borrowed By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $borrowed_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><?= htmlspecialchars($row['loan_date']) ?></td>
                            <td><?= htmlspecialchars($row['due_date']) ?></td>
                            <td><?= htmlspecialchars($row['return_date']) ? htmlspecialchars($row['return_date']) : 'Not Returned' ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No books are currently borrowed.</p>
        <?php endif; ?>


        <h2 class="mt-5">Books Previously Returned</h2>

        <?php

        $returned_query = "SELECT loans.id AS loan_id, books.title, books.author, users.username, loans.return_date 
                           FROM loans 
                           JOIN books ON loans.book_id = books.id 
                           JOIN users ON loans.user_id = users.id 
                           WHERE loans.status = 'returned'";
        $returned_result = $conn->query($returned_query);
        ?>

        <?php if ($returned_result->num_rows > 0): ?>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Returned By</th>
                        <th>Return Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $returned_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['return_date']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No books have been returned yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
