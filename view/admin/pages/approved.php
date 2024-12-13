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
            u.fullname,  -- Assuming 'fullname' exists in tbl_user
            u.phone_number,
            u.isApproved AS user_approved,
            p.id AS pet_id,
            p.pet_name,
            p.pet_species,
            p.pet_age,
            s.service_name, -- Assuming a 'service_name' field in a 'tbl_service' table
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
            tbl_service s ON a.service_id = s.service_id  -- Join with tbl_service to get service_name
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
    <title>Cadiz City Veterinary Office</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/bootstrap.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../../assets/vendors/simple-datatables/style.css">

    <link rel="stylesheet" href="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="../../../assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../assets/css/app.css">
    <link rel="shortcut icon" href="../../../assets/images/favicon.svg" type="image/x-icon">
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                    <div class="logo">
                            <a href="dashboard.php">
                                <img src="../../../assets/images/logo/vetoff.png" alt="Logo" srcset="" style="width: 230px; height: auto"> <!-- Adjust width as needed -->
                            </a>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                </div>s
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item  active has-sub ">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Appointment</span>
                            </a>
                            <ul class="submenu active ">
                                <li class="submenu-item ">
                                    <a href="appointment_transactions.php">Pending</a>
                                </li>
                                <li class="submenu-item active">
                                    <a href="approved.php">Approved</a>
                                </li>
                              
                            </ul>
                        </li>

                        <li class="sidebar-item  ">
                            <a href="all_user.php" class='sidebar-link'>
                            <i class="bi bi-pen-fill"></i>
                                <span>Pet Record</span>
                            </a>
                        </li>
                        
                        <li class="sidebar-item  ">
                            <a href="events.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Events</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Manage User &amp; Staff</li>

                        <li class="sidebar-item  has-sub">
                            <a href="users.php" class='sidebar-link'>
                                <i class="bi bi-hexagon-fill"></i>
                                <span>User Request</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item ">
                                    <a href="users.php">Pending Account</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="user_approve_index.php">Approved Account</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="user_decline_index.php">Declined Account</a>
                                    
                            </ul>
                        </li>

                        <li class="sidebar-item  ">
                            <a href="staff.php" class='sidebar-link'>
                                <i class="bi bi-file-earmark-medical-fill"></i>
                                <span>Staff</span>
                            </a>
                        </li>
                        <div class="logout-btn text-center" style="padding: 50px;">
                    <a href="logout.php" class="btn btn-primary btn-block mt-4 d-flex align-items-center justify-content-center" style="padding: 8px 12px;">
                        <i class="fa fa-sign-out-alt mr-2" aria-hidden="true"></i> Logout
                    </a>
            

                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>
        
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>List of Approved Appointments </h3>
                            <p class="text-subtitle text-muted">Review details of all approved appointments and manage follow-up actions.</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Approved</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                          Approved Appointments Table
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th>Appointment ID</th>
                                        <th>Name</th>  <!-- Changed from Username to Full Name -->
                                        <th>Services</th>    <!-- Changed from Service ID to Service Name -->
                                        <th>Pet Name</th>   <!-- Changed from Pet ID to Pet Name -->
                                        <th>Species</th> <!-- Added Pet Species -->
                                        <th>Pet     Concern</th>
                                        <th>Contact</th>
                                        <th>Appointment Date</th>
                                        <th>Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                            <tr>
                                                <td><?= $appointment['appointment_reference'] ?></td>
                                                <td><?= htmlspecialchars($appointment['fullname']) ?></td>
                                                <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                                                <td><?= htmlspecialchars($appointment['pet_name']) ?></td>
                                                <td><?= htmlspecialchars($appointment['pet_species']) ?></td>
                                                <td><?= htmlspecialchars($appointment['pet_symptoms']) ?></td>
                                                <td><?= htmlspecialchars($appointment['phone_number']) ?></td>
                                                <td><?= date("F d, Y", strtotime($appointment['created_date'])) ?></td>
                                                <td><?= date("h:i A", strtotime($appointment['created_time'])) ?></td>
                                                 <td class="d-flex justify-content-around align-items-center">
                                                    <?php if ($appointment['diagnosis_status'] === null): ?>
                                                        <a href="diagnosis.php?appointment_id=<?= $appointment['appointment_reference'] ?>&pet_id=<?= $appointment['pet_id_reference'] ?>&pet_name=<?= $appointment['pet_name'] ?>" class="btn btn-sm btn-primary">Complete</a>
                                                       

                                                    <?php elseif ($appointment['diagnosis_status'] === 1): ?>
                                                        <p class="text-success">Completed</p>
                                                    <?php endif; ?>
                                                    
                                                </td>

                                             </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                </section>
            </div>

         
    <script src="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>

    <script src="../../../assets/vendors/simple-datatables/simple-datatables.js"></script>
    <script>
        // Simple Datatable
        let table1 = document.querySelector('#table1');
        let dataTable = new simpleDatatables.DataTable(table1);
    </script>

    <script src="../../../assets/js/main.js"></script>
    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../../js/bootstrap.bundle.min.js"></script>
</body>

</html>