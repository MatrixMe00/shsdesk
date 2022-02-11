<?php 
    require_once("../includes/session.php");

    //require autoload
    require 'vendor/urhitech/urhitech-sms-php/src/Usms.php';
    use Urhitech\Usms;

    $client = new Usms;

    $api_key = "254|38FcOsmNuL4uzadDQBJEs3DDmiWhjj0RnEuPTBWU";

    $ap_url = "https://webapp.usmsgh.com/api/sms/send";

    $recipients = "233279284896";
    $message = "Hello world";
    $senderid = "MatrixMe";

    $response = $client->send_sms($url, $api_key, $senderid, $recipients, $message);
    /*$field = "recipient=$recipients&sender_id=YourName&message=This is a test message";

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
    curl_close($ch);*/
    
    echo $response;
?>