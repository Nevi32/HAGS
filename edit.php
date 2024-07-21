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

// Fetch the form data
$memberUniqueID = $_POST['memberUniqueID'];
$fullName = $_POST['fullName'];
$nationalID = $_POST['nationalID'];
$contact = $_POST['contact'];
$groupName = $_POST['groupName'];
$status = $_POST['status'];
$dateOfAdmission = $_POST['dateOfAdmission'];
$nextOfKin = $_POST['nextOfKin'];
$nextOfKinContact = $_POST['nextOfKinContact'];
$projectID = $_POST['projectID'] ?? null;
$varietyOfSeedlings = $_POST['varietyOfSeedlings'] ?? null;
$numberOfSeedlingsOrdered = $_POST['numberOfSeedlingsOrdered'] ?? null;
$amountToBePaid = $_POST['amountToBePaid'] ?? null;
$depositPaid = $_POST['depositPaid'] ?? null;
$balance = $_POST['balance'] ?? null;
$dateOfPayment = $_POST['dateOfPayment'] ?? null;
$dateToCompletePayment = $_POST['dateToCompletePayment'] ?? null;
$countingPayments = $_POST['countingPayments'] ?? null;
$countingPaymentDates = $_POST['countingPaymentDates'] ?? null;

// Get the group ID from the group name and company ID
$sql = "SELECT GroupID FROM `Groups` WHERE GroupName = ? AND CompanyID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $groupName, $companyId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(["message" => "Group not found."]));
}

$groupId = $result->fetch_assoc()['GroupID'];

// Get the current member info from the database
$sql = "SELECT * FROM Members WHERE MemberUniqueID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $memberUniqueID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(["message" => "Member not found."]));
}

$currentMemberInfo = $result->fetch_assoc();

// Check for changes and update the member info
$changes = [];
$fieldsToUpdate = [];

if ($fullName !== $currentMemberInfo['FullName']) {
    $changes[] = "Full Name";
    $fieldsToUpdate['FullName'] = $fullName;
}

if ($nationalID !== $currentMemberInfo['NationalID']) {
    $changes[] = "National ID";
    $fieldsToUpdate['NationalID'] = $nationalID;
}

if ($contact !== $currentMemberInfo['Contact']) {
    $changes[] = "Contact";
    $fieldsToUpdate['Contact'] = $contact;
}

if ($groupId !== $currentMemberInfo['GroupID']) {
    $changes[] = "Group Name";
    $fieldsToUpdate['GroupID'] = $groupId;
}

if ($status !== $currentMemberInfo['Status']) {
    $changes[] = "Status";
    $fieldsToUpdate['Status'] = $status;
}

if ($dateOfAdmission !== $currentMemberInfo['DateOfAdmission']) {
    $changes[] = "Date of Admission";
    $fieldsToUpdate['DateOfAdmission'] = $dateOfAdmission;
}

if ($nextOfKin !== $currentMemberInfo['NextOfKin']) {
    $changes[] = "Next of Kin";
    $fieldsToUpdate['NextOfKin'] = $nextOfKin;
}

if ($nextOfKinContact !== $currentMemberInfo['NextOfKinContact']) {
    $changes[] = "Next of Kin Contact";
    $fieldsToUpdate['NextOfKinContact'] = $nextOfKinContact;
}

// Update the member info in the database if there are changes
if (!empty($fieldsToUpdate)) {
    $setClause = implode(", ", array_map(function($key) {
        return "$key = ?";
    }, array_keys($fieldsToUpdate)));

    $sql = "UPDATE Members SET $setClause WHERE MemberUniqueID = ?";
    $stmt = $conn->prepare($sql);
    
    // Build the parameter types string and values array
    $types = str_repeat("s", count($fieldsToUpdate)) . "s";
    $values = array_values($fieldsToUpdate);
    $values[] = $memberUniqueID;

    // Bind the parameters using the call_user_func_array method
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
}

// Check and update project info if available
if ($projectID) {
    $sql = "SELECT * FROM Projects WHERE ProjectID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $currentProjectInfo = $result->fetch_assoc();
        $fieldsToUpdate = [];

        if ($varietyOfSeedlings !== $currentProjectInfo['VarietyOfSeedlings']) {
            $changes[] = "Variety of Seedlings";
            $fieldsToUpdate['VarietyOfSeedlings'] = $varietyOfSeedlings;
        }

        if ($numberOfSeedlingsOrdered !== $currentProjectInfo['NumberOfSeedlingsOrdered']) {
            $changes[] = "Number of Seedlings Ordered";
            $fieldsToUpdate['NumberOfSeedlingsOrdered'] = $numberOfSeedlingsOrdered;
        }

        if ($amountToBePaid !== $currentProjectInfo['AmountToBePaid']) {
            $changes[] = "Amount to be Paid";
            $fieldsToUpdate['AmountToBePaid'] = $amountToBePaid;
        }

        if ($depositPaid !== $currentProjectInfo['DepositPaid']) {
            $changes[] = "Deposit Paid";
            $fieldsToUpdate['DepositPaid'] = $depositPaid;
        }

        if ($balance !== $currentProjectInfo['Balance']) {
            $changes[] = "Balance";
            $fieldsToUpdate['Balance'] = $balance;
        }

        if ($dateOfPayment !== $currentProjectInfo['DateOfPayment']) {
            $changes[] = "Date of Payment";
            $fieldsToUpdate['DateOfPayment'] = $dateOfPayment;
        }

        if ($dateToCompletePayment !== $currentProjectInfo['DateToCompletePayment']) {
            $changes[] = "Date to Complete Payment";
            $fieldsToUpdate['DateToCompletePayment'] = $dateToCompletePayment;
        }

        if ($countingPayments !== $currentProjectInfo['CountingPayments']) {
            $changes[] = "Counting Payments";
            $fieldsToUpdate['CountingPayments'] = $countingPayments;
        }

        if ($countingPaymentDates !== $currentProjectInfo['CountingPaymentDates']) {
            $changes[] = "Counting Payment Dates";
            $fieldsToUpdate['CountingPaymentDates'] = $countingPaymentDates;
        }

        // Update the project info in the database if there are changes
        if (!empty($fieldsToUpdate)) {
            $setClause = implode(", ", array_map(function($key) {
                return "$key = ?";
            }, array_keys($fieldsToUpdate)));

            $sql = "UPDATE Projects SET $setClause WHERE ProjectID = ?";
            $stmt = $conn->prepare($sql);
            
            // Build the parameter types string and values array
            $types = str_repeat("s", count($fieldsToUpdate)) . "i";
            $values = array_values($fieldsToUpdate);
            $values[] = $projectID;

            // Bind the parameters using the call_user_func_array method
            $stmt->bind_param($types, ...$values);
            $stmt->execute();
        }
    }
}

$conn->close();

if (!empty($changes)) {
    $changesList = implode(", ", $changes);
    echo json_encode(["success" => true, "message" => "Member info successfully edited: $changesList"]);
} else {
    echo json_encode(["success" => false, "message" => "No changes detected."]);
}
?>

