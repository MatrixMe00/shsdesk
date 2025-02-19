<?php
    include_once "./includes/session.php";
    
    // only a post with paystack signature header gets our attention
    if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) ){
        exit("Method Disallowed");
    } 
    
    // Read the raw POST data from the webhook
    $payload = file_get_contents("php://input");

    // create a new file each month for easy tracking
    $year = date("Y"); $month = date("M"); $y_m = "{$year}_{$month}";
    $directory = "./paystack/$year";

    if(!is_dir($directory)){
        mkdir($directory);
    }
    
    // Log the raw payload (for debugging purposes)
    file_put_contents("$directory/paystack_webhook_$month.log", $payload . PHP_EOL, FILE_APPEND);

    // Decode the JSON payload
    $event = json_decode($payload, true);
    
    // Handle different types of events
    switch ($event['event']) {
        case 'charge.success':
            // Logic for successful payment
            handleSuccessfulPayment($event, $directory);
            break;
        case 'charge.failure':
            // Logic for failed payment
            handleFailedPayment($event, $directory);
            break;
        // Add more cases for other events as needed
        default:
            // Unknown event type
            handleUnknownEvent($event, $directory);
    }
    
    // Respond with a 200 OK to confirm receipt of the webhook
    http_response_code(200);
    
    function handleSuccessfulPayment($event, $directory)
    {
        global $month;

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
        $payment_type = $metadata[3]["value"];

        // for admissions
        if($payment_type == "admission"){
            //use the connection that is set
            global $connect;
            $academic_year = getAcademicYear(now(), false);
    
            //pass data if its not in the database
            $exist = fetchData("transactionID","transaction", "transactionID='$reference'");
    
            if(!is_array($exist)){
                $query = "INSERT INTO transaction (transactionID, contactNumber, schoolBought, amountPaid, contactName, 
                    contactEmail, Deduction, Transaction_Date, academic_year) VALUES (?,?,?,?,?,?,?,?, '$academic_year')";
                $result = $connect->prepare($query);
                $result->bind_param("ssidssds", $reference, $customer_number, $school_id, $amount, $customer_name, $customer_email, 
                    $deduction, $paid_at);
                
                // pass payment to database
                $result->execute();
            }   
        }elseif($payment_type == "access_code_bulk" || $payment_type == "access_code"){
            // use connection set
            global $connect2;

            // pass data if its not in the database
            $exist = fetchData1("transactionID", "transaction", "transactionID='$reference'");

            if(!is_array($exist)){
                try{
                    $index_number = $payment_type == "access_code_bulk" ? null : $metadata[3]["value"];
                    $type = $payment_type == "access_code_bulk" ? "bulk" : "single";

                    if($payment_type == "access_code_bulk"){
                        $index_number = $metadata[4]["value"] ?? null;
                    }

                    $sql = "INSERT INTO transaction (transactionID, school_id, price, deduction, phoneNumber, email, index_number, pay_type) VALUES (?,?,?,?,?,?,?,?)";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("siddssss", $reference, $school_id, $amount, $deduction, $customer_number, $customer_email, $index_number, $type);
                    
                    // pass payment to database
                    $stmt->execute();

                    activate_access_pay($index_number, $reference, $school_id);
                    
                    file_put_contents("$directory/paystack_check_$month.log", $reference." confirmed entry for connect2 with message => ok" . PHP_EOL, FILE_APPEND);
                }catch(Throwable $th){
                    file_put_contents("$directory/paystack_check_$month.log", $reference." has error -> ". $th->getMessage() . PHP_EOL, FILE_APPEND);
                }
            }            
        }
    }
    
    function handleFailedPayment($event, $directory)
    {
        global $month;
        // Logic for failed payment
        file_put_contents("$directory/failed_webhook_$month.log", json_encode($event, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
    }
    
    function handleUnknownEvent($event, $directory)
    {
        global $month;
        // pass them into unknown
        file_put_contents("$directory/unknown_webhook_$month.log", json_encode($event, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
    }

    close_connections();

?>
