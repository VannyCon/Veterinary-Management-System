<?php 
// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Function to get user information
    // Function to get user information



    // Function to get pet information
    function getPetInfo($pdo, $pet_id) {
        try {
            $query = "
                SELECT `id`, `pet_id`, `pet_name`, `pet_species`, `pet_age`
                FROM `tbl_pet` WHERE `pet_id` = ?;
            ";
            $stmt = $pdo->prepare($query); 
            $stmt->execute([$pet_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
            return [];
        }
    }

    // Function to get pet history (diagnosis)
    function getPetHistory($pdo, $pet_id) {
        try {
            $query = "
                SELECT 
                    d.diagnosis_id, 
                    d.pet_diagnosis, 
                    d.pet_medication_prescribe, 
                    d.pet_doctor_notes, 
                    a.created_date, 
                    a.created_time
                FROM 
                    tbl_diagnosis d
                JOIN 
                    tbl_appointment_pets ap ON d.appointment_id = ap.appointment_id
                JOIN 
                    tbl_appointment a ON ap.appointment_id = a.appointment_id
                WHERE 
                    ap.pet_id = ?
                ORDER BY a.created_date DESC;
            ";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$pet_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
            return [];
        }
    }

     // Function to get user information but do not display it
     function getUserInfo($pdo, $user_id) {
        try {
            // SQL query to fetch user information by user_id
            $query = "
                SELECT `user_id`, `fullname`, `address`, `phone_number`
                FROM `tbl_user` WHERE `user_id` = ?;
            ";
            
            // Prepare and execute the query
            $stmt = $pdo->prepare($query); 
            $stmt->execute([$user_id]); 
            
            // Fetch the user information (but do not output in the page)
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Handle any errors
            echo json_encode(['error' => $e->getMessage()]);
            return [];
        }
    }

    // Get pet_id and user_id from the URL
    $petID = $_GET['pet_id'];
   
    $user_id = $_GET['user_id'];
    // Fetch the necessary data
  
    $petInfo = getPetInfo($pdo, $petID);
    $petHistory = getPetHistory($pdo, $petID);
    $userInfo = getUserInfo($pdo, $user_id); 

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        h1, h2 {
            text-align: center;
            color: blue;
        }
        .card-body {
            margin-bottom: 10px;
        }
        .btn {
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Pet Owners information</h2>
    <p>Fullname: <?php echo $userInfo['fullname']?></p>
    <p>Address: <?php echo $userInfo['address']?></p>
    <p>Contact: <?php echo $userInfo['phone_number']?></p>
    <h1></h1>
    <!-- Pet Information -->
    <h2>Pet Information</h2>
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($petInfo['pet_name']); ?></p>
            <p><strong>Species:</strong> <?php echo htmlspecialchars($petInfo['pet_species']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($petInfo['pet_age']); ?></p>
        </div>
    </div>

    <!-- Pet History (Diagnosis) -->
    <h2>Pet Diagnosis</h2>
    <?php 
    if (!empty($petHistory)) {
        foreach ($petHistory as $history) {
            echo '
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Date: ' . date("F d, Y", strtotime($history['created_date'])) . '</h5>
                    <h5>Time: ' . $history['created_time'] . '</h5>
                    <p><strong>Diagnosis:</strong> ' . htmlspecialchars($history['pet_diagnosis']) . '</p>
                    <p><strong>Medication:</strong> ' . htmlspecialchars($history['pet_medication_prescribe']) . '</p>
                    <p><strong>Doctor Notes:</strong> ' . htmlspecialchars($history['pet_doctor_notes']) . '</p>
                </div>
            </div>';
        }
    } else {
        echo "<p>No pet history available.</p>";
    }
    ?>

</div>

<script>
    window.print();
</script>

</body>
</html>
