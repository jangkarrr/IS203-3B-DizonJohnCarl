<?php
session_start();
include '/db.php';
include '/includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetching users with loan information
$query = "
    SELECT 
        users.id, 
        users.username, 
        users.email, 
        users.role, 
        users.created_at,
        GROUP_CONCAT(CONCAT('Book ID: ', loans.book_id, 
            ' | Loan Date: ', loans.loan_date, 
            ' | Due Date: ', loans.due_date, 
            ' | Return Date: ', loans.return_date) 
            SEPARATOR '<br>') AS loans
    FROM users
    LEFT JOIN loans ON users.id = loans.user_id
    GROUP BY users.id";

$result = $conn->query($query);

// Add user functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
    $role = $_POST['role'];
    $email = $_POST['email'];
    $profile_data = json_encode(['bio' => 'New user profile']); 

    $insert_query = "INSERT INTO users (username, password, role, email, profile_data) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssss", $username, $password, $role, $email, $profile_data);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>User added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error adding user: " . $stmt->error . "</div>";
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $delete_loans_query = "DELETE FROM loans WHERE user_id = ?";
    $delete_loans_stmt = $conn->prepare($delete_loans_query);
    $delete_loans_stmt->bind_param("i", $delete_id);
    $delete_loans_stmt->execute();

    $delete_user_query = "DELETE FROM users WHERE id = ?";
    $delete_user_stmt = $conn->prepare($delete_user_query);
    $delete_user_stmt->bind_param("i", $delete_id);
    
    if ($delete_user_stmt->execute()) {
        echo "<div class='alert alert-success'>User and associated loans deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting user: " . $delete_user_stmt->error . "</div>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<h2>Manage Accounts</h2>
<div class="container mt-5">
    
    <h4>Add New Account</h4>   
    <form method="post" action="manage_users.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
    </form>

    <h4 class="mt-5">Existing Users</h4>
    <div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Loans</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    <td><?php echo $user['loans'] ? htmlspecialchars($user['loans']) : 'No loans'; ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        <button onclick="printUser(<?php echo $user['id']; ?>)" class="btn btn-secondary btn-sm">Print</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>

    <button onclick="printAllUsers()" class="btn btn-primary mt-3">Print All Users</button>
</div>

<script>
function printUser(userId) {
    const printWindow = window.open('', '', 'height=600,width=800');
    fetch('get_user_info.php?id=' + userId)
        .then(response => response.text())
        .then(data => {
            printWindow.document.write('<html><head><title>User Information</title>');
            printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
            printWindow.document.write('</head><body>');
            printWindow.document.write(data);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        })
        .catch(error => console.error('Error fetching user info:', error));
}

function printAllUsers() {
    const printWindow = window.open('', '', 'height=600,width=800');
    fetch('get_all_users.php')
        .then(response => response.text())
        .then(data => {
            printWindow.document.write('<html><head><title>All Users</title>');
            printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
            printWindow.document.write('</head><body>');
            printWindow.document.write(data);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        })
        .catch(error => console.error('Error fetching all users:', error));
}
</script>
</body>
</html>
<?php include '/includes/footer.php'; ?>
