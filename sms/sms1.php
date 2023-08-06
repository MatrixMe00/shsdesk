<?php
    include("../includes/session.php");
    $jsonFormat = true;

    // api endpoint
    $endPoint = 'https://webapp.usmsgh.com/api/sms/send';

    // api key
    $apiToken = '313|XvzFfivJR6eq7a4Cz3f4pNVkGKdFz4RMF48INToZ ';
    $senderId = "SHSDesk";
    $text_message = "This is a demo text from the test sms.php. This is the @ symbol which is supposed to provide an at symbol";
    $recipients = ["+233279284896"];

    $teacher_phone = "0279284896";
    $teacher_lname = "Seth";
    $teacher_id = "1";
    $recipients = [remakeNumber($teacher_phone, true, false)];
    $text_message = "Hello $teacher_lname, your login details are as follow; username: ".formatItemId($teacher_id, "TID").
        " password: Password@1. Please go to www.teacher.shsdesk.com to access your portal";
    
    $senderId = fetchData1("sms_id, status","school_ussds", "school_id=$user_school_id");
    if(is_array($senderId)){
        if($senderId["status"] === "approve")
            $senderId = $senderId["sms_id"];
        elseif($senderId["status"] === "pending")
            $_REQUEST["system_message"] = "Your USSD has not been validated yet";
        else
            $_REQUEST["system_message"] = "Your USSD was rejected. Please provide a new one and await approval before trying again";
    }else{
        $_REQUEST["system_message"] = "Teacher could not receive a message because you do not have an sms USSD yet.";
    }

    $senderId = "SHSDesk";

    try {
        foreach ($recipients as $key => $value) {
            $ch = curl_init();
            $data = [
                'recipient' => $value,
                'sender_id' => $senderId,
                'message'   => $text_message
            ];
        
            curl_setopt_array($ch, [
                CURLOPT_URL            => $endPoint,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    "accept: application/json",
                    "authorization: Bearer " . $apiToken,
                ],
            ]);
        
            $resp = curl_exec($ch);
        
            if ($e = curl_error($ch)) {
                if($jsonFormat){
                    echo $e;
                }else{
                    $_REQUEST["system_message"] = $e;
                }
            } else {
                if($jsonFormat){
                    $decoded = json_decode($resp, true);
                    echo json_encode($decoded);
                }else{
                    $_REQUEST["system_message"] = "sms sent";
                    $decoded = json_decode($resp, true);
                    echo json_encode($decoded);
                }     
            }
            curl_close($ch);
        }   
    } catch (\Throwable $th) {
        $_REQUEST["system_message"] = $th->getMessage();
        echo $th->getMessage();
    }