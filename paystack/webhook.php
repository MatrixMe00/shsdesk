<?php
    include_once "./includes/session.php";
    
    // only a post with paystack signature header gets our attention
    if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) ){
        exit("Method Disallowed");
    } 
    
    // Read the raw POST data from the webhook
    $payload = file_get_contents("php://input");
    
    // Log the raw payload (for debugging purposes)
    file_put_contents("./paystack/paystack_webhook.log", $payload . PHP_EOL, FILE_APPEND);

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
        $data = $event["data"];
        $metadata = $data["metadata"]["custom_fields"];
        $reference = $data["reference"];
        $amount = (float) $data['amount'] / 100; 
        $customer_name = $metadata[1]["value"];
        $school_id = getSchoolDetail($metadata[2]["value"]);
        $school_id = $school_id == "error" ? 0 : $school_id["id"];
        $customer_email = $data["customer"]["email"];
        $deduction = round($data["fees"] / 100, 2);
        $paid_at = date("Y-m-d H:i:s", strtotime($data["paidAt"]));
        $customer_number = $metadata[0]["value"];

        // for admissions
        if($amount > 10){
            //use the connection that is set
            global $connect;
    
            //pass data if its not in the database
            $exist = fetchData("transactionID","transaction", "transactionID='$reference'");
    
            if(!is_array($exist)){
                $query = "INSERT INTO transaction (transactionID, contactNumber, schoolBought, amountPaid, contactName, 
                    contactEmail, Deduction, Transaction_Date) VALUES (?,?,?,?,?,?,?,?)";
                $result = $connect->prepare($query);
                $result->bind_param("ssidssds", $reference, $customer_number, $school_id, $amount, $customer_name, $customer_email, 
                    $deduction, $paid_at);
                
                // pass payment to database
                $result->execute();
            }   
        }
    }
    
    function handleFailedPayment($event)
    {
        // Logic for failed payment
        file_put_contents("./paystack/failed_webhook.log", json_encode($event, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
    }
    
    function handleUnknownEvent($event)
    {
        // pass them into unknown
        file_put_contents("./paystack/unknown_webhook.log", json_encode($event, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
    }

?>