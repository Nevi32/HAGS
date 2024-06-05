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

        .form-section {
            margin-bottom: 20px;
        }

        .form-section h3 {
            cursor: pointer;
        }

        .form-section-content {
            display: none;
        }

        .form-section.active .form-section-content {
            display: block;
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
        
        <div class="form-section" id="newProjectsSection">
            <h3 onclick="toggleSection('newProjectsSection')">New Projects</h3>
            <div class="form-section-content">
                <form id="newProjectsForm" action="adjustprojects.php" method="post">
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
                        <label for="varietyOfSeedlings">Variety of Seedlings</label>
                        <input type="text" id="varietyOfSeedlings" name="varietyOfSeedlings" required>
                    </div>
                    <div class="form-group">
                        <label for="numberOfSeedlingsOrdered">Number of Seedlings Ordered</label>
                        <input type="number" id="numberOfSeedlingsOrdered" name="numberOfSeedlingsOrdered" required>
                    </div>
                    <div class="form-group">
                        <label for="amountToBePaid">Amount to be Paid (Ksh)</label>
                        <input type="number" step="0.01" id="amountToBePaid" name="amountToBePaid" required>
                    </div>
                    <div class="form-group">
                        <label for="depositPaid">Deposit Paid (Ksh)</label>
                        <input type="number" step="0.01" id="depositPaid" name="depositPaid" required>
                    </div>
                    <div class="form-group">
                        <label for="balance">Balance (Ksh)</label>
                        <input type="number" step="0.01" id="balance" name="balance" required>
                    </div>
                    <div class="form-group">
                        <label for="dateOfPayment">Date of Payment</label>
                        <input type="date" id="dateOfPayment" name="dateOfPayment" required>
                    </div>
                    <div class="form-group">
                        <label for="dateToCompletePayment">Date to Complete Payment</label>
                        <input type="date" id="dateToCompletePayment" name="dateToCompletePayment" required>
                    </div>
                    <button type="submit">Submit New Project</button>
                </form>
            </div>
        </div>

        <div class="form-section" id="currentProjectsSection">
            <h3 onclick="toggleSection('currentProjectsSection')">Current Projects</h3>
            <div class="form-section-content">
                <form id="currentProjectsForm" action="adjustprojects.php" method="post">
                    <h3>Current Project Information</h3>
                    <div class="form-group">
                        <label for="currentProjectID">Project ID</label>
                        <select id="currentProjectID" name="currentProjectID" required>
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="countingPayments">Counting Payments</label>
                        <input type="number" step="0.01" id="countingPayments" name="countingPayments" required>
                    </div>
                    <div class="form-group">
                        <label for="countingPaymentDates">Counting Payment Dates</label>
                        <input type="date" id="countingPaymentDates" name="countingPaymentDates" required>
                    </div>
                    <button type="submit">Submit Current Project Info</button>
                </form>
            </div>
        </div>
        <h2>View Current Projects</h2>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search by Group Name or Member Name...">
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
                const projectIDSelect = document.getElementById('currentProjectID');

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

                data.projects.forEach(project => {
                    const projectIDOption = document.createElement('option');
                    projectIDOption.value = project.ProjectID;
                    projectIDOption.textContent = project.ProjectID;
                    projectIDSelect.appendChild(projectIDOption);
                });
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function viewProjects() {
            try {
                const response = await fetch('fetchcurrentprojects.php');
                const result = await response.json();
                displayProjects(result.projects);
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function displayProjects(projects) {
            const projectsInfo = document.getElementById('projectsInfo');
            projectsInfo.innerHTML = '';

            if (projects.length === 0) {
                projectsInfo.textContent = 'No projects found.';
                return;
            }

            const table = document.createElement('table');
            table.border = 1;

            const headerRow = document.createElement('tr');
            ['Group Name', 'Member ID', 'Member Name', 'Project ID', 'Variety of Seedlings', 'Number of Seedlings Ordered', 'Amount to be Paid', 'Deposit Paid', 'Balance', 'Date of Payment', 'Date to Complete Payment'].forEach(header => {
                const th = document.createElement('th');
                th.textContent = header;
                headerRow.appendChild(th);
            });
            table.appendChild(headerRow);

            projects.forEach(project => {
                const row = document.createElement('tr');
                Object.values(project).forEach(value => {
                    const cell = document.createElement('td');
                    cell.textContent = value;
                    row.appendChild(cell);
                });
                table.appendChild(row);
            });

            projectsInfo.appendChild(table);
        }

        function searchProjects() {
            const searchInput = document.getElementById('searchInput').value.trim();
            if (searchInput === '') {
                alert('Please enter a group name or member name to search.');
                return;
            }

            let url = 'fetchcurrentprojects.php?';
            if (searchInput.includes(' ')) {
                url += `memberName=${encodeURIComponent(searchInput)}`;
            } else {
                url += `groupName=${encodeURIComponent(searchInput)}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => displayProjects(data.projects))
                .catch(error => console.error('Error:', error));
        }

        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            section.classList.toggle('active');
        }

        // Initial fetch for groups and members
        fetchGroupsAndMembers();
    </script>
</body>
</html>

