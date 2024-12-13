<?php
session_start(); // Start session to access $_SESSION

// Database connection settings
$host = 'localhost';
$dbname = 'pet_db';
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
function getTransaction($pdo, $user_id)
{
    try {
        // SQL query to fetch appointments and related pet details
        $query = "
    SELECT 
            a.id AS appointment_id,
            a.user_id AS user_id,
            a.service_id AS service_id,
            s.service_name AS service_name, -- Fetch the service name
            a.isApproved AS isApproved,
            a.created_date AS created_date,
            a.created_time AS created_time,
            pt.pet_name AS pet_name,
            p.pet_id AS pet_id,
            p.pet_symptoms AS pet_symptoms
        FROM 
            tbl_appointment AS a
        INNER JOIN 
            tbl_appointment_pets AS p
        ON 
            a.appointment_id = p.appointment_id
        INNER JOIN 
            tbl_pet AS pt
        ON 
            pt.pet_id = p.pet_id
        INNER JOIN 
            tbl_service AS s -- Correct table alias for services
        ON 
            a.service_id = s.service_id -- Match service ID
        WHERE
            a.user_id = ?;
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
                </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item  ">
                            <a href="appointment.php" class='sidebar-link'>
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span>Appointment</span>
                            </a>
                        </li>

                        <li class="sidebar-item active ">
                            <a href="transaction.php" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Transaction</span>
                            </a>
                        </li>

                     

                        <li class="sidebar-item  ">
                            <a href="profile_view.php" class='sidebar-link'>
                                <i class="bi bi-image-fill"></i>
                                <span>Profile</span>
                            </a>
                        </li>



                        

                    </ul>
                    <div class="logout-btn text-center" style="padding: 50px;">
                    <a href="../logout.php" class="btn btn-primary btn-block mt-4 d-flex align-items-center justify-content-center" style="padding: 8px 12px;">
                        <i class="fa fa-sign-out-alt mr-2" aria-hidden="true"></i> Logout
                    </a>
                </div>
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
                            <h3>Transactions</h3>
                            <p class="text-subtitle text-muted">List of your Transaction status and history</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Transaction</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            Simple Datatable
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Pet Name</th>
                                        <th>Services</th>
                                        <th>Pet Concern</th>
                                        <th>Appointment Date</th>
                                        <th>Appointment Time</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                 
                    <?php foreach ($appointments as $appointment): ?>
                                <tr class="text-center">
                                    <td class="<?= $appointment['isApproved'] === 'Approved' ? 'bg-success text-white' : ($appointment['isApproved'] === '' ? 'bg-warning text-dark' : 'bg-danger text-white') ?>">
                                        <?php
                                        if ($appointment['isApproved'] == '') {
                                            echo "Pending";
                                        } else {
                                            echo htmlspecialchars($appointment['isApproved']);
                                        } ?>
                                    </td>

                                    <td><?= htmlspecialchars($appointment['pet_name']) ?></td>
                                    <td><?= htmlspecialchars($appointment['service_name']) ?></td> <!-- Display service name -->
                                    <td><?= htmlspecialchars($appointment['pet_symptoms']) ?></td>
                                    <td><?= date("F d, Y", strtotime($appointment['created_date'])) ?></td>
                                    <td><?= date("h:i A", strtotime($appointment['created_time'])) ?></td>


                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                    <br>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</body>

</html>