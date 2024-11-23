<?php 
session_start();
   // Generate Custome Status ID
function appointmentID() {
    // Prefix (optional) for the patient ID (e.g., "patient-")
    $prefix = "APT-";
    
    // Get the current timestamp in microseconds
    $timestamp = microtime(true);
    
    // Generate a random number to add more uniqueness
    $randomNumber = mt_rand(100000, 999999);
    
    // Hash the timestamp and random number to create a unique identifier
    $uniqueHash = hash('sha256', $timestamp . $randomNumber);
    
    // Take the first 12 characters of the hash (or any desired length)
    $patientID = substr($uniqueHash, 0, 10);
    
    // Return the final patient ID with prefix
    return $prefix . strtoupper($patientID);
}

//if The Location is not exist yet then this will run which mean it will create new location and User Custome LocID
$AppointID = $this->appointmentID();
$userid = $_SESSION['user_id'];

$table_incident_location_query = "SELECT `id`, `appointment_id`, `user_id`, `service_id`, `created_date`, `created_time` FROM `tbl_appointment` WHERE 1";
$stmt1 = $this->pdo->prepare($table_incident_location_query);
$stmt1->bindParam(':appointment_id', $AppointID);
$stmt1->bindParam(':user_id', $$userid);
$stmt1->bindParam(':pet_id', $pet_id); // Pet ID PET-001
$stmt1->bindParam(':service_id', $service_id);
$stmt1->bindParam(':created_date', $created_date); // Pet ID PET-001
$stmt1->bindParam(':created_time', $created_time);
$stmt1->execute();

$table_incident_location_query = "INSERT INTO `tbl_appointment_pets`(`id`, `appointment_id`, `user_id`, `pet_id`, `service_id`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]')";
$stmt2 = $this->pdo->prepare($table_incident_location_query);
$stmt2->bindParam(':appointment_id', $AppointID);
$stmt2->bindParam(':user_id', $$userid);
$stmt2->bindParam(':pet_id', $pet_id); // Pet ID PET-001
$stmt2->bindParam(':pet_symptoms', $longitude);
$stmt2->execute();


