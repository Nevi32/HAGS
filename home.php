<?php
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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/dstyles.css">
</head>
<body>
    <div class="sidebar">
        <div class="user-section">
            <div class="user-icon"><i class="fas fa-user"></i></div>
            <div class="username"><?php echo htmlspecialchars($username); ?></div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="home.php"><i class="fas fa-home"></i><span class="menu-text">Home</span></a></li>
            <li><a href="tables.php"><i class="fas fa-table"></i><span class="menu-text">Tables</span></a></li>
            <li><a href="fetchgroups.php"><i class="fas fa-users"></i><span class="menu-text">Groups</span></a></li>
            <li><a href="fetchmembers.php"><i class="fas fa-users"></i><span class="menu-text">Members</span></a></li>
            <li><a href="projects.php"><i class="fas fa-projects"></i><span class="menu-text">Projects</span></a></li> 
       </ul>
        <div class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a></div>
    </div>
    <div class="menu-icon"><i class="fas fa-bars"></i></div>
    <div class="content">
        <h1>Welcome Admin to <?php echo htmlspecialchars($companyName); ?> (<?php echo htmlspecialchars($companyInitials); ?>) Management System!</h1>
        <div class="card registration" onclick="window.location.href='registration.php'">
            <div class="icon"><i class="fas fa-clipboard"></i></div>
            <div class="info">Manage Registrations</div>
        </div>
        <div class="card tables" onclick="window.location.href='fetchtables.php'">
            <div class="icon"><i class="fas fa-table"></i></div>
            <div class="info">View Tables</div>
        </div>
        <div class="card analysis" onclick="window.location.href='analysis.php'">
            <div class="icon"><i class="fas fa-chart-bar"></i></div>
            <div class="info">Data Analysis</div>
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
    </script>
</body>
</html>

