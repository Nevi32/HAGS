<?php
// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: login.html');
    exit;
}

// Fetch session data
$username = $_SESSION['username'];
$companyName = $_SESSION['companyName'];
$companyInitials = $_SESSION['companyInitials'];
$groups = $_SESSION['groups'];
$members = $_SESSION['members'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tables</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/dstyles.css">
    <link rel="stylesheet" href="styles/tables.css">
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
            <li><a href="groups.php"><i class="fas fa-users"></i><span class="menu-text">Groups</span></a></li>
            <li><a href="members.php"><i class="fas fa-users"></i><span class="menu-text">Members</span></a></li>
        </ul>
        <div class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a></div>
    </div>
    <div class="menu-icon"><i class="fas fa-bars"></i></div>
    <div class="content">
        <h1>Welcome Admin to <?php echo htmlspecialchars($companyName); ?> (<?php echo htmlspecialchars($companyInitials); ?>) Management System!</h1>
        <p>Please use the search and filter options below to view members and group data.</p>

        <!-- Search and filter bar -->
        <div class="search-bar">
            <input type="text" placeholder="Search..." id="searchInput">
            <i class="fas fa-search search-icon"></i>
            <select id="filterSelect">
                <option value="members">Members</option>
                <option value="groups">Groups</option>
            </select>
            <button onclick="applyFilter()">Filter</button>
        </div>
        
        <!-- Display members or groups based on selection -->
        <div class="members-or-groups" id="results">
            <!-- Initially show all members -->
            <?php foreach ($members as $member): ?>
                <div class="member">
                    <span><?php echo htmlspecialchars($member['FullName']); ?></span>
                    <!-- Add action icons -->
                    <span class="action-icons">
                        <i class="fas fa-trash-alt" onclick="deleteMember(<?php echo $member['MemberID']; ?>)"></i>
                        <i class="fas fa-info-circle" onclick="showMemberInfo(<?php echo $member['MemberID']; ?>)"></i>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function applyFilter() {
            var filter = document.getElementById("filterSelect").value;
            var searchInput = document.getElementById("searchInput").value.toLowerCase();
            var resultsContainer = document.getElementById("results");
            resultsContainer.innerHTML = '';

            if (filter === 'members') {
                <?php foreach ($members as $member): ?>
                    var memberName = "<?php echo htmlspecialchars($member['FullName']); ?>".toLowerCase();
                    if (memberName.includes(searchInput)) {
                        resultsContainer.innerHTML += `
                            <div class="member">
                                <span><?php echo htmlspecialchars($member['FullName']); ?></span>
                                <span class="action-icons">
                                    <i class="fas fa-trash-alt" onclick="deleteMember(<?php echo $member['MemberID']; ?>)"></i>
                                    <i class="fas fa-info-circle" onclick="showMemberInfo(<?php echo $member['MemberID']; ?>)"></i>
                                </span>
                            </div>
                        `;
                    }
                <?php endforeach; ?>
            } else if (filter === 'groups') {
                <?php foreach ($groups as $group): ?>
                    var groupName = "<?php echo htmlspecialchars($group['GroupName']); ?>".toLowerCase();
                    if (groupName.includes(searchInput)) {
                        resultsContainer.innerHTML += `
                            <div class="group">
                                <span><?php echo htmlspecialchars($group['GroupName']); ?></span>
                                <span class="action-icons">
                                    <i class="fas fa-trash-alt" onclick="deleteGroup(<?php echo $group['GroupID']; ?>)"></i>
                                    <i class="fas fa-info-circle" onclick="showGroupInfo(<?php echo $group['GroupID']; ?>)"></i>
                                </span>
                            </div>
                        `;
                    }
                <?php endforeach; ?>
            }
        }

        function deleteMember(memberID) {
            // Implement logic to delete member with given memberID
        }

        function deleteGroup(groupID) {
            // Implement logic to delete group with given groupID
        }

        function showMemberInfo(memberID) {
            // Implement logic to show detailed information about member with given memberID
        }

        function showGroupInfo(groupID) {
            // Implement logic to show detailed information about group with given groupID
        }
        document.querySelector('.menu-icon').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('small');
            document.querySelector('.content').classList.toggle('expanded');
        });
    </script>
</body>
</html>

