<?php
$apiUrl = 'https://api.traccar.org/sms/send'; // Replace with the actual Traccar Cloud API URL
$apiKey = 'dWZuVfIdT4ORlGUsQqwT7a:APA91bHew2lX546HOJBufwadFjX6FKLBhqo2cVYSyMJsRO-mOZpiML3tjRYFeoGrjzf-eGF9XdFqIe5n6Jj5IF7oyUwebJObIGNE6J5HcgF5-wkTJrJD_n0';  // Get this from your Traccar Cloud account

$phoneNumber = '+638678315156';  // Recipient phone number
$message = 'Hello from Traccar Cloud SMS Gateway';

// Prepare data for API request
$data = [
    'apiKey' => $apiKey,
    'to' => $phoneNumber,
    'message' => $message
];

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

// Execute the request and capture the response
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
    echo 'Error:' . curl_error($ch);
} else {
    echo 'SMS sent successfully! Response: ' . $response;
}

// Close cURL session
curl_close($ch);
?>
