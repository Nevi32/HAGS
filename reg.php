<?php
header('Content-Type: application/json');

// Include the database configuration file
require_once 'config.php';

// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: login.html');
    exit;
}

// Get session data
$companyName = $_SESSION['companyName'];

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $fullName = $_POST['fullName'];
    $nationalID = $_POST['nationalID'];
    $contact = '+254' . $_POST['contact'];
    $groupName = $_POST['groupName'];
    $memberUniqueID = $_POST['memberUniqueID'];
    $dateOfAdmission = $_POST['dateOfAdmission'];
    $nextOfKin = $_POST['nextOfKin'];
    $nextOfKinContact = '+254' . $_POST['nextOfKinContact'];
    $status = $_POST['status']; // New status field
    $varietyOfSeedlings = $_POST['varietyOfSeedlings'];
    $numberOfSeedlingsOrdered = $_POST['numberOfSeedlingsOrdered'];
    $amountToBePaid = $_POST['amountToBePaid'];
    $depositPaid = $_POST['depositPaid'];
    $balance = $_POST['balance'];
    $dateOfPayment = $_POST['dateOfPayment'];
    $dateToCompletePayment = $_POST['dateToCompletePayment'];
    $county = strtoupper($_POST['county']);
    $subCounty = strtoupper($_POST['subCounty']);
    $ward = strtoupper($_POST['ward']);
    $location = strtoupper($_POST['location']);
    $subLocation = strtoupper($_POST['subLocation']);
    $village = strtoupper($_POST['village']);
    $termsAccepted = isset($_POST['termsAndConditions']) ? 1 : 0;
    $nextOfKinTermsAccepted = $termsAccepted;

    try {
        // Create a new PDO connection
        $dsn = "mysql:host=$servername;dbname=$dbname";
        $pdo = new PDO($dsn, $db_username, $db_password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Fetch company ID based on company name
        $sql = "SELECT CompanyID FROM Companies WHERE CompanyName = :companyName";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':companyName' => $companyName]);
        $company = $stmt->fetch();
        if (!$company) {
            throw new Exception("Company not found.");
        }
        $companyID = $company['CompanyID'];

        // Check if group name exists
        $sql = "SELECT GroupID FROM `Groups` WHERE GroupName = :groupName AND CompanyID = :companyID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':groupName' => $groupName, ':companyID' => $companyID]);
        $group = $stmt->fetch();

        // If group does not exist, insert it
        if (!$group) {
            // Generate a random GroupID
            $groupID = uniqid();
            $sql = "INSERT INTO `Groups` (GroupID, GroupName, CompanyID) VALUES (:groupID, :groupName, :companyID)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':groupID' => $groupID, ':groupName' => $groupName, ':companyID' => $companyID]);
        } else {
            $groupID = $group['GroupID'];
        }

        // Generate a unique MemberID
        $memberID = uniqid();

        // Insert data into the Members table
        $sql = "INSERT INTO Members (MemberID, FullName, NationalID, Contact, GroupID, MemberUniqueID, DateOfAdmission, NextOfKin, NextOfKinContact, Status, TermsAccepted, NextOfKinTermsAccepted)
                VALUES (:memberID, :fullName, :nationalID, :contact, :groupID, :memberUniqueID, :dateOfAdmission, :nextOfKin, :nextOfKinContact, :status, :termsAccepted, :nextOfKinTermsAccepted)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':memberID' => $memberID,
            ':fullName' => $fullName,
            ':nationalID' => $nationalID,
            ':contact' => $contact,
            ':groupID' => $groupID,
            ':memberUniqueID' => $memberUniqueID,
            ':dateOfAdmission' => $dateOfAdmission,
            ':nextOfKin' => $nextOfKin,
            ':nextOfKinContact' => $nextOfKinContact,
            ':status' => $status, // New status field
            ':termsAccepted' => $termsAccepted,
            ':nextOfKinTermsAccepted' => $nextOfKinTermsAccepted,
        ]);

        // Insert data into the Projects table
        $sql = "INSERT INTO Projects (MemberID, VarietyOfSeedlings, NumberOfSeedlingsOrdered, AmountToBePaid, DepositPaid, Balance, DateOfPayment, DateToCompletePayment)
                VALUES (:memberID, :varietyOfSeedlings, :numberOfSeedlingsOrdered, :amountToBePaid, :depositPaid, :balance, :dateOfPayment, :dateToCompletePayment)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':memberID' => $memberID,
            ':varietyOfSeedlings' => $varietyOfSeedlings,
            ':numberOfSeedlingsOrdered' => $numberOfSeedlingsOrdered,
            ':amountToBePaid' => $amountToBePaid,
            ':depositPaid' => $depositPaid,
            ':balance' => $balance,
            ':dateOfPayment' => $dateOfPayment,
            ':dateToCompletePayment' => $dateToCompletePayment,
        ]);

        // Insert data into the Areas table
        $sql = "INSERT INTO Areas (MemberID, County, SubCounty, Ward, Location, SubLocation, Village)
                VALUES (:memberID, :county, :subCounty, :ward, :location, :subLocation, :village)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':memberID' => $memberID,
            ':county' => $county,
            ':subCounty' => $subCounty,
            ':ward' => $ward,
            ':location' => $location,
            ':subLocation' => $subLocation,
            ':village' => $village,
        ]);

        $response['success'] = true;
        $response['message'] = 'Member registered successfully';
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>

