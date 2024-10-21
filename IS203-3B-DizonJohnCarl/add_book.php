<?php
ob_start();
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $copies_available = $_POST['copies_available'];
    $publication_year = $_POST['publication_year']; 
    $isbn = $_POST['isbn']; 


    $query = "INSERT INTO books (title, author, genre, copies_available, publication_year, isbn) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssiis", $title, $author, $genre, $copies_available, $publication_year, $isbn);
    $stmt->execute();

    header("Location: manage_books.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Add New Book</h2>
        <form method="post" action="add_book.php">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" class="form-control" name="author" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" class="form-control" name="genre" required>
            </div>
            <div class="form-group">
                <label for="copies_available">Copies Available</label>
                <input type="number" class="form-control" name="copies_available" required>
            </div>
            <div class="form-group">
                <label for="publication_year">Publication Year</label> 
                <input type="text" class="form-control" name="publication_year" required>
            </div>
            <div class="form-group">
                <label for="isbn">ISBN</label> 
                <input type="text" class="form-control" name="isbn" required>
            </div>
            <button type="submit" class="btn btn-success">Add Book</button>
        </form>
    </div>
</body>
</html>
<?php include 'includes/footer.php'; ?>