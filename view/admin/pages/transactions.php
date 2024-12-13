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
        $query = "
                SELECT 
                    a.id AS appointment_id,
                    a.appointment_id AS appointment_reference,
                    a.user_id AS appointment_user_id,
                    a.service_id,
                    a.isApproved,
                    a.created_date,
                    a.created_time,
                    ap.id AS appointment_pet_id,
                    ap.pet_id AS pet_id_reference,
                    ap.pet_symptoms,
                    u.id AS user_id,
                    u.username,
                    u.fullname,  -- Assuming the fullname column exists
                    u.phone_number,
                    u.isApproved AS user_approved,
                    p.id AS pet_id,
                    p.pet_name,
                    p.pet_species,
                    s.service_name -- Assuming there's a service_name in a 'service' table
                FROM 
                    tbl_appointment a
                JOIN 
                    tbl_appointment_pets ap ON a.appointment_id = ap.appointment_id
                JOIN 
                    tbl_user u ON a.user_id = u.user_id
                JOIN 
                    tbl_pet p ON ap.pet_id = p.pet_id
                JOIN
                    tbl_service s ON a.service_id = s.service_id  -- Assuming there's a service table
                WHERE 
                    a.isApproved = ''
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

        if (!$appointment_id) {
            throw new Exception("Appointment ID is required.");
        }

        if (isset($_POST['approved'])) {
            $status = "Approved";
        } elseif (isset($_POST['decline'])) {
            $status = "Declined";
        } else {
            throw new Exception("Invalid action.");
        }

        $user_id = $_POST['user_id'];
        $phone_number = $_POST['phone_number'];
        $fullname = $_POST['fullname'];

        $stmt = $pdo->prepare("UPDATE tbl_appointment SET isApproved = ? WHERE appointment_id = ?");
        $stmt->execute([$status, $appointment_id]);

        // Trigger the SMS notification
        $url = "http://localhost/vetsystem/pet_system_5%20(2)/pet_system_5/view/admin/pages/sms_appointment_approval.php";
        $postData = [
            'appointment_id' => $appointment_id,
            'user_id' => $user_id,
            'phone_number' => $phone_number,
            'fullname' => $fullname,
            'status' => $status,
            'submit' => true
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $_SESSION['message'] = "Appointment has been " . strtolower($status) . "d successfully!";

        if ($status === "Approved") {
            header("Location: approved.php"); // Redirect to the approved page
        } else {
            header("Location: transactions.php"); // Redirect to pending transactions
        }
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}


// Display messages if they exist
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success text-center" style="margin-top: 20px;">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger text-center" style="margin-top: 20px;">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}



// Fetch transactions for the current user
$appointments = getTransaction($pdo);
?>
<!DOCTYPE html>
<html lang="en">


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadiz City Veterinary Office</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/bootstrap.css">

    <link rel="stylesheet" href="../../../assets/vendors/simple-datatables/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

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
                        <a href="dashboard.php">
                            <img src="../../../assets/images/logo/vetoff.png" alt="Logo" srcset="" style="width: 230px; height: auto"> <!-- Adjust width as needed -->
                        </a>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item  ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item  has-sub active">
                            <a href="transactions.php" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Appointment</span>
                            </a>
                            <ul class="submenu active">
                                <li class="submenu-item active">
                                    <a href="transactions.php">Pending</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="approved.php">Approved</a>
                                </li>


                            </ul>

                        <li class="sidebar-item  ">
                            <a href="all_user.php" class='sidebar-link'>
                                <i class="bi bi-pen-fill"></i>
                                <span>Pet Record</span>
                            </a>
                        </li>

                        <li class="sidebar-item  ">
                            <a href="index.html" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Events</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Manage User &amp; Staff</li>

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
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
                                <h3>List of Pending Appointments</h3>
                                <p class="text-subtitle text-muted">For admin to check the pending appointments</p>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Pending Appointments</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <section class="section">
                        <div class="card">
                            <div class="card-header">
                                Pending Appointments Table
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Appointment Id</th>
                                            <th>Name</th>
                                            <th>Services</th>
                                            <th>Pet Name</th>
                                            <th>Species</th>
                                            <th>Pet Concern</th>
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
                                                <td><?= htmlspecialchars($appointment['fullname']) ?></td> <!-- Display fullname -->
                                                <td><?= htmlspecialchars($appointment['service_name']) ?></td> <!-- Display service_name -->
                                                <td><?= htmlspecialchars($appointment['pet_name']) ?></td> <!-- Display pet_name -->
                                                <td><?= htmlspecialchars($appointment['pet_species']) ?></td> <!-- Display pet_species -->
                                                <td><?= htmlspecialchars($appointment['pet_symptoms']) ?></td>
                                                <td><?= htmlspecialchars($appointment['phone_number']) ?></td>
                                                <td><?= date("F d, Y", strtotime($appointment['created_date'])) ?></td>
                                                <td><?= date("h:i A", strtotime($appointment['created_time'])) ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <form action="" method="post" class="me-2">
                                                            <input type="hidden" name="userid" value="<?= htmlspecialchars($appointment['appointment_reference']); ?>">
                                                            <input type="hidden" name="approved" value="Approved">
                                                            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['appointment_id']) ?>">
                                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($appointment['user_id']) ?>">
                                                            <input type="hidden" name="phone_number" value="<?= htmlspecialchars($appointment['phone_number']) ?>">
                                                            <input type="hidden" name="fullname" value="<?= htmlspecialchars($appointment['fullname']) ?>">

                                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                        </form>
                                                        <form action="" method="post">
                                                            <input type="hidden" name="userid" value="<?= htmlspecialchars($appointment['appointment_reference']); ?>">
                                                            <input type="hidden" name="decline" value="Decline">
                                                            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['appointment_id']) ?>">
                                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($appointment['user_id']) ?>">
                                                            <input type="hidden" name="phone_number" value="<?= htmlspecialchars($appointment['phone_number']) ?>">
                                                            <input type="hidden" name="fullname" value="<?= htmlspecialchars($appointment['fullname']) ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">Decline</button>
                                                        </form>
                                                    </div>
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
    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>