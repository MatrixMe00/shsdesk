<?php
if(isset($_REQUEST['submit'])){
    $submit = $_REQUEST['submit'];

    // api endpoint
    $endPoint = 'https://webapp.usmsgh.com/api/sms/send';

    // api key
    $apiToken = '309|57D6wfBwUEPtd2MdSOkJkRA0wR0kHJpanM4Y6yBS';

    if($submit == 'sendTransaction'){
        $senderId = "MatrixMe";
        $recipients = [$_REQUEST["phone"]];
        $message = $_REQUEST['message'];
    }elseif($submit == 'exeat_request' || $submit == 'exeat_request_ajax'){
        $student = fetchData("lastname, othername, primaryPhone","enrol_table","indexNumber='$student_index'");
        if(is_array($student)){
            $recipients = [$student["primaryPhone"]];
            if(intval(date("H")) < 12){
                $message = "Good Morning! ";
            }elseif(intval(date("H")) < 17){
                $message = "Good Afternoon! ";
            }else{
                $message = "Good Evening! ";
            }
            $message .= "This message is to inform you that your ward, ".$student["lastname"]." ".$student["othername"];
            $message .= ", has received an ".formatName($exeat_type)." Exeat to $exeat_town";
            $senderId = fetchData("smsID","admissiondetails","schoolID= $user_school_id");
        }else{
            exit(1);
        }
    }

    foreach ($recipients as $key => $value) {
        $ch = curl_init();
        $data = [
            'recipient' => $value,
            'sender_id' => $senderId,
            'message'   => $message
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
            echo $e;
        } else {
            $decoded = json_decode($resp, true);
            echo json_encode($decoded);
        }
        curl_close($ch);
    }
}else{
    echo "No submission was received";
}