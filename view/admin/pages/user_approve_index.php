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

// Delete User
if (isset($_POST['delete_user'])) {
    $id = $_POST['id'];

    try {
        // Prepare delete query
        $deleteQuery = "DELETE FROM tbl_user WHERE id = :id";
        $stmt = $pdo->prepare($deleteQuery);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>('User deleted successfully.'); window.location.href='user_approve_index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Error deleting user: " . $e->getMessage();
    }
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
        header("Location: user_approve_index.php");
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
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        nav.navbar {
            background-color: #dc3545;
        }

        nav .navbar-brand {
            font-family: 'Montserrat', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        h1 {
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 30px;
        }

        table {
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 15px;
            text-align: center;
        }

        table th {
            background-color: #f1f1f1;
        }

        table tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .navbar-nav .nav-link {
            font-size: 16px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #ffc107;
        }

        .no-records {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 16px;
        }

        footer {
            text-align: center;
            margin-top: 50px;
            padding: 15px;
            background-color: #343a40;
            color: #fff;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="staff.php">Staff</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transactions.php">Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="approved.php">Approved Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../index.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1>Accepted Accounts</h1>

        <?php if (empty($result)): ?>
            <div class="no-records">
                No accepted accounts found.
            </div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>STATUS</th>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Phone Number</th>
                        <th>Actions Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td class="text-success" style="font-weight: bolder;"><?= $row['isApproved'] ? 'APPROVED' : 'DECLINED' ?></td>
                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td><?= htmlspecialchars($row['phone_number']) ?></td>
                            <td>
                                <button
                                    class="btn btn-info btn-sm view-button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewUserModal"
                                    data-id="<?= htmlspecialchars($row['id']) ?>"
                                    data-fullname="<?= htmlspecialchars($row['fullname']) ?>"
                                    data-address="<?= htmlspecialchars($row['address']) ?>"
                                    data-phone_number="<?= htmlspecialchars($row['phone_number']) ?>"
                                    data-username="<?= htmlspecialchars($row['username']) ?>"
                                    data-password="<?= htmlspecialchars($row['password']) ?>">
                                    View
                                </button>
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
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm delete-button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteUserModal"
                                    data-id="<?= htmlspecialchars($row['id']) ?>"
                                    data-username="<?= htmlspecialchars($row['username']) ?>"
                                    data-fullname="<?= htmlspecialchars($row['fullname']) ?>">
                                    Delete
                                </button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
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
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this account?</p>
                    <p><strong>Full Name:</strong> <span id="delete-fullname"></span></p>
                    <p><strong>Username:</strong> <span id="delete-username"></span></p>
                    <input type="hidden" name="id" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Prefill Edit Modal
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', () => {
                // Get the data attributes from the button
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
        // Prefill Delete Modal
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', () => {
                // Set the modal's input and text fields
                document.getElementById('delete-id').value = button.dataset.id;
                document.getElementById('delete-username').textContent = button.dataset.username;
                document.getElementById('delete-fullname').textContent = button.dataset.fullname;
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

</body>

</html>