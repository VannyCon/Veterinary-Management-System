<?php
session_start(); // Start session to access $_SESSION

// Database connection settings
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

// Check if user is logged in
if (!isset($_SESSION['admin'])) {
    die("Unauthorized access. Please log in.");
}

$user_id = $_SESSION['admin'];

try {
    // Establish PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Initialize variables
    $fullname = $_POST['fullname'];
    $phone_number = $_POST['phone_number'];
    $id = intval($_POST['id']);
    $status = '';  // Default status
    $isApproved = 1; // Default is approved, can be modified for decline
    $process_by = $_SESSION['isStaff'];

    // Check if the approval button is pressed
    if (isset($_POST['approved'])) {
        $status = "Approved";
        $isApproved = 1;  // Approved
    }
    // Check if the decline button is pressed
    elseif (isset($_POST['declined'])) {
        $status = "Declined";
        $isApproved = 0;  // Declined
    } else {
        throw new Exception("Invalid action.");
    }

    try {
        // Update user approval status in the database
        $updateQuery = "UPDATE tbl_user SET  process_by = :process_by, isApproved = :isApproved WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute(['process_by' => $process_by, 'isApproved' => $isApproved, 'id' => $id]);

        // Prepare SMS parameters
        $send_data = [
            'sender_id' => "PhilSMS", // Replace with your sender ID
            'recipient' => "+63$phone_number", // Replace with the recipient's number
            'message' => "", // Placeholder for message
        ];

        // Your API Token
        $token = "1222|Pr4dssTM79z2BbdzZOQKle1BWOrWL28eRo0TNjVV"; // Replace with your API token

        // Set SMS message based on approval or decline
        if ($status === "Approved") {
            $send_data['message'] = "Hi $fullname, Congratulations! Your account is approved.";
        } elseif ($status === "Declined") {
            $send_data['message'] = "Hi $fullname, Sorry, but your account has been declined.";
        }

        // Send SMS using the sendSMS function
        $parameters = json_encode($send_data);
        sendSMS($parameters, $token);

        // Set session message for success
        $_SESSION['message'] = "User has been " . strtolower($status) . " successfully!";
        
        // Redirect to another page after success
        header("Location: user_approve_index.php");
        exit();

    } catch (Exception $e) {
        // Log error and show to user
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: user_approve_index.php");  // Go back to the user approval page
        exit();
    }

} else {
    // Redirect if accessed incorrectly
    header('Location: user_approve_index.php');
    exit();
}

// Function to send SMS using the provided parameters and token
function sendSMS($parameters, $token) {
    // Initialize cURL
    $ch = curl_init();

    // Set the API endpoint for sending SMS
    curl_setopt($ch, CURLOPT_URL, "https://app.philsms.com/api/v3/sms/send");

    // Use POST method
    curl_setopt($ch, CURLOPT_POST, true);

    // Add the JSON data as the request body
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);

    // Expect a response from the server
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Add headers
    $headers = [
        "Content-Type: application/json",            // Set content type to JSON
        "Authorization: Bearer $token"              // Add Authorization Bearer Token
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute the request
    $get_sms_status = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);

    // Output the response for debugging (optional)
    echo "Response from API:\n";
    var_dump($get_sms_status);
}
?>

