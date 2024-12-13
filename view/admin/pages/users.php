<?php
session_start();

// Database connection settings
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    // Establish the database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch all users
$query = "SELECT id, user_id, username, password, fullname, address, phone_number, isApproved 
            FROM tbl_user 
            WHERE isApproved IS NULL;
            ";
$result = $pdo->query($query);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        // Add a new user
        $user_id = uniqid('USR_');
        $username = $_POST['username'];
        $password = $_POST['password'];
        $fullname = $_POST['fullname'];
        $address = $_POST['address'];
        $phone_number = $_POST['phone_number'];

        $insertQuery = "INSERT INTO tbl_user (user_id, username, password, fullname, address, phone_number) VALUES (:user_id, :username, :password, :fullname, :address, :phone_number)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([
            'user_id' => $user_id,
            'username' => $username,
            'password' => $password,
            'fullname' => $fullname,
            'address' => $address,
            'phone_number' => $phone_number,
        ]);

        header("Location: users.php");
        exit;
    } elseif (isset($_POST['delete_user'])) {
        // Delete user
        $id = $_POST['id'];
        $deleteQuery = "DELETE FROM tbl_user WHERE id = :id";
        $stmt = $pdo->prepare($deleteQuery);
        $stmt->execute(['id' => $id]);

        header("Location: users.php");
        exit;
    } elseif (isset($_POST['edit_user'])) {
        // Debugging: Log received data
        error_log('ID: ' . $_POST['id']);
        error_log('Username: ' . $_POST['username']);
        error_log('Password: ' . $_POST['password']);


        $id = $_POST['id'];
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];
        $fullname = $_POST['fullname'];
        $address = $_POST['address'];
        $phone_number = $_POST['phone_number'];

        $updateQuery = "UPDATE tbl_user SET username = :username, password = :password, fullname = :fullname, address = :address, phone_number = :phone_number  WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute(['username' => $username, 'password' => $password, 'fullname' => $fullname, 'address' => $address, 'phone_number' => $phone_number, 'id' => $id]);

        header("Location: users.php");
        exit;
    }
    // Determine the status based on which button was clicked


    try {
        // Get the process by value from session
        $process_by = $_SESSION['isWho'];
        
        // Check the action type (approved or declined) and set status and approval value
        if (isset($_POST['approved'])) {
            $status = "Approved";
            $isApproved = 1;  // Approved
        } elseif (isset($_POST['declined'])) {
            $status = "Declined";
            $isApproved = 0;  // Declined
        } else {
            throw new Exception("Invalid action.");
        }
    
        // Retrieve form data from POST request
        $id = $_POST['id'];
        $user_id = $_POST['user_id'];
        $phone_number = $_POST['phone_number'];
        $fullname = $_POST['fullname'];
    
        // Remove leading zero from phone number (if it exists)
        if ($phone_number[0] === '0') {
            $phone_number = substr($phone_number, 1);
        }
    
        // Ensure that you have validated the form data, e.g., checking if $id and other variables are set
    
        // Update user approval status in the database
        $updateQuery = "UPDATE tbl_user SET process_by = :process_by, isApproved = :isApproved WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([
            'process_by' => $process_by, 
            'isApproved' => $isApproved, 
            'id' => $id
        ]);
        
        // Set success message
        $_SESSION['message'] = "User has been " . strtolower($status) . " successfully!";
        
        // Redirect to transactions page after success
        header("Location: transactions.php");
        exit();
    
    } catch (Exception $e) {
        // Log error and show to user
        $_SESSION['error'] = "Error: " . $e->getMessage();
        
        // Redirect to the users page on error
        header("Location: users.php");
        exit();
    }
    
    
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadiz City Veterenary Office</title>

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
                        </li>

                        <li class="sidebar-item  ">
                            <a href="all_user.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Pet Record</span>
                            </a>
                        </li>
                        <li class="sidebar-item  ">
                            <a href="events.php" class='sidebar-link'>
                            <i class="bi bi-pen-fill"></i>
                                <span>Events</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Manage User &amp; Staff</li>

                        <li class="sidebar-item active  has-sub">
                            <a href="users.php" class='sidebar-link'>
                                <i class="bi bi-hexagon-fill"></i>
                                <span>User Request</span>
                            </a>
                            <ul class="submenu active">
                                <li class="submenu-item active ">
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
                        <div class="col-12 col-md-12 order-md-1 order-last">
                        <h3>List of Pending Accounts for Users</h3>
                            <p class="text-subtitle text-muted">Check users you want to approve</p>
                        </div>
                        <div class="col-12 col-md-12 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Pending Account</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            Pending Account
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['phone_number']) ?></td>
                        <td>
                            <!-- Approve Button -->
                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#approveModal<?= htmlspecialchars($row['id']) ?>">
                                Approve
                            </button>

                            <!-- Approve Modal -->
                            <div class="modal fade" id="approveModal<?= htmlspecialchars($row['id']) ?>" tabindex="-1" aria-labelledby="approveModalLabel<?= htmlspecialchars($row['id']) ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="approve_user.php">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="approveModalLabel<?= htmlspecialchars($row['id']) ?>">Approve User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to approve <strong><?= htmlspecialchars($row['fullname']) ?></strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                                <input type="hidden" name="approved" value="Approved">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>">
                                                <input type="hidden" name="phone_number" value="<?= htmlspecialchars($row['phone_number']) ?>">
                                                <input type="hidden" name="fullname" value="<?= htmlspecialchars($row['fullname']) ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Yes, Approve</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Decline Button -->
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#declineModal<?= htmlspecialchars($row['id']) ?>">
                                Decline
                            </button>

                            <!-- Decline Modal -->
                            <div class="modal fade" id="declineModal<?= htmlspecialchars($row['id']) ?>" tabindex="-1" aria-labelledby="declineModalLabel<?= htmlspecialchars($row['id']) ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="decline_user.php">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="declineModalLabel<?= htmlspecialchars($row['id']) ?>">Decline User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to decline <strong><?= htmlspecialchars($row['fullname']) ?></strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                                <input type="hidden" name="declined" value="Declined">
                                                <input type="hidden" name="user_id" valsue="<?= htmlspecialchars($row['user_id']) ?>">
                                                <input type="hidden" name="phone_number" value="<?= htmlspecialchars($row['phone_number']) ?>">
                                                <input type="hidden" name="fullname" value="<?= htmlspecialchars($row['fullname']) ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Yes, Decline</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" name="fullname" id="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" id="address" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Contact Number</label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="password" class="form-control pe-5" required>
                            <i class="fa-solid fa-eye position-absolute" id="togglePassword" style="top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></i>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Users Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Full Name:</strong> <span id="view-fullname"></span></p>
                    <p><strong>Address:</strong> <span id="view-address"></span></p>
                    <p><strong>Phone Number:</strong> <span id="view-phone_number"></span></p>
                    <p><strong>Username:</strong> <span id="view-username"></span></p>
                    <p><strong>Password:</strong> <span id="view-password"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Users Modal -->
    <div class="modal fade" id="editUsersModal" tabindex="-1" aria-labelledby="editUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUsersModalLabel">Edit Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">

                    <div class="mb-3">
                        <label for="edit-fullname" class="form-label">Full Name</label>
                        <input type="text" name="fullname" id="edit-fullname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit-address" class="form-label">Address</label>
                        <input type="text" name="address" id="edit-address" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit-phone_number" class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" id="edit-phone_number" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit-username" class="form-label">Username</label>
                        <input type="text" name="username" id="edit-username" class="form-control" required>
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="edit-password" class="form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="edit-password" class="form-control pe-5" required>
                            <i class="fa-solid fa-eye position-absolute" id="toggleEditPassword" style="top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></i>
                        </div>
                
                <div class="modal-footer">
                    <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    <script>
        // Prefill Edit Modal
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('edit-id').value = button.dataset.id;
                document.getElementById('edit-username').value = button.dataset.username;
                document.getElementById('edit-password').value = button.dataset.password;
                document.getElementById('edit-fullname').value = button.dataset.fullname;
                document.getElementById('edit-address').value = button.dataset.address;
                document.getElementById('edit-phone_number').value = button.dataset.phone_number;
            });
        });
    </script>

    <script>
        // Prefill View Modal
        document.querySelectorAll('.view-button').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('view-fullname').textContent = button.dataset.fullname;
                document.getElementById('view-address').textContent = button.dataset.address;
                document.getElementById('view-phone_number').textContent = button.dataset.phone_number;
                document.getElementById('view-username').textContent = button.dataset.username;
                document.getElementById('view-password').textContent = button.dataset.password;
            });
        });
    </script>

    <script>
        // Toggle password visibility for Add Modal
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye-slash');
        });

        // Toggle password visibility for Edit Modal
        const toggleEditPassword = document.querySelector('#toggleEditPassword');
        const editPassword = document.querySelector('#edit-password');

        toggleEditPassword.addEventListener('click', () => {
            const type = editPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            editPassword.setAttribute('type', type);
            toggleEditPassword.classList.toggle('fa-eye-slash');
        });
    </script>


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