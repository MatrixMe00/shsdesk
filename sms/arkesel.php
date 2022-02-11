<?php

    $recipient = "233279284896";
    $sender = "MatrixMe";
    $sms = "Hello, this is a test sms";
    $api_key = "OjdZRmpnRjAyVW9rcnFQd3E=";

    // // SEND SMS
    // $curl = curl_init();
    // curl_setopt_array($curl, array(
    // CURLOPT_URL => 'https://sms.arkesel.com/sms/api?action=send-sms&api_key=cE9QRUkdjsjdfjkdsj9kdiieieififiw=&to=233544919953&from=Arkesel&sms=Hello%20world.%20Spreading%20peace%20and%20joy%20only.%20Remeber%20to%20put%20on%20your%20face%20mask.%20Stay%20safe!',
    // CURLOPT_RETURNTRANSFER => true,
    // CURLOPT_ENCODING => '',
    // CURLOPT_MAXREDIRS => 10,
    // CURLOPT_TIMEOUT => 10,
    // CURLOPT_FOLLOWLOCATION => true,
    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    // CURLOPT_CUSTOMREQUEST => 'GET', ));
    // $response = curl_exec($curl);
    // curl_close($curl);
    // echo $response;

    // SEND SMS
    /*$curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://sms.arkesel.com/sms/api?action=send-sms&api_key='.$api_key.'=&to='.$recipient.'&from='.$sender.'&sms='.$sms,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET', )
    );

    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;*/

    echo "<a href=\"https://sms.arkesel.com/sms/api?action=send-sms&api_key=OjdZRmpnRjAyVW9rcnFQd3E=&to=233279284896&from=MatrixMe&sms=YourMessage\">Click</a>"
?>