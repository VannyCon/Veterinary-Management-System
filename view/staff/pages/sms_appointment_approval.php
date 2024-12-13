<?php
session_start();

// Define constants
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

// Function to send a single SMS message
function sendSingleMessage($number, $message, $device = 0, $schedule = null, $isMMS = false, $attachments = null, $prioritize = false)
{
    $url = SERVER . "/services/send.php";
    $postData = [
        'number' => $number,
        'message' => $message,
        'schedule' => $schedule,
        'key' => API_KEY,
        'devices' => $device,
        'type' => $isMMS ? "mms" : "sms",
        'attachments' => $attachments,
        'prioritize' => $prioritize ? 1 : 0
    ];

    return sendRequest($url, $postData);
}

if (isset($_POST['submit'])) {
    // Fetch form data
    $appointmentId = $_POST['appointment_id'];
    $userId = $_POST['user_id'];
    $phone_number = $_POST['phone_number'];
    $fullname = $_POST['fullname'];
    $status = $_POST['status']; // Receive the status here

    // Construct the message based on the status
    if ($status === "Approved") {
        $smsMessage = "Hi $fullname, your appointment has been approved! Please check your account for details.";
    } elseif ($status === "Declined") {
        $smsMessage = "Hi $fullname, your appointment has been declined! Please check your account for details.";
    } else {
        // Handle unexpected status (optional)
        $smsMessage = "Hi $fullname, your appointment has been declined! Please check your account for details.";
    }

    try {
        // Send the SMS
        $response = sendSingleMessage($phone_number, $smsMessage);

        // Handle the response
        if (isset($response['ID'])) {
            $_SESSION['status'] = "Message sent successfully to $fullname! Message ID: " . $response['ID'];
        } else {
            $_SESSION['status'] = "Message sent successfully to $fullname.";
        }
    } catch (Exception $e) {
        $_SESSION['status'] = "Failed to send message: " . $e->getMessage();
    }

    // Redirect to transactions page
    header('Location: transactions.php');
    exit();
} else {
    // Redirect if the script is accessed directly
    header('Location: transactions.php');
    exit();
}
