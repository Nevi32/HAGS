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
    <style>
        /* Additional styles for the popup */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50%;
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
                        <i class="fas fa-trash-alt" onclick="deleteMember('<?php echo $member['MemberID']; ?>')"></i>
                        <i class="fas fa-info-circle" onclick="showMemberInfo('<?php echo $member['MemberID']; ?>')"></i>
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
                                    <i class="fas fa-trash-alt" onclick="deleteMember('<?php echo $member['MemberID']; ?>')"></i>
                                    <i class="fas fa-info-circle" onclick="showMemberInfo('<?php echo $member['MemberID']; ?>')"></i>
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
                                    <i class="fas fa-trash-alt" onclick="deleteGroup('<?php echo $group['GroupID']; ?>')"></i>
                                    <i class="fas fa-info-circle" onclick="showGroupInfo('<?php echo $group['GroupID']; ?>')"></i>
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
            var members = <?php echo json_encode($members); ?>;
            var member = members.find(m => m.MemberID == memberID);
            var group = <?php echo json_encode($groups); ?>.find(g => g.GroupID == member.GroupID);
            if (member && group) {
                var content = `<h2>${member.FullName}</h2>
                               <p><strong>Group:</strong> ${group.GroupName}</p>
                               <p><strong>Group ID:</strong> ${member.GroupID}</p>
                               <p><strong>National ID:</strong> ${member.NationalID}</p>
                               <p><strong>Contact:</strong> ${member.Contact}</p>
                               <p><strong>Member Unique ID:</strong> ${member.MemberUniqueID}</p>
                               <p><strong>Terms Accepted:</strong> ${member.TermsAccepted}</p>
                               <p><strong>Date Of Admission:</strong> ${member.DateOfAdmission}</p>
                               <p><strong>Next Of Kin:</strong> ${member.NextOfKin}</p>
                               <p><strong>Next Of Kin Contact:</strong> ${member.NextOfKinContact}</p>
                               <p><strong>Next Of Kin Terms Accepted:</strong> ${member.NextOfKinTermsAccepted}</p>`;
                showPopup(content);
            }
        }

        function showGroupInfo(groupID) {
            var groups = <?php echo json_encode($groups); ?>;
            var group = groups.find(g => g.GroupID == groupID);
            if (group) {
                var membersInGroup = <?php echo json_encode($members); ?>.filter(member => member.GroupID == groupID);
                var membersList = membersInGroup.map(member => `<li>${member.FullName}</li>`).join('');
                var content = `<h2>${group.GroupName}</h2>
                               <div class="group-icon"><i class="fas fa-users"></i></div>
                               <p><strong>Group ID:</strong> ${group.GroupID}</p>
                               <h3>Members</h3>
                               <ul>${membersList}</ul>`;
                showPopup(content);
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

                                    
