<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'includes/header.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: view_borrowed_books.php"); 
    exit;
}

$user_role = $_SESSION['role'];

$books_listed = 0;
$times_books_issued = 0;
$times_books_returned = 0;
$registered_users = 0;
$authors_listed = 0;
$listed_categories = 0;


$queries = [
    "SELECT COUNT(*) AS count FROM books",
    "SELECT COUNT(*) AS count FROM loans WHERE status = 'borrowed'", 
    "SELECT COUNT(*) AS count FROM loans WHERE status = 'returned'",
    "SELECT COUNT(*) AS count FROM users",
    "SELECT COUNT(DISTINCT author) AS count FROM books",
    "SELECT COUNT(DISTINCT genre) AS count FROM books"
];

$results = [];
foreach ($queries as $query) {
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $results[] = $result->fetch_assoc()['count'];
    } else {
        $results[] = 0;
    }
}

list($books_listed, $times_books_issued, $times_books_returned, $registered_users, $authors_listed, $listed_categories) = $results;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; 
        }
        .dashboard-container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05); 
        }
        .card-body {
            text-align: center;
        }
        .stat-title {
            font-size: 18px;
            color: #343a40;
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
        }
        
    </style>
</head>
<body>
<h2>Welcome to Dashboard</h2>
    <div class="container dashboard-container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-title">Books Listed</div>
                        <div class="stat-value"><?= $books_listed ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-title">Times Books Issued</div>
                        <div class="stat-value"><?= $times_books_issued ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-title">Times Books Returned</div>
                        <div class="stat-value"><?= $times_books_returned ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-title">Registered Users</div>
                        <div class="stat-value"><?= $registered_users ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-title">Authors Listed</div>
                        <div class="stat-value"><?= $authors_listed ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-title">Listed Categories</div>
                        <div class="stat-value"><?= $listed_categories ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
