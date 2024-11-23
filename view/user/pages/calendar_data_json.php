<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Define the events array
$events = [
    [ "date" => "2024-11-15", "title" => "Holiday" ],
    [ "date" => "2024-11-02", "title" => "National Heroes Day" ],
];

// Return the JSON-encoded data
echo json_encode($events);
?>
