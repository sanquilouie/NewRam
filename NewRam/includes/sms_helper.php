<?php
function sendSMS($number, $message) {
    $ch = curl_init();
    $parameters = array(
        'apikey' => '24f3a870f7c5a97cf1c6ec0f2e2491b9', // Your API KEY
        'number' => $number,
        'message' => $message,
        'sendername' => 'RAMSTAR'
    );
    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}
?>
