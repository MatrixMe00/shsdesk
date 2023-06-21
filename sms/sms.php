<?php
if(isset($_REQUEST['submit'])){
    $submit = $_REQUEST['submit'];
    $jsonFormat = true;

    // api endpoint
    $endPoint = 'https://webapp.usmsgh.com/api/sms/send';

    // api key
    $apiToken = '313|XvzFfivJR6eq7a4Cz3f4pNVkGKdFz4RMF48INToZ ';

    if($submit == 'sendTransaction'){
        $senderId = "SHSDesk";
        $recipients = [$_REQUEST["phone"]];
    }elseif($submit == 'exeat_request' || $submit == 'exeat_request_ajax'){
        $student = fetchData1("lastname, othernames, guardianContact","students_table","indexNumber='$student_index'");
        if(is_array($student)){
            $recipients = [$student["guardianContact"]];
            if(intval(date("H")) < 12){
                $text_message = "Good Morning! ";
            }elseif(intval(date("H")) < 17){
                $text_message = "Good Afternoon! ";
            }else{
                $text_message = "Good Evening! ";
            }
            $text_message .= "This message is to inform you that your ward, ".$student["lastname"]." ".$student["othername"];
            $text_message .= ", has received an ".ucwords($exeat_type)." Exeat to $exeat_town";
            $senderId = fetchData("sms_id","school_ssids","school_id= $user_school_id");
        }else{
            echo "<script>alert_box('Unrecognized student stored','warning',10)</script>";
            exit(1);
        }
    }elseif($submit == "send_sms" || $submit == "send_sms_ajax"){
        $jsonFormat = false;
        $text_message = $sms_text;
        
        if($group == "student"){
            if($individuals == "all"){
                $numbers = fetchData1("guardianContact","students_table","school_id=$user_school_id",0);
            }elseif(strpos($individuals, ",")){
                $individuals = explode(", ", $individuals);
                $numbers = [];
                foreach($individuals as $individual){
                    $number = fetchData1("guardianContact","students_table","indexNumber='$individual'");
                    if(is_array($number)){
                        if(strtolower($number["guardianContact"]) == "null" || is_null($number["guardianContact"]) || empty($number["guardianContact"])){
                            $_REQUEST["system_message"] = "Process has been stopped because student with index number $individual has no valid contact number";
                            return;
                        }else{
                            array_push($numbers, remakeNumber($number["guardianContact"], true, false));
                        }
                    }else{
                        $_REQUEST["system_message"] = "Student with index number $individual was not found. Process has stopped";
                        return;
                    }
                }
            }elseif(intval($individuals) == 1 || intval($individuals) == 2 || intval($individuals) == 3){
                $numbers = fetchData1("guardianContact","students_table","school_id=$user_school_id AND studentYear=$individuals",0);
            }else{
                $numbers = fetchData1("guardianContact","students_table","indexNumber='$individuals'");
            }

            if(is_array($numbers) && array_key_exists(0,$numbers)){
                $recipients = [];
                foreach($numbers as $number){
                    if(is_array($number)){
                        if(!empty($number["guardianContact"]) && !is_null($number["guardianContact"])){
                            array_push($recipients, remakeNumber($number["guardianContact"], true, false));
                        }
                    }else{
                        if(!empty($number) && !is_null($number)){
                            array_push($recipients, remakeNumber($number, true, false));
                        }
                    }                    
                }
            }elseif(is_array($numbers) && array_key_exists("guardianContact", $numbers)){
                if(!empty($numbers["guardianContact"])){
                    $recipients = [remakeNumber($numbers["guardianContact"], true, false)];
                }
            }else{
                $_REQUEST["system_message"] = "No recipients were discovered";
            }
        }elseif($group == "teacher"){
            if($individuals == "all"){
                $numbers = fetchData1("phone_number","teachers","school_id=$user_school_id",0);
            }elseif(strtolower($individuals) == "male" || strtolower($individuals) == "female"){
                $numbers = fetchData1("phone_number", "teachers", "school_id=$user_school_id AND gender='$individuals'",0);
            }elseif(strpos($individuals, ",")){
                $individuals = explode(", ", $individuals);
                $numbers = [];
                foreach($individuals as $individual){
                    $individual = formatItemId(strtoupper($individual), "TID", true);
                    $number = fetchData1("phone_number","teachers","teacher_id=$individual");
                    if(is_array($number)){
                        if(strtolower($number["phone_number"]) == "null" || is_null($number["phone_number"]) || empty($number["phone_number"])){
                            $_REQUEST["system_message"] = "Process has been stopped because student with index number ".formatItemId($individual,"TID")." has no valid contact number";
                            return;
                        }else{
                            array_push($numbers, $number["phone_number"]);
                        }
                    }else{
                        $_REQUEST["system_message"] = "Student with index number $individual was not found. Process has stopped";
                        return;
                    }
                }
            }else{
                $individuals = formatItemId(strtoupper($individuals), "TID", true);
                $numbers = fetchData1("phone_number","teachers","teacher_id=$individuals");
            }

            if(is_array($numbers) && array_key_exists(0,$numbers)){
                $recipients = [];
                foreach($numbers as $number){
                    if(is_array($number)){
                        if(!empty($number["phone_number"]) && !is_null($number["phone_number"])){
                            array_push($recipients, remakeNumber($number["phone_number"], true, false));
                        }
                    }else{
                        if(!empty($number) && !is_null($number)){
                            array_push($recipients, remakeNumber($number, true, false));
                        }
                    }                    
                }
            }elseif(is_array($numbers) && array_key_exists("phone_number", $numbers)){
                $recipients = [remakeNumber($numbers["phone_number"], true, false)];
            }else{
                $_REQUEST["system_message"] = "No recipients were discovered";
            }
        }

        //set the sender id
        $senderId = fetchData1("sms_id, status","school_ussds", "school_id=$user_school_id");
        if(is_array($senderId)){
            if($senderId["status"] === "approve")
                $senderId = $senderId["sms_id"];
            elseif($senderId["status"] === "pending")
                $_REQUEST["system_message"] = "Your USSD has not been validated yet";
            else
                $_REQUEST["system_message"] = "Your USSD was rejected. Please provide a new one and await approval before trying again";
        }else{
            $_REQUEST["system_message"] = "You have not set an SMS USSD yet. Please provide one and await approval before trying again";
        }

        if(isset($_REQUEST["system_message"])){
            $_REQUEST["system_message"] = implode(", ",$recipients);
            return;
        }
        
    }elseif($submit == "addNewTeacher" || $submit == "addNewTeacher_ajax"){
        $jsonFormat = false;
        $recipients = [remakeNumber($teacher_phone, true, false)];
        $text_message = "Hello $teacher_lname, your login details are as follow; username: ".formatItemId($teacher_id, "TID").
            " password: Password@1. Please go to https://www.teacher.shsdesk.com to access your portal";
        
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
    }elseif($submit == "make_payment" || $submit == "make_payment_ajax"){
        if(!is_null($transaction_id) && !empty($transaction_id)){
            if($status == false){
                $text_message = "Your transaction was successful but your transaction reference was not stored. Please contact the admins and give them this referece: $transaction_id";
            }else{
                $text_message = "Thank you for your purchase. Your access code is $accessToken, and it is valid through to ".date("M d, Y H:i:s",strtotime($expiryDate));
            }
            $senderId = "SHSDesk";
            $recipients = [remakeNumber($phoneNumber, true, false)];
        }
    }

    //stop process if there are no recipients
    if(!isset($recipients) || count($recipients) < 1){
        $_REQUEST["system_message"] = "No valid contact number(s) were found"; return;
    }

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
                }     
            }
            curl_close($ch);
        }   
    } catch (\Throwable $th) {
        $_REQUEST["system_message"] = $th->getMessage();
    }
}else{
    echo "No submission was received";
}