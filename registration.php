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
    <title>Registration Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/registration.css">
    <script>
        function generateUniqueID() {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let uniqueID = '';
            for (let i = 0; i < 10; i++) {
                uniqueID += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            document.getElementById('memberUniqueID').value = uniqueID;
        }

        window.onload = function() {
            generateUniqueID();
        }

        async function submitForm(event) {
            event.preventDefault();
            const form = document.getElementById('registrationForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('backend/reg.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    alert('Member registered successfully!');
                    form.reset();
                    generateUniqueID();
                } else {
                    alert('Registration failed: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while registering the member.');
            }
        }
    </script>
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
        <h1>Welcome to the <?php echo htmlspecialchars($companyName); ?> (<?php echo htmlspecialchars($companyInitials); ?>) Management System!</h1>
        <h2>Please register the new member below:</h2>
        <form id="registrationForm" onsubmit="submitForm(event)">
            <h3>Personal Information</h3>
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" required>
            </div>
            <div class="form-group">
                <label for="nationalID">National ID</label>
                <input type="text" id="nationalID" name="nationalID" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact</label>
                <div class="input-group">
                    <span class="input-group-text">+254</span>
                    <input type="text" id="contact" name="contact" pattern="[0-9]{9}" required>
                </div>
            </div>
            <div class="form-group">
                <label for="groupName">Group Name</label>
                <input type="text" id="groupName" name="groupName" required>
            </div>
            <div class="form-group">
                <label for="memberUniqueID">Member Unique ID</label>
                <input type="text" id="memberUniqueID" name="memberUniqueID" readonly>
            </div>
            <div class="form-group">
                <label for="dateOfAdmission">Date of Admission</label>
                <input type="date" id="dateOfAdmission" name="dateOfAdmission" required>
            </div>
            <div class="form-group">
                <label for="nextOfKin">Next of Kin</label>
                <input type="text" id="nextOfKin" name="nextOfKin" required>
            </div>
            <div class="form-group">
                <label for="nextOfKinContact">Next of Kin Contact</label>
                <div class="input-group">
                    <span class="input-group-text">+254</span>
                    <input type="text" id="nextOfKinContact" name="nextOfKinContact" pattern="[0-9]{9}" required>
                </div>
            </div>

            <h3>Project Information</h3>
            <div class="form-group">
                <label for="varietyOfSeedlings">Variety of Seedlings</label>
                <input type="text" id="varietyOfSeedlings" name="varietyOfSeedlings">
            </div>
            <div class="form-group">
                <label for="numberOfSeedlingsOrdered">Number of Seedlings Ordered</label>
                <input type="number" id="numberOfSeedlingsOrdered" name="numberOfSeedlingsOrdered">
            </div>
            <div class="form-group">
                <label for="amountToBePaid">Amount to be Paid (Ksh)</label>
                <input type="number" step="0.01" id="amountToBePaid" name="amountToBePaid">
            </div>
            <div class="form-group">
                <label for="depositPaid">Deposit Paid (Ksh)</label>
                <input type="number" step="0.01" id="depositPaid" name="depositPaid">
            </div>
            <div class="form-group">
                <label for="balance">Balance (Ksh)</label>
                <input type="number" step="0.01" id="balance" name="balance">
            </div>
            <div class="form-group">
                <label for="dateOfPayment">Date of Payment</label>
                <input type="date" id="dateOfPayment" name="dateOfPayment">
            </div>
            <div class="form-group">
                <label for="dateToCompletePayment">Date to Complete Payment</label>
                <input type="date" id="dateToCompletePayment" name="dateToCompletePayment">
            </div>

            <h3>Area Information</h3>
            <div class="form-group">
                <label for="county">County</label>
                <input type="text" id="county" name="county">
            </div>
            <div class="form-group">
                <label for="subCounty">Sub-County</label>
                <input type="text" id="subCounty" name="subCounty">
            </div>
            <div class="form-group">
                <label for="ward">Ward</label>
                <input type="text" id="ward" name="ward">
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location">
            </div>
            <div class="form-group">
                <label for="subLocation">Sub-Location</label>
                <input type="text" id="subLocation" name="subLocation">
            </div>
            <div class="form-group">
                <label for="village">Village</label>
                <input type="text" id="village" name="village">
            </div>

            <h3>Terms and Conditions</h3>
            <div class="form-group">
                <input type="checkbox" id="termsAndConditions" name="termsAndConditions" required>
                <label for="termsAndConditions">I accept that this is a representation of my signature and that of my kin.</label>
            </div>
            
            <button type="submit">Register Member</button>
        </form>
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

