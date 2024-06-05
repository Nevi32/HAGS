<?php
session_start();
header('Content-Type: application/json');

require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['companyName'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

// Get the company ID
$companyName = $_SESSION['companyName'];

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Get the CompanyID
$sql = "SELECT CompanyID FROM Companies WHERE CompanyName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $companyName);
$stmt->execute();
$stmt->bind_result($companyID);
$stmt->fetch();
$stmt->close();

if (!$companyID) {
    echo json_encode(["success" => false, "message" => "Invalid company"]);
    exit();
}

// Fetch members information
$sql = "
    SELECT 
        m.MemberID, m.FullName, m.NationalID, m.Contact, m.MemberUniqueID, m.TermsAccepted, m.DateOfAdmission, m.Status, 
        m.NextOfKin, m.NextOfKinContact, m.NextOfKinTermsAccepted,
        g.GroupID, g.GroupName,
        p.ProjectID, p.VarietyOfSeedlings, p.NumberOfSeedlingsOrdered, p.AmountToBePaid, p.DepositPaid, p.Balance, p.DateOfPayment, p.DateToCompletePayment,
        a.AreaID, a.County, a.SubCounty, a.Ward, a.Location, a.SubLocation, a.Village
    FROM Members m
    LEFT JOIN `Groups` g ON m.GroupID = g.GroupID
    LEFT JOIN Projects p ON m.MemberID = p.MemberID
    LEFT JOIN Areas a ON m.MemberID = a.MemberID
    WHERE g.CompanyID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $companyID);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = [
        'MemberID' => $row['MemberID'],
        'FullName' => $row['FullName'],
        'NationalID' => $row['NationalID'],
        'Contact' => $row['Contact'],
        'Status' => $row['Status'],
        'MemberUniqueID' => $row['MemberUniqueID'],
        'TermsAccepted' => $row['TermsAccepted'] ? 'Yes' : 'No',
        'DateOfAdmission' => $row['DateOfAdmission'],
        'NextOfKin' => $row['NextOfKin'],
        'NextOfKinContact' => $row['NextOfKinContact'],
        'NextOfKinTermsAccepted' => $row['NextOfKinTermsAccepted'] ? 'Yes' : 'No',
        'Group' => [
            'GroupID' => $row['GroupID'],
            'GroupName' => $row['GroupName']
        ],
        'Project' => [
            'ProjectID' => $row['ProjectID'],
            'VarietyOfSeedlings' => $row['VarietyOfSeedlings'],
            'NumberOfSeedlingsOrdered' => $row['NumberOfSeedlingsOrdered'],
            'AmountToBePaid' => $row['AmountToBePaid'],
            'DepositPaid' => $row['DepositPaid'],
            'Balance' => $row['Balance'],
            'DateOfPayment' => $row['DateOfPayment'],
            'DateToCompletePayment' => $row['DateToCompletePayment']
        ],
        'Area' => [
            'AreaID' => $row['AreaID'],
            'County' => $row['County'],
            'SubCounty' => $row['SubCounty'],
            'Ward' => $row['Ward'],
            'Location' => $row['Location'],
            'SubLocation' => $row['SubLocation'],
            'Village' => $row['Village']
        ]
    ];
}

$stmt->close();
$conn->close();

// Session the data
$_SESSION['membersinfo'] = $members;

// Redirect to members.php
header('Location: members.php');
exit();
?>

