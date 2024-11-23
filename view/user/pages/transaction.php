<?php 
session_start(); // Start session to access $_SESSION

// Database connection settings
$host = 'localhost';
$dbname = 'system_db';
$username = 'root';
$password = '';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}

$user_id = $_SESSION['user_id'];

try {
    // Establish PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to get all transactions for the logged-in user
function getTransaction($pdo, $user_id) {
    try {
        // SQL query to fetch appointments and related pet details
        $query = "
             SELECT 
            a.id AS appointment_id,
            a.user_id AS user_id,
            a.service_id AS service_id,
            a.isApproved AS isApproved,
            a.created_date AS created_date,
            a.created_time AS created_time,
            p.pet_id AS pet_id,
            p.pet_symptoms AS pet_symptoms
        FROM 
            tbl_appointment AS a
        INNER JOIN 
            tbl_appointment_pets AS p
        ON 
            a.appointment_id = p.appointment_id
        WHERE
                a.user_id = ?
        ";

        // Prepare and execute the query securely
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);

        // Fetch all matching appointments
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle and display errors
        die("Error fetching transactions: " . $e->getMessage());
    }
}

// Fetch transactions for the current user
$appointments = getTransaction($pdo, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .day-cell {
            cursor: pointer;
            height: 80px;
            vertical-align: top;
        }
        .day-cell.today {
            background-color: #d4edda;
        }
        .event {
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 5px;
            padding: 2px 5px;
            margin-top: 5px;
            font-size: 0.8em;
        }
    </style>
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
                    <li class="nav-item"><a class="nav-link " href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
                    <li class="nav-item"><a class="nav-link active" href="transaction.php">Transaction</a></li>
                    <li class="nav-item"><a class="nav-link " href="appointment.php">Appointment</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

<div class="container mt-5">
    <div class="row">
        <div class="col">
            <div class="card p-3">
                <h2 class="card-title">Transaction</h2>
                <br>
                <?php foreach ($appointments as $appointment): ?>
                <table class="table table-bordered rounded">
                    <thead class="thead-light">
                        <tr>
                            <?php 
                                if($appointment['isApproved'] == "Approved"){
                                     echo "<th colspan='2' class='bg-success text-white'>Status: Approved</th>";
                                }else if($appointment['isApproved'] == "Pending"){
                                     echo "<th colspan='2' class='bg-warning text-white'>Status: Pending</th>";
                                      // Skip remaining appointment details if denied
                                }else{
                                    echo "<th colspan='2' class='bg-danger text-white'>Status: Denied</th>";
                                }
                            
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                       
                            <tr>
                                <td>Date</td>
                                <td><?= date("F d, Y", strtotime($appointment['created_date'])) ?></td>
                            </tr>
                            <tr>
                                <td>Time</td>
                                <td><?= date("h:i A", strtotime($appointment['created_time'])) ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bg-secondary text-white"><strong>Pet Details</strong></td>
                            </tr>
                            <tr>
                                <td>Pet ID</td>
                                <td><?= htmlspecialchars($appointment['pet_id']) ?></td>
                            </tr>
                            <tr>
                                <td>Symptoms</td>
                                <td><?= htmlspecialchars($appointment['pet_symptoms']) ?></td>
                            </tr>
                      
                    </tbody>
                </table>
                <br>
                <?php endforeach; ?>
                <br>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
