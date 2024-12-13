<?php
session_start();

// Database connection settings
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_user'])) {
    $user_id = uniqid('USR_');
    $fullname = trim($_POST['fullname']);
    $address = trim($_POST['address']);
    $phone_number = trim($_POST['phone_number']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Insert user into database
    $insertQuery = "INSERT INTO tbl_user (user_id, fullname, address, phone_number, username, password)
                    VALUES (:user_id, :fullname, :address, :phone_number, :username, :password)";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->execute([
        'user_id' => $user_id,
        'fullname' => $fullname,
        'address' => $address,
        'phone_number' => $phone_number,
        'username' => $username,
        'password' => $password,
    ]);

    // Redirect or show success message
    $success = "Registration successful! Please wait for admin approval.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm w-100" style="max-width: 600px;">
            <div class="card-header bg-primary text-white text-center">
                <h4>Register Login</h4>
            </div>
            <div class="card-body">
                <h1 class="text-primary mb-4">Register New User</h1>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <form method="POST" action="registration.php" class="border p-4 rounded bg-light">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" name="fullname" id="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" id="address" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="password" class="form-control pe-5" required>
                            <i class="fa-solid fa-eye position-absolute" id="togglePassword" style="top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></i>
                        </div>
                    </div>

                    <button type="submit" name="register_user" class="btn btn-primary w-100">Register</button>
                    <a href="../../view/user/" class="btn btn-secondary w-100 mt-2">Back to User Login</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>
