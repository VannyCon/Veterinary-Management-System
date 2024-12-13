<?php
session_start();

// Database connection settings
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

// Check if user is logged in
if (!isset($_SESSION['admin'])) {
    die("Unauthorized access. Please log in.");
}

// Set default date or get from query string
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$user_id = $_SESSION['admin'];

try {
    // Establish PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to get all transactions for the logged-in user
function getTransaction($pdo, $date)
{
    try {
        // SQL query to fetch appointments and related pet details
        $query = "
        SELECT 
            a.id,
            a.appointment_id AS appointment_reference,
            u.fullname,                 -- Get the username instead of user_id
            s.service_name,             -- Get the service name instead of service_id
            a.isApproved,
            a.created_date,
            a.created_time,
            ap.pet_symptoms,
            u.phone_number,
            p.pet_name,                 -- Get the pet_name instead of pet_id
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
        LEFT JOIN
            tbl_service s ON a.service_id = s.service_id  -- Join with tbl_service to get service_name
        WHERE 
            a.created_date = ? AND a.isApproved = ''
        ORDER BY 
            a.created_date ASC, a.created_time ASC;
    ";

        // Prepare and execute the query securely
        $stmt = $pdo->prepare($query);
        $stmt->execute([$date]);

        // Fetch all matching appointments
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
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

        // Remove leading zero from phone number (if it exists)
        if ($phone_number[0] === '0') {
            $phone_number = substr($phone_number, 1);
        }

        $stmt = $pdo->prepare("UPDATE tbl_appointment SET isApproved = ? WHERE appointment_id = ?");
        $stmt->execute([$status, $appointment_id]);

        // Prepare SMS parameters
        $send_data = [
            'sender_id' => "PhilSMS", // Replace with your sender ID
            'recipient' => "+63$phone_number", // Replace with the recipient's number
            'message' => "",
        ];

        // Your API Token
        $token = "1222|Pr4dssTM79z2BbdzZOQKle1BWOrWL28eRo0TNjVV"; // Replace with your API token

        // Check if status is "Approved"
        if ($status === "Approved") {
            $send_data['message'] = "Hi $fullname, Your Appoinment Is Approved";

            // Send SMS for approved status
            $parameters = json_encode($send_data);
            sendSMS($parameters, $token);
            header("Location: approved.php"); // Redirect to the approved page
        } elseif ($status === "Declined") {
            $send_data['message'] = "Hi $fullname, Your Appoinment Is Declined";

            // Send SMS for declined status
            $parameters = json_encode($send_data);
            sendSMS($parameters, $token);
            header("Location: transactions.php"); // Redirect to pending transactions
        }
        
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Function to send SMS using the provided parameters and token
function sendSMS($parameters, $token) {
    // Initialize cURL
    $ch = curl_init();

    // Set the API endpoint for sending SMS
    curl_setopt($ch, CURLOPT_URL, "https://app.philsms.com/api/v3/sms/send");

    // Use POST method
    curl_setopt($ch, CURLOPT_POST, true);

    // Add the JSON data as the request body
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);

    // Expect a response from the server
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Add headers
    $headers = [
        "Content-Type: application/json",            // Set content type to JSON
        "Authorization: Bearer $token"              // Add Authorization Bearer Token
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute the request
    $get_sms_status = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);

    // Output the response for debugging
    echo "Response from API:\n";
    var_dump($get_sms_status);
}


// Display messages
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-white">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

// Fetch transactions for the current user
$appointments = getTransaction($pdo, $date);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataTable - Mazer Admin Dashboard</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/bootstrap.css">

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
                            <a href="index.html">
                                <img src="../../../assets/images/logo/vetoff.png" alt="Logo" srcset="" style="width: 230px; height: auto"> <!-- Adjust width as needed -->
                            </a>
                            <div class="toggler">
                                <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                            </div>
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
                            <a href="appointment_transactions.php" class='sidebar-link'>
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
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-hexagon-fill"></i>
                                <span>User Request</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item ">
                                    <a href="form-element-input.html">Pending Account</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="form-element-input-group.html">Approved Account</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="form-element-select.html">Declined Account</a>

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
                                <h3>List of Pending Apponments</h3>
                                <p class="text-subtitle text-muted">View and manage all pending appointments awaiting confirmation or action.</p>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">DataTable</li>
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
                                            <th>Appointment Id</th>
                                            <th>Name</th>
                                            <th>Services</th>
                                            <th>Petname</th>
                                            <th>Species</th>
                                            <th>Pet Concern</th>
                                            <th>Contact</th>
                                            <th>Appointment Date</th>
                                            <th>Time</th>
                                            <th>Action</th>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php foreach ($appointments as $appointment): ?>
                                            <tr>
                                                <td><?= isset($appointment['appointment_reference']) ? htmlspecialchars($appointment['appointment_reference']) : 'N/A'; ?></td>
                                                <td><?= isset($appointment['fullname']) ? htmlspecialchars($appointment['fullname']) : 'N/A'; ?></td>
                                                <td><?= isset($appointment['service_name']) ? htmlspecialchars($appointment['service_name']) : 'N/A'; ?></td>
                                                <td><?= isset($appointment['pet_name']) ? htmlspecialchars($appointment['pet_name']) : 'N/A'; ?></td>
                                                <td><?= isset($appointment['pet_species']) ? htmlspecialchars($appointment['pet_species']) : 'N/A'; ?></td>
                                                <td><?= isset($appointment['pet_symptoms']) ? htmlspecialchars($appointment['pet_symptoms']) : 'N/A'; ?></td>
                                                <td><?= isset($appointment['phone_number']) ? htmlspecialchars($appointment['phone_number']) : 'N/A'; ?></td>
                                                <td><?= isset($appointment['created_date']) ? date("F d, Y", strtotime($appointment['created_date'])) : 'N/A'; ?></td>
                                                <td><?= isset($appointment['created_time']) ? date("h:i A", strtotime($appointment['created_time'])) : 'N/A'; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <form action="" method="post" class="me-2">
                                                            <input type="hidden" name="userid" value="<?= isset($appointment['appointment_reference']) ? htmlspecialchars($appointment['appointment_reference']) : ''; ?>">
                                                            <input type="hidden" name="approved" value="Approved">
                                                            <input type="hidden" name="appointment_id" value="<?= isset($appointment['appointment_id']) ? htmlspecialchars($appointment['appointment_id']) : ''; ?>">
                                                            <input type="hidden" name="user_id" value="<?= isset($appointment['user_id']) ? htmlspecialchars($appointment['user_id']) : ''; ?>">
                                                            <input type="hidden" name="phone_number" value="<?= isset($appointment['phone_number']) ? htmlspecialchars($appointment['phone_number']) : ''; ?>">
                                                            <input type="hidden" name="fullname" value="<?= isset($appointment['fullname']) ? htmlspecialchars($appointment['fullname']) : ''; ?>">
                                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                        </form>
                                                        <form action="" method="post">
                                                            <input type="hidden" name="userid" value="<?= isset($appointment['appointment_reference']) ? htmlspecialchars($appointment['appointment_reference']) : ''; ?>">
                                                            <input type="hidden" name="decline" value="Decline">
                                                            <input type="hidden" name="appointment_id" value="<?= isset($appointment['appointment_id']) ? htmlspecialchars($appointment['appointment_id']) : ''; ?>">
                                                            <input type="hidden" name="user_id" value="<?= isset($appointment['user_id']) ? htmlspecialchars($appointment['user_id']) : ''; ?>">
                                                            <input type="hidden" name="phone_number" value="<?= isset($appointment['phone_number']) ? htmlspecialchars($appointment['phone_number']) : ''; ?>">
                                                            <input type="hidden" name="fullname" value="<?= isset($appointment['fullname']) ? htmlspecialchars($appointment['fullname']) : ''; ?>">
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
                    </section>
                </div>
            </div>

        </div>
    </div>
    </div>
    </div>
    </div>

    </section>
    </div>

    <footer>
        <div class="footer clearfix mb-0 text-muted">
            <div class="float-start">
                <p>2021 &copy; Mazer</p>
            </div>
            <div class="float-end">
                <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a
                        href="http://ahmadsaugi.com">A. Saugi</a></p>
            </div>
        </div>
    </footer>
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