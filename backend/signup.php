<?php
header('Content-Type: application/json');

require 'config.php';

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

$input = json_decode(file_get_contents('php://input'), true);

$fullName = $conn->real_escape_string($input['fullName']);
$username = $conn->real_escape_string($input['username']);
$password = password_hash($input['password'], PASSWORD_DEFAULT);
$companyName = $conn->real_escape_string($input['companyName']);
$companyInitials = $conn->real_escape_string($input['companyInitials']);

$companyID = null;

$sql = "SELECT CompanyID FROM Companies WHERE CompanyName = '$companyName'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $companyID = $row['CompanyID'];
} else {
    $sql = "INSERT INTO Companies (CompanyName, CompanyInitials) VALUES ('$companyName', '$companyInitials')";
    if ($conn->query($sql) === TRUE) {
        $companyID = $conn->insert_id;
    } else {
        echo json_encode(["message" => "Error: " . $sql . "<br>" . $conn->error]);
        exit();
    }
}

$sql = "INSERT INTO Users (FullName, Username, PasswordHash, CompanyID) VALUES ('$fullName', '$username', '$password', $companyID)";
if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Sign up successful"]);
} else {
    echo json_encode(["message" => "Error: " . $sql . "<br>" . $conn->error]);
}

$conn->close();
?>

