<?php
    include_once("includes/session.php");

    if(isset($_POST['submit'])){
        $submit = $_POST['submit'];

        if($submit == "admissionFormSubmit" || $submit == "admissionFormSubmit_ajax"){
            //receive details of the student
            //cssps details
            try {
                $shs_placed = getSchoolDetail($_POST["shs_placed"])["id"];
                $ad_enrol_code = $_POST["ad_enrol_code"];
                $ad_index = $_POST["ad_index"];
                $ad_aggregate = $_POST["ad_aggregate"];

                //check if the aggregate is not a single number
                if(strlen($ad_aggregate) == 1){
                    $ad_aggregate = "0".$ad_aggregate;
                }

                $ad_course = $_POST["ad_course"];

                //personal details of candidate
                $ad_lname = formatName($_POST["ad_lname"]);
                $ad_oname = formatName($_POST["ad_oname"]);
                $ad_gender = formatName($_POST["ad_gender"]);
                $ad_jhs = formatName($_POST["ad_jhs"]);
                $ad_jhs_town = formatName($_POST["ad_jhs_town"]);
                $ad_jhs_district = formatName($_POST["ad_jhs_district"]);

                //birthdate
                $ad_year = $_POST["ad_year"];
                $ad_month = $_POST["ad_month"];
                $ad_day = $_POST["ad_day"];
                $ad_birthdate = $ad_year."-".$ad_month."-".$ad_day;
                $ad_birthdate = date("Y-m-d", strtotime($ad_birthdate));

                $ad_birth_place = formatName($_POST["ad_birth_place"]);

                //parents particulars
                $ad_father_name = formatName($_POST["ad_father_name"]);
                $ad_father_occupation = formatName($_POST["ad_father_occupation"]);
                $ad_mother_name = formatName($_POST["ad_mother_name"]);
                $ad_mother_occupation = formatName($_POST["ad_mother_occupation"]);
                $ad_guardian_name = formatName($_POST["ad_guardian_name"]);
                $ad_resident = formatName($_POST["ad_resident"]);
                $ad_postal_address = $_POST["ad_postal_address"];
                $ad_phone = $_POST["ad_phone"];
                $ad_other_phone = $_POST["ad_other_phone"];

                //interests
                $interest = formatName($_POST["interest"]);

                //others
                $ad_awards = formatName($_POST["ad_awards"]);
                $ad_position = formatName($_POST["ad_position"]);

                //witness
                $ad_witness = formatName($_POST["ad_witness"]);
                $ad_witness_phone = $_POST["ad_witness_phone"];

                $current_date = date("Y-m-d H:i:s");

                //bind the statement
                $sql = "INSERT INTO enrol_table (indexNumber, enrolCode, shsID, aggregateScore, program, 
                lastname, othername, gender, jhsName, jhsTown, jhsDistrict, birthdate, birthPlace, fatherName, 
                fatherOccupation, motherName, motherOccupation, guardianName, residentAddress, postalAddress, primaryPhone, 
                secondaryPhone, interest, award, position, witnessName, witnessPhone, transactionID, enrolDate) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)" or die($connect->error);

                //prepare query for entry into database
                $result = $connect->prepare($sql);
                $result->bind_param("ssissssssssssssssssssssssssss", 
                $ad_index,$ad_enrol_code,$shs_placed, $ad_aggregate, $ad_course, $ad_lname, $ad_oname, 
                $ad_gender, $ad_jhs, $ad_jhs_town, $ad_jhs_district, $ad_birthdate, $ad_birth_place, $ad_father_name, 
                $ad_father_occupation, $ad_mother_name, $ad_mother_occupation, $ad_guardian_name, $ad_resident, $ad_postal_address, 
                $ad_phone, $ad_other_phone, $interest, $ad_awards, $ad_position, $ad_witness, $ad_witness_phone, $_POST["ad_transaction_id"], $current_date);

                //check for errors
                if(!isset($_POST["ad_transaction_id"]) || empty($_POST["ad_transaction_id"])){
                    $message = "no-transaction-id";
                }elseif(empty($ad_index)){
                    $message = "no-index-number";
                }elseif(empty($ad_enrol_code)){
                    $message = "no-enrolment-code";
                }elseif(strlen($ad_enrol_code) != 6){
                    $message = "enrolment-code-short";
                }elseif(fetchData("enrolCode","enrol_table","enrolCode='$ad_enrol_code'") != "empty"){
                    $message = "enrolment-code-exist";
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
                }elseif(!checkPhoneNumber($ad_phone)){
                    $message = "p-p-invalid";
                }elseif(!empty($ad_other_phone) && strlen($ad_other_phone) < 10){
                    $message = "s-p-short";
                }elseif(strlen($ad_phone) > 16){
                    $message = "p-p-long";
                }elseif(!empty($ad_other_phone) && strlen($ad_other_phone) > 16){
                    $message = "s-p-long";
                }elseif(!empty($ad_other_phone) && !checkPhoneNumber($ad_other_phone)){
                    $message = "s-p-invalid";
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
                }elseif(!checkPhoneNumber($ad_witness_phone)){
                    $message = "witness-phone-invalid";
                }else{
                    if($result->execute()){
                        // update the cssps table that the student has enroled and also jhs attended and dob
                        $sql = "UPDATE cssps 
                            SET jhsAttended = '$ad_jhs', dob='$ad_birthdate', enroled = 1 
                            WHERE indexNumber = '$ad_index'";
                        $result = $connect->query($sql);
                        
                        //verify if transaction id can be found in database
                        $transaction_id = $_POST['ad_transaction_id'];
                        $db_transaction = fetchData("transactionID","transaction","transactionID='$transaction_id'");
                        
                        //insert if it cannot be found
                        if(!is_array($db_transaction)){
                            $sql = "INSERT INTO `transaction` (`transactionID`, `contactNumber`, `schoolBought`, `amountPaid`, `contactName`, `contactEmail`, `Deduction`, `Transaction_Date`, `indexNumber`, `Transaction_Expired`) 
                            VALUES ('$transaction_id', '$ad_phone', '$shs_placed', 30, 'No Name', NULL, 0.59, '$current_date', NULL, '0')";
                            
                            $connect->query($sql);
                        }

                        //update the transaction table the transaction has been used
                        $sql = "UPDATE transaction 
                            SET Transaction_Expired = 1, indexNumber='$ad_index' 
                            WHERE transactionID='$transaction_id'";
                        $result = $connect->query($sql);

                        //automatically give user a house if school permits
                        $permit = getSchoolDetail($shs_placed, true)["autoHousePlace"];
                        $null_entry = false;

                        if($permit){
                            //count number of houses of the school
                            $houses = decimalIndexArray(fetchData(...[
                                "columns" => ["id","maleHeadPerRoom", "maleTotalRooms", "femaleHeadPerRoom", "femaleTotalRooms"],
                                "table" => "houses",
                                "where" => ["schoolID=$shs_placed", "(gender='$ad_gender'", "gender='Both')"],
                                "limit" => 0,
                                "where_binds" => ["AND","OR"]
                            ]));

                            //create an array for house details
                            $house = array();

                            //what to do if there are houses available
                            if(is_array($houses)){
                                $total = count($houses);
                                $house = array_map(function($data) use ($ad_gender){
                                    $gender_room = strtolower($ad_gender)."HeadPerRoom";
                                    $gender_total_room = strtolower($ad_gender)."TotalRooms";
                                    return [
                                        "id" => $data["id"],
                                        "totalHeads" => intval($data[$gender_room]) * intval($data[$gender_total_room])
                                    ];
                                }, $houses);

                                //fetch student details for entry from cssps
                                $student_details = fetchData("*", "cssps", "indexNumber='$ad_index'");
                                
                                //allocate a house for student
                                $next_room = setHouse($ad_gender,$shs_placed, $ad_index, $house, $student_details["boardingStatus"]);
                                
                                if(is_null($next_room)){
                                    //enter students into table but without houses
                                    $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus)
                                        VALUES(?,?,?,?,NULL,?,?)";
                                    $stmt = $connect->prepare($sql);
                                    $stmt->bind_param("sissss", $student_details["indexNumber"], $student_details["schoolID"], $student_details["Lastname"], 
                                        $student_details["Othernames"], $student_details["Gender"], $student_details["boardingStatus"]);
                                    $stmt->execute();
                                    
                                    $_SESSION["ad_stud_house"] = "e";
                                }else{
                                    //parse entry into allocation table
                                    $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus)
                                        VALUES(?,?,?,?,?,?,?)";
                                    $stmt = $connect->prepare($sql);
                                    $stmt->bind_param("sississ", $student_details["indexNumber"], $student_details["schoolID"], $student_details["Lastname"], 
                                        $student_details["Othernames"], $next_room, $student_details["Gender"], $student_details["boardingStatus"]);
                                    $stmt->execute();
                                    
                                    $_SESSION["ad_stud_house"] = fetchData("title","houses","id=$next_room")["title"];
                                }
                            }else{
                                //enter students into table but without houses
                                $null_entry = true;
                            }
                        }else{
                            $null_entry = true;
                        }

                        //provide data into database with a null house value
                        if($null_entry){
                            $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus)
                                VALUES(?,?,?,?,NULL,?,?)";
                            $stmt = $connect->prepare($sql);
                            $stmt->bind_param("sissss", $student_details["indexNumber"], $student_details["schoolID"], $student_details["Lastname"], $student_details["Othernames"], $student_details["Gender"], $student_details["boardingStatus"]);
                            $stmt->execute();
                            
                            $_SESSION["ad_stud_house"] = "e";
                        }
                        
                        //all data required are written now
                        $message = "success";

                        //create a session responsible for preparing admission letter
                        $school = getSchoolDetail($shs_placed, true);
                        $student = fetchData("c.*, e.enrolCode","cssps c JOIN enrol_table e ON c.indexNumber = e.indexNumber", "c.indexNumber='$ad_index'");

                        //details for school
                        $_SESSION["ad_school_name"] = $school["schoolName"];
                        $_SESSION["ad_box_address"] = $school["postalAddress"];
                        $_SESSION["ad_school_phone"] = remakeNumber($school["techContact"], false, false);
                        $_SESSION["ad_school_logo"] = $school["logoPath"];
                        $_SESSION["ad_school_head"] = $school["headName"];
                        $_SESSION["ad_it_admin"] = $school["techName"];
                        $_SESSION["ad_message"] = $school["admissionPath"];
                        $_SESSION["ad_school_prospectus"] = $school["prospectusPath"];
                        $_SESSION["ad_reopening"] = fetchData("reopeningDate","admissiondetails","schoolID=$shs_placed")["reopeningDate"] ?? "Not Set";
                        $_SESSION["ad_admission_title"] = $school["admissionHead"];

                        //student details
                        $_SESSION["ad_stud_index"] = $student["indexNumber"];
                        $_SESSION["ad_stud_lname"] = $student["Lastname"];
                        $_SESSION["ad_stud_oname"] = $student["Othernames"];
                        $_SESSION["ad_stud_enrol_code"] = $student["enrolCode"];
                        $_SESSION["ad_stud_residence"] = $student["boardingStatus"];
                        $_SESSION["ad_stud_program"] = $student["programme"];
                        $_SESSION["ad_stud_gender"] = $student["Gender"];

                        //convert house name for only auto house placed students
                        if($_SESSION["ad_stud_house"] !== "e"){
                            $_SESSION["ad_stud_house"] = fetchData(
                                "h.title", [
                                    "join" => "house_allocation houses",
                                    "on" => "houseID id",
                                    "alias" => "ho h"
                                ],
                                "ho.indexNumber='$ad_index'"
                            )["title"];
                        }
                    }else{
                        if(str_contains(strtolower(strval($result->error)), "duplicate")){
                            $message = "Your data has already been written. Please navigate to <a href='https://www.shsdesk.com/student'>shsdesk.com/student</a> to print document";
                        }else{
                            $message = "Navigate to <a href='https://www.shsdesk.com/student'>shsdesk.com/student</a> to check for document";
                        }
                    }
                }
            } catch (\Throwable $th) {
                $message = throwableMessage($th);
            }

            echo $message;
        }elseif($submit == "send_contact" || $submit == "send_contact_ajax"){
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $message= $_POST['message'];

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
                require $rootPath.'/mailer.php';
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
            $query = "INSERT INTO transaction (transactionID, contactNumber, schoolBought, amountPaid, contactName, 
                contactEmail, Deduction, Transaction_Date) VALUES (?,?,?,?,?,?,?,?)";
            $result = $connect->prepare($query);
            $result->bind_param("ssidssds", $transaction_id, $contact_number, $school, $amount, $contact_name, $contact_email, $deduction, $trans_time);

            //check for successful execution
            if($result->execute()){
                echo "success";

                //add admin number
                echo "-".getSchoolDetail($school, true)["techContact"];
            }else{
                echo "database_send_error";
            }
        }else if($submit == "checkReference" || $submit == "checkReference_ajax"){
            $reference = $_POST["reference_id"];
            $school = $_POST["school_id"];
            $transaction = fetchData(["transactionID","Transaction_Expired","schoolBought"], "transaction", "transactionID='$reference'");

            if(is_array($transaction)){
                //check if transaction is expired
                if($transaction["Transaction_Expired"] == FALSE){
                    if($transaction["schoolBought"] == $school){
                        echo "success";

                        //add admin number
                        echo "-".getSchoolDetail($school, true)["techContact"];
                    }else{
                        echo "This transaction id does not match the selected school";
                    }                    
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

        }elseif($submit == "trackTransaction"){
            //receive data from data string
            $transaction_id = $_POST["transaction_id"];
            $contact_number = $_POST["contact_number"];
            $school = $_POST["school"];
            $amount = $_POST["amount"];
            $deduction = $_POST["deduction"];
            $contact_email = $_POST["contact_email"];
            $contact_name = $_POST["contact_name"];
            $trans_time = date("Y-m-d H:i:s");

            //check if transation id already exists
            $exist = fetchData("transactionID","transaction","transactionID='$transaction_id'");

            if(is_array($exist)){
                echo "already-exist";
            }else{
                //prepare and bind parameters
                $query = "INSERT INTO transaction (transactionID, contactNumber, schoolBought, amountPaid, 
                    contactName, contactEmail, Deduction, Transaction_Date) VALUES (?,?,?,?,?,?,?,?)";
                $result = $connect->prepare($query);
                $result->bind_param("ssidssds", $transaction_id, $contact_number, $school, $amount, 
                    $contact_name, $contact_email, $deduction, $trans_time);

                //check for successful execution
                if($result->execute()){
                    echo "success";
                }else{
                    echo "database_send_error";
                }
            }            
        }elseif($submit == "compressimage"){
            if(isset($_FILES)){
                $file_input_name = "image";
                $local_directory = $_SERVER["DOCUMENT_ROOT"]."/shsdesk/purity/";
                $default_path = "";
                $pic_quality = 15;

                echo getImageDirectory($file_input_name, $local_directory, $default_path, $pic_quality);
            }else{
                echo "found no files";
            }
        }
    }elseif(isset($_GET['submit'])){ 
        $submit = $_GET["submit"];

        if($submit == "getStudentIndex" || $submit == "getStudentIndex_ajax"){
            $index_number = $_GET["index_number"] ?? "";
            $school_id = $_GET["school_id"] ?? "";

            $student = fetchData("*","cssps","indexNumber='$index_number'");            
            $array = array();
            
            if(is_array($student)){
                if($student["enroled"] == true && !empty($school_id)){
                    $array["status"] = "already-registered";
                }elseif(empty($school_id)){
                    if($student["enroled"] == false){
                        $array["status"] = "not-registered";
                    }else{
                        $array["status"] = "student_success";
                    }
                }else{
                    //check if right school is selected
                    if($student["schoolID"] == $school_id){
                        $array = $student;
                        $array["status"] = "success";
                    }else{
                        $array["status"] = "wrong-school-select";
                    }
                }
            }else{
                $array["status"] = "wrong-index";
            }

            header("Content-type: application/json");
            echo json_encode($array);
        }elseif($submit == "studentSchool" || $submit == "studentSchool_ajax"){
            $message = array();
            if(!isset($_GET["indexNumber"]) || $_GET["indexNumber"] === ""){
                $message["status"] = "No index number provided";
            }elseif(strlen($_GET["indexNumber"]) < 5){
                $message["status"] = "Index number must be at least 5 characters";
            }elseif(!preg_match("/^[0-9]{5,}$/",$_GET["indexNumber"])){
                $message["status"] = "Your index number should only be numeric";
            }else{
                $indexNumber = $_GET["indexNumber"];
                $student = fetchData(
                    ["c.schoolID", "c.Gender", "c.enroled", "c.Lastname", "s.schoolName", "s.abbr"],
                    [ "join" => "schools cssps", "alias" => "s c", "on" => "id schoolID"],
                    "c.indexNumber='$indexNumber'"
                );

                if(is_array($student)){
                    if($student["enroled"] == false){
                        $active = (int) fetchData("COUNT(id) as total","houses","schoolID=".$student["schoolID"])["total"];
                        
                        if($active > 0){
                            $message = $student;
                            $message["status"] = "success";
                            if(strtolower($student["Gender"]) == "male"){
                                $sal = "Mr";
                            }else{
                                $sal = "Mad";
                            }
                            $message["successMessage"] = "Congratulations $sal. ".$student["Lastname"]." on your admission to ".$student["abbr"];
                        }else{
                            $message["status"] = "Your school is not ready for admission. Please try again at another time";
                        }
                    }else{
                        $message["status"] = "This index number has already been enroled. Visit the Student Menu to get your documents";
                    }                        
                }else{
                    $message["status"] = "Index Number could not be found. Please check and try again, else use the message us button";
                }
            }
            
            header("Content-type: application/json");
            echo json_encode($message);
        }elseif($submit == "getContact" || $submit == "getContact_ajax"){
            $schoolID = $_REQUEST["schoolID"];

            //search admins table for school admin
            $admins = fetchData("id","roles","id > 2 AND title LIKE 'admin%'", 0);
            $sql = "SELECT fullname, contact FROM admins_table";
            
            if(is_array($admins)){
                $sql .= " WHERE school_id=$schoolID AND (";
                foreach($admins as $admin){
                    $sql .= " role=".$admin["id"];

                    if(end($admins) != $admin){
                        $sql .= " OR ";
                    }
                }
                $sql .= ")";
            }
            $result = $connect->query($sql);

            if($result->num_rows > 0){
                if($result->num_rows > 1){
                    $counter = 1;
                    $total = $result->num_rows;

                    while($counter <= $total && $row = $result->fetch_assoc()){
                        echo "<a href=\"tel:".remakeNumber($row["contact"],true,false)."\">".remakeNumber($row["contact"],false)."</a> - ".$row["fullname"];

                        if($counter < $total){
                            echo "<br>";
                        }
                    }
                }else{
                    $row = $result->fetch_assoc();
                    echo "<a href=\"tel:".remakeNumber($row["contact"],true,false)."\">".remakeNumber($row["contact"],false)."</a> - ".$row["fullname"];
                }
            }else{
                echo "<p>Admin contact is unavailable</p>";
            }
        }elseif($submit == "get_keys" || $submit == "get_keys_ajax"){
            $school_id = $_REQUEST["schoolID"] ?? null;

            if(is_null($school_id) || empty($school_id)){
                $message = "School required was not selected. Contact the admin for help";
            }elseif(ctype_digit($school_id) == false){
                $message = "School selected has an invalid index. Please contact admin for help";
            }else{
                /*$message = getSchoolSplit($school_id, APIKEY::ADMISSION);

                if(is_array($message)){
                    $stat = $message["status"];
                    $message = $message[APIKEY::ADMISSION];
                    
                    $message = $stat == true ? $message : "disabled"; 
                    $message = empty($message) ? "empty1" : $message;
                }

                if($message == "empty"){
                    $message = "Transaction for this school cannot be done yet";
                }elseif($message == "disabled"){
                    $message = "Transaction to this school has been disabled. Please try again later";
                }else{
                    $message .= " | $priceKey";
                }*/

                $message = "{$splitKey['matrix_school_management']} | $priceKey";
            }

            echo $message;
        }else{
            echo "Provided submission could not be found. Please try again, else use the message us button";
        }
    }else{
        echo "No recognized submission detected. Please try again, else use the message us button";
    }
    // echo date("Y-m-d H:i:s")
?>