<?php
session_start();

if (!isset($_SESSION['membersInfo'])) {
    die("No member info found in session.");
}

$membersInfo = $_SESSION['membersInfo'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Member Information</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Arial', sans-serif;
            background: #fff;
            color: #000;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 20px auto;
            text-align: center;
        }
        .search-bar-container {
            position: sticky;
            top: 0;
            background: #fff;
            padding: 20px;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .search-bar {
            padding: 10px;
            width: 80%;
            max-width: 600px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .members-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }
        .member-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 10px;
            padding: 20px;
            width: calc(50% - 40px);
            box-sizing: border-box;
        }
        .member-card h2 {
            margin-top: 0;
        }
        .member-card .buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .member-card .btn {
            background: #000;
            border: none;
            color: #fff;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }
        .member-card .btn:hover {
            background: #444;
            transform: scale(1.05);
        }
        @media (max-width: 768px) {
            .member-card {
                width: calc(100% - 40px);
            }
        }
        .popup-form {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }
        .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            max-height: 80vh;
            overflow-y: auto;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #000;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .close-btn:hover {
            background: #444;
        }
        .save-btn {
            background: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .save-btn:hover {
            background: #444;
        }
        .form-field {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-field label {
            display: block;
            margin-bottom: 5px;
        }
        .form-field input, .form-field textarea {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
    </style>
    <script>
        function searchMembers() {
            const input = document.getElementById("searchBar").value.toUpperCase().replace(/\s+/g, '');
            const cards = document.getElementsByClassName("member-card");

            for (let i = 0; i < cards.length; i++) {
                const card = cards[i];
                const name = card.querySelector("h2").textContent.toUpperCase().replace(/\s+/g, '');
                card.style.display = name.indexOf(input) > -1 ? "" : "none";
            }
        }

        function openEditForm(member) {
            const popupForm = document.getElementById("popupForm");
            const form = popupForm.querySelector("form");

            form.memberUniqueID.value = member.MemberUniqueID;
            form.fullName.value = member.FullName;
            form.nationalID.value = member.NationalID;
            form.contact.value = member.Contact;
            form.groupName.value = member.GroupName;
            form.status.value = member.Status;
            form.dateOfAdmission.value = member.DateOfAdmission;
            form.nextOfKin.value = member.NextOfKin;
            form.nextOfKinContact.value = member.NextOfKinContact;

            if (member.ProjectID) {
                form.projectID.value = member.ProjectID;
                form.varietyOfSeedlings.value = member.VarietyOfSeedlings;
                form.numberOfSeedlingsOrdered.value = member.NumberOfSeedlingsOrdered;
                form.amountToBePaid.value = member.AmountToBePaid;
                form.depositPaid.value = member.DepositPaid;
                form.balance.value = member.Balance;
                form.dateOfPayment.value = member.DateOfPayment;
                form.dateToCompletePayment.value = member.DateToCompletePayment;
                form.countingPayments.value = member.CountingPayments;
                form.countingPaymentDates.value = member.CountingPaymentDates;
            }

            popupForm.style.display = "flex";
        }

        function closeEditForm() {
            const popupForm = document.getElementById("popupForm");
            popupForm.style.display = "none";
        }

        function saveMember() {
            const form = document.getElementById("editForm");
            form.submit();
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="search-bar-container">
            <h1>Member Information</h1>
            <input type="text" id="searchBar" class="search-bar" onkeyup="searchMembers()" placeholder="Search for names..">
        </div>
        <div class="members-container">
        <?php foreach ($membersInfo as $member): ?>
            <div class="member-card">
                <h2><?php echo htmlspecialchars($member['FullName']); ?></h2>
                <p>National ID: <?php echo htmlspecialchars($member['NationalID']); ?></p>
                <p>Contact: <?php echo htmlspecialchars($member['Contact']); ?></p>
                <p>Group: <?php echo htmlspecialchars($member['GroupName']); ?></p>
                <p>Member Unique ID: <?php echo htmlspecialchars($member['MemberUniqueID']); ?></p>
                <p>Status: <?php echo htmlspecialchars($member['Status']); ?></p>
                <p>Date of Admission: <?php echo htmlspecialchars($member['DateOfAdmission']); ?></p>
                <p>Next of Kin: <?php echo htmlspecialchars($member['NextOfKin']); ?></p>
                <p>Next of Kin Contact: <?php echo htmlspecialchars($member['NextOfKinContact']); ?></p>
                <?php if (isset($member['ProjectID'])): ?>
                    <h3>Project Information</h3>
                    <p>Project ID: <?php echo htmlspecialchars($member['ProjectID']); ?></p>
                    <p>Variety of Seedlings: <?php echo htmlspecialchars($member['VarietyOfSeedlings']); ?></p>
                    <p>Number of Seedlings Ordered: <?php echo htmlspecialchars($member['NumberOfSeedlingsOrdered']); ?></p>
                    <p>Amount to be Paid: <?php echo htmlspecialchars($member['AmountToBePaid']); ?></p>
                    <p>Deposit Paid: <?php echo htmlspecialchars($member['DepositPaid']); ?></p>
                    <p>Balance: <?php echo htmlspecialchars($member['Balance']); ?></p>
                    <p>Date of Payment: <?php echo htmlspecialchars($member['DateOfPayment']); ?></p>
                    <p>Date to Complete Payment: <?php echo htmlspecialchars($member['DateToCompletePayment']); ?></p>
                    <p>Counting Payments: <?php echo htmlspecialchars($member['CountingPayments']); ?></p>
                    <p>Counting Payment Dates: <?php echo htmlspecialchars($member['CountingPaymentDates']); ?></p>
                <?php endif; ?>
                <div class="buttons">
                    <button class="btn" onclick='openEditForm(<?php echo json_encode($member); ?>)'>Edit</button>
                    <button class="btn" onclick="window.location.href='delete.php?id=<?php echo $member['MemberUniqueID']; ?>'">Delete</button>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>

    <div id="popupForm" class="popup-form">
        <div class="popup-content">
            <button class="close-btn" onclick="closeEditForm()">Close</button>
            <form id="editForm" action="edit.php" method="POST">
                <div class="form-field">
                    <label for="memberUniqueID">Member Unique ID</label>
                    <input type="text" name="memberUniqueID" readonly>
                </div>
                <div class="form-field">
                    <label for="fullName">Full Name</label>
                    <input type="text" name="fullName">
                </div>
                <div class="form-field">
                    <label for="nationalID">National ID</label>
                    <input type="text" name="nationalID">
                </div>
                <div class="form-field">
                    <label for="contact">Contact</label>
                    <input type="text" name="contact">
                </div>
                <div class="form-field">
                    <label for="groupName">Group Name</label>
                    <input type="text" name="groupName">
                </div>
                <div class="form-field">
                    <label for="status">Status</label>
                    <input type="text" name="status">
                </div>
                <div class="form-field">
                    <label for="dateOfAdmission">Date of Admission</label>
                    <input type="text" name="dateOfAdmission">
                </div>
                <div class="form-field">
                    <label for="nextOfKin">Next of Kin</label>
                    <input type="text" name="nextOfKin">
                </div>
                <div class="form-field">
                    <label for="nextOfKinContact">Next of Kin Contact</label>
                    <input type="text" name="nextOfKinContact">
                </div>
                <div class="form-field">
                    <label for="projectID">Project ID</label>
                    <input type="text" name="projectID">
                </div>
                <div class="form-field">
                    <label for="varietyOfSeedlings">Variety of Seedlings</label>
                    <input type="text" name="varietyOfSeedlings">
                </div>
                <div class="form-field">
                    <label for="numberOfSeedlingsOrdered">Number of Seedlings Ordered</label>
                    <input type="text" name="numberOfSeedlingsOrdered">
                </div>
                <div class="form-field">
                    <label for="amountToBePaid">Amount to be Paid</label>
                    <input type="text" name="amountToBePaid">
                </div>
                <div class="form-field">
                    <label for="depositPaid">Deposit Paid</label>
                    <input type="text" name="depositPaid">
                </div>
                <div class="form-field">
                    <label for="balance">Balance</label>
                    <input type="text" name="balance">
                </div>
                <div class="form-field">
                    <label for="dateOfPayment">Date of Payment</label>
                    <input type="text" name="dateOfPayment">
                </div>
                <div class="form-field">
                    <label for="dateToCompletePayment">Date to Complete Payment</label>
                    <input type="text" name="dateToCompletePayment">
                </div>
                <div class="form-field">
                    <label for="countingPayments">Counting Payments</label>
                    <textarea name="countingPayments"></textarea>
                </div>
                <div class="form-field">
                    <label for="countingPaymentDates">Counting Payment Dates</label>
                    <textarea name="countingPaymentDates"></textarea>
                </div>
                <button type="button" class="save-btn" onclick="saveMember()">Save</button>
            </form>
        </div>
    </div>
</body>
</html>

