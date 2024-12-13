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
    function getAllPetInfo($pdo, $pet_id) {
        try {
            $query = "SELECT `id`, `pet_id`, `pet_name`, `pet_species`, `pet_age` 
                      FROM `tbl_pet` WHERE `pet_id` = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$pet_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
            return [];
        }
    }

    // Function to get pet history
    function getAllPetHistory($pdo, $pet_id) {
        try {
            $query = "
                SELECT 
                    ap.id AS appointment_pet_id,
                    ap.appointment_id,
                    ap.user_id,
                    ap.pet_id,
                    ap.pet_symptoms,
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
                    tbl_diagnosis d ON ap.appointment_id = d.appointment_id
                JOIN 
                    tbl_appointment a ON ap.appointment_id = a.appointment_id
                JOIN 
                    tbl_service s ON a.service_id = s.service_id
                WHERE 
                    ap.pet_id = ?
                GROUP BY 
                    ap.appointment_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$pet_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
            return [];
        }
    }

    // Function to get user profile information
    function getUserProfile($pdo, $user_id) {
        try {
            $query = "SELECT fullname, address, phone_number
                      FROM tbl_user WHERE user_id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
            return null;
        }
    }

    session_start(); // Start session
    $user_id = $_SESSION['user_id'];
    $petID = $_GET['pet_id'];

    // Fetch data
    $pets = getAllPetInfo($pdo, $petID);
    $petHistory = getAllPetHistory($pdo, $petID);
    $userProfile = getUserProfile($pdo, $user_id);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
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

    <link rel="stylesheet" href="../../../assets/vendors/iconly/bold.css">
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

                        <li class="sidebar-item active ">
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

                        <li class="sidebar-item  ">
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
                </div>

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
                            
                            <h3>Pet Record</h3>
                            <p class="text-subtitle text-muted">Your Pet Information and Daignosis</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Pet Record</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            <div class="page-heading">
</div>

                <!-- Print Button placed above Pet Information -->

                <button class="btn btn-primary text-white px-3 py-1" style="font-size: 1rem;" onclick="printDiv()">
    <i class="fa fa-print mr-1"></i> Print
</button>
                </div>
    <!-- Printable Section -->
    <div id="printSection" class="row mt-4">
        <!-- Pet Owner's Profile -->
        <div class="col-12 col-md-6 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white" style="font-size: 1.1rem; font-weight: 500;">Pet Owner's Profile</div>
                <div class="card-body">
                    <?php 
                    if ($userProfile) {
                        echo '<p><strong>Name:</strong> ' . htmlspecialchars($userProfile['fullname']) . '</p>';
                        echo '<p><strong>Address:</strong> ' . htmlspecialchars($userProfile['address']) . '</p>';
                        echo '<p><strong>Phone:</strong> ' . htmlspecialchars($userProfile['phone_number']) . '</p>';
                    } else {
                        echo '<p>No profile information found.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Pet Information -->
        <div class="col-12 col-md-6 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white" style="font-size: 1.1rem; font-weight: 500;">Pet Information</div>
                <div class="card-body">
                    <?php 
                    if (!empty($pets)) {
                        foreach ($pets as $pet) {
                            echo '<p><strong>Name:</strong> ' . htmlspecialchars($pet['pet_name']) . '</p>';
                            echo '<p><strong>Species:</strong> ' . htmlspecialchars($pet['pet_species']) . '</p>';
                            echo '<p><strong>Age:</strong> ' . htmlspecialchars($pet['pet_age']) . '</p>';
                        }
                    } else {
                        echo '<p>No pets found.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Pet Diagnosis History -->
        <div class="col-12 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white" style="font-size: 1.1rem; font-weight: 500;">Pet Diagnosis History</div>
                <div class="card-body">
                    <?php 
                    if (!empty($petHistory)) {
                        foreach ($petHistory as $history) {
                            echo '<p><strong>Date:</strong> ' . date("F d, Y", strtotime($history['created_date'])) . '</p>';
                            echo '<p><strong>Service:</strong> ' . htmlspecialchars($history['service_name']) . '</p>';
                            echo '<p><strong>Pet Concern:</strong> ' . htmlspecialchars($history['pet_symptoms']) . '</p>';
                            echo '<p><strong>Diagnosis:</strong> ' . htmlspecialchars($history['pet_diagnosis']) . '</p>';
                            echo '<p><strong>Medicine Prescribe:</strong> ' . htmlspecialchars($history['pet_medication_prescribe']) . '</p>';
                            echo '<p><strong>Doctor Remarks:</strong> ' . htmlspecialchars($history['pet_doctor_notes']) . '</p>';
                            echo '<hr>';
                        }
                    } else {
                        echo '<p>No history found for this pet.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End Printable Section -->


<script>
    function printDiv() {
        var printContents = document.getElementById('printSection').innerHTML; 
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="../../../assets/js/bootstrap.bundle.min.js"></script>
<script src="../../../assets/vendors/simple-datatables/simple-datatables.js"></script>
<script src="../../../assets/js/main.js"></script>

</body>
</html>
