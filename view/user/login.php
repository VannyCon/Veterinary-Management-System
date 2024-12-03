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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Check if fields are empty
    if (empty($username) || empty($password)) {
        echo "Username and password are required!";
        exit;
    }

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
                echo "Invalid approval status.";
                exit;
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
    $conn->close();
}

?>
