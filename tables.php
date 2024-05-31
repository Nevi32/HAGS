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
$groups = isset($_SESSION['groups']) ? $_SESSION['groups'] : [];
$members = isset($_SESSION['members']) ? $_SESSION['members'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            <li><a href="#"><i class="fas fa-home"></i><span class="menu-text">Home</span></a></li>
            <li><a href="#"><i class="fas fa-table"></i><span class="menu-text">Tables</span></a></li>
            <li><a href="#"><i class="fas fa-users"></i><span class="menu-text">Groups</span></a></li>
            <li><a href="#"><i class="fas fa-users"></i><span class="menu-text">Members</span></a></li>
        </ul>
        <div class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a></div>
    </div>
    <div class="menu-icon"><i class="fas fa-bars"></i></div>
    <div class="content">
        <h1>Welcome Admin to <?php echo htmlspecialchars($companyName); ?> (<?php echo htmlspecialchars($companyInitials); ?>) Management System!</h1>
        <div class="search-sort-bar">
            <div class="search-bar">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search...">
            </div>
            <div class="sort-option">
                <select id="sortOption">
                    <option value="groups">Groups</option>
                    <option value="members">Members</option>
                </select>
            </div>
        </div>
        <div class="list" id="list">
            <!-- List items will be dynamically inserted here -->
        </div>
    </div>
    <div class="popup" id="popup">
        <div class="popup-header">
            <h2 id="popupTitle"></h2>
            <span class="popup-close" id="popupClose">&times;</span>
        </div>
        <div class="popup-content" id="popupContent">
            <!-- Popup content will be dynamically inserted here -->
        </div>
    </div>
    <div class="overlay" id="overlay"></div>
    <script>
        const groups = <?php echo json_encode($groups); ?>;
        const members = <?php echo json_encode($members); ?>;
        const list = document.getElementById('list');
        const searchInput = document.getElementById('searchInput');
        const sortOption = document.getElementById('sortOption');
        const popup = document.getElementById('popup');
        const overlay = document.getElementById('overlay');
        const popupTitle = document.getElementById('popupTitle');
        const popupContent = document.getElementById('popupContent');
        const popupClose = document.getElementById('popupClose');

        function displayList(items) {
            list.innerHTML = '';
            items.forEach(item => {
                const listItem = document.createElement('div');
                listItem.className = 'list-item';
                listItem.dataset.id = item.GroupID || item.MemberID;
                listItem.dataset.type = item.GroupID ? 'group' : 'member';
                listItem.innerHTML = `
                    <div class="icon"><i class="fas ${item.GroupID ? 'fa-users' : 'fa-user'}"></i></div>
                    <div class="info">${item.GroupID ? item.GroupName : item.FullName}</div>
                    <div class="actions">
                        <i class="fas fa-trash"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                `;
                list.appendChild(listItem);
            });
        }

        function filterItems() {
            const query = searchInput.value.toLowerCase();
            const type = sortOption.value;
            const items = type === 'groups' ? groups : members;
            const filteredItems = items.filter(item => {
                const name = item.GroupName || item.FullName;
                return name.toLowerCase().includes(query);
            });
            displayList(filteredItems);
        }

        function showPopup(type, id) {
            const item = (type === 'group' ? groups : members).find(item => item.GroupID === id || item.MemberID === id);
            popupTitle.textContent = type === 'group' ? item.GroupName : item.FullName;
            popupContent.innerHTML = `
                <p><strong>ID:</strong> ${type === 'group' ? item.GroupID : item.MemberID}</p>
                ${type === 'group' ? '' : `
                    <p><strong>National ID:</strong> ${item.NationalID}</p>
                    <p><strong>Contact:</strong> ${item.Contact}</p>
                    <p><strong>Member Unique ID:</strong> ${item.MemberUniqueID}</p>
                    <p><strong>Terms Accepted:</strong> ${item.TermsAccepted ? 'Yes' : 'No'}</p>
                    <p><strong>Date of Admission:</strong> ${item.DateOfAdmission}</p>
                    <p><strong>Next of Kin:</strong> ${item.NextOfKin}</p>
                    <p><strong>Next of Kin Contact:</strong> ${item.NextOfKinContact}</p>
                    <p><strong>Next of Kin Terms Accepted:</strong> ${item.NextOfKinTermsAccepted ? 'Yes' : 'No'}</p>
                `}
            `;
            popup.classList.add('active');
            overlay.classList.add('active');
        }

        list.addEventListener('click', e => {
            const listItem = e.target.closest('.list-item');
            if (listItem) {
                const id = listItem.dataset.id;
                const type = listItem.dataset.type;
                showPopup(type, id);
            }
        });

        popupClose.addEventListener('click', () => {
            popup.classList.remove('active');
            overlay.classList.remove('active');
        });

        overlay.addEventListener('click', () => {
            popup.classList.remove('active');
            overlay.classList.remove('active');
        });

        searchInput.addEventListener('input', filterItems);
        sortOption.addEventListener('change', filterItems);

        // Initial display
        displayList(groups);
    </script>
</body>
</html>

