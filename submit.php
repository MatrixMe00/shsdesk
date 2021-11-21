<?php
    @include_once("includes/session.php");

    if(isset($_POST['submit'])){
        $submit = $_POST['submit'];

        if($submit == "admissionFormSubmit"){
            //receive details of the student
            //cssps details
            $shs_placed = $_POST["shs_placed"];
            $ad_enrol_code = $_POST["ad_enrol_code"];
            $ad_index = $_POST["ad_index"];
            $ad_aggregate = $_POST["ad_aggregate"];

            //check if the aggregate is not a single number
            if(strlen($ad_aggregate) == 1){
                $ad_aggregate = "0".$ad_aggregate;
            }

            $ad_course = $_POST["ad_course"];

            //personal details of candidate
            $ad_fname = $_POST["ad_fname"];
            $ad_lname = $_POST["ad_lname"];
            $ad_oname = $_POST["ad_oname"];
            $ad_gender = $_POST["ad_gender"];
            $ad_jhs = $_POST["ad_jhs"];
            $ad_jhs_town = $_POST["ad_jhs_town"];
            $ad_jhs_district = $_POST["ad_jhs_district"];

            //birthdate
            $ad_year = $_POST["ad_year"];
            $ad_month = $_POST["ad_month"];
            $ad_day = $_POST["ad_day"];
            $ad_birthdate = $ad_year + "-" + $ad_month + "-" + $ad_day;
            $ad_birthdate = date("Y-m-d", strtotime($ad_birthdate));

            $ad_birth_place = $_POST["ad_birth_place"];

            //parents particulars
            $ad_father_name = $_POST["ad_father_name"];
            $ad_father_occupation = $_POST["ad_father_occupation"];
            $ad_mother_name = $_POST["ad_mother_name"];
            $ad_mother_occupation = $_POST["ad_mother_occupation"];
            $ad_guardian_name = $_POST["ad_guardian_name"];
            $ad_resident = $_POST["ad_resident"];
            $ad_postal_address = $_POST["ad_postal_address"];
            $ad_phone = $_POST["ad_phone"];
            $ad_other_phone = $_POST["ad_other_phone"];

            //interests
            $interest = $_POST["interest"];

            //others
            $ad_awards = $_POST["ad_awards"];
            $ad_position = $_POST["ad_position"];

            //witness
            $ad_witness = $_POST["ad_witness"];
            $ad_witness_phone = $_POST["ad_witness_phone"];

            //bind the statement
            $sql = "INSERT INTO enrol_table (indexNumber, enrolCode, shsID, aggregateScore, program, firstname, 
            lastname, othername, gender, jhsName, jhsTown, jhsDistrict, birthdate, birthPlace, fatherName, 
            fatherOccupation, motherName, motherOccupation, guardianName, residentAddress, postalAddress, primaryPhone, 
            secondaryPhone, interest, award, position, witnessName, witnessPhone) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)" or die($connect->error);

            //prepare query for entry into database
            $result = $connect->prepare($sql);
            $result->bind_param("ssisssssssssssssssssssssssss", 
            $ad_index,$ad_enrol_code,$shs_placed, $ad_aggregate, $ad_course, $ad_fname, $ad_lname, $ad_oname, 
            $ad_gender, $ad_jhs, $ad_jhs_town, $ad_jhs_district, $ad_birthdate, $ad_birth_place, $ad_father_name, 
            $ad_father_occupation, $ad_mother_name, $ad_mother_occupation, $ad_guardian_name, $ad_resident, $ad_postal_address, 
            $ad_phone, $ad_other_phone, $interest, $ad_awards, $ad_position, $ad_witness, $ad_witness_phone);

            //check for errors
            if(!isset($_POST["ad_transaction_id"]) || $_POST["ad_transaction_id"] == "" || $_POST["ad_transaction_id"] == null){
                echo "no-transaction-id";
                exit(1);
            }elseif($ad_index == ""){
                echo "no-index-number";
                exit(1);
            }elseif($ad_enrol_code == ""){
                echo "no-enrolment-code";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-aggregate-score";
                exit(1);
            }elseif($ad_course == ""){
                echo "no-course-provided";
                exit(1);
            }elseif($ad_fname == ""){
                echo "no-firstname-provided";
                exit(1);
            }elseif($ad_lname == ""){
                echo "no-lastname-provided";
                exit(1);
            }elseif($ad_gender == ""){
                echo "no-gender-provided";
                exit(1);
            }elseif($ad_jhs == ""){
                echo "no-jhs-name-provided";
                exit(1);
            }elseif($ad_jhs_town == ""){
                echo "no-jhs-town-provided";
                exit(1);
            }elseif($ad_jhs_district == ""){
                echo "no-jhs-district-provided";
                exit(1);
            }elseif($ad_year == ""){
                echo "no-year-provided";
                exit(1);
            }elseif($ad_month == ""){
                echo "no-month-provided";
                exit(1);
            }elseif($ad_day == ""){
                echo "no-day-provided";
                exit(1);
            }elseif($ad_birth_place == ""){
                echo "no-birth-place-provided";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }elseif($ad_aggregate == ""){
                echo "no-fname";
                exit(1);
            }

            //execute the command
            if($result->execute()){
                echo "success";
            }

            //update the cssps table that the code has expired
            $sql = "UPDATE TABLE cssps SET expired = 1, date_used = 'CURRENT_TIMESTAMP()' WHERE enrolment_code = '$ad_enrol_code'";
            $result = $connect->query($sql);

            //update the transaction table the transaction has been used
            $sql = "UPDATE TABLE transaction SET expired = 1 WHERE transactionID='".$_POST["ad_transaction_id"]."'";
            $result->$connect->query($sql);
        }elseif($submit === "send_contact"){
            $fullname = mysqli_real_escape_string($connect, $_POST['fullname']);
            $email = mysqli_real_escape_string($connect, $_POST['email']);
            $message= mysqli_real_escape_string($connect, $_POST['message']);

            $email2 = "";
            $subject = "Contact Message";

            if (strlen($fullname) > 50) {
                echo 'fname_long';

            } elseif (strlen($fullname) < 6) {
                echo 'fname_short';

            } elseif (strlen($email) > 50) {
                echo 'email_long';

            } elseif (strlen($email) < 6) {
                echo 'email_short';

            } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                echo 'eformat';

            } elseif (strlen($message) > 500) {
                echo 'message_long';

            } elseif (strlen($message) < 10) {
                echo 'message_short';

            } else {
                require $rootPath.'/phpmailer/PHPMailerAutoload.php';

                $mail = new PHPMailer;
                
                //$mail->SMTPDebug = 3;                               // Enable verbose debug output

                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = '';                 // SMTP username
                $mail->Password = '';                           // SMTP password
                $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 587;                                    // TCP port to connect to

                $mail->AddReplyTo($email);
                $mail->From = $email2;
                $mail->FromName = $fname;
                $mail->addAddress('', 'Admin');     // Add a recipient

                $mail->isHTML(true);                                  // Set email format to HTML

                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                if (!$mail->send()) {
                    echo 'Message could not be sent.';
                    echo 'Mailer Error: ' . $mail->ErrorInfo;
                } else {
                    echo 'true';
                }


            }
        }elseif($submit == "search_school_id"){
            $school_name= $_POST["school_name"];

            $sql = "SELECT id FROM schools WHERE schoolName='$school_name'";
            $res = $connect->query($sql);

            if($res->num_rows > 0){
                $rows = $res->fetch_array();

                echo $rows["id"];
            }else{
                echo "error";
            }
        }elseif($submit == "add_payment_data"){
            //receive data from data string
            $transaction_id = $_POST["transaction_id"];
            $contact_number = $_POST["contact_number"];
            $school = $_POST["school"];
            $amount = $_POST["amount"];
            $deduction = $_POST["deduction"];
            $contact_email = $_POST["contact_email"];
            $contact_name = $_POST["contact_name"];
            $trans_time = date("Y-m-d H:i:s");

            //prepare and bind parameters
            $query = "INSERT INTO transaction (transactionID, contactNumber, schoolBought, amountPaid, contactName, contactEmail, Deduction, Transaction_Date) VALUES (?,?,?,?,?,?,?,?)";
            $result = $connect->prepare($query);
            $result->bind_param("ssiissis", $transaction_id, $contact_number, $school, $amount, $contact_name, $contact_email, $deduction, $trans_time);

            //check for successful execution
            if($result->execute()){
                echo "success";
            }else{
                echo "database_send_error";
            }
        }else if($submit == "checkReference"){
            $reference = $_POST["reference_id"];

            $res = $connect->query("SELECT transactionID, Transaction_Expired FROM transaction WHERE transactionID = '$reference'");

            if($res->num_rows == 1){
                $row = $res->fetch_array();

                //check if transaction is expired
                if($row["Transaction_Expired"] == FALSE){
                    echo "success";
                }else{
                    echo "ref_expired";
                }
            }else{
                echo "error";
            }
        }elseif($submit == "faq_send"){
            $fullname = $_POST["fullname"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];
            $question = $_POST["question"];
            $answer = $_POST["answer"];
            $active = $_POST["active"];

            $sql = "INSERT INTO faq (Fullname, Email, ContactNumber, Question, Answer, Active) 
                    VALUES (?,?,?,?,?,?)" or die("Query Error: ".$connect->error);
            $res = $connect->prepare($sql);
            $res->bind_param("sssssi",$fullname,$email,$phone,$question,$answer,$active);

            if($res->execute()){
                echo "success";
            }else{
                echo "error";
            }

        }
    }elseif(isset($_GET['submit'])){
        $submit = $_GET["submit"];

        if($submit == "getStudentIndex"){
            $index_number = $_GET["index_number"];
            $school_id = $_GET["school_id"];

            $sql = "SELECT enrolment_code, school_id, expired
                    FROM cssps
                    WHERE student_index_number='$index_number'";
            $result = $connect->query($sql);
            
            $array = array();
            if($result->num_rows == 1){
                $row = $res->fetch_array();

                if($row["expired"] === true){
                    $array = array(
                        "status" => "already-registered"
                    );
                }else{
                    $array = $row;
                    $array += array(
                        "status" => "success"
                    );
                }
            }else{
                $array = array(
                    "status" => "wrong-index"
                );
            }

            echo json_encode($array);
        }
    }
    // echo date("Y-m-d H:i:s")
?>