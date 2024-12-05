<?php

/////////////////////////////////////////////////////
session_start(); // Ensure the session is started  
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); 
}
/////////////////////////////////////////////////////

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
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">User Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="navfitem"><a class="nav-link active" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="transaction.php">Transaction</a></li>
                    <li class="nav-item"><a class="nav-link" href="appointment.php">Appointment</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-primary">Welcome, User!</h1>
        <p>Here, you can view and update your profile information.</p>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Profile</h5>
                <p class="card-text">Update your personal information and preferences.</p>
                <!-- Fetch user info -->
                <?php
                try {
                    $query = "SELECT id, username, password, fullname, address, phone_number, isApproved 
                    FROM tbl_user 
                    WHERE user_id = ?"; 
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user) {
                        echo '<ul class="list-group">';
                        echo '<li class="list-group-item"><strong>Full Name:</strong> ' . htmlspecialchars($user['fullname']) . '</li>';
                        echo '<li class="list-group-item"><strong>Username:</strong> ' . htmlspecialchars($user['username']) . '</li>';
                        echo '<li class="list-group-item"><strong>Password:</strong> ' . htmlspecialchars($user['password']) . '</li>';
                        echo '<li class="list-group-item"><strong>Address:</strong> ' . htmlspecialchars($user['address']) . '</li>';
                        echo '<li class="list-group-item"><strong>Phone Number:</strong> ' . htmlspecialchars($user['phone_number']) . '</li>';
                        echo '<li class="list-group-item"><strong>Account Approval Status:</strong> ' . ($user['isApproved'] ? 'Approved' : 'Pending') . '</li>';
                        echo '</ul>';
                    } else {
                        echo '<p class="text-danger">User information not found for ID: ' . htmlspecialchars($_SESSION['user_id']) . '</p>';
                    }
                } catch (PDOException $e) {
                    echo '<p class="text-danger">Error fetching user information: ' . htmlspecialchars($e->getMessage()) . '</p>';
                }
                ?>

                <a href="edit_profile.php" class="btn btn-primary mt-3">Edit Profile</a>
            </div>
        </div>

        <br>

        <div class="card mb-3">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title">Pet</h5>
                    </div>
                    <div class="col text-end">
                        <a href="pet_add.php" class="btn btn-warning">Add Pet</a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <?php
                    // Check if there are any pets to display
                    if (!empty($pets)) {
                        foreach ($pets as $pet) {
                            echo '
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">' . htmlspecialchars($pet['pet_name']) . '</h5>
                                            <p class="card-text">Species: ' . htmlspecialchars($pet['pet_species']) . '</p>
                                            <p class="card-text">Age: ' . htmlspecialchars($pet['pet_age']) . '</p>
                                            <!-- Trigger Edit Modal -->
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editPetModal" data-pet-id="' . htmlspecialchars($pet['pet_id']) . '" data-pet-name="' . htmlspecialchars($pet['pet_name']) . '" data-pet-species="' . htmlspecialchars($pet['pet_species']) . '" data-pet-age="' . htmlspecialchars($pet['pet_age']) . '">Edit</button>
                                            
                                            <!-- Trigger Delete Modal -->
                                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletePetModal" data-pet-id="' . htmlspecialchars($pet['pet_id']) . '">Delete</button>
                                            
                                            <a href="pet_history.php?pet_id=' . htmlspecialchars($pet['pet_id']) . '" class="btn btn-info">History</a>
                                        </div>
                                    </div>
                                </div>';
                        }
                    } else {
                        // If no pets are found, display a message
                        echo "<p class='text-muted'>No pets found. Please add a pet.</p>";
                    }
                    ?>
                </div>
            </div>
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
