<?php
$ch = curl_init();
$parameters = array(
    'apikey' => '24f3a870f7c5a97cf1c6ec0f2e2491b9', //Your API KEY
    'number' => '09678315156',
    'message' => 'Test SMS from Semaphore, please ignore.',
    'sendername' => 'SEMAPHORE'
);
curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
curl_setopt( $ch, CURLOPT_POST, 1 );

//Send the parameters set above with the request
curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );

// Receive response from server
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$output = curl_exec( $ch );
curl_close ($ch);

//Show the server response
echo $output;
?>
