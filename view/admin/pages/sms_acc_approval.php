<?php
session_start(); // Start session for flash messages

// Define constants for server and API key
define("SERVER", "https://app.sms-gateway.app");
define("API_KEY", "fa76b2deff12986e365779fe7dbecc48750fe84e");

// Function to send a cURL request
function sendRequest($url, $postData)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $serverOutput = curl_exec($ch);
    curl_close($ch);

    return json_decode($serverOutput, true);
}

// Function to send a single message
function sendSingleMessage($number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $prioritize = false)
{
    $url = SERVER . "/services/send.php";
    $postData = array(
        'number' => $number,
        'message' => $message,
        'schedule' => $schedule,
        'key' => API_KEY,
        'devices' => $device,
        'type' => $isMMS ? "mms" : "sms",
        'attachments' => $attachments,
        'prioritize' => $prioritize ? 1 : 0
    );

    return sendRequest($url, $postData);
}

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Fetch form data
    $userId = $_POST['user_id'];
    $phone_number = $_POST['phone_number'];
    $fullname = $_POST['fullname']; // This should now be populated

    // Construct the message
    $smsMessage = "Hi $fullname, your account has been approved by the Admin. You can now log in.";

    // Send the SMS and handle response
    try {
        $response = sendSingleMessage($phone_number, $smsMessage);

        // Handle the response
        if (isset($response['ID'])) {
            $_SESSION['status'] = "Message sent successfully to $fullname! Message ID: " . $response['ID'];
        } else {
            $_SESSION['status'] = "Message sent successfully to $fullname.";
        }

        // Update the user's approval status in the database
        $pdo = new PDO("mysql:host=localhost;dbname=pet_db", "root", "");
        $sql = "UPDATE tbl_user SET isApproved = 1 WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit(0);
    } catch (Exception $e) {
        $_SESSION['status'] = "Failed to send message: " . $e->getMessage();
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit(0);
    }
}
 else {
    // Redirect if the script is accessed directly
    header('Location: user_approve_index.php');
    exit(0);
}
