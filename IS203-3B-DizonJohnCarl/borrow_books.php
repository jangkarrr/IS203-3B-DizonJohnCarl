<?php
session_start();
include 'db.php'; 
include 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$search_term = "";

if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

$query = "SELECT * FROM books WHERE copies_available > 0";
if (!empty($search_term)) {
    $query .= " AND (title LIKE ? OR author LIKE ?)";
}

$stmt = $conn->prepare($query);

if (!empty($search_term)) {
    $search_param = '%' . $search_term . '%';
    $stmt->bind_param("ss", $search_param, $search_param);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Books</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<h2>Available Books for Borrowing</h2>
    <div class="container mt-5">
        <form class="form-inline my-4" method="get" action="borrow_books.php">
            <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search by Title or Author" value="<?= htmlspecialchars($search_term) ?>">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>

        <div class="table-responsive">
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Genre</th>
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
                                <td><?= htmlspecialchars($row['copies_available']) ?></td>
                                <td>
                                    <a href="borrow.php?book_id=<?= $row['id'] ?>" class="btn btn-warning">Borrow</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No books are currently available for borrowing.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
