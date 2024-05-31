k<?php
// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: login.html');
    exit;
}

// Get session data
$username = $_SESSION['username'];
$companyName = $_SESSION['companyName'];
$companyInitials = $_SESSION['companyInitials'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tables</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/tables.css">
</head>
<body>
    <div class="sidebar">
        <div class="user-section">
            <div class="user-icon"><i class="fas fa-user"></i></div>
            <div class="username"><?php echo htmlspecialchars($username); ?></div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i><span class="menu-text">Home</span></a></li>
            <li><a href="tables.php"><i class="fas fa-table"></i><span class="menu-text">Tables</span></a></li>
            <li><a href="#"><i class="fas fa-users"></i><span class="menu-text">Groups</span></a></li>
            <li><a href="#"><i class="fas fa-users"></i><span class="menu-text">Members</span></a></li>
        </ul>
        <div class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a></div>
    </div>
    <div class="menu-icon"><i class="fas fa-bars"></i></div>
    <div class="content">
        <h1>Welcome Admin to <?php echo htmlspecialchars($companyName); ?> (<?php echo htmlspecialchars($companyInitials); ?>) Management System - Tables</h1>
        <div class="card">
            <div class="search-sort-bar">
                <div class="search-bar">
                    <input type="text" placeholder="Search...">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="sort-option">
                    <select>
                        <option value="members">Members</option>
                        <option value="groups">Groups</option>
                    </select>
                </div>
            </div>
            <div class="list">
                <div class="list-item">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="info">Group 1</div>
                    <div class="actions">
                        <i class="fas fa-trash"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
                <div class="list-item">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="info">Group 2</div>
                    <div class="actions">
                        <i class="fas fa-trash"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
                <div class="list-item">
                    <div class="icon"><i class="fas fa-user"></i></div>
                    <div class="info">Member 1</div>
                    <div class="actions">
                        <i class="fas fa-trash"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
                <div class="list-item">
                    <div class="icon"><i class="fas fa-user"></i></div>
                    <div class="info">Member 2</div>
                    <div class="actions">
                        <i class="fas fa-trash"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const menuIcon = document.querySelector('.menu-icon');
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content');

        menuIcon.addEventListener('click', function() {
            sidebar.classList.toggle('small');
            content.classList.toggle('shifted');
        });

        const sortOption = document.querySelector('.sort-option select');
        const listItems = document.querySelectorAll('.list-item');

        sortOption.addEventListener('change', function() {
            const selected = this.value;
            listItems.forEach(item => {
                if (selected === 'members') {
                    item.style.display = item.querySelector('.icon .fa-user') ? 'flex' : 'none';
                } else {
                    item.style.display = item.querySelector('.icon .fa-users') ? 'flex' : 'none';
                }
            });
        });
    </script>
</body>
</html>

