<?php
session_start();
header('Content-Type: application/json');

require 'config.php';

// Check if the company name is set in the session
if (!isset($_SESSION['companyName'])) {
    die(json_encode(["message" => "No company name found in session."]));
}

$companyName = $_SESSION['companyName'];

// Connect to the database
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Get the company ID from the company name
$sql = "SELECT CompanyID FROM Companies WHERE CompanyName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $companyName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(["message" => "Company not found."]));
}

$companyId = $result->fetch_assoc()['CompanyID'];

// Fetch the member unique ID from the request
if (!isset($_GET['id'])) {
    die(json_encode(["message" => "Member ID not provided."]));
}

$memberUniqueID = $_GET['id'];

// Get the member info from the database
$sql = "SELECT * FROM Members WHERE MemberUniqueID = ? AND CompanyID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $memberUniqueID, $companyId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(["message" => "Member not found."]));
}

// Delete the member from the database
$sql = "DELETE FROM Members WHERE MemberUniqueID = ? AND CompanyID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $memberUniqueID, $companyId);

if ($stmt->execute()) {
    echo json_encode(["message" => "Member deleted successfully."]);
} else {
    echo json_encode(["message" => "Error deleting member: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

