<?php
// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

// Create a PDO instance at the start
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Check if the form for approval is submitted
if (isset($_POST['approve_user'])) {
    $id = $_POST['id'];
    $isApproved = 1; // Approved value

    try {
        // Prepare the update query
        $updateQuery = "UPDATE tbl_user SET isApproved = :isApproved WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':isApproved', $isApproved, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('User approved successfully.'); window.location.href='user_decline_index.php';</script>";
        } else {
            echo "Error approving user.";
        }
        header("Location: user_approve_index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error approving user: " . $e->getMessage();
    }
}

// Fetch declined users
try {
    $query = "SELECT id, isApproved, user_id, username, fullname, address, phone_number FROM tbl_user WHERE isApproved = 0";
    $stmt = $pdo->query($query);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching declined users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../index.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Declined Accounts</h1>
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
                    <th>Confirmation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td class="text-danger" style="font-weight: bolder;"><?= $row['isApproved'] == 0 ? 'DECLINED' : 'APPROVED' ?></td>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
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
                                                <button type="submit" name="approve_user" class="btn btn-success">Yes, Approve</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Approve Confirmation Modal -->
    <div class="modal fade" id="approveUserModal" tabindex="-1" aria-labelledby="approveUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveUserModalLabel">Confirm Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this user?</p>
                    <p><strong>Full Name:</strong> <span id="approve-fullname"></span></p>
                    <p><strong>Username:</strong> <span id="approve-username"></span></p>
                    <input type="hidden" id="approve-id" name="id">
                    <input type="hidden" id="approve-isApproved" name="isApproved">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="">
                        <button type="submit" name="approve_user" class="btn btn-primary">Approve</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prefill the Approve Modal with user data
        document.querySelectorAll('.approve-button').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('approve-id').value = button.dataset.id;
                document.getElementById('approve-username').textContent = button.dataset.username;
                document.getElementById('approve-fullname').textContent = button.dataset.fullname;
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>