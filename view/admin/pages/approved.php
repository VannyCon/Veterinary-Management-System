<?php
session_start(); // Start session to access $_SESSION

// Database connection settings
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

// Check if user is logged in
if (!isset($_SESSION['admin'])) {
    die("Unauthorized access. Please log in.");
}

$user_id = $_SESSION['admin'];

try {
    // Establish PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to get all transactions for the logged-in user
function getTransaction($pdo)
{
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
                u.phone_number,
                u.isApproved AS user_approved,
                p.id AS pet_id,
                p.pet_name,
                p.pet_species,
                p.pet_age,


                d.id AS diagnosis_record_id,
                d.diagnosis_id,
                d.pet_diagnosis,
                d.pet_medication_prescribe,
                d.pet_doctor_notes,
                d.isComplete AS diagnosis_status

            FROM 
                tbl_appointment a
            JOIN 
                tbl_appointment_pets ap ON a.appointment_id = ap.appointment_id
            JOIN 
                tbl_user u ON a.user_id = u.user_id
            JOIN 
                tbl_pet p ON ap.pet_id = p.pet_id
            LEFT JOIN 
                tbl_diagnosis d ON a.appointment_id = d.appointment_id
            WHERE 
                a.isApproved = 'Approved'
            ORDER BY 
                a.created_date DESC, a.created_time DESC
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
                        <a class="nav-link" href="appoinment_calendar.php">Calendar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transactions.php">Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="approved.php">Approved Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h1 class="text-danger">Approved Transaction</h1>
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
                                                <td><?= $appointment['username'] ?></td>
                                                <td><?= $appointment['service_id'] ?></td>
                                                <td><?= htmlspecialchars($appointment['pet_id_reference']) ?></td>
                                                <td><?= htmlspecialchars($appointment['pet_symptoms']) ?></td>
                                                <td><?= htmlspecialchars($appointment['phone_number']) ?></td>
                                                <td><?= date("F d, Y", strtotime($appointment['created_date'])) ?></td>
                                                <td><?= date("h:i A", strtotime($appointment['created_time'])) ?></td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col">
                                                            <?php if ($appointment['diagnosis_status'] == null) { ?>
                                                                <a href="diagnosis.php?appointment_id=<?php echo $appointment['appointment_reference'] ?>&pet_id=<?php echo $appointment['pet_id_reference'] ?>&pet_name=<?php echo $appointment['pet_name'] ?>" class="btn btn-sm btn-primary text-white w-100">Complete</a>
                                                            <?php } else if ($appointment['diagnosis_status'] === 1) { ?>
                                                                <a href="diagnosis.php?appointment_id=<?php echo $appointment['appointment_reference'] ?>&pet_id=<?php echo $appointment['pet_id_reference'] ?>&pet_name=<?php echo $appointment['pet_name'] ?>" class="btn btn-sm btn-outline-info w-100" disabled>Print</a>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="col">
                                                            <a href="pet_diagnosis.php?appointment_id=<?php echo $appointment['appointment_reference'] ?>&pet_id=<?php echo $appointment['pet_id_reference'] ?>" class="btn btn-sm btn-info w-100" disabled>View</a>
                                                        </div>
                                                        <div class="col">
                                                            <a href="diagnosis.php?appointment_id=<?php echo $appointment['appointment_reference'] ?>&pet_id=<?php echo $appointment['pet_id_reference'] ?>&pet_name=<?php echo $appointment['pet_name'] ?>" class="btn btn-sm btn-outline-primary w-100" disabled>Edit</a>
                                                        </div>




                                                    </div>
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