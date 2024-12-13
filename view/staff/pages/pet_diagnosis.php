<?php
// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Function to get all pets for the logged-in user
    function getAllPetInfo($pdo, $pet_id)
    {
        try {
            // SQL query to fetch pets by pet_id
            $query = "
                SELECT `id`, `pet_id`, `pet_id`, `pet_name`, `pet_species`, `pet_age` 
                FROM `tbl_pet` WHERE `pet_id` = ?;
            ";

            // Prepare and execute the query
            $stmt = $pdo->prepare($query);
            $stmt->execute([$pet_id]);

            // Fetch all results (multiple pets for the user)
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle any errors
            echo json_encode(['error' => $e->getMessage()]);
            return [];
        }
    }

    function getAllPetHistory($pdo, $pet_id, $appoinment_id)
    {
        try {
            // SQL query to fetch pet history by pet_id
            $query = "
                SELECT 
                    ap.id AS appointment_pet_id,
                    ap.appointment_id,
                    ap.user_id,
                    ap.pet_id,
                    ap.pet_symptoms,
                    u.username,
                    u.phone_number,
                    p.pet_name,
                    p.pet_age,
                    p.pet_species,
                    d.diagnosis_id,
                    d.pet_diagnosis,
                    d.pet_medication_prescribe,
                    d.pet_doctor_notes,
                    a.service_id,
                    s.service_name,
                    s.description AS service_description,
                    a.created_date,
                    a.created_time
                FROM 
                    tbl_appointment_pets ap
                JOIN 
                    tbl_user u ON u.user_id = ap.user_id
                JOIN 
                    tbl_diagnosis d ON ap.appointment_id = d.appointment_id
                JOIN 
                    tbl_appointment a ON ap.appointment_id = a.appointment_id
                 JOIN 
                    tbl_pet p ON p.pet_id = ap.pet_id
                JOIN 
                    tbl_service s ON a.service_id = s.service_id
                WHERE 
                    ap.pet_id = ? AND a.appointment_id = ?
                GROUP BY 
                    ap.appointment_id
            ";

            // Prepare and execute the query
            $stmt = $pdo->prepare($query);
            $stmt->execute([$pet_id, $appoinment_id]); // Correct
            // Correctly bind $pet_id

            // Fetch all results
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle any errors
            echo json_encode(['error' => $e->getMessage()]);
            return [];
        }
    }

    $petID = $_GET['pet_id']; // Get user_id from session
    $appoinment_id = $_GET['appointment_id'];
    // Call the function and pass the PDO connection and user_id
    $pets = getAllPetInfo($pdo, $petID); // Get all pets for the user
    $petHistory = getAllPetHistory($pdo, $petID, $appoinment_id); // Get all pets for the user

} catch (PDOException $e) {
    // Handle database connection errors
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php
session_start(); // Start the session at the top of the file

$user_id =  $_SESSION['user_id'];
?>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="staff.php">Staff</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transactions.php">Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="approved.php">Approved Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../index.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <h1>Diagnosis</h1>
<div class="card">
    <div class="card-body">
        <?php
        // Display pet diagnosis history
        if (!empty($petHistory)) {
            foreach ($petHistory as $history) {
                echo '
                <div class="card mb-3">
                    <div class="card-body">
                        <h1>PROFILE</h1>
                        <div class="row">
                            <!-- Profile Column -->
                            <div class="col-md-6">
                                <p class="card-text">Username: ' . htmlspecialchars($history['username']) . '</p>
                                <p class="card-text">Contact: ' . htmlspecialchars($history['phone_number']) . '</p>
                            </div>
                            <!-- Pet Information Column -->
                            <div class="col-md-6">
                                <h1>Pet Information</h1>
                                <p class="card-text">Pet Name: ' . htmlspecialchars($history['pet_name']) . '</p>
                                <p class="card-text">Age: ' . htmlspecialchars($history['pet_age']) . '</p>
                            </div>
                        </div>

                        <h5 class="card-title">Date: ' . date("F d, Y", strtotime($history['created_date'])) . '</h5> 
                        <h5 class="card-title">Time: ' . $history['created_time'] . '</h5>

                        <h1>Pet Diagnosis</h1>
                        <p class="card-text">Service: ' . htmlspecialchars($history['service_name']) . '</p>
                        <p class="card-text">Symptoms: ' . htmlspecialchars($history['pet_symptoms']) . '</p>
                        <p class="card-text">Diagnosis: ' . htmlspecialchars($history['pet_diagnosis']) . '</p>
                        <p class="card-text">Medication: ' . htmlspecialchars($history['pet_medication_prescribe']) . '</p>
                        <p class="card-text">Doctor Notes: ' . htmlspecialchars($history['pet_doctor_notes']) . '</p>
                    </div>
                </div>';
            }
        } else {
            echo "<p>No history found for this pet.</p>";
        }
        ?>
    </div>
</div>
<br>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
