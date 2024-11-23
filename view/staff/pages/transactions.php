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
function getTransaction($pdo) {
    try {
        // SQL query to fetch appointments and related pet details
        $query = "
            SELECT 
                a.id AS appointment_id,
                a.appointment_id AS appointment_reference,
                a.user_id AS appointment_user_id,
                a.service_id,
                a.isApproved AS appointment_approved,
                a.created_date,
                a.created_time,
                ap.id AS appointment_pet_id,
                ap.pet_id AS pet_id_reference,
                ap.pet_symptoms,
                u.id AS user_id,
                u.username,
                u.contact_number,
                u.isApproved AS user_approved,
                p.id AS pet_id,
                p.pet_name,
                p.pet_species,
                p.pet_age
            FROM 
                tbl_appointment a
            JOIN 
                tbl_appointment_pets ap ON a.appointment_id = ap.appointment_id
            JOIN 
                tbl_user u ON a.user_id = u.user_id
            JOIN 
                tbl_pet p ON ap.pet_id = p.pet_id
            WHERE 
                a.isApproved = 'Pending'
                ";

        // Prepare and execute the query securely
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        // Fetch all matching appointments
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle and display errors
        die("Error fetching transactions: " . $e->getMessage());
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $appointment_id = isset($_POST['userid']) ? $_POST['userid'] : null;
        
        // Check if we have an appointment ID
        if (!$appointment_id) {
            throw new Exception("Appointment ID is required");
        }

        // Determine the status based on which button was clicked
        if (isset($_POST['approved'])) {
            $status = "Approved";
        } elseif (isset($_POST['decline'])) {
            $status = "Decline";
        } else {
            throw new Exception("Invalid action");
        }

        // Prepare and execute the update query
        $stmt = $pdo->prepare("UPDATE tbl_appointment SET isApproved = ? WHERE appointment_id = ?");
        $stmt->execute([$status, $appointment_id]);

        // Set success message
        $_SESSION['message'] = "Appointment has been " . strtolower($status) . "d successfully!";
        
        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Display messages if they exist
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

// Fetch transactions for the current user
$appointments = getTransaction($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Staff Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h1 class="text-success">Transaction</h1>
        <p>Here, you can manage users, view reports, and perform administrative tasks.</p>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                    <div class="row">
                        <div class="col">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Service</th>
                                    <th>Petname</th>
                                    <th>Symptoms</th>
                                    <th>Contact</th>
                                    <th>Created Date</th>
                                    <th>Created Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?= $appointment['appointment_reference'] ?></td>
                                        <td><?= $appointment['user_id'] ?></td>
                                        <td><?= $appointment['service_id'] ?></td>
                                        <td><?= htmlspecialchars($appointment['pet_id']) ?></td>
                                        <td><?= htmlspecialchars($appointment['pet_symptoms']) ?></td>
                                        <td><?= htmlspecialchars($appointment['contact_number']) ?></td>
                                        <td><?= date("F d, Y", strtotime($appointment['created_date'])) ?></td>
                                        <td><?= date("h:i A", strtotime($appointment['created_time'])) ?></td>
                                        <td>
                                            <form action="" method="post" class="d-inline">
                                                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($appointment['appointment_reference']); ?>">
                                                <input type="hidden" name="approved" value="Approved">
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form action="" method="post" class="d-inline">
                                                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($appointment['appointment_reference']); ?>">
                                                <input type="hidden" name="decline" value="Decline">
                                                <button type="submit" class="btn btn-sm btn-danger">Decline</button>
                                            </form>
                                        </td>
                                    </tr>
     
                                <?php endforeach; ?>
                                <!-- Additional rows can be added dynamically -->
                            </tbody>
                        </table>

                        </div>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
