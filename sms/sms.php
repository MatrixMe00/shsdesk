<?php 
    //require_once("../includes/session.php");

    //require autoload
    require 'sms/vendor/autoload.php';

    use Urhitech\Usms;

    $client = new Usms;

    $api_key = "309|57D6wfBwUEPtd2MdSOkJkRA0wR0kHJpanM4Y6yBS";

    $end_point = "https://webapp.usmsgh.com/ap/sms/send";

    $recipients = "233279284896";
    $message = "Hello world, this is a test message";
    $senderid = "MatrixMe";

    $send = $client->send_sms(
        //endpoint
        "$end_point",

        //api token
        "$api_key",

        //sender id
        "$senderid",

        //recipient
        "$recipients",

        //message
        "$message"
    );

    print_r($send);

    /*$response = $client->send_sms($url, $api_key, $senderid, $recipients, $message);
    $field = "recipient=$recipients&sender_id=MatrixMe&message=This is a test message";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $ap_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $field);

    $headers = array();
    $headers[] = 'Authorization: Bearer 254|38FcOsmNuL4uzadDQBJEs3DDmiWhjj0RnEuPTBWU';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }else{
        print_r($result);
    }
    curl_close($ch);
    
    echo $response;*/


?>