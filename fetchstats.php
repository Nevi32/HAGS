<?php
session_start();
header('Content-Type: application/json');

require 'config.php';

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

if (!isset($_SESSION['companyName'])) {
    die(json_encode(["message" => "No company name found in session."]));
}

$companyName = $conn->real_escape_string($_SESSION['companyName']);

// Fetch total number of groups
$groupsQuery = "SELECT COUNT(*) AS totalGroups FROM `Groups` 
                LEFT JOIN Companies ON `Groups`.CompanyID = Companies.CompanyID 
                WHERE Companies.CompanyName = '$companyName'";
$groupsResult = $conn->query($groupsQuery);
$totalGroups = $groupsResult->fetch_assoc()['totalGroups'];

// Fetch total number of members
$membersQuery = "SELECT COUNT(*) AS totalMembers FROM Members 
                 LEFT JOIN `Groups` ON Members.GroupID = `Groups`.GroupID 
                 LEFT JOIN Companies ON `Groups`.CompanyID = Companies.CompanyID 
                 WHERE Companies.CompanyName = '$companyName'";
$membersResult = $conn->query($membersQuery);
$totalMembers = $membersResult->fetch_assoc()['totalMembers'];

// Fetch total number of projects
$projectsQuery = "SELECT COUNT(*) AS totalProjects FROM Projects 
                  LEFT JOIN Members ON Projects.MemberID = Members.MemberID 
                  LEFT JOIN `Groups` ON Members.GroupID = `Groups`.GroupID 
                  LEFT JOIN Companies ON `Groups`.CompanyID = Companies.CompanyID 
                  WHERE Companies.CompanyName = '$companyName'";
$projectsResult = $conn->query($projectsQuery);
$totalProjects = $projectsResult->fetch_assoc()['totalProjects'];

// Fetch total deposit paid and counting payments
$paymentsQuery = "SELECT 
                    SUM(DepositPaid) AS totalDepositPaid, 
                    SUM(CountingPayments) AS totalCountingPayments 
                  FROM Projects 
                  LEFT JOIN Members ON Projects.MemberID = Members.MemberID 
                  LEFT JOIN `Groups` ON Members.GroupID = `Groups`.GroupID 
                  LEFT JOIN Companies ON `Groups`.CompanyID = Companies.CompanyID 
                  WHERE Companies.CompanyName = '$companyName'";
$paymentsResult = $conn->query($paymentsQuery);
$paymentsData = $paymentsResult->fetch_assoc();
$totalDepositPaid = $paymentsData['totalDepositPaid'];
$totalCountingPayments = $paymentsData['totalCountingPayments'];

// Fetch most popular seedlings in different areas
$seedlingsQuery = "SELECT 
                    Areas.County, 
                    Areas.SubCounty, 
                    Projects.VarietyOfSeedlings, 
                    COUNT(Projects.VarietyOfSeedlings) AS seedlingsCount 
                  FROM Projects 
                  LEFT JOIN Members ON Projects.MemberID = Members.MemberID 
                  LEFT JOIN Areas ON Members.MemberID = Areas.MemberID 
                  LEFT JOIN `Groups` ON Members.GroupID = `Groups`.GroupID 
                  LEFT JOIN Companies ON `Groups`.CompanyID = Companies.CompanyID 
                  WHERE Companies.CompanyName = '$companyName' 
                  GROUP BY Areas.County, Areas.SubCounty, Projects.VarietyOfSeedlings 
                  ORDER BY seedlingsCount DESC";
$seedlingsResult = $conn->query($seedlingsQuery);

$seedlingsData = [];
while ($row = $seedlingsResult->fetch_assoc()) {
    $seedlingsData[] = $row;
}

// Prepare the response
$response = [
    'totalGroups' => $totalGroups,
    'totalMembers' => $totalMembers,
    'totalProjects' => $totalProjects,
    'totalDepositPaid' => $totalDepositPaid,
    'totalCountingPayments' => $totalCountingPayments,
    'seedlingsData' => $seedlingsData
];

echo json_encode($response);

$conn->close();
?>

