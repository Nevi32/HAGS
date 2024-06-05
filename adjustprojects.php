<?php
// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: login.html');
    exit;
}

// Include the config file for database credentials
require_once 'config.php';

// Database connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get session data
$username = $_SESSION['username'];
$companyName = $_SESSION['companyName'];

// Get the company ID based on the company name
$sql = "SELECT CompanyID FROM Companies WHERE CompanyName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $companyName);
$stmt->execute();
$stmt->bind_result($companyID);
$stmt->fetch();
$stmt->close();

// Function to insert new project
function insertNewProject($conn, $companyID) {
    $groupName = $_POST['groupName'];
    $memberName = $_POST['memberName'];
    $memberID = $_POST['memberID'];
    $varietyOfSeedlings = $_POST['varietyOfSeedlings'];
    $numberOfSeedlingsOrdered = $_POST['numberOfSeedlingsOrdered'];
    $amountToBePaid = $_POST['amountToBePaid'];
    $depositPaid = $_POST['depositPaid'];
    $balance = $_POST['balance'];
    $dateOfPayment = $_POST['dateOfPayment'];
    $dateToCompletePayment = $_POST['dateToCompletePayment'];

    // Insert the new project into the Projects table
    $sql = "INSERT INTO Projects (MemberID, VarietyOfSeedlings, NumberOfSeedlingsOrdered, AmountToBePaid, DepositPaid, Balance, DateOfPayment, DateToCompletePayment) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiddsss", $memberID, $varietyOfSeedlings, $numberOfSeedlingsOrdered, $amountToBePaid, $depositPaid, $balance, $dateOfPayment, $dateToCompletePayment);

    if ($stmt->execute()) {
        echo "New project inserted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Function to update current project
function updateCurrentProject($conn) {
    $projectID = $_POST['currentProjectID'];
    $countingPayments = $_POST['countingPayments'];
    $countingPaymentDates = $_POST['countingPaymentDates'];

    // Update the project in the Projects table
    $sql = "UPDATE Projects SET CountingPayments = CountingPayments + ?, CountingPaymentDates = CONCAT_WS(',', CountingPaymentDates, ?) WHERE ProjectID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $countingPayments, $countingPaymentDates, $projectID);

    if ($stmt->execute()) {
        // Adjust the balance
        $sql = "UPDATE Projects SET Balance = Balance - ? WHERE ProjectID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $countingPayments, $projectID);

        if ($stmt->execute()) {
            echo "Project updated successfully.";
        } else {
            echo "Error updating balance: " . $stmt->error;
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Determine which form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['groupName']) && isset($_POST['memberName']) && isset($_POST['memberID'])) {
        // New project form submitted
        insertNewProject($conn, $companyID);
    } elseif (isset($_POST['currentProjectID']) && isset($_POST['countingPayments']) && isset($_POST['countingPaymentDates'])) {
        // Current project form submitted
        updateCurrentProject($conn);
    } else {
        echo "Invalid form submission.";
    }
}

// Close the database connection
$conn->close();
?>

