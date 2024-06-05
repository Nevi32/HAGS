<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "message" => "You are not logged in."]);
    exit();
}

// Get the logged-in company's name from the session
$companyName = $_SESSION['companyName'];

// Create a connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the company ID based on the company name
$sql = "SELECT CompanyID FROM Companies WHERE CompanyName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $companyName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $companyID = $row['CompanyID'];
} else {
    echo json_encode(["success" => false, "message" => "Company not found."]);
    exit();
}

$stmt->close();

// Base SQL query
$sql = "
SELECT 
    g.GroupName, 
    m.MemberID, 
    m.FullName AS MemberFullName, 
    p.ProjectID, 
    p.VarietyOfSeedlings, 
    p.NumberOfSeedlingsOrdered, 
    p.AmountToBePaid, 
    p.DepositPaid, 
    p.Balance, 
    p.DateOfPayment, 
    p.DateToCompletePayment
FROM 
    `Groups` g
JOIN 
    Members m ON g.GroupID = m.GroupID
JOIN 
    Projects p ON m.MemberID = p.MemberID
WHERE 
    g.CompanyID = ?";

// Check if a group name or member name is provided in the request
$filter = "";
$params = [$companyID];
$types = "i";

if (isset($_GET['groupName']) && !empty($_GET['groupName'])) {
    $filter = " AND g.GroupName = ?";
    $params[] = $_GET['groupName'];
    $types .= "s";
} elseif (isset($_GET['memberName']) && !empty($_GET['memberName'])) {
    $filter = " AND m.FullName = ?";
    $params[] = $_GET['memberName'];
    $types .= "s";
}

$sql .= $filter;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Fetch and output the data
$projectsData = [];
while ($row = $result->fetch_assoc()) {
    $projectsData[] = $row;
}

$stmt->close();
$conn->close();

// Output the data as JSON
header('Content-Type: application/json');
echo json_encode(["success" => true, "projects" => $projectsData]);
?>

