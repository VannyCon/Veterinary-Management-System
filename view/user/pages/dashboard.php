<?php

/////////////////////////////////////////////////////
session_start(); // Ensure the session is started  
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); 
}
/////////////////////////////////////////////////////

// Redirect based on `isApproved` status
if (isset($_SESSION['isApproved'])) {
    if ($_SESSION['isApproved'] == 0) {
        header("Location: decline.php");
        exit;
    } elseif (is_null($_SESSION['isApproved'])) {
        header("Location: pending.php");
        exit;
    }
} else {
    // Handle case where `isApproved` is not set (optional)
    header("Location: ../index.php");
    exit;
}


// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user information
    $user_id = $_SESSION['user_id'];
    $query = "SELECT fullname FROM tbl_user WHERE user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Set the user name for the welcome message
    if ($user) {
        $user_name = $user['fullname'];
    } else {
        $user_name = 'User';
    }

    // Function to fetch all pets for the logged-in user
    function getAllPetInfo($pdo, $user_id) {
        try {
            // SQL query to fetch pets belonging to the user
            $query = "
                SELECT `id`, `pet_id`, `user_id`, `pet_name`, `pet_species`, `pet_age`
                FROM `tbl_pet`
                WHERE `user_id` = ?;
            ";

            // Prepare and execute the query
            $stmt = $pdo->prepare($query);
            $stmt->execute([$user_id]);

            // Return the results as an associative array
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle errors gracefully
            echo '<p class="text-danger">Error fetching pets: ' . htmlspecialchars($e->getMessage()) . '</p>';
            return [];
        }
    }

    // Fetch pet information for the logged-in user
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $pets = getAllPetInfo($pdo, $_SESSION['user_id']);
    } else {
        echo '<p class="text-danger">User is not logged in.</p>';
        exit;
    }
} catch (PDOException $e) {
    // Handle database connection errors
    echo '<p class="text-danger">Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
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
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="row">
                            <div class="col-12 col-lg-8 col-md-12">
                                <div class="card">
                                    <div class="card-body px-3 py-4-5">
                                        <div class="row">
                                            </div>
                                            
                                            <div class="col-md-12">
                                            <h1 id="welcomeMessage" class="text-primary">Hi, <?php echo htmlspecialchars($user_name); ?>!</h1>
                                            <p>Welocome to Cadiz City Veterinary Office. We're so glad you're here. </p>

                                            <a href="profile_view.php" class="btn btn-primary">View Your Profile</a>
                                          
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4 col-md-12">
                                <div class="card">
                                    <div class="card-body px-3 py-4-5">
                                        <div class="row">
                                            </div>
                                            <div class="col-md-12">
                                            <h4>Pet Registration </h4>
                                            <p>Add Your Pet's Information to Schedule an Appointment </p>
                                            <a href="pet_add.php" class="btn btn-primary">Add Pet</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h2>Pet Information</h2>

                               <?php
                                if (!empty($pets)) {
                                    foreach ($pets as $pet) {
                                        echo '
                                        <div class="col-6 col-lg-4 col-md-6">
                                            <div class="card">
                                                <div class="card-body px-3 py-4-5">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <h6 class="text-muted font-semibold">Pet Name</h6>
                                                            <h6 class="font-extrabold mb-0">' . htmlspecialchars($pet['pet_name']) . '</h6>
                                                            <p class="text-muted">Species: ' . htmlspecialchars($pet['pet_species']) . '</p>
                                                            <p class="text-muted">Age: ' . htmlspecialchars($pet['pet_age']) . '</p>
                                                            <div class="d-flex justify-content-around mt-3">
                                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editPetModal" data-pet-id="' . htmlspecialchars($pet['pet_id']) . '" data-pet-name="' . htmlspecialchars($pet['pet_name']) . '" data-pet-species="' . htmlspecialchars($pet['pet_species']) . '" data-pet-age="' . htmlspecialchars($pet['pet_age']) . '">Edit</button>
                                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deletePetModal" data-pet-id="' . htmlspecialchars($pet['pet_id']) . '">Delete</button>
                                                                <a href="pet_history.php?pet_id=' . htmlspecialchars($pet['pet_id']) . '" class="btn btn-success btn-sm">View</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                                    }
                                } else {
                                    echo "<p class='text-muted'>No pets found. Please add a pet.</p>";
                                }
                                ?>

                         </div>       
                             </div>
                                    
                            </section>
                        </div>

   

    
      
    
           <!-- Edit Pet Modal -->
        <div class="modal fade" id="editPetModal" tabindex="-1" aria-labelledby="editPetModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPetModalLabel">Edit Pet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="pet_edit.php" method="POST">
                            <input type="hidden" name="pet_id" id="editPetId">
                            <div class="mb-3">
                                <label for="editPetName" class="form-label">Pet Name</label>
                                <input type="text" class="form-control" id="editPetName" name="pet_name">
                            </div>
                            <div class="mb-3">
                                <label for="editPetSpecies" class="form-label">Species</label>
                                <input type="text" class="form-control" id="editPetSpecies" name="pet_species">
                            </div>
                            <div class="mb-3">
                                <label for="editPetAge" class="form-label">Age</label>
                                <input type="text" class="form-control" id="editPetAge" name="pet_age">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Pet Modal -->
        <div class="modal fade" id="deletePetModal" tabindex="-1" aria-labelledby="deletePetModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePetModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this pet?
                    </div>
                    <div class="modal-footer">
                        <form action="pet_delete.php" method="POST">
                            <input type="hidden" name="pet_id" id="deletePetId">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript for populating the modals with pet information
        var editPetModal = document.getElementById('editPetModal');
        editPetModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var petId = button.getAttribute('data-pet-id');
            var petName = button.getAttribute('data-pet-name');
            var petSpecies = button.getAttribute('data-pet-species');
            var petAge = button.getAttribute('data-pet-age');

            var modalPetId = editPetModal.querySelector('#editPetId');
            var modalPetName = editPetModal.querySelector('#editPetName');
            var modalPetSpecies = editPetModal.querySelector('#editPetSpecies');
            var modalPetAge = editPetModal.querySelector('#editPetAge');

            modalPetId.value = petId;
            modalPetName.value = petName;
            modalPetSpecies.value = petSpecies;
            modalPetAge.value = petAge;
        });

        var deletePetModal = document.getElementById('deletePetModal');
        deletePetModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var petId = button.getAttribute('data-pet-id');

            var modalDeletePetId = deletePetModal.querySelector('#deletePetId');
            modalDeletePetId.value = petId;
        });
    </script>
    <script src="../../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
                <script src="../../../assets/js/bootstrap.bundle.min.js"></script>

                <script src="../../../assets/vendors/apexcharts/apexcharts.js"></script>
                <script src="../../../assets/js/pages/dashboard.js"></script>

                <script src="../../../assets/js/main.js"></script>
            </body>

            </html>