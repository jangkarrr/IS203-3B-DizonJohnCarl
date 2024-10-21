<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'db.php'; 
include 'includes/header.php'; 

$query = "SELECT * FROM books";
$result = $conn->query($query);
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info">
        <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<h2>Manage Books</h2>
        <a href="add_book.php" class="btn btn-primary">Add New Book</a>
    <div class="table-responsive">
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Publication Year</th>
                    <th>ISBN</th> 
                    <th>Copies Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td><?= htmlspecialchars($row['genre']) ?></td>
                        <td><?= htmlspecialchars($row['publication_year']) ?></td> 
                        <td><?= htmlspecialchars($row['isbn']) ?></td> 
                        <td><?= htmlspecialchars($row['copies_available']) ?></td>
                        <td>
                            <a href="edit_book.php?id=<?= $row['id'] ?>" class="btn btn-info">Edit</a>
                            <a href="delete_book.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            <a href="borrow.php?book_id=<?= $row['id'] ?>" class="btn btn-warning">Borrow</a> 
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php include 'includes/footer.php'; ?> 
