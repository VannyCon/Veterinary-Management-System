<?php
session_start();

// Database connection settings
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch all staff
$query = "SELECT * FROM tbl_staff";
$result = $pdo->query($query);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_staff'])) {
        // Add new staff
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        $insertQuery = "INSERT INTO tbl_staff (fullname, username, password) VALUES (:fullname, :username, :password)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute(['fullname' => $fullname, 'username' => $username, 'password' => $password]);

        header("Location: staff.php");
        exit;
    } elseif (isset($_POST['delete_staff'])) {
        // Delete staff
        $id = $_POST['id'];
        $deleteQuery = "DELETE FROM tbl_staff WHERE id = :id";
        $stmt = $pdo->prepare($deleteQuery);
        $stmt->execute(['id' => $id]);

        header("Location: staff.php");
        exit;
    } elseif (isset($_POST['edit_staff'])) {
        // Edit staff
        $id = $_POST['id'];
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        $updateQuery = "UPDATE tbl_staff SET fullname = :fullname, username = :username, password = :password WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute(['id' => $id, 'fullname' => $fullname, 'username' => $username, 'password' => $password]);

        header("Location: staff.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/bootstrap.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/vendors/simple-datatables/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">

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

                        <li class="sidebar-itemactive ">
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
                                    <a href="appointment_transactions.php">Pending</a>
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

                        <li class="sidebar-item active ">
                            <a href="staff.php" class='sidebar-link'>
                            <i class="bi bi-person-badge-fill"></i>
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
                            <h3>Staff</h3>
                            <p class="text-subtitle text-muted">Manage and view details of all staff members</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">staff</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                         <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Staff Table</h5>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                                    <i class="bi bi-plus"></i> Add Staff
                                </button>
                            </div>
                            <div class="card-body">
                            <table class="table table-striped" id="table1">
                                <thead>
                                    <tr>
                    <th>#</th>
                    <th>Fullname</th>
                    <th>Username</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                         <!-- Edit Button -->
                        <button
                            class="btn btn-success btn-sm edit-button"
                            data-bs-toggle="modal"
                            data-bs-target="#editStaffModal"
                            data-id="<?= htmlspecialchars($row['id']) ?>"
                            data-fullname="<?= htmlspecialchars($row['fullname']) ?>"
                            data-username="<?= htmlspecialchars($row['username']) ?>"
                            data-password="<?= htmlspecialchars($row['password']) ?>">
                            Edit
                        </button>


                           <!-- Delete Button -->
                    <button
                        type="button"
                        class="btn btn-danger btn-sm delete-button"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-id="<?= htmlspecialchars($row['id']) ?>">
                        Delete
                    </button>


                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>


    <!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <p>Are you sure you want to delete this staff member?</p>
                    <input type="hidden" name="id" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="submit" name="delete_staff" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>





    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStaffModalLabel">Add Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Fullname</label>
                        <input type="text" name="fullname" id="fullname" class="form-control" required>
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
                    <button type="submit" name="add_staff" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>


<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStaffModalLabel">Edit Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="edit-id">
                <div class="mb-3">
                    <label for="edit-fullname" class="form-label">Fullname</label>
                    <input type="text" name="fullname" id="edit-fullname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="edit-username" class="form-label">Username</label>
                    <input type="text" name="username" id="edit-username" class="form-control" required>
                </div>
                <div class="mb-3 position-relative">
                    <label for="edit-password" class="form-label">Password</label>
                    <input type="password" name="password" id="edit-password" class="form-control pe-5" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="edit_staff" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function () {
                // Get the data attributes from the button
                const id = this.dataset.id;
                const fullname = this.dataset.fullname;
                const username = this.dataset.username;
                const password = this.dataset.password;

                // Set values in the modal fields
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-fullname').value = fullname;
                document.getElementById('edit-username').value = username;
                document.getElementById('edit-password').value = password;
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listeners to all delete buttons
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function () {
                // Get the staff ID from the data-id attribute
                const id = this.dataset.id;

                // Set the value of the hidden input field in the modal
                document.getElementById('delete-id').value = id;
            });
        });
    });
</script>


    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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