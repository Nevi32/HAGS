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
    <title>Projects Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/registration.css">
    <style>
        .search-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-container input {
            width: 100%;
            padding: 10px;
            margin-right: 10px;
        }

        .search-container button {
            padding: 10px;
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
            <li><a href="fetchgroups.php"><i class="fas fa-users"></i><span class="menu-text">Groups</span></a></li>
            <li><a href="fetchmembers.php"><i class="fas fa-users"></i><span class="menu-text">Members</span></a></li>
        </ul>
        <div class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a></div>
    </div>
    <div class="menu-icon"><i class="fas fa-bars"></i></div>
    <div class="content">
        <h1>Welcome to the <?php echo htmlspecialchars($companyName); ?> (<?php echo htmlspecialchars($companyInitials); ?>) Management System!</h1>
        <h2>Please provide the project information below:</h2>
        
        <form id="projectsForm" action="adjustprojects.php" method="post">
            <h3>Project Information</h3>
            <div class="form-group">
                <label for="groupName">Group Name</label>
                <select id="groupName" name="groupName" required>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label for="memberName">Member Name</label>
                <select id="memberName" name="memberName" required>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label for="memberID">Member ID</label>
                <select id="memberID" name="memberID" required>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label for="projectName">Project Name</label>
                <input type="text" id="projectName" name="projectName" required>
            </div>
            <div class="form-group">
                <label for="projectDescription">Project Description</label>
                <textarea id="projectDescription" name="projectDescription" required></textarea>
            </div>
            <div class="form-group">
                <label for="startDate">Start Date</label>
                <input type="date" id="startDate" name="startDate" required>
            </div>
            <div class="form-group">
                <label for="endDate">End Date</label>
                <input type="date" id="endDate" name="endDate" required>
            </div>
            <div class="form-group">
                <label for="budget">Budget (Ksh)</label>
                <input type="number" step="0.01" id="budget" name="budget" required>
            </div>
            <button type="submit">Submit Project</button>
        </form>
        
        <h2>View Current Projects</h2>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search by Group Name...">
            <button onclick="searchProjects()"><i class="fas fa-search"></i></button>
        </div>
        <div id="projectsInfo"></div>
    </div>
    <script>
        const menuIcon = document.querySelector('.menu-icon');
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content');

        menuIcon.addEventListener('click', function() {
            sidebar.classList.toggle('small');
            content.classList.toggle('shifted');
        });

        async function fetchGroupsAndMembers() {
            try {
                const response = await fetch('fetchgroupsandmembers.php');
                const data = await response.json();

                const groupSelect = document.getElementById('groupName');
                const memberSelect = document.getElementById('memberName');
                const memberIDSelect = document.getElementById('memberID');

                // Use a Set to keep track of unique group names
                const groupNames = new Set();

                data.groups.forEach(group => {
                    if (!groupNames.has(group.GroupName)) {
                        groupNames.add(group.GroupName);
                        const option = document.createElement('option');
                        option.value = group.GroupID;
                        option.textContent = group.GroupName;
                        groupSelect.appendChild(option);
                    }
                });

                data.members.forEach(member => {
                    const nameOption = document.createElement('option');
                    nameOption.value = member.MemberID;
                    nameOption.textContent = member.FullName;
                    memberSelect.appendChild(nameOption);

                    const idOption = document.createElement('option');
                    idOption.value = member.MemberID;
                    idOption.textContent = member.MemberID;
                    memberIDSelect.appendChild(idOption);
                });
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function viewProjects() {
            try {
                const response = await fetch('fetchprojectsinfo.php');
                const result = await response.json();
                displayProjects(result);
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('projectsInfo').innerHTML = 'An error occurred while fetching project information.';
            }
        }

        function displayProjects(projects) {
            const projectsInfoDiv = document.getElementById('projectsInfo');

            if (projects.length > 0) {
                projectsInfoDiv.innerHTML = '';
                projects.forEach(project => {
                    const projectDiv = document.createElement('div');
                    projectDiv.className = 'project';
                    projectDiv.innerHTML = `
                        <p><strong>Group Name:</strong> ${project.GroupName}</p>
                        <p><strong>Member Name:</strong> ${project.MemberFullName}</p>
                        <p><strong>Member ID:</strong> ${project.MemberID}</p>
                        <p><strong>Project ID:</strong> ${project.ProjectID}</p>
                        <p><strong>Variety of Seedlings:</strong> ${project.VarietyOfSeedlings}</p>
                        <p><strong>Number of Seedlings Ordered:</strong> ${project.NumberOfSeedlingsOrdered}</p>
                        <p><strong>Amount to be Paid:</strong> ${project.AmountToBePaid}</p>
                        <p><strong>Deposit Paid:</strong> ${project.DepositPaid}</p>
                        <p><strong>Balance:</strong> ${project.Balance}</p>
                        <p><strong>Date of Payment:</strong> ${project.DateOfPayment}</p>
                        <p><strong>Date to Complete Payment:</strong> ${project.DateToCompletePayment}</p>
                    `;
                    projectsInfoDiv.appendChild(projectDiv);
                });
            } else {
                projectsInfoDiv.innerHTML = 'No project information available.';
            }
        }

        async function searchProjects() {
            const searchInput = document.getElementById('searchInput').value;
            try {
                const response = await fetch(`fetchprojectsinfo.php?groupName=${encodeURIComponent(searchInput)}`);
                const result = await response.json();
                displayProjects(result);
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('projectsInfo').innerHTML = 'An error occurred while searching project information.';
            }
        }

        // Fetch groups and members when the page loads
        window.onload = fetchGroupsAndMembers;
    </script>
</body>
</html>

