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

// Fetch groups and members for the company
$sql = "
SELECT g.GroupID, g.GroupName, m.MemberID, m.FullName
FROM `Groups` g
JOIN Members m ON g.GroupID = m.GroupID
WHERE g.CompanyID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $companyID);
$stmt->execute();
$result = $stmt->get_result();

$groups = [];
$members = [];

while ($row = $result->fetch_assoc()) {
    $groups[] = ["GroupID" => $row["GroupID"], "GroupName" => $row["GroupName"]];
    $members[] = ["MemberID" => $row["MemberID"], "FullName" => $row["FullName"]];
}

$stmt->close();
$conn->close();

// Output the data as JSON
header('Content-Type: application/json');
echo json_encode(["success" => true, "groups" => $groups, "members" => $members]);
?>

