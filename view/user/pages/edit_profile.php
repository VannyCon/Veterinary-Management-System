<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user info using user_id
    $query = "SELECT `id`, `user_id`, `fullname`, `address`, `phone_number`, `username`, `password` 
              FROM `tbl_user` WHERE `user_id` = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo '<p class="text-danger">User not found.</p>';
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate input data
        $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        if (empty($fullname) || empty($phone_number) || empty($username)) {
            echo '<p class="text-danger">Full Name, Phone Number, and Username are required fields.</p>';
        } else {
            // Update query
            $updateQuery = "
                UPDATE `tbl_user`
                SET `fullname` = ?, `address` = ?, `phone_number` = ?, `username` = ?, `password` = ?
                WHERE `user_id` = ?;
            ";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([$fullname, $address, $phone_number, $username, $password, $_SESSION['user_id']]);

            // Set a success message and redirect
            $_SESSION['message'] = "Profile updated successfully!";
            header('Location: dashboard.php');
            exit;
        }
    }
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add Font Awesome for the icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function togglePassword() {
            var passwordField = document.getElementById('password');
            var icon = document.getElementById('togglePassword');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-primary">Edit Profile</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" 
                    value="<?php echo isset($user['fullname']) ? htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" 
                    value="<?php echo isset($user['address']) ? htmlspecialchars($user['address'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" 
                    value="<?php echo isset($user['phone_number']) ? htmlspecialchars($user['phone_number'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" 
                    value="<?php echo isset($user['username']) ? htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="password" name="password" 
                        value="<?php echo isset($user['password']) ? htmlspecialchars($user['password'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    <i class="fa-solid fa-eye position-absolute" id="togglePassword" style="top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;" onclick="togglePassword()"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
