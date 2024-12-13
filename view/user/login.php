<?php
session_start();
$servername = "localhost"; // Change as per your setup
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "pet_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ''; // Variable to store error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Check if fields are empty
    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required!";
    } else {
        // Query to check user credentials
        $query = "SELECT id, user_id, username, password, isApproved FROM tbl_user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Compare plaintext passwords (Consider hashing in production)
            if ($password === $user['password']) {
                /////////////////////////////////////////////////////
                // Store user_id and isApproved in session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['isApproved'] = $user['isApproved'];
                /////////////////////////////////////////////////////

                // Redirect based on approval status
                if ($user['isApproved'] === 1) {
                    header("Location: pages/dashboard.php");
                    exit;
                } else if ($user['isApproved'] === 0) {
                    header("Location: pages/decline.php");
                    exit;
                } else if (is_null($user['isApproved'])) { // Check explicitly for NULL
                    header("Location: pages/pending.php");
                    exit;
                } else {
                    $error_message = "Invalid approval status.";
                }
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "User not found.";
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow" style="width: 500px;">
            <div class="card-header bg-primary text-white text-center">
                <h4>User Login</h4>
            </div>
            <div class="card-body">
                <!-- Login Form -->
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="userUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="userUsername" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Password</label>   
                        <input type="password" class="form-control" id="userPassword" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <!-- Links for Registration and Forgot Password -->
                <div class="mt-4 text-center">
                    <p class="mb-2">
                        <span>Are you new? </span><a href="registration.php" class="text-primary fw-bold">Register Now</a>
                    </p>
                    <p class="mb-3">
                        <span>Forgot password? </span><a href="forgot_password.php" class="text-primary fw-bold">Reset Password</a>
                    </p>
                    <a href="../../index.php" class="btn btn-success w-100">Back to Main Login</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Error Messages -->
    <?php if ($error_message): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Login Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo $error_message; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Show the error modal if there's an error message
        <?php if ($error_message): ?>
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
        <?php endif; ?>
    </script>
</body>

</html>
