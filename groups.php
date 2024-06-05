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
$groups = $_SESSION['groupinfo'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/dstyles.css">
    <link rel="stylesheet" href="styles/tables.css">
    <style>
        /* Additional styles for the popup */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%; /* Adjusted width for responsiveness */
            max-height: 80%; /* Maximum height for scrolling */
            overflow-y: auto; /* Enable vertical scrolling */
            background: white;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 20px;
        }
        .popup-header {
            display: flex;
            justify-content: flex-end;
        }
        .popup-header .close {
            cursor: pointer;
            font-size: 1.5em;
        }
        .popup-content {
            padding: 20px;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
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
            <li><a href="projects.php"><i class="fas fa-projects"></i><span class="menu-text">Projects</span></a></li>
        </ul>
        <div class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a></div>
    </div>
    <div class="menu-icon"><i class="fas fa-bars"></i></div>
    <div class="content">
        <h1>Welcome Admin to <?php echo htmlspecialchars($companyName); ?> (<?php echo htmlspecialchars($companyInitials); ?>) Management System!</h1>
        <p>Please use the search and filter options below to view groups and member data.</p>

        <!-- Search and filter bar -->
        <div class="search-bar">
            <input type="text" placeholder="Search by group name..." id="searchInput">
            <i class="fas fa-search search-icon"></i>
            <select id="filterSelect">
                <option value="all">All Areas</option>
                <option value="county">County</option>
                <option value="subcounty">SubCounty</option>
                <option value="ward">Ward</option>
                <option value="location">Location</option>
                <option value="sublocation">SubLocation</option>
                <option value="village">Village</option>
            </select>
            <input type="text" placeholder="Filter value..." id="filterValue">
            <button onclick="applyFilter()">Filter</button>
        </div>
        
        <!-- Display groups -->
        <div class="members-or-groups" id="results">
            <!-- Initially show all groups -->
            <?php foreach ($groups as $group): ?>
                <div class="group">
                    <span><?php echo htmlspecialchars($group['GroupName']); ?></span>
                    <!-- Add action icons -->
                    <span class="action-icons">
                        <i class="fas fa-trash-alt" onclick="deleteGroup('<?php echo $group['GroupID']; ?>')"></i>
                        <i class="fas fa-info-circle" onclick="showGroupInfo('<?php echo $group['GroupID']; ?>')"></i>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Popup overlay and container -->
    <div class="popup-overlay" id="popupOverlay"></div>
    <div class="popup" id="infoPopup">
        <div class="popup-header">
            <span class="close" onclick="closePopup()">&times;</span>
        </div>
        <div class="popup-content" id="popupContent"></div>
    </div>

    <script>
        function applyFilter() {
            var filterType = document.getElementById("filterSelect").value;
            var filterValue = document.getElementById("filterValue").value.toLowerCase();
            var searchInput = document.getElementById("searchInput").value.toLowerCase();
            var resultsContainer = document.getElementById("results");
            resultsContainer.innerHTML = '';

            <?php foreach ($groups as $group): ?>
                var groupName = "<?php echo htmlspecialchars($group['GroupName']); ?>".toLowerCase();
                var groupArea = "<?php echo htmlspecialchars($group['Area'][$filterType] ?? ''); ?>".toLowerCase();

                if (groupName.includes(searchInput) && (filterType === 'all' || groupArea.includes(filterValue))) {
                    resultsContainer.innerHTML += `
                        <div class="group">
                            <span><?php echo htmlspecialchars($group['GroupName']); ?></span>
                            <span class="action-icons">
                                <i class="fas fa-trash-alt" onclick="deleteGroup('<?php echo $group['GroupID']; ?>')"></i>
                                <i class="fas fa-info-circle" onclick="showGroupInfo('<?php echo $group['GroupID']; ?>')"></i>
                            </span>
                        </div>
                    `;
                }
            <?php endforeach; ?>
        }

        function deleteGroup(groupID) {
            // Implement logic to delete group with given groupID
        }

         function showGroupInfo(groupID) {
    var groupinfo = <?php echo json_encode($_SESSION['groupinfo']); ?>;
    var group = groupinfo[groupID];
    if (group) {
        var membersList = group.Members.map(member => `
            <li>
                <p><strong>Full Name:</strong> ${member.FullName}</p>
                <p><strong>Contact:</strong> ${member.Contact}</p>
                <p><strong>Member ID:</strong> ${member.MemberID}</p>
                <p><strong>Status:</strong> ${member.Status}</p>
                <p><strong>County:</strong> ${member.Area.County}</p>
                <p><strong>SubCounty:</strong> ${member.Area.SubCounty}</p>
                <p><strong>Ward:</strong> ${member.Area.Ward}</p>
                <p><strong>Location:</strong> ${member.Area.Location}</p>
                <p><strong>SubLocation:</strong> ${member.Area.SubLocation}</p>
                <p><strong>Village:</strong> ${member.Area.Village}</p>
            </li>
        `).join('');
        var content = `<h2>${group.GroupName}</h2>
                       <p><strong>Group ID:</strong> ${group.GroupID}</p>
                       <h3>Members</h3>
                       <ul>${membersList}</ul>`;
        showPopup(content);
    } else {
        console.error("Group not found with ID:", groupID);
    }
}
function showGroupInfo(groupID) {
    var groupinfo = <?php echo json_encode($_SESSION['groupinfo']); ?>;
    var group = groupinfo[groupID];
    if (group) {
        var membersList = group.Members.map(member => `
            <li>
                <p><strong>Full Name:</strong> ${member.FullName}</p>
                <p><strong>Contact:</strong> ${member.Contact}</p>
                <p><strong>Member ID:</strong> ${member.MemberID}</p>
                <p><strong>Status:</strong> ${member.Status}</p>
                <p><strong>County:</strong> ${member.Area.County}</p>
                <p><strong>SubCounty:</strong> ${member.Area.SubCounty}</p>
                <p><strong>Ward:</strong> ${member.Area.Ward}</p>
                <p><strong>Location:</strong> ${member.Area.Location}</p>
                <p><strong>SubLocation:</strong> ${member.Area.SubLocation}</p>
                <p><strong>Village:</strong> ${member.Area.Village}</p>
            </li>
        `).join('');
        var content = `<h2>${group.GroupName}</h2>
                       <p><strong>Group ID:</strong> ${group.GroupID}</p>
                       <h3>Members</h3>
                       <ul>${membersList}</ul>`;
        showPopup(content);
    } else {
        console.error("Group not found with ID:", groupID);
    }
}


        function showPopup(content) {
            document.getElementById('popupContent').innerHTML = content;
            document.getElementById('popupOverlay').style.display = 'block';
            document.getElementById('infoPopup').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('popupOverlay').style.display = 'none';
            document.getElementById('infoPopup').style.display = 'none';
        }

        document.querySelector('.menu-icon').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('small');
            document.querySelector('.content').classList.toggle('expanded');
        });
    </script>
</body>
</html>

