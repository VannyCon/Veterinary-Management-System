<?php
// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    // Create a PDO instancea
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch accepted users
    $query = "SELECT id, isApproved, user_id, username, password, fullname, address, phone_number FROM tbl_user WHERE isApproved = 1";
    $stmt = $pdo->query($query);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}



// Edit User
if (isset($_POST['edit_user'])) {
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

    try {
        // Update query
        $updateQuery = "UPDATE tbl_user 
                        SET username = :username, 
                            password = :password, 
                            fullname = :fullname, 
                            address = :address, 
                            phone_number = :phone_number  
                        WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([
            'username' => $username,
            'password' => $password,
            'fullname' => $fullname,
            'address' => $address,
            'phone_number' => $phone_number,
            'id' => $id
        ]);

        $_SESSION['message'] = "User information updated successfully!";
        header("Location: all_user.php");
        exit;
        
    } catch (PDOException $e) {
        echo "Error updating user: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadiz City Veterinary Office</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Montserrat:wght@600&display=swap" rel="stylesheet">
   
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
    <!-- Main Content -->

    <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Pet Record</h3>
                            <p class="text-subtitle text-muted">Manage and view details of all staff members</p>
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
                <section class="section">

                <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Client and Pet Records</h5>
    </div>
    <div class="card-body">
    <table class="table table-striped" id="table1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Information</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['phone_number']) ?></td>
                        <td>
                            <a href="userinfo.php?user_id=<?= htmlspecialchars($row['user_id']) ?>" class="btn btn-primary">View</a>
                        </td>
                        <td>
                        <button
                                    class="btn btn-success btn-sm edit-button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUsersModal"
                                    data-id="<?= htmlspecialchars($row['id']) ?>"
                                    data-fullname="<?= htmlspecialchars($row['fullname']) ?>"
                                    data-address="<?= htmlspecialchars($row['address']) ?>"
                                    data-phone_number="<?= htmlspecialchars($row['phone_number']) ?>"
                                    data-username="<?= htmlspecialchars($row['username']) ?>">
                                    Edit
                                </button>
                           
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


  <!-- Edit Modal -->
<div class="modal fade" id="editUsersModal" tabindex="-1" aria-labelledby="editUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUsersModalLabel">Edit User</h5>
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
            </div>
            <div class="modal-footer">
                <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>


  
<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Prefill Edit Modal
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('edit-id').value = button.dataset.id;
                document.getElementById('edit-username').value = button.dataset.username;
                document.getElementById('edit-fullname').value = button.dataset.fullname;
                document.getElementById('edit-address').value = button.dataset.address;
                document.getElementById('edit-phone_number').value = button.dataset.phone_number;
            });
        });

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

        // Prefill Delete Modal
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('delete-id').value = button.dataset.id;
                document.getElementById('delete-username').textContent = button.dataset.username;
                document.getElementById('delete-fullname').textContent = button.dataset.fullname;
            });
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>