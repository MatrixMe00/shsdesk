<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require_once("includes/session.php");
    
    // require the phpmailer
    require "$rootPath/phpmailer/src/Exception.php";
    require "$rootPath/phpmailer/src/PHPMailer.php";
    require "$rootPath/phpmailer/src/SMTP.php";

    $mail = new PHPMailer(true);
    $subject = "Message from SHSDesk Contact Form";

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $mailserver;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailserver_email;
        $mail->Password   = $mailserver_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;

        $mail->AddReplyTo($email);
        $mail->setFrom($email, $fullname);
        $mail->addAddress('successinnovativehub@gmail.com', 'Admin');     // Add main recipient

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients. '.$message;

        if (!$mail->send()) {
            echo 'Message could not be sent.';
        } else {
            echo 'true';
        }
    } catch (\Throwable $th) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        echo " | ".throwableMessage($th);
    }