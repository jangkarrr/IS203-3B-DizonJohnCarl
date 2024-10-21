<?php
session_start();
include '/db.php';
include '/includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $query = "SELECT username, email, role, profile_data FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "<div class='alert alert-danger'>User not found!</div>";
        exit;
    }
    
    $user = $result->fetch_assoc();
    $profile_data = json_decode($user['profile_data'], true);
} else {
    echo "<div class='alert alert-danger'>No user ID provided!</div>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    $bio = isset($_POST['bio']) ? $_POST['bio'] : '';
    $profile_data = json_encode(['bio' => $bio]);

    $update_query = "UPDATE users SET username = ?, email = ?, role = ?, profile_data = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssi", $username, $email, $role, $profile_data, $user_id);
    
    if ($update_stmt->execute()) {
        echo "<div class='alert alert-success'>User updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating user: " . $update_stmt->error . "</div>";
    }
}
?>

<div class="container mt-5">
    <h2>Edit User</h2>
    
    <form method="post" action="edit_user.php?id=<?php echo $user_id; ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" name="role" required>
                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea class="form-control" name="bio"><?php echo htmlspecialchars($profile_data['bio']); ?></textarea>
        </div>
        <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
    </form>
</div>

<?php include '/includes/footer.php'; ?>
