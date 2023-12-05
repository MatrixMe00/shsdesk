<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/includes/session.php";
    
    // only a post with paystack signature header gets our attention
    if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER) ) 
          exit("Method Disallowed");
    
    // Read the raw POST data from the webhook
    $payload = file_get_contents("php://input");
    
    define('PAYSTACK_SECRET_KEY',$server_secret);

    // validate event do all at once to avoid timing attack
    if($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY))
      exit("Process Disallowed");
    
    // Log the raw payload (for debugging purposes)
    file_put_contents("paystack_webhook.log", $payload . PHP_EOL, FILE_APPEND);
    
    // Decode the JSON payload
    $event = json_decode($payload, true);
    
    // Handle different types of events
    switch ($event['event']) {
        case 'charge.success':
            // Logic for successful payment
            handleSuccessfulPayment($event);
            break;
        case 'charge.failure':
            // Logic for failed payment
            handleFailedPayment($event);
            break;
        // Add more cases for other events as needed
        default:
            // Unknown event type
            handleUnknownEvent($event);
    }
    
    // Respond with a 200 OK to confirm receipt of the webhook
    http_response_code(200);
    
    function handleSuccessfulPayment($event)
    {
        // Logic for successful payment
        $reference = $event['data']['reference'];
        $amount = $event['data']['amount'] / 100; // Amount is in kobo, convert to naira
        // Implement your business logic here, e.g., update database, send email, etc.
        // Example: Update a database record with the payment status
        // $sql = "UPDATE orders SET payment_status = 'success' WHERE reference = '$reference'";
        // mysqli_query($connection, $sql);
        // Example: Send a thank you email to the customer
        // mail($event['data']['customer']['email'], 'Payment Received', 'Thank you for your payment!');
    }
    
    function handleFailedPayment($event)
    {
        // Logic for failed payment
        $reference = $event['data']['reference'];
        // Implement your business logic here for failed payments
    }
    
    function handleUnknownEvent($event)
    {
        // Logic for unknown event types
        // Implement your business logic here for unknown events
    }

?>
