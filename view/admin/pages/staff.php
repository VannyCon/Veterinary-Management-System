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
        $username = $_POST['username'];
        $password = $_POST['password'];

        $insertQuery = "INSERT INTO tbl_staff (username, password) VALUES (:username, :password)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute(['username' => $username, 'password' => $password]);

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
        $username = $_POST['username'];
        $password = $_POST['password'];

        $updateQuery = "UPDATE tbl_staff SET username = :username, password = :password WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute(['id' => $id, 'username' => $username, 'password' => $password]);

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
                        <a class="nav-link" href="../../../index.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-danger">Manage Staff</h1>
        <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addStaffModal">Add Staff</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <span class="password-text"><?= htmlspecialchars($row['password']) ?></span>
                        </td>
                        <td>
                            <!-- Edit Button -->
                            <button
                                class="btn btn-success btn-sm edit-button"
                                data-bs-toggle="modal"
                                data-bs-target="#editStaffModal"
                                data-id="<?= htmlspecialchars($row['id']) ?>"
                                data-username="<?= htmlspecialchars($row['username']) ?>"
                                data-password="<?= htmlspecialchars($row['password']) ?>">
                                Edit
                            </button>
                            <!-- Delete Form -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                <button type="submit" name="delete_staff" class="btn btn-danger btn-sm">Delete</button>
                            </form>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
                    <button type="submit" name="edit_staff" class="btn btn-primary">Save Changes</button>
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

</body>

</html>