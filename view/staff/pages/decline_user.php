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
    $id = intval($_POST['id']);
    $fullname = $_POST['fullname'];
    $phone_number = $_POST['phone_number'];
    $status = "Declined";  // Set status to Declined
    $isApproved = 0; // Declined
    $process_by = $_SESSION['isStaff'];
    
    // Handle approval or decline based on POST values
    if (isset($_POST['declined'])) {
        $isApproved = 0;  // Declined
    }

    try {
        // Update user approval status in the database (decline the user)
        $updateQuery = "UPDATE tbl_user SET  process_by = :process_by, isApproved = :isApproved WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute(['process_by' => $process_by, 'isApproved' => $isApproved, 'id' => $id]);

        // Prepare SMS parameters
        $send_data = [
            'sender_id' => "PhilSMS", // Replace with your sender ID
            'recipient' => "+63$phone_number", // Replace with the recipient's number
            'message' => "", // Placeholder for the message
        ];
    
        // Your API Token
        $token = "1222|Pr4dssTM79z2BbdzZOQKle1BWOrWL28eRo0TNjVV"; // Replace with your API token

        // Set the message for declined status
        $send_data['message'] = "Hi $fullname, Sorry, your account has been declined.";
        
        // Send SMS for declined status
        $parameters = json_encode($send_data);
        $response = sendSMS($parameters, $token);  // Call the function to send SMS
        
        // Handle the SMS API response
        $httpCode = $response['httpCode'];  // Assuming the response contains HTTP status code
        if ($httpCode === 200) {
            $_SESSION['status'] = "SMS notification triggered successfully!";
        } else {
            $_SESSION['status'] = "Failed to trigger SMS notification. HTTP Code: $httpCode.";
        }

        // Success message and redirection
        $_SESSION['message'] = "User has been declined successfully!";
        header("Location: user_decline_index.php"); // Redirect to the declined accounts page
        exit();
        
    } catch (Exception $e) {
        // Log error and show to user
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: user_decline_index.php");  // Go back to the declined user page
        exit();
    }
} else {
    // Redirect if accessed incorrectly
    header('Location: user_decline_index.php');
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

    // Execute the request and capture the response
    $get_sms_status = curl_exec($ch);

    // Get the HTTP response code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close the cURL session
    curl_close($ch);

    // Return response data for further processing
    return [
        'response' => $get_sms_status, 
        'httpCode' => $httpCode
    ];
}
?>
