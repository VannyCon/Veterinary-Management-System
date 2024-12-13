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
    $query = "SELECT `id`, `username`, `password` FROM `tbl_staff` WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Compare plaintext passwords (use password hashing in production)
        if ($password === $user['password']) {
            // Start session and store session variables
            session_start(); 
            $_SESSION['staff'] = $user['username']; // Store username
            $_SESSION['isStaff'] = "Staff";
    
            // Redirect to dashboard
            header("Location: pages/dashboard.php");
            exit;
        } else {
            echo "Invalid password."; // Handle invalid password
        }
    } else {
        echo "User not found."; // Handle user not found
    }
    

    $stmt->close();
    $conn->close();
}
?>
