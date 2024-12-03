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
    $query = "SELECT `id`, `user_id`, `fullname`, `address`, `phone_number` 
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

        if (empty($fullname) || empty($phone_number)) {
            echo '<p class="text-danger">Full Name and Phone Number are required fields.</p>';
        } else {
            // Update query
            $updateQuery = "
                UPDATE `tbl_user`
                SET `fullname` = ?, `address` = ?, `phone_number` = ?
                WHERE `user_id` = ?;
            ";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([$fullname, $address, $phone_number, $_SESSION['user_id']]);

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
                <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" 
                       value="<?= htmlspecialchars($user['address'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" 
                       value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
