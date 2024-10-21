<?php
include '/db.php';

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
    GROUP BY users.id";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<h2>All Users</h2>";
    echo "<table class='table'><thead><tr><th>Username</th><th>Email</th><th>Role</th><th>Created At</th><th>Loans</th></tr></thead><tbody>";
    while ($user = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($user['username']) . "</td>
                <td>" . htmlspecialchars($user['email']) . "</td>
                <td>" . htmlspecialchars($user['role']) . "</td>
                <td>" . htmlspecialchars($user['created_at']) . "</td>
                <td>" . ($user['loans'] ? htmlspecialchars($user['loans']) : 'No loans') . "</td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No users found.</p>";
}
?>
