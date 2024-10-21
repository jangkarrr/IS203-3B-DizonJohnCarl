<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .sidebar {
            background: linear-gradient(90deg, #007bff, #00c6ff);
            height: 100vh;
            width: 230px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        .sidebar .navbar-brand, .sidebar .nav-link {
            color: white !important;
            transition: color 0.3s;
            border-radius: 20px;
            padding: 10px 15px;
            display: block;
        }
        .sidebar .nav-item {
            margin-bottom: 15px;
            margin-left: 20px;
        }
        .sidebar .nav-link:hover {
            color: #ffe600 !important;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }
        .content {
            margin-left: 270px;
            padding: 30px;
            transition: margin-left 0.3s ease;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .hamburger {
            cursor: pointer;
            font-size: 24px;
            position: absolute;
            right: 10px;
            top: 5px;
            z-index: 1001;
        }
        @media (min-width: 700px) {
        .hamburger {
            display: none;
        }
    }
        
        @media (max-width: 700px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                transform: translateX(-100%);
                
            }
            .content {
                margin-left: 0;
            }
            .sidebar.collapsed {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
    <h3 class="navbar-brand" >JANGKAR'S LIBRARY</h3>
        <div class="navbar-brand">
            <?php if (isset($_SESSION['username'])): ?>
                <span>WELCOME, <?php echo $_SESSION['username']; ?>!</span>
            <?php endif; ?>
        </div>
        <ul class="navbar-nav">
        <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">📝 Dashboard</a>
                    </li>
                <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">👤 Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_borrowed_books.php">📚 My Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="borrow_books.php">📖 Borrow Books</a>
                </li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_books.php">📖 Manage Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users.php">👥 Manage Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_return_book.php">🔄 Return Books</a>
                    </li>
                <?php endif; ?>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">🔑 Login</a>
                </li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" onclick="return confirmLogout()">🚪 Logout</a>
                </li>
            <?php endif; ?>
        </ul>
        <script>
            function confirmLogout() {
                return confirm("Are you sure you want to logout?");
            }

            function toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('collapsed');
                document.querySelector('.content').classList.toggle('moved');
            }
        </script>
    </div>

    <div class="content">
        <div class="hamburger" onclick="toggleSidebar()">☰</div>