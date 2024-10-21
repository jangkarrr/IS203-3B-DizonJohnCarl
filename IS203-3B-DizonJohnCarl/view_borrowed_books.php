<?php
session_start();
include 'db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['role'] === 'admin';

if ($is_admin) {
    $query = "SELECT loans.id AS loan_id, books.title, books.author, users.username, loans.loan_date, loans.due_date, loans.return_date 
              FROM loans 
              JOIN books ON loans.book_id = books.id 
              JOIN users ON loans.user_id = users.id";
} else {
    $query = "SELECT loans.id AS loan_id, books.title, books.author, loans.loan_date, loans.due_date, loans.return_date 
              FROM loans 
              JOIN books ON loans.book_id = books.id 
              WHERE loans.user_id = ?";
}

$stmt = $conn->prepare($query);

if (!$is_admin) {
    $stmt->bind_param("i", $user_id);
}

if (isset($_GET['msg'])) {
    echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['msg']) . "</div>";
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Borrowed Books</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="table-responsive">
        <h2>Borrowed Books</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Loan Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <?php if ($is_admin): ?>
                            <th>Borrowed By</th>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><?= htmlspecialchars($row['loan_date']) ?></td>
                            <td><?= htmlspecialchars($row['due_date']) ?></td>
                            <td><?= htmlspecialchars($row['return_date']) ? htmlspecialchars($row['return_date']) : 'Not Returned' ?></td>
                            <?php if ($is_admin): ?>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                            <?php endif; ?>
                            <td>
                                <?php if (!$row['return_date']):?>
                                    <a href="return_book.php?loan_id=<?= $row['loan_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to return this book?')">Return</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No books have been borrowed.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$stmt->close();
include 'includes/footer.php';
?>
