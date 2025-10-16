<?php
    include_once("includes/session.php");

    if(isset($_POST['submit']) || isset($_POST["submit_admission"]) || isset($_POST["ad_transaction_id"])){
        $submit = $_POST['submit'] ?? ($_POST["submit_admission"] ?? "no-submit");

        if($submit == "admissionFormSubmit" || $submit == "admissionFormSubmit_ajax" || isset($_POST["ad_transaction_id"])){
            //receive details of the student
            //cssps details
            $ad_profile_pic = null;
            $academic_year = getAcademicYear(now(), false);
            $required_profile = [23];

            try {
                $school_name = $connect->real_escape_string($_POST["shs_placed"]);
                $school = fetchData("s.*, a.reopeningDate, a.letter_prefix", "schools s JOIN admissiondetails a ON s.id = a.schoolID", "s.schoolName='$school_name'", join_type: "left");
                $shs_placed = $school["id"];
                $ad_enrol_code = $_POST["ad_enrol_code"];
                $ad_index = $_POST["ad_index"];
                $ad_aggregate = $_POST["ad_aggregate"];

                //check if the aggregate is not a single number
                $ad_aggregate = is_numeric($ad_aggregate) && $ad_aggregate > 0 ? str_pad(intval($ad_aggregate), 2, "0", STR_PAD_LEFT) : null;

                $ad_course = $_POST["ad_course"];
                $program_id = isset($_POST["program_id"]) && !empty($_POST["program_id"]) ? $_POST["program_id"] : get_program_from_course($ad_course, $shs_placed);

                //personal details of candidate
                $ad_lname = ucwords($_POST["ad_lname"]);
                $ad_oname = ucwords($_POST["ad_oname"]);
                $ad_gender = ucwords($_POST["ad_gender"]);
                $ad_jhs = ucwords($_POST["ad_jhs"]);
                $ad_jhs_town = ucwords($_POST["ad_jhs_town"]);
                $ad_jhs_district = ucwords($_POST["ad_jhs_district"]);

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

                //create profile picture
                if (isset($_FILES["profile_pic"]) && !empty($_FILES["profile_pic"]["tmp_name"])) {
                    // get file extension
                    $ext = strtolower(fileExtension("profile_pic"));
                    $allowed = ["jpg", "jpeg", "png"];
                    
                    // max file size (2MB)
                    $maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
                    
                    if (in_array($ext, $allowed)) {
                        if ($_FILES["profile_pic"]["size"] <= $maxFileSize) {
                            
                            $file_input_name = "profile_pic";
                            $local_storage_directory = "$rootPath/assets/images/profiles/students";
                            
                            if (!is_dir($local_storage_directory)) {
                                mkdir($local_storage_directory, recursive: true);
                            }
                            
                            $ad_profile_pic = getFileDirectory($file_input_name, $local_storage_directory, $ad_index, true);
                            
                            // remove rootPath
                            $ad_profile_pic = explode("$rootPath/", $ad_profile_pic);
                            $ad_profile_pic = $ad_profile_pic[1];
                            
                        } else {
                            exit("Profile picture is too large. Upload a file 2MB or less"); // file exceeds 2MB
                        }
                    } else {
                        exit("profile-wrong-ext"); // invalid extension
                    }
                } elseif (in_array($shs_placed, $required_profile) && empty($_FILES["profile_pic"]["tmp_name"])) {
                    exit("profile-pic-required");
                }

                // generate extra info for student
                $admission_number = generateAdmissionNumber($shs_placed, $school["letter_prefix"]);
                $reopening_date = $school["reopeningDate"];

                //bind the statement
                $sql = "INSERT INTO enrol_table (indexNumber, enrolCode, shsID, aggregateScore, program, 
                lastname, othername, gender, jhsName, jhsTown, jhsDistrict, birthdate, birthPlace, fatherName, 
                fatherOccupation, motherName, motherOccupation, guardianName, residentAddress, postalAddress, primaryPhone, 
                secondaryPhone, interest, award, position, witnessName, witnessPhone, transactionID, profile_pic, academic_year, admission_number, reopening_date) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, '$academic_year', ?, ?)" or die($connect->error);

                //prepare query for entry into database
                $result = $connect->prepare($sql);
                $result->bind_param("ssissssssssssssssssssssssssssss", 
                $ad_index,$ad_enrol_code,$shs_placed, $ad_aggregate, $ad_course, $ad_lname, $ad_oname, 
                $ad_gender, $ad_jhs, $ad_jhs_town, $ad_jhs_district, $ad_birthdate, $ad_birth_place, $ad_father_name, 
                $ad_father_occupation, $ad_mother_name, $ad_mother_occupation, $ad_guardian_name, $ad_resident, $ad_postal_address, 
                $ad_phone, $ad_other_phone, $interest, $ad_awards, $ad_position, $ad_witness, $ad_witness_phone, $_POST["ad_transaction_id"], $ad_profile_pic,
                $admission_number, $reopening_date);

                //check for errors
                if(!isset($_POST["ad_transaction_id"]) || empty($_POST["ad_transaction_id"])){
                    $message = "no-transaction-id";
                }elseif(empty($ad_index)){
                    $message = "no-index-number";
                }elseif(empty($ad_enrol_code)){
                    $message = "no-enrolment-code";
                }elseif(strlen($ad_enrol_code) < 6 || strlen($ad_enrol_code) > 12){
                    $message = "enrolment-code-short";
                }elseif(fetchData("enrolCode","enrol_table","enrolCode='$ad_enrol_code'") != "empty"){
                    $message = "enrolment-code-exist";
                }elseif($shs_placed == "error"){
                    $message = "wrong-school";
                }/*elseif(empty($ad_aggregate)){
                    $message = "no-aggregate-score";
                }*/elseif($ad_aggregate && (intval($ad_aggregate) < 6 || intval($ad_aggregate) > 54)){
                    $message = "wrong-aggregate-score";
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
                        $result->close();

                        // update the cssps table that the student has enroled and also jhs attended and dob
                        $retry = 0;

                        $sql = "UPDATE cssps 
                            SET jhsAttended = ?, dob=?, profile_pic = ?, aggregate = ?, enroled = 1 
                            WHERE indexNumber = ?";
                        $stmt = $connect->prepare($sql);
                        $stmt->bind_param("sssis", $ad_jhs, $ad_birthdate, $ad_profile_pic, $ad_aggregate, $ad_index);
                        $stmt->execute();
                        $stmt->close();

                        //verify if transaction id can be found in database
                        $transaction_id = $_POST['ad_transaction_id'];
                        $db_transaction = fetchData("transactionID","transaction","transactionID='$transaction_id'");

                        //update the transaction table the transaction has been used
                        $sql = "UPDATE transaction 
                            SET Transaction_Expired = 1, indexNumber='$ad_index' 
                            WHERE transactionID='$transaction_id'";
                        $result = $connect->query($sql);

                        //automatically give user a house if school permits
                        $permit = $school["autoHousePlace"];
                        $null_entry = false;

                        //fetch boarding status for entry from cssps
                        $student_details = fetchData("boardingStatus", "cssps", "indexNumber='$ad_index'");
                        if (!isset($student_details["boardingStatus"])) {
                            exit("Student not found. Contact admin for help.");
                        }

                        $boarding_status = $student_details["boardingStatus"] ?? exit("Student not found");

                        if($permit){
                            //count number of houses of the school
                            $houses = decimalIndexArray(fetchData(...[
                                "columns" => ["id","title", "maleHeadPerRoom", "maleTotalRooms", "femaleHeadPerRoom", "femaleTotalRooms"],
                                "table" => "houses",
                                "where" => ["schoolID=$shs_placed", "(gender='$ad_gender'", "gender='Both')"],
                                "limit" => 0,
                                "where_binds" => ["AND","OR"]
                            ]));

                            //create an array for house details
                            $house = array();
                            $house_names = array();

                            //what to do if there are houses available
                            if(is_array($houses)){
                                $house_names = pluck($houses, "id", "title");
                                $total = count($houses);
                                $house = array_map(function($data) use ($ad_gender){
                                    $gender_room = strtolower($ad_gender)."HeadPerRoom";
                                    $gender_total_room = strtolower($ad_gender)."TotalRooms";
                                    return [
                                        "id" => $data["id"],
                                        "totalHeads" => intval($data[$gender_room]) * intval($data[$gender_total_room])
                                    ];
                                }, $houses);
                                
                                //allocate a house for student
                                $next_room = setHouse($ad_gender,$shs_placed, $ad_index, $house, $boarding_status);
                                
                                if(is_null($next_room)){
                                    //enter students into table but without houses
                                    $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus, academic_year)
                                        VALUES(?,?,?,?,NULL,?,?, '$academic_year')";
                                    $stmt = $connect->prepare($sql);
                                    $stmt->bind_param("sissss", $ad_index, $shs_placed, $ad_lname, $ad_oname, $ad_gender, $boarding_status);
                                    $stmt->execute();
                                    
                                    $_SESSION["ad_stud_house"] = "e";
                                }else{
                                    //parse entry into allocation table
                                    $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus, academic_year)
                                        VALUES(?,?,?,?,?,?,?, '$academic_year')";
                                    $stmt = $connect->prepare($sql);
                                    $stmt->bind_param("sississ", $ad_index, $shs_placed, $ad_lname, $ad_oname, $next_room, $ad_gender, $boarding_status);
                                    $stmt->execute();
                                    
                                    // $stud_house = fetchData("title","houses","id=$next_room");
                                    $_SESSION["ad_stud_house"] = $house_names[$next_room] ?? "e";
                                    $_SESSION["ad_stud_house"] = trim($_SESSION["ad_stud_house"]);
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
                            $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus, academic_year)
                                VALUES(?,?,?,?,NULL,?,?, '$academic_year')";
                            $stmt = $connect->prepare($sql);
                            $stmt->bind_param("sissss", $ad_index, $shs_placed, $ad_lname, $ad_oname, $ad_gender, $boarding_status);
                            $stmt->execute();
                            
                            $_SESSION["ad_stud_house"] = "e";
                        }
                        
                        //all data required are written now
                        $message = "success";

                        //create a session responsible for preparing admission letter
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
                        $_SESSION["ad_reopening"] = $school["reopeningDate"] ?? "Not Set";
                        $_SESSION["ad_admission_title"] = $school["admissionHead"];

                        //student details
                        $_SESSION["ad_stud_index"] = $ad_index;
                        $_SESSION["ad_stud_lname"] = $ad_lname;
                        $_SESSION["ad_stud_oname"] = $ad_oname;
                        $_SESSION["ad_stud_enrol_code"] = $ad_enrol_code;
                        $_SESSION["ad_stud_residence"] = $boarding_status;
                        $_SESSION["ad_stud_program"] = $ad_course;
                        $_SESSION["ad_stud_gender"] = $ad_gender;
                        $_SESSION["ad_profile_pic"] = $ad_profile_pic;
                        $_SESSION["ad_admission_number"] = $admission_number ?? null;

                        //convert house name for only auto house placed students
                        /*if($_SESSION["ad_stud_house"] !== "e" && array_search()){
                            $_SESSION["ad_stud_house"] = fetchData(
                                "h.title", [
                                    "join" => "house_allocation houses",
                                    "on" => "houseID id",
                                    "alias" => "ho h"
                                ],
                                "ho.indexNumber='$ad_index'"
                            )["title"];
                        }*/

                        // insert student data into database
                        if(!empty($program_id)){
                            $password = password_hash("Password@1", PASSWORD_DEFAULT);
                            $sql = "INSERT INTO students_table (
                                indexNumber, profile_pic, Lastname, Othernames, Gender, 
                                houseID, school_id, studentYear, guardianContact, programme, program_id, 
                                boardingStatus, password, uploaded) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                            $stmt = $connect2->prepare($sql);
                            $house_id = $next_room ?? 0;
                            $uploaded = false;
                            $year = 1;
                            $stmt->bind_param("sssssiiississi", $ad_index, $ad_profile_pic, $ad_lname, $ad_oname, $ad_gender, $house_id, $shs_placed,
                                $year, $ad_phone, $ad_course, $program_id, $student["boardingStatus"], $password, $uploaded);
                            $stmt->execute();
                            $stmt->close();
                        }
                    }else{
                        $error = strtolower(strval($result->error));
                        if(str_contains($error, "duplicate")){
                            if(str_contains($error, "enrol_table.primary"))
                                $message = "Your data has already been written. Please navigate to <a href='https://www.shsdesk.com/student'>shsdesk.com/student</a> to print document";
                            elseif(str_contains($error, "enrol_table.transactionid"))
                                $message = "This transaction has already been used. Please contact admins via the message us button for help";
                            else
                                $message = "A duplicate entry has been recognized. Please contact admin for help";
                        }else{
                            $message = "Navigate to <a href='https://www.shsdesk.com/student'>shsdesk.com/student</a> to check for document";
                        }
                    }
                }
            } catch (\Throwable $th) {
                // remove profile pic if it has been created
                if(!is_null($ad_profile_pic)){
                    unlink($_SERVER["DOCUMENT_ROOT"]."/".$ad_profile_pic);
                }
                
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
                $subject = "SHSDesk Customer Enquiry - $email";
                $message .= "<br>Sender: $email<br>Fullname: $fullname";
                $response = send_email($message, $subject, ["safosah00@gmail.com", "successinnovativehub@gmail.com"], reply: $email);
                $response = $response === true ? "true" : $response;
                $response = $response === false ? "Message could not be sent, try again" : $response;

                echo $response;
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

            //check if webhook has already entered the detail
            $trans_data = fetchData("transactionID", "transaction", "transactionID='$transaction_id'");

            if(is_array($trans_data)){
                echo "success-".getSchoolDetail($school, true)["techContact"];
            }else{
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
                        echo "-".remakeNumber(getSchoolDetail($school, true)["techContact"], true, false);
                    }else{
                        echo "This transaction id does not match the selected school";
                    }                    
                }else{
                    echo "ref_expired";
                }
            }else{
                // check on paystack to see if its valid
                $response = transaction_exists($reference)["response"];

                if($response["status"]){
                    $data = $response["data"];
                    $metadata = $data["metadata"]["custom_fields"];

                    // insert transaction
                    insert_transaction($data);

                    $school = getSchoolDetail($metadata[2]["value"], true);
                    $tech = $school["techContact"] ?? "N/A";

                    echo "success-$tech";
                }else{
                    echo "error";
                }

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
                try {
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
                } catch (\Throwable $th) {
                    echo throwableMessage($th);
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
        }elseif($submit == "activate_index_number"){
            $index_number = $_POST["students_index"];
            $r_index_number = $_POST["check_index_number"];
            $hashed_index = $_POST["hashed_index"] ?? "";

            if(empty($r_index_number)){
                $message = "JHS index number not provided";
            }elseif(!is_numeric($r_index_number)){
                $message = "JHS index number provided has invalid format";
            }elseif(strlen($r_index_number) != 12){
                $message = "JHS index number is expected to be 12 characters long";
            }elseif(($y = substr($r_index_number, 10)) != $index_end){
                $message = "JHS index number provided is not registered for current admission year $y";
            }elseif(!verify_index_number_hash($hashed_index, $r_index_number)){
                $message = "JHS index number provided to this account is invalid";
            }else{
                $sql = "UPDATE cssps SET indexNumber=? WHERE indexNumber=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ss", $r_index_number, $index_number);
                $message = $stmt->execute() === true ? "success" : $stmt->error;
            }

            echo $message;
        }elseif($submit == "get_cssps_programs"){
            $school_id = $_POST["school_id"];
            $programs = decimalIndexArray(fetchData("DISTINCT programme", "cssps", "schoolID=$school_id", 0));
            if($programs){
                $response = json_encode(convertToUtf8(["data" => array_column($programs, "programme")]));
            }else{
                $response = json_encode(["data" => "No student record found"]);
            }

            header("Content-type: application/json");
            echo $response;
        }elseif($submit == "get_cssps_students"){
            $school_id = $_POST["school_id"] ?? null;
            $programme = $_POST["programme"] ?? null;
            $academic_year = getAcademicYear(now(), false);

            if(empty($school_id)){
                $message = "School was not selected or is invalid";
            }elseif(empty($programme)){
                $message = "Program name has not been selected or is invalid";
            }

            if(!isset($message)){
                $students = decimalIndexArray(fetchData("indexNumber, hidden_index, CONCAT(Lastname,' ',Othernames) as fullname", "cssps", "schoolID=$school_id AND academic_year='$academic_year' AND enroled=FALSE AND hidden_index IS NOT NULL AND programme='$programme'", 0));
            
                if($students){
                    $response = json_encode(convertToUtf8(["data" => $students]));
                }else{
                    $response = json_encode(["data" => "No student record found"]);
                }
            }else{
                $response = json_encode(convertToUtf8(["data" => $message]));
            }

            header("Content-type: application/json");
            echo $response;
        }elseif($submit == "get_admission_price"){
            header("Content-type: application/json");
            echo json_encode(["price" => ($system_usage_price + $system_up_gross)]);
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
                        //cross check if user has no issue
                        $has_issue = fetchData("COUNT(indexNumber) as total","enrol_table","indexNumber='$index_number'")["total"];
                        if($has_issue > 0){
                            $array["status"] = "student_success";
                        }else{
                            $array["status"] = "not-registered";
                        }                        
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
            echo json_encode(convertToUtf8($array));
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
                    ["c.schoolID", "c.Gender", "c.academic_year", "c.enroled", "c.Lastname", "s.schoolName", "s.abbr"],
                    [ "join" => "schools cssps", "alias" => "s c", "on" => "id schoolID"],
                    "c.indexNumber='$indexNumber'"
                );

                if(is_array($student)){
                    if($student["enroled"] == false){
                        $academic_year = getAcademicYear(now(), false);

                        if(formatAcademicYear($student["academic_year"], false) == $academic_year){
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
                            $message["status"] = "You are not registered for this admission year. Contact school admin for help";
                        }
                        
                    }else{
                        $message["status"] = "This index number has already been enroled. Visit the Student Menu to get your documents";
                    }                        
                }else{
                    // check if its an affiliate index number
                    $url = "https://myseniorhigh.com/api/v1/search/$indexNumber";
                    $token = $env["affiliate_bearer"];
                    $response = curl_get($url, headers: [
                        "authorization: Bearer $token"
                    ])["response"];

                    if($response["exist"]){
                        // ensure user hasnt completed his registration
                        $school = fetchData("id, abbr, schoolName", "schools", "affiliate_code='{$response['school_uuid']}'");

                        if(is_array($school)){
                            // store student into our system
                            $academic_year = getAcademicYear(now(), false);
                            $connect->query("INSERT IGNORE INTO affiliate_cssps (index_number, school_id, academic_year) VALUES ('$indexNumber', {$school['id']}, '$academic_year')");
                            $message["status"] = "success";
                            $message["successMessage"] = "Congratulations $indexNumber on your admission to {$school['abbr']}";
                            $message["schoolID"] = $school["id"];
                            $message["schoolName"] = $school["schoolName"];
                            $message["link"] = $response["admission_url"];
                        }else{
                            $message["status"] = "The school you are trying to enrol to is not registered on our platform. Contact admin for help";
                        }
                    }else{
                        $message["status"] = "Index Number could not be found. Please check and try again, else use the message us button";
                    }
                }
            }
            
            header("Content-type: application/json");
            echo json_encode(convertToUtf8($message));
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
            }elseif(!is_numeric($school_id)){
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

                $message = "{$splitKey['matrix_admission']} | $priceKey";
            }

            echo $message;
        }elseif($submit == "get_programs"){
            $course_name = $_GET["course_name"];
            $school_id = $_GET["school_id"];
            $programs = decimalIndexArray(fetchData1(["program_id", "program_name", "course_ids"], "program", ["school_id=$school_id", "LOWER(associate_program) = '".strtolower($course_name)."'"], 0, where_binds: "AND"));
            $auto_class_place = fetchData("a.autoClassPlace", [
                "join" => "schools admissiondetails", "on" => "id schoolID", "alias" => "s a"
            ], "s.id=$school_id")["autoClassPlace"] ?? false;

            if($programs && $auto_class_place){
                $progs = [];
                foreach($programs as $program){
                    $ids = array_filter(explode(" ", $program["course_ids"]));
                    $ids = implode(", ", $ids);
                    $courses = decimalIndexArray(fetchData1("course_name", "courses", "course_id IN ($ids)", 0));
                    $progs[] = [
                        "id" => $program["program_id"],
                        "name" => $program["program_name"],
                        "courses" => implode(", ", array_column($courses, "course_name"))
                    ];
                }
                echo json_encode(convertToUtf8($progs));
            }else{
                echo "none";
            }
        }else{
            echo "Provided submission could not be found. Please try again, else use the message us button";
        }
    }else{
        echo "No recognized submission detected. Please try again, else use the message us button";
    }
    close_connections();
?>