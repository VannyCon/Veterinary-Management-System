<?php 
session_start(); // Start the session at the top of the file

// Database connection
$host = 'localhost';
$dbname = 'system_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Function to get all pets for the logged-in user
    function getAllPetInfo($pdo, $user_id) {
        try {
            // SQL query to fetch pets by user_id
            $query = "
                SELECT `id`, `pet_id`, `user_id`, `pet_name`, `pet_species`, `pet_age` 
                FROM `tbl_pet` 
                WHERE `user_id`= ?;
            ";
            
            // Prepare and execute the query
            $stmt = $pdo->prepare($query); 
            $stmt->execute([$user_id]); 
            
            // Fetch all results
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Handle any errors
            echo json_encode(['error' => $e->getMessage()]);
            return [];
        }
    }

    // Call the function and pass the PDO connection and user_id
    $pets = getAllPetInfo($pdo, $_SESSION['user_id']);

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
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">User Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="transaction.php">Transaction</a></li>
                    <li class="nav-item"><a class="nav-link" href="appointment.php">Appointment</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-primary">Welcome, User!</h1>
        <p>Here, you can view and update your profile information.</p>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Profile</h5>
                <p class="card-text">Update your personal information and preferences.</p>
                <a href="#" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>
        <br>

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title">Pet</h5>
                    </div>
                    <div class="col text-end">
                        <a href="#" class="btn btn-warning">Add Pet</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php 
                        // Display pets in a card format
                        if (!empty($pets)) {
                            foreach ($pets as $pet) {
                                echo '
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">' . htmlspecialchars($pet['pet_name']) . '</h5>
                                            <p class="card-text">Species: ' . htmlspecialchars($pet['pet_species']) . '</p>
                                            <a href="#" class="btn btn-primary">Edit</a>
                                            <a href="pet_history.php?pet_id=' . htmlspecialchars($pet['pet_id']) . '" class="btn btn-info">History</a>
                                        </div>
                                    </div>
                                </div>';
                            }
                        } else {
                            echo "<p>No pets found.</p>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
