<?php
include '/db.php';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    $query = "
        SELECT 
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
        WHERE users.id = ?
        GROUP BY users.id";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo "<h2>User Information</h2>";
        echo "<p><strong>Username:</strong> " . htmlspecialchars($user['username']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
        echo "<p><strong>Role:</strong> " . htmlspecialchars($user['role']) . "</p>";
        echo "<p><strong>Created At:</strong> " . htmlspecialchars($user['created_at']) . "</p>";
        echo "<h4>Loans:</h4>";
        echo $user['loans'] ? htmlspecialchars($user['loans']) : 'No loans';
    } else {
        echo "<p>No user found.</p>";
    }
} else {
    echo "<p>No user ID provided.</p>";
}
?>
