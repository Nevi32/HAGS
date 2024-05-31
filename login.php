<?php
session_start();
header('Content-Type: application/json');

require 'config.php';

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

$input = json_decode(file_get_contents('php://input'), true);

$username = $conn->real_escape_string($input['username']);
$password = $input['password'];

$sql = "SELECT Users.UserID, Users.PasswordHash, Companies.CompanyName, Companies.CompanyInitials
        FROM Users
        LEFT JOIN Companies ON Users.CompanyID = Companies.CompanyID
        WHERE Users.Username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['PasswordHash'])) {
        $_SESSION['username'] = $username;
        $_SESSION['companyName'] = $row['CompanyName'];
        $_SESSION['companyInitials'] = $row['CompanyInitials'];

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Username or password not found"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Username or password not found"]);
}

$conn->close();
?>

