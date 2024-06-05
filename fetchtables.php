<?php
// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: login.html');
    exit;
}

// Include the database configuration file
require_once 'config.php';

// Connect to the database
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the company name from the session
$companyName = $_SESSION['companyName'];

// Fetch the company ID based on the company name
$sql = "SELECT CompanyID FROM Companies WHERE CompanyName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $companyName);
$stmt->execute();
$result = $stmt->get_result();
$company = $result->fetch_assoc();
$companyID = $company['CompanyID'];

// Fetch all groups associated with the company ID
$sql = "SELECT GroupID, GroupName FROM `Groups` WHERE CompanyID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $companyID);
$stmt->execute();
$result = $stmt->get_result();

$groups = [];
while ($group = $result->fetch_assoc()) {
    $groups[] = $group;
}

// Fetch all members associated with the group IDs of the company
$members = [];
foreach ($groups as $group) {
    $sql = "SELECT MemberID, FullName, NationalID, Contact, GroupID, MemberUniqueID, Status,  TermsAccepted, DateOfAdmission, NextOfKin, NextOfKinContact, NextOfKinTermsAccepted FROM Members WHERE GroupID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $group['GroupID']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($member = $result->fetch_assoc()) {
        $members[] = $member;
    }
}

// Close the database connection
$conn->close();

// Store groups and members in session variables
$_SESSION['groups'] = $groups;
$_SESSION['members'] = $members;

// Redirect to tables.php
header('Location: tables.php');
exit;
?>

