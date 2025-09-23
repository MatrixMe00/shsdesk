<?php
    /**
     * This is used to send an sms message
     * @param string $ussd The ussd to use
     * @param string $message The message to send
     * @param array $recipients The recipient to the message
     * @param string $provider The provider to use
     */
    function send_sms(string $ussd, string $message, array $receipients, string $provider = SMS_PROVIDER::USMSGH){
        $response = false;

        switch($provider){
            case SMS_PROVIDER::USMSGH:
                $response = usmsgh($ussd, $message, $receipients);
                break;
            case SMS_PROVIDER::MNOTIFY:
                $response = mnotify($ussd, $message, $receipients);
                break;
        }

        return $response;
    }

    /**
     * Send sms via usmsgh
     * @param string $ussd The sender id
     * @param string $message The message to be sent
     * @param array $receipients The receipients to send message to
     */
    function usmsgh(string $ussd, string $message, array $receipients){
        global $env;

        $provider = $env["sms_provider"][SMS_PROVIDER::USMSGH];

        if(!$provider) return false;

        $endPoint = $provider["url"];
        $apiToken = $provider["key"];
        $response = false;

        foreach($receipients as $key => $recipient){
            $recipient = remakeNumber($recipient, true, false);
            $data = [
                'recipient' => $recipient,
                'sender_id' => has_non_english_chars($ussd) ? "SHSDesk" : $ussd,
                'message'   => strip_tags($message)
            ];

            $response = curl_post($endPoint, $data, [
                "accept: application/json",
                "authorization: Bearer " . $apiToken,
            ]);
        }

        return $response;
    }

    /**
     * Send sms via mnotify
     * @param string $ussd The sender id
     * @param string $message The message to be sent
     * @param array $receipients The receipients to send message to
     */
    function mnotify(string $ussd, string $message, array $receipients, string $action = "sms"){
        global $env;

        $provider = $env["sms_provider"][SMS_PROVIDER::MNOTIFY];

        if(!$provider) return false;

        $endPoint = $provider["url"];
        $apiToken = $provider["key"];
        $response = false;

        $url = $endPoint . '?key=' . $apiToken;

        $data = [
            'recipient' => $receipients,
            'sender' => has_non_english_chars($ussd) ? "SHSDesk" : $ussd,
            'message' => strip_tags($message),
        ];

        $response = curl_post($url, $data, [
            "Content-Type: application/json",
        ]);

        return $response;
    }