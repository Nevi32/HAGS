<?php
session_start();
header('Content-Type: application/json');

require 'config.php';

if (!isset($_SESSION['companyName'])) {
    die(json_encode(["message" => "No company name found in session"]));
}

$companyName = $_SESSION['companyName'];

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Retrieve the company ID using the company name
$companyIDQuery = $conn->prepare("SELECT CompanyID FROM Companies WHERE CompanyName = ?");
$companyIDQuery->bind_param("s", $companyName);
$companyIDQuery->execute();
$companyIDResult = $companyIDQuery->get_result();

if ($companyIDResult->num_rows > 0) {
    $companyIDRow = $companyIDResult->fetch_assoc();
    $companyID = $companyIDRow['CompanyID'];
} else {
    die(json_encode(["message" => "No company found with the given name"]));
}

// Fetch member information including project info using the company ID
$sql = "SELECT Members.MemberID, Members.FullName, Members.NationalID, Members.Contact, 
               Members.GroupID, Members.MemberUniqueID, Members.Status, Members.TermsAccepted, 
               Members.DateOfAdmission, Members.NextOfKin, Members.NextOfKinContact, 
               Members.NextOfKinTermsAccepted, `Groups`.GroupName,
               Projects.ProjectID, Projects.VarietyOfSeedlings, Projects.NumberOfSeedlingsOrdered, 
               Projects.AmountToBePaid, Projects.DepositPaid, Projects.Balance, Projects.DateOfPayment, 
               Projects.DateToCompletePayment, Projects.CountingPayments, Projects.CountingPaymentDates
        FROM Members
        LEFT JOIN `Groups` ON Members.GroupID = `Groups`.GroupID
        LEFT JOIN Projects ON Members.MemberID = Projects.MemberID
        WHERE `Groups`.CompanyID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $companyID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $membersInfo = [];
    while ($row = $result->fetch_assoc()) {
        $membersInfo[] = $row;
    }
    $_SESSION['membersInfo'] = $membersInfo;
    header('Location: viewinfo.php');
} else {
    echo json_encode(["message" => "No members found for the given company ID"]);
}

$stmt->close();
$companyIDQuery->close();
$conn->close();
?>

