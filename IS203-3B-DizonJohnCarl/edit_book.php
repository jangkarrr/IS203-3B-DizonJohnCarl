<?php
session_start();
include 'db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    $query = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Book not found!</div>";
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $genre = $_POST['genre'];
        $publication_year = $_POST['publication_year'];
        $isbn = $_POST['isbn'];
        $copies_available = $_POST['copies_available'];

        $update_query = "UPDATE books SET title = ?, author = ?, genre = ?, publication_year = ?, isbn = ?, copies_available = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sssissi", $title, $author, $genre, $publication_year, $isbn, $copies_available, $book_id);

        if ($update_stmt->execute()) {
            echo "<div class='alert alert-success'>Book updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating book: " . $update_stmt->error . "</div>";
        }

        $update_stmt->close();
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>No book ID provided!</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Edit Book</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" class="form-control" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" class="form-control" id="genre" name="genre" value="<?= htmlspecialchars($book['genre']) ?>" required>
            </div>
            <div class="form-group">
                <label for="publication_year">Publication Year</label>
                <input type="number" class="form-control" id="publication_year" name="publication_year" value="<?= htmlspecialchars($book['publication_year']) ?>" required>
            </div>
            <div class="form-group">
                <label for="isbn">ISBN</label>
                <input type="text" class="form-control" id="isbn" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>" required>
            </div>
            <div class="form-group">
                <label for="copies_available">Copies Available</label>
                <input type="number" class="form-control" id="copies_available" name="copies_available" value="<?= htmlspecialchars($book['copies_available']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Book</button>
        </form>
    </div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
