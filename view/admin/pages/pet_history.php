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
            // SQL query to fetch pets by pet_id
            $query = "
                SELECT `id`, `pet_id`, `pet_name`, `pet_species`, `pet_age` 
                FROM `tbl_pet` WHERE `pet_id` = ?;
            ";
            
            // Prepare and execute the query
            $stmt = $pdo->prepare($query); 
            $stmt->execute([$pet_id]); 
            
            // Fetch all results (multiple pets for the user)
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Handle any errors
            echo json_encode(['error' => $e->getMessage()]);
            return [];
        }
    }

    function getAllPetHistory($pdo, $pet_id) {
        try {
            // SQL query to fetch pet history by pet_id
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
                    ap.appointment_id
            ";
            
            // Prepare and execute the query
            $stmt = $pdo->prepare($query);
            $stmt->execute([$pet_id]); // Correctly bind $pet_id
            
            // Fetch all results
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Handle any errors
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

    // Get pet_id from the URL
    $petID = $_GET['pet_id']; // Assuming pet_id is passed via the URL
    
    // Get user_id from session or hardcode it (this should come from session or a secure source)
    $user_id = $_GET['user_id'];  // Example hardcoded user_id

    // Call the function to get all pets and pet history
    $pets = getAllPetInfo($pdo, $petID); // Get all pets for the user
    $petHistory = getAllPetHistory($pdo, $petID); // Get all pet history
    $userInfo = getUserInfo($pdo, $user_id); // Get user information (not displayed)

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
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
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

                        <li class="sidebar-item  has-sub ">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Appointment</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item ">
                                    <a href="appointment_transactions.php">Pending</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="approved.php">Approved</a>
                                    </li>
                              
                              </ul>

                                <li class="sidebar-item active ">
                                 <a href="all_user.php" class='sidebar-link'>
                                 <i class="bi bi-pen-fill"></i>
                                <span>Pet Record</span>
                            </a>
                        </li>
                              
                         
                        </li>
                        <li class="sidebar-item  ">
                            <a href="events.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Events</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Manage User &amp; Staff</li>

                        <li class="sidebar-item   has-sub">
                            <a href="users.php" class='sidebar-link'>
                                <i class="bi bi-hexagon-fill"></i>
                                <span>User Request</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item  ">
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

</div>
<div id="main">
                <header class="mb-3">
                    <a href="#" class="burger-btn d-block d-xl-none">
                        <i class="bi bi-justify fs-3"></i>
                    </a>
                </header>
                <div class="page-heading">
                <div id="printSection" class="row mt-4">
        <!-- Pet Owner's Profile -->
     

        <div class="page-heading">
                
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            
                            <h3>Pet Data</h3>
                            <p class="text-subtitle text-muted">Client and Daignosis data</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item "> <a href="userinfo.php">Pet Owner Data</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Pet  Data</li>
                                </ol>
                            </nav>
                        </div>
                        
                    </div>
                </div>
            <div class="page-heading">

        
        
        <a href="print_pet_info.php?pet_id=<?php echo htmlspecialchars($petID); ?>&user_id=<?php echo htmlspecialchars($user_id); ?>" 
   class="btn btn-primary" 
   target="_blank">
   <i class="fa fa-print"></i> Print Record
</a>
      </div>

        <div class="card">
            <div class="card-body">


            <?php 
                // Display pets in a card format
                if (!empty($pets)) {
                    foreach ($pets as $pet) {
                        echo '
                            <h5 class="card-title">Name: ' . htmlspecialchars($pet['pet_name']) . '</h5>
                            <p class="card-text">Species: ' . htmlspecialchars($pet['pet_species']) . '</p>
                            <p class="card-text">Age: ' . htmlspecialchars($pet['pet_age']) . '</p>';
                    }
                } else {
                    echo "<p>No pets found.</p>";
                }
            ?>
              
                
            </div>
        </div>
        <h4>Pet Diagnosis History</h4>
        <div class="card">
            <div class="card-body">
            <?php 
            // Display pet diagnosis history
            if (!empty($petHistory)) {
                foreach ($petHistory as $history) {
                    echo '
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Date: ' . date("F d, Y", strtotime($history['created_date'])) . '</h5> 
                            <h5 class="card-title">Time: ' . $history['created_time'] . '</h5> 
                            <p class="card-text">Service: ' . htmlspecialchars($history['service_name']) . '</p>
                            <p class="card-text">Pet Concern: ' . htmlspecialchars($history['pet_symptoms']) . '</p>
                            <p class="card-text">Diagnosis: ' . htmlspecialchars($history['pet_diagnosis']) . '</p>
                            <p class="card-text">Medicine: ' . htmlspecialchars($history['pet_medication_prescribe']) . '</p>
                            <p class="card-text">Doctor Notes: ' . htmlspecialchars($history['pet_doctor_notes']) . '</p>
                        </div>
                    </div>';
                }
            } else {
                echo "<p>No history found for this pet.</p>";
            }
            ?>
            </div>
        </div>
        <br>
       
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    
</script>


<script src="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="../../../assets/js/bootstrap.bundle.min.js"></script>

<script src="../../../assets/vendors/simple-datatables/simple-datatables.js"></script>


<script src="../../../assets/js/main.js"></script>
<script src="../../../assets/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="../../../js/bootstrap.bundle.min.js"></script>
</body>

</html>
</body>
