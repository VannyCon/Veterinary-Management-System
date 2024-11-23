<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'system_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Generate Unique Appointment ID
    function generateAppointmentID() {
        $prefix = "APT-";
        $timestamp = microtime(true);
        $randomNumber = mt_rand(100000, 999999);
        $uniqueHash = hash('sha256', $timestamp . $randomNumber);
        return $prefix . strtoupper(substr($uniqueHash, 0, 10));
    }

    // Start transaction
    $pdo->beginTransaction();

    $appointmentID = generateAppointmentID();
    $userID = $_SESSION['user_id']; // Ensure this session value is set
    $petID = $_POST['pet_id']; // Assuming data comes from form POST
    $serviceID = $_POST['service_id'];
    $symptoms = $_POST['pet_symptoms'];
    $createdDate = $_POST['created_date'];// Current date
    $createdTime =$_POST['created_time']; // Current time
    $isApproved = "Pending"; // Current time
    // Prepare for tbl_appointment query (Make sure all columns are handled)
    $insertAppointmentQuery = "
        INSERT INTO `tbl_appointment` (`appointment_id`, `user_id`, `service_id`, `isApproved`, `created_date`, `created_time`) 
        VALUES (:appointment_id, :user_id, :service_id, :isApproved, :created_date, :created_time)";
    $stmt1 = $pdo->prepare($insertAppointmentQuery);
    $stmt1->bindParam(':appointment_id', $appointmentID);
    $stmt1->bindParam(':user_id', $userID);
    $stmt1->bindParam(':service_id', $serviceID);
    $stmt1->bindParam(':isApproved', $isApproved); // Assuming you set this variable
    $stmt1->bindParam(':created_date', $createdDate);
    $stmt1->bindParam(':created_time', $createdTime);
    $stmt1->execute();

    // Prepare for tbl_appointment_pets query
    $insertAppointmentPetsQuery = "
        INSERT INTO `tbl_appointment_pets` (`appointment_id`, `user_id`, `pet_id`, `pet_symptoms`)
        VALUES (:appointment_id, :user_id, :pet_id, :pet_symptoms)";
    $stmt2 = $pdo->prepare($insertAppointmentPetsQuery);
    $stmt2->bindParam(':appointment_id', $appointmentID);
    $stmt2->bindParam(':user_id', $userID);
    $stmt2->bindParam(':pet_id', $petID);
    $stmt2->bindParam(':pet_symptoms', $symptoms);
    $stmt2->execute();


    // Commit the transaction
    $pdo->commit();
    header("Location: appointment.php"); // Error: Headers already sent
} catch (Exception $e) {
    // Roll back the transaction if an error occurs
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
