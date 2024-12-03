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
    } elseif (isset($_POST['approve_user'])) {
        // Toggle user approval
        $id = $_POST['id'];
        $isApproved = $_POST['isApproved'] ? 0 : 1;

        $updateQuery = "UPDATE tbl_user SET isApproved = :isApproved WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute(['isApproved' => $isApproved, 'id' => $id]);

        header("Location: users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>


<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
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
                        <a class="nav-link" href="events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h1 class="text-danger">Manage Users</h1>
        <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
        <br>
        <a href="user_approve_index.php" class="btn btn-success mb-3">View Approved Users</a>
        <a href="user_decline_index.php" class="btn btn-danger mb-3">View Declined Users</a>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Confirmation</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><span class="password-text"><?= htmlspecialchars($row['password']) ?></span></td>
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
                                                <button type="submit" name="approve_user" class="btn btn-success">Yes, Approve</button>
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
                                                <button type="submit" name="decline_user" class="btn btn-danger">Yes, Decline</button>
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
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
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



</body>

</html>