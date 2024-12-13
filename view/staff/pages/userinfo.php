<?php

/////////////////////////////////////////////////////
session_start(); // Ensure the session is started  
if(!isset($_GET['user_id'])) {
    header("Location: ../index.php"); 
}
/////////////////////////////////////////////////////

$user_id = $_GET['user_id'];
// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Function to fetch all pets for the logged-in user
    function getAllPetInfo($pdo, $user_id)
    {
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
    if (isset($user_id) && !empty($user_id)) {
        $pets = getAllPetInfo($pdo, $user_id);
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
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

   
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mazer Admin Dashboard</title>

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
<style>
    .day-cell {
        cursor: pointer;
        height: 80px;
        vertical-align: top;
        width: 90px;
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

    .appointment-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
        width: 100%;
    }

    .day-cell:hover {
        background-color: #f0f0f0;
    }
</style>

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

                        <li class="sidebar-item  ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Appointment</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item ">
                                    <a href="transactions.php">Pending</a>
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


                        <li class="sidebar-item  ">
                            <a href="events.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Events</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Manage User &amp; Staff</li>

                        <li class="sidebar-item  has-sub ">
                            <a href="users.php" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
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
                                </li>


                            </ul>
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





<div class="container mt-4">
    <div class="row">
        <!-- Breadcrumb Navigation -->
        <div class="col-12 col-md-6 order-md-2 ms-auto">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="all_user.php">Record</a></li>
                   
                    <li class="breadcrumb-item active" aria-current="page">Pet History</li>
                </ol>
            </nav>
        </div>
    </div> 
    <div class="page-heading">
                </div>
                <div class="page-content">
                    <section class="row">
                        <div class="col-12 col-lg-12">
                            <div class="row">
                                <div class="col-12 col-lg-12 col-md-2">
                                    <div class="card">
                                        <div class="card-body px-3 py-4-5">
                                            <div class="row">
                                            </div>
                                            <div class="col-md-12">
                                    <!-- Fetch user info -->
                                    <?php
                                    try {
                                        $query = "SELECT user_id, fullname, address, phone_number, isApproved 
                                                  FROM tbl_user 
                                                  WHERE user_id = ?"; 
                                        $stmt = $pdo->prepare($query);
                                        $stmt->execute([$_GET['user_id']]);
                                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                                        if ($user) {
                                            echo '<div class="profile-info">';
                                            echo '<p><strong>Full Name:</strong> ' . htmlspecialchars($user['fullname']) . '</p>';
                                            echo '<p><strong>Address:</strong> ' . htmlspecialchars($user['address']) . '</p>';
                                            echo '<p><strong>Phone Number:</strong> ' . htmlspecialchars($user['phone_number']) . '</p>';
                                            echo '<p><strong>Account Approval Status:</strong> ' . ($user['isApproved'] ? 'Approved' : 'Pending') . '</p>';
                                            echo '</div>';
                                        } else {
                                            echo '<p class="text-danger">User information not found for ID: ' . htmlspecialchars($_GET['user_id']) . '</p>';
                                        }
                                    } catch (PDOException $e) {
                                        echo '<p class="text-danger">Error fetching user information: ' . htmlspecialchars($e->getMessage()) . '</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                        </div>
                                <div class="col-6 col-lg-4 col-md-2">
                                    <div class="card">
                                        <div class="card-body px-3 py-4-5">
                                            <div class="row">
                                            </div>
                                            <div class="col-md-12">
                <?php
                // Check if there are any pets to display
                if (!empty($pets)) {
                    $counter = 0;
                    foreach ($pets as $pet) {
                        if ($counter % 3 == 0 && $counter > 0) {
                            // Create a new row every 3 pets
                            echo '</div><div class="row">';
                        }
                        echo '
                       <div class="col-6 col-lg-4 col-md-2">
                                    <div class="card">
                                        <div class="card-body px-1 py-4-5">
                                            <div class="row">
                                            </div>
                                            <div class="col-md-12">
                                           
                                            <h6 class="text-muted font-semibold">Pet Name</h6>
                                            <h5 class="card-title">' . htmlspecialchars($pet['pet_name']) . '</h5>
                                            <p class="card-text">Species: ' . htmlspecialchars($pet['pet_species']) . '</p>
                                            <p class="card-text">Age: ' . htmlspecialchars($pet['pet_age']) . '</p>
                                            <a href="pet_history.php?pet_id=' . htmlspecialchars($pet['pet_id']) .'&user_id='. htmlspecialchars($user['user_id']). '" class="btn btn-info">History</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                        $counter++;
                    }
                } else {
                    echo "<p class='text-muted'>No pets found. Please add a pet.</p>";
                }
                ?>
            </div> <!-- End of row -->
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
</body>

</html>
