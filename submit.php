<?php
    @include_once("includes/session.php");

    if(isset($_POST['submit'])){
        $submit = $_POST['submit'];

        if($submit == "admissionFormSubmit" || $submit == "admissionFormSubmit_ajax"){
            //receive details of the student
            //cssps details
            $shs_placed = getSchoolDetail($connect->real_escape_string($_POST["shs_placed"]))["id"];
            $ad_enrol_code = $connect->real_escape_string($_POST["ad_enrol_code"]);
            $ad_index = $connect->real_escape_string($_POST["ad_index"]);
            $ad_aggregate = $connect->real_escape_string($_POST["ad_aggregate"]);

            //check if the aggregate is not a single number
            if(strlen($ad_aggregate) == 1){
                $ad_aggregate = "0".$ad_aggregate;
            }

            $ad_course = $connect->real_escape_string($_POST["ad_course"]);

            //personal details of candidate
            $ad_lname = formatName($connect->real_escape_string($_POST["ad_lname"]));
            $ad_oname = formatName($connect->real_escape_string($_POST["ad_oname"]));
            $ad_gender = formatName($connect->real_escape_string($_POST["ad_gender"]));
            $ad_jhs = formatName($connect->real_escape_string($_POST["ad_jhs"]));
            $ad_jhs_town = formatName($connect->real_escape_string($_POST["ad_jhs_town"]));
            $ad_jhs_district = formatName($connect->real_escape_string($_POST["ad_jhs_district"]));

            //birthdate
            $ad_year = $connect->real_escape_string($_POST["ad_year"]);
            $ad_month = $connect->real_escape_string($_POST["ad_month"]);
            $ad_day = $connect->real_escape_string($_POST["ad_day"]);
            $ad_birthdate = $ad_year."-".$ad_month."-".$ad_day;
            $ad_birthdate = date("Y-m-d", strtotime($ad_birthdate));

            $ad_birth_place = formatName($connect->real_escape_string($_POST["ad_birth_place"]));

            //parents particulars
            $ad_father_name = formatName($connect->real_escape_string($_POST["ad_father_name"]));
            $ad_father_occupation = formatName($connect->real_escape_string($_POST["ad_father_occupation"]));
            $ad_mother_name = formatName($connect->real_escape_string($_POST["ad_mother_name"]));
            $ad_mother_occupation = formatName($connect->real_escape_string($_POST["ad_mother_occupation"]));
            $ad_guardian_name = formatName($connect->real_escape_string($_POST["ad_guardian_name"]));
            $ad_resident = $connect->real_escape_string($_POST["ad_resident"]);
            $ad_postal_address = $connect->real_escape_string($_POST["ad_postal_address"]);
            $ad_phone = $connect->real_escape_string($_POST["ad_phone"]);
            $ad_other_phone = $connect->real_escape_string($_POST["ad_other_phone"]);

            //interests
            $interest = formatName($connect->real_escape_string($_POST["interest"]));

            //others
            $ad_awards = formatName($connect->real_escape_string($_POST["ad_awards"]));
            $ad_position = formatName($connect->real_escape_string($_POST["ad_position"]));

            //witness
            $ad_witness = formatName($connect->real_escape_string($_POST["ad_witness"]));
            $ad_witness_phone = $connect->real_escape_string($_POST["ad_witness_phone"]);

            $current_date = date("Y-m-d");

            //bind the statement
            $sql = "INSERT INTO enrol_table (indexNumber, enrolCode, shsID, aggregateScore, program, 
            lastname, othername, gender, jhsName, jhsTown, jhsDistrict, birthdate, birthPlace, fatherName, 
            fatherOccupation, motherName, motherOccupation, guardianName, residentAddress, postalAddress, primaryPhone, 
            secondaryPhone, interest, award, position, witnessName, witnessPhone, enrolDate) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)" or die($connect->error);

            //prepare query for entry into database
            $result = $connect->prepare($sql);
            $result->bind_param("ssisssssssssssssssssssssssss", 
            $ad_index,$ad_enrol_code,$shs_placed, $ad_aggregate, $ad_course, $ad_lname, $ad_oname, 
            $ad_gender, $ad_jhs, $ad_jhs_town, $ad_jhs_district, $ad_birthdate, $ad_birth_place, $ad_father_name, 
            $ad_father_occupation, $ad_mother_name, $ad_mother_occupation, $ad_guardian_name, $ad_resident, $ad_postal_address, 
            $ad_phone, $ad_other_phone, $interest, $ad_awards, $ad_position, $ad_witness, $ad_witness_phone, $current_date);

            //check for errors
            if(!isset($_POST["ad_transaction_id"]) || empty($connect->real_escape_string($_POST["ad_transaction_id"]))){
                $message = "no-transaction-id";
            }elseif(empty($ad_index)){
                $message = "no-index-number";
            }elseif(empty($ad_enrol_code)){
                $message = "no-enrolment-code";
            }elseif($shs_placed == "error"){
                $message = "wrong-school";
            }elseif(empty($ad_aggregate)){
                $message = "no-aggregate-score";
            }elseif(empty($ad_course)){
                $message = "no-course-set";
            }elseif(empty($ad_lname)){
                $message = "no-lname-set";
            }elseif(empty($ad_oname)){
                $message = "no-oname-set";
            }elseif(empty($ad_gender)){
                $message = "no-gender-set";
            }elseif(empty($ad_jhs)){
                $message = "no-jhs-name-set";
            }elseif(empty($ad_jhs_town)){
                $message = "no-jhs-town-set";
            }elseif(empty($ad_jhs_district)){
                $message = "no-jhs-district-set";
            }elseif(empty($ad_year) || $ad_year <= 0){
                $message = "no-year-set";
            }elseif(empty($ad_month) || $ad_month <= 0){
                $message = "no-month-set";
            }elseif(empty($ad_day || $ad_day <= 0)){
                $message = "no-day-set";
            }elseif(empty($ad_birth_place)){
                $message = "no-birth-place-set";
            }elseif(empty($ad_father_name) && !empty($ad_father_occupation)){
                $message = "no-father-name";
            }elseif(!empty($ad_father_name) && empty($ad_father_occupation)){
                $message = "no-f-occupation-set";
            }elseif(empty($ad_mother_name) && !empty($ad_mother_occupation)){
                $message = "no-mother-name";
            }elseif(!empty($ad_mother_name) && empty($ad_mother_occupation)){
                $message = "no-m-occupation-set";
            }elseif(empty($ad_father_name) && empty($ad_mother_name) && empty($ad_guardian_name)){
                $message = "no-elder-name";
            }elseif(empty($ad_resident)){
                $message = "no-residence-set";
            }elseif(empty($ad_phone)){
                $message = "no-p-p-set";
            }elseif(strlen($ad_phone) < 10){
                $message = "p-p-short";
            }elseif(!empty($ad_other_phone) && strlen($ad_other_phone) < 10){
                $message = "s-p-short";
            }elseif(strlen($ad_phone) > 16){
                $message = "p-p-long";
            }elseif(!empty($ad_other_phone) && strlen($ad_other_phone) > 16){
                $message = "s-p-long";
            }elseif(empty($interest)){
                $message = "no-interest-set";
            }elseif(empty($ad_witness)){
                $message = "no-witness-set";
            }elseif(empty($ad_witness_phone)){
                $message = "no-witness-phone";
            }elseif(strlen($ad_witness_phone) > 16){
                $message = "witness-phone-long";
            }elseif(strlen($ad_witness_phone) < 10){
                $message = "witness-phone-short";
            }else{
                if($result->execute()){
                    // update the cssps table that the student has enroled
                    $sql = "UPDATE cssps 
                        SET enroled = 1 
                        WHERE indexNumber = '$ad_index'";
                    $result = $connect->query($sql);

                    //update the transaction table the transaction has been used
                    $sql = "UPDATE transaction 
                        SET Transaction_Expired = 1, indexNumber='$ad_index' 
                        WHERE transactionID='".$_POST["ad_transaction_id"]."'";
                    $result = $connect->query($sql);

                    //automatically give user a house of school permits
                    $permit = getSchoolDetail($shs_placed, true)["autoHousePlace"];
                    
                    if($permit){
                        //count number of houses of the school
                        $sql = "SELECT id, headPerRoom, totalRooms
                            FROM houses
                            WHERE schoolID = $shs_placed AND gender = '$ad_gender'";
                        $result = $connect->query($sql);

                        //create an array for details
                        $house = array();

                        if($result->num_rows > 0){
                            $total = $result->num_rows;
                            
                            while($row = $result->fetch_assoc()){
                                $new = array(
                                    array(
                                        "id" => $row["id"],
                                        "totalHeads" => intval($row["headPerRoom"]) * intval($row["totalRooms"])
                                    )                                    
                                );

                                //add to house array
                                $house = array_merge($house, $new);
                            }

                            //search for last house allocation entry
                            $sql = "SELECT indexNumber, houseID
                                FROM house_allocation
                                WHERE schoolID = $shs_placed
                                ORDER BY indexNumber DESC
                                LIMIT 1";
                            $result = $connect->query($sql);

                            $hid = $result->fetch_assoc()["houseID"];

                            //fetch student details for entry
                            $student_details = fetchData("*", "cssps", "indexNumber=$ad_index");

                            if($result->num_rows == 1){
                                //retrieve house id
                                $id = $hid;

                                //get total number of boarding students in house
                                $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$id AND boardingStatus='Boarder'");
                                
                                if(is_array($ttl)){
                                    $ttl = $ttl["total"];
                                }

                                $next_room = 0;

                                for($i = 0; $i < $total; $i++){
                                    //try choosing the next house
                                    if($house[$i]["id"] == $id){
                                        //check immediate available houses
                                        if($i+1 < $total && $ttl < $house[$i+1]["totalHeads"]){
                                            $next_room = $house[$i+1]["id"];
                                        }elseif($i-1 >= 0 && $ttl < $house[$i-1]["totalHeads"]){                                            
                                            $next_room = $house[$i-1]["id"];
                                        }elseif($i+1 == $total && $ttl < $house[0]["totalHeads"]){
                                            $next_room = $house[0]["id"];
                                        }
                                        
                                        if($next_room > 0){
                                            break;
                                        }
                                    }
                                }

                                echo "next room: $next_room";

                                //parse entry into allocation table
                                $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus)
                                    VALUES(?,?,?,?,?,?,?)";
                                $stmt = $connect->prepare($sql);
                                $stmt->bind_param("sississ", $student_details["indexNumber"], $student_details["schoolID"], $student_details["Lastname"], $student_details["Othernames"],
                                    $next_room, $student_details["Gender"], $student_details["boardingStatus"]);
                                $stmt->execute();
                            }elseif($result->num_rows == 0){
                                //this is the first entry, place student into house
                                $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus)
                                    VALUES(?,?,?,?,?,?,?)";
                                $stmt = $connect->prepare($sql);
                                $stmt->bind_param("sississ", $student_details["indexNumber"], $student_details["schoolID"], $student_details["Lastname"], $student_details["Othernames"],
                                    $house[0]["id"], $student_details["Gender"], $student_details["boardingStatus"]);
                                $stmt->execute();
                            }
                        }
                    }

                    $message = "success";

                    //create a session responsible for preparing admission letter
                    $school = getSchoolDetail($shs_placed, true);

                    //details for school
                    $_SESSION["ad_school_name"] = $school["schoolName"];
                    $_SESSION["ad_box_address"] = $school["postalAddress"];
                    $_SESSION["ad_school_phone"] = "+".$school["techContact"];
                    $_SESSION["ad_school_head"] = $school["headName"];
                    $_SESSION["ad_it_admin"] = $school["techName"];
                    $_SESSION["ad_message"] = $school["admissionPath"];
                    $_SESSION["ad_school_prospectus"] = $school["prospectusPath"];
                    $_SESSION["ad_reopening"] = fetchData("reopeningDate","admissiondetails","schoolID=$shs_placed");

                    //student details
                    $_SESSION["ad_stud_index"] = $ad_index;
                    $_SESSION["ad_stud_lname"] = $ad_lname;
                    $_SESSION["ad_stud_oname"] = $ad_oname;
                    $_SESSION["ad_stud_enrol_code"] = $ad_enrol_code;
                    $_SESSION["ad_stud_residence"] = $ad_resident;
                    $_SESSION["ad_stud_program"] = $ad_course;
                    $_SESSION["ad_stud_house"] = fetchData("title","houses","id=".fetchData("houseID","house_allocation","indexNumber=".$ad_index)["houseID"])["title"];
                }else{
                    $message = "error";
                }
            }

            echo $message;
        }elseif($submit == "send_contact" || $submit == "send_contact_ajax"){
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
            $result->bind_param("ssidssds", $transaction_id, $contact_number, $school, $amount, $contact_name, $contact_email, $deduction, $trans_time);

            //check for successful execution
            if($result->execute()){
                echo "success";
            }else{
                echo "database_send_error";
            }
        }else if($submit == "checkReference" || $submit == "checkReference_ajax"){
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

        if($submit == "getStudentIndex" || $submit == "getStudentIndex_ajax"){
            $index_number = $_GET["index_number"];
            $school_id = $_GET["school_id"];

            $sql = "SELECT *
                    FROM cssps
                    WHERE indexNumber='$index_number'";
            $result = $connect->query($sql);
            
            $array = array();
            if($result->num_rows == 1){
                $row = $result->fetch_array();

                if($row["enroled"] === true){
                    $array = array(
                        "status" => "already-registered"
                    );
                }else{
                    //check if right school is selected
                    if($row["schoolID"] == $school_id){
                        $array = $row;
                        $array += array(
                            "status" => "success",
                            "day" => date("j",strtotime($row["dob"])),
                            "month" => date("n",strtotime($row["dob"])),
                            "year" => date("Y",strtotime($row["dob"])),
                        );
                    }else{
                        $array = array(
                            "status" => "wrong-school-select"
                        );
                    }
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