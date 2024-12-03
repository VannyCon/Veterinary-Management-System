<?php
session_start(); // Start session for flash messages

// Define constants for server and API key
define("SERVER", "https://app.sms-gateway.app");
define("API_KEY", "a5c4ecaf4c15268ff086464c3af8ae8600156ff8");

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
    $fullname = $_POST['full_name'];
    $mobileNumber = $_POST['mobile_number'];

    // Create the SMS message
    $smsMessage = "Hi $fullname, your account has been approved by the Admin. You can now log in.";

    try {
        // Send the SMS
        $response = sendSingleMessage($mobileNumber, $smsMessage);

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

        // Redirect with success
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit(0);
    } catch (Exception $e) {
        // Handle errors
        $_SESSION['status'] = "Failed to send message: " . $e->getMessage();
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit(0);
    }
} else {
    // Redirect if the script is accessed directly
    header('Location: users.php');
    exit(0);
}
?>
