<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");

    /**
     * This function is used to merge mails which are not in the database to the mailing list
     * @param array $original This is the original data from the enduser
     * @param array $db_data This is the data from the database
     * @return array Returns the merged results
     */
    function mergeRecipients(array $original, array $db_data){
        $found_emails = array_column($db_data, "email");
        $emails_not_found = array_diff($original, $found_emails);

        foreach($emails_not_found as $email){
            $db_data[] = [
                "email" => $email,
                "name" => "No name",
                "username" => "No username",
                "school" => "No school",
                "phone" => "No phone",
            ];
        }

        return $db_data;
    }
    
    // require the phpmailer
    require "$rootPath/phpmailer/src/Exception.php";
    require "$rootPath/phpmailer/src/PHPMailer.php";
    require "$rootPath/phpmailer/src/SMTP.php";

    $mail = new PHPMailer(true);

    //depending on the sender name should provide sender email
    $sender_email = get_default_email($sender_name);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $mailserver;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailserver_email;
        $mail->Password   = $mailserver_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;
    
        //Recipients
        $mail->setFrom($sender_email, $sender_name);

        // get recipients
        $recipients = str_replace(" ", "", $recipients);
        $original_recipients = $recipients = explode(",", $recipients);
        
        //check if the extra section has been turned on
        $extra_keys = array("email","name","username","phone","school");

        $message = "";

        // make sure all recipients are email addresses
        $recipientsIsValid = validate_email($recipients, $message);

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