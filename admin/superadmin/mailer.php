<?php 
    require_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");
    
    // require the phpmailer
    require($rootPath."/phpmailer/PHPMailerAutoload.php");

    $mail = new PHPMailer(true);

    $sender_email = $server_email;

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $server_email;
        $mail->Password   = $server_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
    
        //Recipients
        $mail->setFrom($sender_email, $sender_name);

        // get recipients
        $recipients = str_replace(" ", "", $recipients);
        $original_recipients = $recipients = explode(",", $recipients);
        
        //check if the extra section has been turned on
        $extra_keys = array("email","name","username","phone","school");

        $message = "";

        // make sure all recipients are email addresses
        $recipientsIsValid = checkRecipients($recipients, $message);

        if(!$recipientsIsValid) {
            exit($message);
        }

        //counters for send
        $total = count($original_recipients);
        $pass = 0;

        if(strtolower($extra) === "true"){
            //prepare recipients for where statement
            $recipients = "a.email='".implode("',a.email='", $recipients)."'";
            $recipients = explode(",", $recipients);
            
            $recipients = decimalIndexArray(fetchData(...[
                "columns" => ["a.email","fullname as name","username", "schoolName as school", "contact as phone"],
                "table" => [
                    "join" => "admins_table schools",
                    "alias" => "a s",
                    "on" => "school_id id"
                ],
                "where" => $recipients,
                "limit" => 0,
                "where_binds" => "or",
                "join_type" => "left"
            ]));

            //join any recipient which is not in the database
            if(count($original_recipients) != count($recipients)) {
                $recipients = mergeRecipients($original_recipients, $recipients);
            }

            //send the messages
            foreach($recipients as $recipient) {
                $mail->addAddress($recipient["email"], $recipient["name"]);

                $message_body = $template;

                //format the message body
                foreach($extra_keys as $key) {
                    $key_search = "{".$key."}";
                    if(str_contains($message_body, $key_search)){
                        if(is_null($recipient[$key])){
                            $recipient[$key] = "Not Set";
                        }
                        $message_body = str_replace($key_search, $recipient[$key], $message_body);
                    }
                }
        
                //Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message_body;
            
                if($mail->send()){
                    ++$pass;
                }
            }
        }else{
            foreach($recipients as $recipient) {
                $mail->addAddress($recipient);
        
                //Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $template;
            
                if($mail->send()){
                    ++$pass;
                }
            }
        }
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }