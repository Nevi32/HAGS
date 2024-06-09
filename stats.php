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
    <title>Stats Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/stats.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <script>
    async function fetchStats() {
        try {
            const response = await fetch('fetchstats.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            
            // Check for PHP error messages in the response
            if (data.message) {
                console.error('Server error:', data.message);
                return;
            }

            document.getElementById('totalGroups').textContent = data.totalGroups;
            document.getElementById('totalMembers').textContent = data.totalMembers;
            document.getElementById('totalProjects').textContent = data.totalProjects;
            document.getElementById('totalDepositPaid').textContent = data.totalDepositPaid;
            document.getElementById('totalCountingPayments').textContent = data.totalCountingPayments;

            const seedlingsData = data.seedlingsData;
            const counties = [...new Set(seedlingsData.map(item => item.County))];
            const subCounties = [...new Set(seedlingsData.map(item => item.SubCounty))];

            const countySelect = document.getElementById('countySelect');
            const subCountySelect = document.getElementById('subCountySelect');

            countySelect.innerHTML = '<option value="">Select County</option>';
            subCountySelect.innerHTML = '<option value="">Select Sub-County</option>';

            counties.forEach(county => {
                const option = document.createElement('option');
                option.value = county;
                option.textContent = county;
                countySelect.appendChild(option);
            });

            subCounties.forEach(subCounty => {
                const option = document.createElement('option');
                option.value = subCounty;
                option.textContent = subCounty;
                subCountySelect.appendChild(option);
            });

            drawCharts(seedlingsData);
        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    }

    function drawCharts(seedlingsData) {
        const countySelect = document.getElementById('countySelect').value;
        const subCountySelect = document.getElementById('subCountySelect').value;

        const filteredData = seedlingsData.filter(item => 
            (!countySelect || item.County === countySelect) && 
            (!subCountySelect || item.SubCounty === subCountySelect));

        const seedlingVarieties = filteredData.map(item => item.VarietyOfSeedlings);
        const seedlingsCount = filteredData.map(item => item.seedlingsCount);

        const ctxPie = document.getElementById('pieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: seedlingVarieties,
                datasets: [{
                    data: seedlingsCount,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']
                }]
            }
        });

        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: seedlingVarieties,
                datasets: [{
                    label: 'Seedlings Count',
                    data: seedlingsCount,
                    backgroundColor: '#36A2EB'
                }]
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchStats();

        document.getElementById('countySelect').addEventListener('change', () => fetchStats());
        document.getElementById('subCountySelect').addEventListener('change', () => fetchStats());
    });
</script>

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
        <h1>Company Stats for <?php echo htmlspecialchars($companyName); ?> (<?php echo htmlspecialchars($companyInitials); ?>)</h1>
        <div class="stats-cards">
            <div class="card">
                <h3>Total Groups</h3>
                <p id="totalGroups">0</p>
            </div>
            <div class="card">
                <h3>Total Members</h3>
                <p id="totalMembers">0</p>
            </div>
            <div class="card">
                <h3>Total Projects</h3>
                <p id="totalProjects">0</p>
            </div>
            <div class="card">
                <h3>Total Deposit Paid (Ksh)</h3>
                <p id="totalDepositPaid">0</p>
            </div>
            <div class="card">
                <h3>Total Counting Payments</h3>
                <p id="totalCountingPayments">0</p>
            </div>
        </div>

        <h2>Seedlings Distribution</h2>
        <div class="filter-group">
            <label for="countySelect">County:</label>
            <select id="countySelect">
                <option value="">Select County</option>
            </select>
            <label for="subCountySelect">Sub-County:</label>
            <select id="subCountySelect">
                <option value="">Select Sub-County</option>
            </select>
        </div>
        <div class="charts">
            <canvas id="pieChart"></canvas>
            <canvas id="barChart"></canvas>
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

