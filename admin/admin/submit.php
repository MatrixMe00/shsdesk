<?php
    require_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");

    if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != NULL){
        $submit = $_REQUEST["submit"];

        if($submit == "new_user_update" || $submit == "new_user_update_ajax"){
            $new_username = $_REQUEST["new_username"];
            $new_password = $_REQUEST["new_password"];
            $fullname = $_REQUEST["fullname"];
            $email = $_REQUEST["email"];

            //search for email
            $sql = "SELECT user_id, email FROM admins_table WHERE email=? AND LOWER(username) = 'new user'";
            $res = $connect->prepare($sql);
            $res->bind_param("s", $email);
            
            //execute statement
            $res->execute();

            //get results
            $res = $res->get_result();

            if($res->num_rows > 0){
                $row = $res->fetch_assoc();

                if(strtolower($new_username) == "new user"){
                    echo "same-username";
                }elseif(strtolower($new_password) == "password@1"){
                    echo "same-password";
                }elseif(intval(strlen($new_password)) < 8){
                    echo "short-password";
                }else{
                    //check if the username already exists in database
                    $user_exist = fetchData("username","admins_table","username='$new_username'");

                    if($user_exist == "empty"){
                        $connect->begin_transaction();
                        try {
                            //update the content
                            $sql = "UPDATE admins_table SET username=?, password=?, new_login=0 WHERE email=?";
                            $res = $connect->prepare($sql);

                            //hash password
                            $new_password = password_hash($new_password, PASSWORD_DEFAULT);
                            
                            $res->bind_param("sss",$new_username,$new_password,$email);

                            if($res->execute()){
                                //grab the time now
                                $now = date('Y-m-d H:i:s');

                                //create login awareness
                                $sql = "INSERT INTO login_details (user_id, login_time) VALUES (".$row['user_id'].", '$now')";

                                if($connect->query($sql)){
                                    //update user session id
                                    $_SESSION['user_login_id'] = $row['user_id'];

                                    //set login id
                                    $_SESSION['login_id'] = $connect->insert_id;

                                    $connect->commit();
                                }else{
                                    echo 'cannot login';
                                }

                                //reload or redirect admin page
                                if($submit == "new_user_update"){
                                    $location = $_SERVER["HTTP_REFERER"];
                                    header("location:$location");
                                }else{
                                    echo "success";
                                }
                            }else{
                                echo "update-error";
                            }
                        } catch (\Throwable $th) {
                            $connect->rollback();
                            echo throwableMessage($th);
                        }
                    }else{
                        echo "username-exist";
                    }
                }
            }else{
                echo "wrong-email-fullname";
            }
        }elseif($submit == "adminAddStudent" || $submit == "adminAddStudent_ajax"){
            $student_index = strip_tags(stripslashes($_REQUEST["student_index"]));
            $lname = formatName(strip_tags(stripslashes($_REQUEST["lname"])));
            $oname = formatName(strip_tags(stripslashes($_REQUEST["oname"])));
            $gender = formatName(strip_tags(stripslashes($_REQUEST["gender"])));
            $boarding_status = formatName(strip_tags(stripslashes($_REQUEST["boarding_status"])));
            $student_course = formatName(strip_tags(stripslashes($_REQUEST["student_course"])));
            $aggregate = strip_tags(stripslashes($_REQUEST["aggregate"]));
            $jhs = formatName(strip_tags(stripslashes($_REQUEST["jhs"])));
            $dob = strip_tags(stripslashes($_REQUEST["dob"]));
            $track_id = strip_tags(stripslashes($_REQUEST["track_id"]));
            $school_id = $user_school_id;

            //variable to hold messages
            $message = "";

            if(empty($student_index) || is_null($student_index)){
                $message = "index-number-empty";
            }elseif(empty($lname) || is_null($lname)){
                $message = "lastname-empty";
            }elseif(empty($oname) || is_null($oname)){
                $message = "no-other-name";
            }elseif(empty($gender) || is_null($gender)){
                $message = "gender-not-set";
            }elseif(empty($boarding_status) || is_null($boarding_status)){
                $message = "boarding-status-not-set";
            }elseif(empty($student_course) || is_null($student_course)){
                $message = "no-student-program-set";
            }elseif(empty($aggregate) || is_null($aggregate)){
                $message = "no-aggregate-set";
            }elseif(intval($aggregate) < 6 || intval($aggregate) > 81){
                $message = "aggregate-wrong";
            }elseif(empty($track_id) || is_null($track_id)){
                $message = "no-track-id";
            }elseif(empty($school_id) || is_null($school_id)){
                $message = "no-school-id";
            }else{
                //format date
                $dob = date("Y-m-d", strtotime($dob));

                //verify if index number is unavailable
                $valid = fetchData("indexNumber","cssps","indexNumber='$student_index'");

                if($valid == "empty"){
                    $academic_year = getAcademicYear(date("d-m-Y"), false);
                    //insert data into CSSPS table
                    $sql = "INSERT INTO cssps (indexNumber,Lastname,Othernames,Gender,
                            boardingStatus,programme, aggregate, jhsAttended, dob, trackID, schoolID, academic_year) 
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,'$academic_year')";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("ssssssisssi",$student_index,$lname,$oname,$gender,$boarding_status,$student_course,
                        $aggregate,$jhs,$dob,$track_id,$school_id);
                    $stmt->execute();

                    $message = "success";
                }else{
                    $message = "data-exist";
                }
            }

            echo $message;
        }elseif($submit == "adminAddStudent1" || $submit == "adminAddStudent1_ajax"){
            $student_index = $_REQUEST["student_index"] ?? null;
            $lname = ucwords($_REQUEST["lname"]) ?? null;
            $oname = ucwords($_REQUEST["oname"]) ?? null;
            $gender = ucfirst($_REQUEST["gender"]) ?? null;
            $boarding_status = ucfirst($_REQUEST["boarding_status"]) ?? null;
            $house = $_REQUEST["house"] ?? null;
            $student_course = ucwords($_REQUEST["student_course"]) ?? null;
            $guardian_contact = $_REQUEST["guardian_contact"] ?? null;
            $student_year = $_REQUEST["student_year"] ?? null;
            $program_id = $_REQUEST["program_id"] ?? null;
            $school_id = $user_school_id;

            //variable to hold messages
            $message = "";

            if(is_null($student_index)){
                $message = "no-index";
            }elseif(!empty($student_index) && strlen($student_index) < 6){
                $message = "student-index-short";
            }elseif(!empty($student_index) && strlen($student_index) > 13){
                $message = "student-index-long";
            }elseif(empty($lname) || is_null($lname)){
                $message = "lastname-empty";
            }elseif(empty($oname) || is_null($oname)){
                $message = "no-other-name";
            }elseif(empty($gender) | is_null($gender)){
                $message = "gender-not-set";
            }elseif(empty($boarding_status) || is_null($boarding_status)){
                $message = "boarding-status-not-set";
            }elseif(empty($house) || is_null($house)){
                $message = "no-house-id";
            }elseif(empty($student_course) || is_null($student_course)){
                $message = "no-student-program-set";
            }elseif(empty($program_id) || is_null($program_id)){
                $message = "no-program-id";
            }elseif(empty($student_year) || is_null($student_year)){
                $message = "no-student-year";
            }elseif(empty($school_id) || is_null($school_id)){
                $message = "no-school-id";
            }else{
                //verify if index number is unavailable
                if($student_index == ""){
                    do{
                        $student_index = generateIndexNumber($school_id);
                        $original_index = false;
                        
                        $valid = fetchData1("indexNumber", "students_table","indexNumber='$student_index'");
                    }while($valid != "empty");
                }else{
                    $original_inde = true;
                    $valid = fetchData1("indexNumber","students_table","indexNumber='$student_index'");
                }
                
                if($valid == "empty"){
                    //insert data into students table
                    $sql = "INSERT INTO students_table (indexNumber,Lastname,Othernames,Gender, houseID, school_id, studentYear, guardianContact, programme, program_id, boardingStatus)
                        VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("ssssiiissis",$student_index,$lname,$oname,$gender,$house,$school_id,$student_year,$guardian_contact,$student_course, $program_id,$boarding_status);
                    $stmt->execute();

                    $message = "success";
                }else{
                    $message = "data-exist";
                }
            }

            echo $message;
        }elseif($submit == "adminUpdateStudent" || $submit == "adminUpdateStudent_ajax"){
            //perform operation based on mode
            $admin_mode = $_REQUEST["admin_mode"];

            if($admin_mode == "admission"){
                $enrolCode = $_REQUEST["enrolCode"];
                $aggregate = strip_tags(stripslashes($_REQUEST["aggregate"])) ?? null;
                $jhs = strip_tags(stripslashes($_REQUEST["jhs"])) ?? null;
                $dob = strip_tags(stripslashes($_REQUEST["dob"])) ?? null;
                $track_id = strip_tags(stripslashes($_REQUEST["track_id"])) ?? null;
            }else{
                $guardianContact = $_REQUEST["guardianContact"] ?? null;
                $year_level = $_REQUEST["year_level"] ?? null;
                $program_id = $_REQUEST["program_id"] ?? null;
            }
            // print_r($_REQUEST); return;
            $student_index = strip_tags(stripslashes($_REQUEST["student_index"])) ?? null;
            $lname = strip_tags(stripslashes($_REQUEST["lname"])) ?? null;
            $oname = strip_tags(stripslashes($_REQUEST["oname"])) ?? null;
            $gender = strip_tags(stripslashes($_REQUEST["gender"])) ?? null;
            $boarding_status = strip_tags(stripslashes($_REQUEST["boarding_status"])) ?? null;
            $student_course = strip_tags(stripslashes($_REQUEST["student_course"])) ?? null;

            if(isset($_REQUEST["house"]))
                $house = strip_tags(stripslashes($_REQUEST["house"]));

            //variable to hold messages
            $message = "";

            if(is_null($student_index) || empty($student_index)){
                $message = "index-number-empty";
            }elseif(is_null($lname) || empty($lname)){
                $message = "lastname-empty";
            }elseif(is_null($oname) || empty($oname)){
                $message = "no-other-name";
            }elseif(is_null($gender) || empty($gender)){
                $message = "gender-not-set";
            }elseif(is_null($boarding_status) || empty($boarding_status)){
                $message = "boarding-status-not-set";
            }elseif(is_null($student_course) || empty($student_course)){
                $message = "no-student-program-set";
            }elseif($admin_mode == "admission" && (is_null($aggregate) || empty($aggregate))){
                $message = "no-aggregate-set";
            }elseif($admin_mode == "admission" && (intval($aggregate) < 6 || intval($aggregate) > 81)){
                $message = "aggregate-wrong";
            }elseif($admin_mode == "admission" && (is_null($track_id) || empty($track_id))){
                $message = "no-track-id";
            }elseif($admin_mode == "admission" && (isset($_REQUEST['house']) && empty($house))){
                $message = "no-house";
            }elseif($admin_mode == "records" && (is_null($year_level) || empty($year_level))){
                $message = "Student year was not specified. Please specify before you continue";
            }elseif($admin_mode == "records" && (is_null($program_id) || empty($program_id))){
                $message = "Student class not specified. Please specify before you continue";
            }else{
                try{
                    if($admin_mode == "records"){
                        if(!is_null($guardianContact) && !empty($guardianContact)){
                            if(strlen($guardianContact) != 10){
                                $message = "Guardian's phone number is too short";
                            }elseif(is_numeric($guardianContact) === false){
                                $message = "Your phone number should only contain numbers";
                            }elseif(array_search(substr($guardianContact,0,3), $phoneNumbers) === false){
                                $message = "Phone number has an invalid operator";
                            }
    
                            if(!empty($message)){
                                echo $message;
                                return;
                            }
                        }else{
                            $guardianContact = "";
                        }

                        $sql = "UPDATE students_table SET Lastname=?, Othernames=?, Gender=?,
                            studentYear=?, guardianContact=?, programme=?, program_id=?, boardingStatus=?
                            WHERE indexNumber=?";
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("sssississ", $lname, $oname, $gender, $year_level, $guardianContact, $student_course, $program_id, $boarding_status, $student_index);
                        if($stmt->execute()){
                            $message = "success";
                        }else{
                            $message = "There was an error while updating the student detail";
                        }
                        // $message = "Everything is fine";
                    }else{
                        //format date
                        $dob = date("Y-m-d", strtotime($dob));

                        //update data in CSSPS table
                        $sql = "UPDATE cssps SET Lastname=?, Othernames=?, Gender=?, boardingStatus=?,
                                programme=?, aggregate=?, jhsAttended=?, dob=?, trackID=? 
                                WHERE indexNumber=?";
                        $stmt = $connect->prepare($sql);
                        $stmt->bind_param("sssssissss",$lname,$oname,$gender,$boarding_status,$student_course,
                            $aggregate,$jhs,$dob,$track_id, $student_index);
                        if($stmt->execute()){
                            //update student enrolment data
                            if(fetchData("enroled","cssps","indexNumber='$student_index'")["enroled"]){
                                if(empty($enrolCode)){
                                    $message = "no-enrol-code";
                                }elseif(strlen($enrolCode) < 6){
                                    $message = "enrol-code-short";
                                }else{
                                    $sql = "UPDATE enrol_table SET program=?, lastname=?, othername=?, gender=?, jhsName=?, birthdate=?, enrolCode=? WHERE indexNumber=?";
                                    $stmt = $connect->prepare($sql);
                                    $stmt->bind_param("ssssssss",$student_course, $lname, $oname, $gender, $jhs, $dob, $enrolCode, $student_index);
                                    $stmt->execute();
                                }

                                //make update to house allocation
                                $sql = "UPDATE house_allocation SET studentLname=?, studentOname=?, houseID=?, studentGender=?, boardingStatus=? 
                                    WHERE indexNumber=?";
                                $stmt = $connect->prepare($sql);
                                $stmt->bind_param("ssisss", $lname, $oname, $house, $gender, $boarding_status, $student_index);
                            }
                            if($stmt->execute()){
                                $message = "success";
                            }else{
                                $message = "Could not update details";
                            }
                        }else{
                            $message = "Could not update details";
                        }  
                    }
                }catch(\Throwable $th){
                    if($developmentServer){
                        $message = $th->getTraceAsString();
                    }else{
                        $message = $th->getMessage();
                    }
                }
            }

            echo $message;
        }elseif($submit == "fetchStudentDetails"){
            $index_number = $_REQUEST["index_number"];
            $registered = $_REQUEST["registered"];
            $db = $_REQUEST["db"];

            if(empty($db)){
                if($registered == "true"){
                    $sql = "SELECT c.*, e.enrolCode, h.houseID 
                        FROM cssps c JOIN house_allocation h
                        ON c.indexNumber = h.indexNumber 
                        JOIN enrol_table e ON c.indexNumber = e.indexNumber
                        WHERE c.indexNumber='$index_number'";
                }else{
                    $sql = "SELECT * FROM cssps WHERE indexNumber='$index_number' AND enroled=FALSE";
                }
            }else{
                $sql = "SELECT indexNumber, Lastname, Othernames, studentYear, houseID, boardingStatus, program_id 
                    FROM students_table s
                    WHERE indexNumber='$index_number'";
            }            
            
            $query = $connect->query($sql);

            $result = array();

            if($query->num_rows > 0){
                $result = $query->fetch_assoc();
                $result += array(
                    "status" => "success"
                );
            }else{
                $result = array("status" => "no-result", "sql" => $sql);
            }

            echo json_encode($result);
        }elseif($submit == "fetchStudentsDetail" || $submit == "fetchStudentsDetail_ajax"){
            $index_number = $_REQUEST["index_number"];

            $sql = "SELECT * FROM students_table WHERE indexNumber='$index_number'";

            $query = $connect2->query($sql);

            $result = array();

            if($query->num_rows > 0){
                $result = $query->fetch_assoc();
                $result += array(
                    "status" => "success"
                );
            }else{
                $result = array("status" => "no-result", "sql" => $sql);
            }

            echo json_encode($result);
        }elseif($submit == "addHouse" || $submit == "addHouse_ajax"){
            $house_name = $_REQUEST["house_name"];
            $gender = @$_REQUEST["gender"];
            $male_house_room_total = $_REQUEST["male_house_room_total"];
            $male_head_per_room = $_REQUEST["male_head_per_room"];
            $female_house_room_total = $_REQUEST["female_house_room_total"];
            $female_head_per_room = $_REQUEST["female_head_per_room"];
            $school_id = $_REQUEST["school_id"];

            $message = "";

            if(empty($house_name)){
                $message = "no-house-name";
            }elseif(empty($gender)){
                $message = "no-gender";
            }elseif(empty($male_house_room_total) && (strtolower($gender) == "both" || strtolower($gender) == "male")){
                $message = "male-room-total-empty";
            }elseif(intval($male_house_room_total) <= 0 && (strtolower($gender) == "both" || strtolower($gender) == "male")){
                $message = "male-room-zero";
            }elseif(empty($female_house_room_total) && (strtolower($gender) == "both" || strtolower($gender) == "female")){
                $message = "female-room-total-empty";
            }elseif(intval($female_house_room_total) <= 0 && (strtolower($gender) == "both" || strtolower($gender) == "female")){
                $message = "female-room-zero";
            }elseif(empty($male_head_per_room) && (strtolower($gender) == "both" || strtolower($gender) == "male")){
                $message = "male-head-total-empty";
            }elseif(intval($male_head_per_room) <= 0 && (strtolower($gender) == "both" || strtolower($gender) == "male")){
                $message = "male-head-zero";
            }elseif(empty($female_head_per_room) && (strtolower($gender) == "both" || strtolower($gender) == "female")){
                $message = "female-head-total-empty";
            }elseif(intval($female_head_per_room) <= 0 && (strtolower($gender) == "both" || strtolower($gender) == "female")){
                $message = "female-head-zero";
            }elseif(empty($school_id) || !intval($school_id)){
                $message = "No School provided or school selection was unsuccessful. Please try again later";
            }else{
                //query into database by gender
                if(strtolower($gender) == "male"){
                    $sql = "INSERT INTO houses (title, schoolID, maleTotalRooms, maleHeadPerRoom, gender) 
                        VALUES (?,?,?,?,?)";
                }elseif(strtolower($gender == "female")){
                    $sql = "INSERT INTO houses (title, schoolID, femaleTotalRooms, femaleHeadPerRoom, gender) 
                        VALUES (?,?,?,?,?)";
                }else{
                    $sql = "INSERT INTO houses (title, schoolID, maleTotalRooms, maleHeadPerRoom, femaleTotalRooms, femaleHeadPerRoom, gender) 
                        VALUES (?,?,?,?,?,?,?)";
                }

                //prepare sql statement
                $stmt = $connect->prepare($sql);

                //bind parameters
                if(strtolower($gender) == "male"){
                    $stmt->bind_param("siiis",$house_name, $school_id, $male_house_room_total, $male_head_per_room, $gender);
                }elseif(strtolower($gender == "female")){
                    $stmt->bind_param("siiis",$house_name, $school_id, $female_house_room_total, $female_head_per_room, $gender);
                }else{
                    $stmt->bind_param("siiiiis",$house_name, $school_id, $male_house_room_total, $male_head_per_room, $female_house_room_total, $female_head_per_room, $gender);
                }

                $stmt->execute();

                $message = "success";
            }

            echo $message;
            
        }elseif($submit == "updateHouse" || $submit == "updateHouse_ajax"){
            $house_name = $_REQUEST["house_name"];
            $gender = @$_REQUEST["gender"];
            $male_house_room_total = $_REQUEST["male_house_room_total"];
            $male_head_per_room = $_REQUEST["male_head_per_room"];
            $female_house_room_total = $_REQUEST["female_house_room_total"];
            $female_head_per_room = $_REQUEST["female_head_per_room"];
            $school_id = $_REQUEST["school_id"];

            if(isset($_REQUEST["house_id"])){
                $id = $_REQUEST["house_id"];
            }else{
                echo "ID for selected house not found or provided. Process execution was aborted"; exit(1);
            }

            $message = "";

            if(empty($house_name)){
                $message = "no-house-name";
            }elseif(empty($gender)){
                $message = "no-gender";
            }elseif(empty($male_house_room_total) && (strtolower($gender) == "both" || strtolower($gender) == "male")){
                $message = "male-room-total-empty";
            }elseif(intval($male_house_room_total) <= 0 && (strtolower($gender) == "both" || strtolower($gender) == "male")){
                $message = "male-room-zero";
            }elseif(empty($female_house_room_total) && (strtolower($gender) == "both" || strtolower($gender) == "female")){
                $message = "female-room-total-empty";
            }elseif(intval($female_house_room_total) <= 0 && (strtolower($gender) == "both" || strtolower($gender) == "female")){
                $message = "female-room-zero";
            }elseif(empty($male_head_per_room) && (strtolower($gender) == "both" || strtolower($gender) == "male")){
                $message = "male-head-total-empty";
            }elseif(intval($male_head_per_room) <= 0 && (strtolower($gender) == "both" || strtolower($gender) == "male")){
                $message = "male-head-zero";
            }elseif(empty($female_head_per_room) && (strtolower($gender) == "both" || strtolower($gender) == "female")){
                $message = "female-head-total-empty";
            }elseif(intval($female_head_per_room) <= 0 && (strtolower($gender) == "both" || strtolower($gender) == "female")){
                $message = "female-head-zero";
            }else{
                //query into database by gender
                if(strtolower($gender) == "male"){
                    $sql = "UPDATE houses SET title=?, maleTotalRooms=?, maleHeadPerRoom=?, gender=?
                        WHERE id=?";
                }elseif(strtolower($gender == "female")){
                    $sql = "UPDATE houses SET title=?, femaleTotalRooms=?, femaleHeadPerRoom=?, gender=?
                        WHERE id=?";
                }else{
                    $sql = "UPDATE houses SET title=?, maleTotalRooms=?, maleHeadPerRoom=?, femaleTotalRooms=?, femaleHeadPerRoom=?, gender=?
                        WHERE id=?";
                }

                //prepare sql statement
                $stmt = $connect->prepare($sql);

                //bind parameters
                if(strtolower($gender) == "male"){
                    $stmt->bind_param("siisi",$house_name, $male_house_room_total, $male_head_per_room, $gender, $id);
                }elseif(strtolower($gender == "female")){
                    $stmt->bind_param("siisi",$house_name, $female_house_room_total, $female_head_per_room, $gender, $id);
                }else{
                    $stmt->bind_param("siiiisi",$house_name, $male_house_room_total, $male_head_per_room, $female_house_room_total, $female_head_per_room, $gender, $id);
                }

                //execute statement
                $stmt->execute();

                $message = "success";
            }

            echo $message;
        }elseif($submit == "fetchHouseDetails"){
            $id = $_REQUEST["id"];
            $result = fetchData("*","houses","id=$id");
            if($result == "empty"){
                $result = array();
                $result["status"] = "error";
            }else{
                $result["status"] = "success";
            }

            echo json_encode($result);
        }elseif($submit == "exeat_request" || $submit == "exeat_request_ajax"){
            $student_index = $_REQUEST["student_index"];
            $exeat_town = $_REQUEST["exeat_town"];
            $exeat_date = $_REQUEST["exeat_date"];
            $return_date = $_REQUEST["return_date"];
            $exeat_type = $_REQUEST["exeat_type"];
            $exeat_reason = $_REQUEST["exeat_reason"];
            $school_id = $_REQUEST["school_id"];

            $message = "";

            if(empty($student_index)){
                $message = "no-index";
            }elseif(empty($exeat_town)){
                $message = "no-town";
            }elseif(empty($exeat_date)){
                $message = "no-exeat-date";
            }elseif(empty($return_date)){
                $message = "no-return-date";
            }elseif(strtotime($exeat_date) > strtotime($return_date)){
                $message = "date-conflict";
            }elseif(empty($exeat_type)){
                $message = "no-exeat-type";
            }elseif(empty($exeat_reason)){
                $message = "no-reason";
            }elseif(strlen($exeat_reason) < 3 || strlen($exeat_reason) > 80){
                $message = "range-error";
            }elseif(empty($school_id) || !intval($school_id)){
                $message = "No School provided or school selection was unsuccessful. Please try again later";
            }else{
                //validate index number
                $sql = "SELECT houseID FROM students_table WHERE indexNumber=?";
                $stmt = $connect2->prepare($sql);
                $stmt->bind_param("s",$student_index);
                $stmt->execute();

                $result = $stmt->get_result();

                if($result->num_rows > 0){
                    //get house id
                    $column = "houseID";
                    $table = "students_table";
                    $where = "indexNumber='$student_index'";

                    $data = fetchData1($column, $table, $where);
                    
                    //parse data into database
                    $sql = "INSERT INTO exeat (indexNumber,houseID,exeatTown,exeatDate,expectedReturn,exeatReason,exeatType,school_id,givenBy)
                        VALUES (?,?,?,?,?,?,?,?,?)";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("sisssssis",$student_index, $data["houseID"],$exeat_town,$exeat_date,$return_date,$exeat_reason,$exeat_type, $school_id, $user_details["fullname"]);
                    $stmt->execute();

                    #code to send letter to parents will go here
                    include_once($rootPath."/sms/sms.php");

                    $message = "success";
                }else{
                    //check if user is registered
                    $column = "enroled";
                    $table = "cssps";
                    $where = "indexNumber='$student_index'";

                    $data = fetchData($column, $table, $where);

                    if($data == "empty"){
                        $message = "not-student";
                    }elseif($data["enroled"] == false || $data["enroled"] == "false"){
                        $message = "not-registered";
                    }
                }                
            }

            echo $message;
        }elseif($submit == "register"){
            //get search data
            $search_value = $connect->real_escape_string($_REQUEST["search_value"]);

            if(empty($search_value)){
                echo "no-search-value";
            }else{
                $sql = "SELECT c.*, e.enrolCode 
                    FROM cssps c JOIN enrol_table e
                    ON c.indexNumber = e.indexNumber
                    WHERE c.enroled=TRUE AND c.schoolID = $user_school_id 
                    AND (c.indexNumber LIKE '%$search_value%' OR c.Lastname LIKE 
                    '%$search_value%' OR c.Othernames LIKE '%$search_value%')";
                $query = $connect->query($sql);

                $table_data = "";
                $total = 0;

                if($query->num_rows > 0){
                    $total = $query->num_rows;

                    while($row = $query->fetch_assoc()){
                        $table_data .= "
                    <tr>
                        <td>".$row["indexNumber"]."</td>
                        <td>".$row["enrolCode"]."</td>
                        <td>".$row["Lastname"]." ".$row["Othernames"]."</td>
                        <td>".$row["boardingStatus"]."</td>
                        <td>".$row["programme"]."</td>
                        <td>".$row["Gender"]."</td>
                        <td>".$row["trackID"]."</td>
                        <td class=\"flex\">
                            <span class=\"item-event edit\">Edit</span>
                            <span class=\"item-event delete\">Delete</span>
                        </td>
                    </tr>
                        ";
                    }
                }else{
                    $table_data = "no-result";
                }
            }
            $data = array(
                "total" => $total,
                "data" => $table_data
            );

            echo json_encode($data);
        }elseif($submit == "unregister"){
            //get search data
            $search_value = $_REQUEST["search_value"];

            if(empty($search_value)){
                echo "no-search-value";
            }else{
                $sql = "SELECT * FROM cssps WHERE enroled=FALSE 
                AND schoolID=$user_school_id AND (indexNumber LIKE '%$search_value%' 
                OR Lastname LIKE '%$search_value%' OR Othernames LIKE '%$search_value%')";

                $query = $connect->query($sql);

                $table_data = "";
                $total = 0;

                if($query->num_rows > 0){
                    $total = $query->num_rows;

                    while($row = $query->fetch_assoc()){
                        $table_data .= "
                    <tr>
                        <td>".$row["indexNumber"]."</td>
                        <td>".$row["Lastname"]." ".$row["Othernames"]."</td>
                        <td>".$row["boardingStatus"]."</td>
                        <td>".$row["programme"]."</td>
                        <td>".$row["Gender"]."</td>
                        <td>".$row["trackID"]."</td>
                        <td class=\"flex\">
                            <span class=\"item-event edit\">Edit</span>
                            <span class=\"item-event delete\">Delete</span>
                        </td>
                    </tr>";
                    }
                }else{
                    $table_data = "no-result";
                }
            }
            $data = array(
                "total" => $total,
                "data" => $table_data
            );

            echo json_encode($data);
        }elseif($submit == "table_yes_no_submit"){
            $indexNumber = $_REQUEST["indexNumber"] ?? null;
            $school_id = $_REQUEST["school_id"] ?? null;

            if(is_null($indexNumber) || empty($indexNumber)){
                $message = "Index Number could not be received";
            }elseif(is_null($school_id) || empty($school_id)){
                $message = "Student's school selection error. Please reload the page and try again";
            }else{//delete record
                if($_REQUEST["db"] === ""){
                    try{
                        if($indexNumber == "all"){
                            $sql = "DELETE FROM cssps WHERE schoolID=$school_id";
                        }else{
                            $sql = "DELETE FROM cssps WHERE indexNumber='$indexNumber'";
                        }
                        if($connect->query($sql)){
                            if($indexNumber == "all"){
                                $sql = "DELETE FROM enrol_table WHERE shsID=$school_id";
                            }else{
                                $sql = "DELETE FROM enrol_table WHERE indexNumber='$indexNumber'";
                            }
                            if($connect->query($sql)){
                                if($indexNumber == "all"){
                                    $sql = "DELETE FROM house_allocation WHERE schoolID=$school_id";
                                }else{
                                    $sql = "DELETE FROM house_allocation WHERE indexNumber='$indexNumber'";
                                }
                                if($connect->query($sql)){
                                    $message = "success";
                                }else{
                                    $message = "Student detail could not be removed from house allocated";
                                }
                            }else{
                                $message = "Could not remove student from your enrolment list";
                            }
                        }else{
                            $message = "Could not remove student from cssps";
                        }
                    }catch(\Throwable $th){
                        if($developmentServer){
                            $message = $th->getTraceAsString();
                        }else{
                            $message = $th->getMessage();
                        }
                    }
                }elseif($_REQUEST["db"] == "shsdesk2"){
                    try{
                        if($indexNumber == "all"){
                            //delete third years
                            $sql = "UPDATE students_table SET studentYear=4 WHERE school_id=$school_id AND studentYear=3";
                        }elseif($indexNumber == "wipe"){
                            $sql = "DELETE FROM students_table WHERE school_id=$school_id";
                        }else{
                            $sql = "DELETE FROM students_table WHERE indexNumber = '$indexNumber'";
                        }
        
                        if($connect2->query($sql)){
                            if($indexNumber == "all"){
                                //promote students
                                $sql = "UPDATE students_table SET studentYear=3 WHERE school_id=$school_id AND studentYear=2";
                                if($connect2->query($sql)){
                                    $sql = "UPDATE students_table SET studentYear=2 WHERE school_id=$school_id AND studentYear=1";
                                    if($connect2->query($sql)){
                                        //report that this school has updated its records
                                        $cleanDate = date("Y-m-d H:i:s");
                                        $sql = "INSERT INTO record_cleaning (school_id, cleanDate) VALUES ('$school_id','$cleanDate')";
                                        $connect2->query($sql);
                                        
                                        $message = "success";
                                    }else{
                                        $message = "Promotion from Year 1 to Year 2 failed";
                                    }
                                }else{
                                    $message = "Promotion from Year 2 to Year 3 failed";
                                }
                            }else{
                                //clean every record about the student
                                $connect2->query("DELETE FROM attendance WHERE indexNumber='$indexNumber'");
                                $connect2->query("DELETE FROM exeat WHERE indexNumber='$indexNumber'");
                                $connect2->query("DELETE FROM results WHERE indexNumber='$indexNumber'");
                                $connect2->query("DELETE FROM saved_results WHERE indexNumber='$indexNumber'");
                                $message = "success";
                            }
                        }else{
                            if($indexNumber == "all"){
                                $message = "Could not remove Year 3 students from records. Cleaning Failed";
                            }elseif($indexNumber == "wipe"){
                                $message = "Could not wipe out all students from the system";
                            }else{
                                $message = "Could not remove student with index number '$indexNumber' from records";
                            }                    
                        }
                    }catch(\Throwable $th){
                        if($developmentServer){
                            $message = $th->getTraceAsString();
                        }else{
                            $message = $th->getMessage();
                        }
                    }
                }
            }

            echo $message;
        }elseif($submit == "admissiondetails" ||  $submit == "admissiondetails_ajax"){
            $school_name = formatName($_REQUEST["school_name"]);
            $school_email = $_POST["school_email"];
            $postal_address = formatName($_POST["postal_address"]);
            $head_name = $_POST["head_name"];
            $head_title = $_POST["head_title"];
            $reopening = $_POST["reopening"];
            $admission_head = $_POST["admission_head"];
            $admission = htmlentities($_POST["admission"]);
            $autoHousePlace = $_POST["autoHousePlace"];
            $description = htmlentities($_POST["description"]);
            $school_id = $_REQUEST["school_id"];

            $message = "";

            if(empty($school_name)){
                $message = "No school name was provided";
            }elseif(empty($postal_address)){
                $message = "No Postal Address provided for school";
            }elseif(empty($head_name)){
                $message = "School Head Name field is empty";
            }elseif(empty($head_title)){
                $message = "Please provide the title of the school head";
            }elseif(empty($reopening)){
                $message = "No Reopening date provided";
            }elseif(empty($school_id) || !intval($school_id)){
                $message = "No School provided or school selection was unsuccessful. Please try again later";
            }

            //avatar check
            if(isset($_FILES['avatar']) && !empty($_FILES["avatar"]["tmp_name"])){
                $image_input_name = "avatar";
                $local_storage_directory = "$rootPath/admin/admin/assets/images/schools/";
                $default_image_path = "$rootPath/admin/admin/assets/images/schools/default_user.png";
    
                $image_directory = getImageDirectory($image_input_name, $local_storage_directory,$default_image_path);

                //remove the root path
                $image_directory = explode("$rootPath/",$image_directory);
                $image_directory = $image_directory[1];
            }else{
                $image_directory = $_POST["old_avatar"];
            }

            //prospectus check
            if(isset($_FILES["prospectus"]) && !empty($_FILES["prospectus"]["tmp_name"])){
                //get file extension
                $ext = strtolower(fileExtension("prospectus"));
    
                if($ext =="pdf"){
                    $file_input_name = "prospectus";
                    $local_storage_directory = "$rootPath/admin/admin/assets/files/prospectus/";
    
                    $prostectusDirectory = getFileDirectory($file_input_name, $local_storage_directory);

                    //remove rootPath
                    $prostectusDirectory = explode("$rootPath/", $prostectusDirectory);
                    $prostectusDirectory = $prostectusDirectory[1];
                }else{
                    echo "<p>File provided for prospectus is not a PDF</p>";
                    echo "<p>Please provide a valid document</p>";
                }
            }else{
                $prostectusDirectory = $_POST["old_prospectus"];
            }

            //admission template check
            if(isset($_FILES["admission_template"]) && !empty($_FILES["admission_template"]["tmp_name"])){
                //get file extension
                $ext = strtolower(fileExtension("admission_template"));
    
                if($ext =="pdf"){
                    $file_input_name = "admission_template";
                    $local_storage_directory = "$rootPath/admin/admin/assets/files/admission templates/";

                    if(!is_dir($local_storage_directory)){
                        mkdir($local_storage_directory, recursive: true);
                    }
    
                    $template_dir = getFileDirectory($file_input_name, $local_storage_directory);

                    //remove rootPath
                    $template_dir = explode("$rootPath/", $template_dir);
                    $template_dir = $template_dir[1];
                }else{
                    echo "<p>File provided for admission template is not a PDF</p>";
                    echo "<p>Please provide a valid document</p>";
                }
            }else{
                $template_dir = empty($_POST["old_admission_template"]) ? null : $_POST["old_admission_template"];
            }

            if($autoHousePlace == "true" || $autoHousePlace == "on"){
                $autoHousePlace = true;
            }

            if($message == ""){
                $sql = "UPDATE schools SET logoPath=?, prospectusPath=?, admissionPath=?, admissionHead=?, schoolName=?, postalAddress=?, headName=?, email=?,
                        description=?, autoHousePlace=?, admission_template=? WHERE id=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("sssssssssisi",$image_directory,$prostectusDirectory, $admission, $admission_head, $school_name, $postal_address, $head_name,
                    $school_email, $description, $autoHousePlace, $template_dir, $school_id);

                if($stmt->execute()){
                    // current academic year
                    $academic_year = getAcademicYear(now());
                    $admission_year = date("Y");

                    //update admission details table
                    $sql = "UPDATE admissiondetails SET titleOfHead=?, headName=?, admissionYear=?, academicYear=?,
                    reopeningDate=? WHERE schoolID=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("sssssi", $head_title, $head_name, $admission_year, $academic_year, $reopening, $school_id);
                    
                    $stmt->execute();
                    $message = "success";
                }else{
                    $message = $stmt->error;
                }
            }

            echo $message;
        }elseif($submit == "markReturn"){
            $id = $_REQUEST["id"];
            $date_now = date("Y-m-d");

            //parse into exeat
            if($id > 0){
                $sql = "UPDATE exeat SET returnDate='$date_now', returnStatus=TRUE WHERE id=$id";
                if($connect->query($sql)){
                    $data = array(
                        "status" => "success",
                        "date" => date("M d, Y")
                    );
                }else{
                    echo "Could not make an update. Please try again";
                }
            }else{
                echo "Cannot specify your exeat results";
            }
        }elseif($submit == "getExeat" || $submit == "getExeat_ajax"){
            $id = $_REQUEST["id"];

            $sql = "SELECT c.Lastname, c.Othernames, e.*, h.title
                FROM exeat e JOIN cssps c
                ON e.indexNumber = c.indexNumber
                JOIN houses h
                ON e.houseID = h.id
                WHERE e.id = $id";
            
            $res = $connect->query($sql);

            $data = array();
            if($res->num_rows > 0){
                $row = $res->fetch_assoc();

                $data = array(
                    "indexNumber" => $row["indexNumber"],
                    "fullname" => $row["Lastname"]." ".$row["Othernames"],
                    "house" => $row["title"],
                    "exeat_town" => $row["exeatTown"],
                    "exeat_date" => date("jS F, Y", strtotime($row["exeatDate"])),
                    "exp_date" => date("jS F, Y", strtotime($row["expectedReturn"])),
                    "returnStatus" => $row["returnStatus"],
                    "exeat_reason" => $row["exeatReason"],
                    "issueBy" => $row["givenBy"]
                );

                if($row["returnStatus"] == true || $row["returnStatus"] == "true"){
                    $data += array(
                        "ret_date" => date("jS F, Y", strtotime($row["returnDate"]))
                    );
                }

                $data += array(
                    "status" => "success"
                );
            }else{
                $data = array(
                    "status" => "No Details were returned"
                );
            }

            echo json_encode($data);
        }elseif($submit == "addFirstYears"){
            $addFirstYears = boolval($_POST["addFirstYears"]);

            if($addFirstYears){
                $sql = "SELECT c.indexNumber, c.Lastname, c.Othernames, c.Gender, c.programme, c.boardingStatus, h.houseID, e.primaryPhone
                    FROM cssps c JOIN house_allocation h
                    ON c.indexNumber = h.indexNumber
                    JOIN enrol_table e
                    ON c.indexNumber = e.indexNumber
                    WHERE c.schoolID= $user_school_id AND c.enroled=TRUE";
                $res = $connect->query($sql);

                if($res->num_rows >= 10){
                    while($row = $res->fetch_assoc()){
                        //insert found details into shsdesk2
                        if(is_array(fetchData1("indexNumber","students_table","indexNumber='".$row["indexNumber"]."'"))){
                            continue;
                        }else{
                            $sql = "INSERT INTO students_table (indexNumber, Lastname, Othernames, Gender, houseID, school_id, studentYear, guardianContact, programme, boardingStatus)
                                VALUES (?,?,?,?,?,?,?,?,?,?)";
                            $studentYear = 1;
                            $stmt = $connect2->prepare($sql);
                            $stmt->bind_param("ssssiiisss",$row["indexNumber"], $row["Lastname"], $row["Othernames"], $row["Gender"], $row["houseID"], $user_school_id, $studentYear,
                                $row["primaryPhone"], $row["programme"], $row["boardingStatus"]);
                            if(!$stmt->execute()){
                                echo "Student with index number '".$row["indexNumber"]."' could not be saved. Procedure was stopped";
                                break;
                            }
                        }
                    }
                    echo "success";
                }else{
                    echo "You have less than 10 first years enroled into the system. Please try again later";
                }
            }else{
                echo "First years are not going to be added";
            }
        }elseif($submit == "addNewCourse" || $submit == "addNewCourse_ajax"){
            $course_name = $_GET["course_name"];
            $course_alias = $_GET["course_alias"];
            $course_credit = $_GET["course_credit"];
            $school_id = intval($_GET["school_id"]);

            $message = ""; $status = false; $final = array(); $isFirst = false;

            if(empty($course_name)){
                $message = "Course Name was not provided";
            }elseif(empty($school_id) || $school_id <= 0){
                $message = "No school has been selected. Please check and try again";
            }else{
                $sql = "INSERT INTO courses (school_id, course_name, short_form, credit_hours) VALUES (?,?,?,?)";
                $stmt = $connect2->prepare($sql);
                $stmt->bind_param("issi", $school_id, $course_name, $course_alias, $course_credit);

                if($stmt->execute()){
                    $message = !empty($course_alias) ? $course_alias : $course_name;
                    $message .= " has been added";
                    $status = true;

                    //mark if this is the first course
                    if(intval(fetchData1("COUNT(*) as total","courses","school_id=$school_id")["total"]) == 1){
                        $isFirst = true;
                    }
                }else{
                    $message = !empty($course_alias) ? $course_alias : $course_name;
                    $message .= " could not be added";
                }
            }

            $final = [
                "message" => $message,
                "status" => $status,
                "isFirst" => $isFirst
            ];

            header("Content-Type: application/json");
            echo json_encode($final);
        }elseif($submit == "addNewTeacher" || $submit == "addNewTeacher_ajax"){
            @$teacher_lname = $_GET["teacher_lname"] ?? null;
            @$teacher_oname = $_GET["teacher_oname"] ?? null;
            @$teacher_gender = $_GET["teacher_gender"] ?? null;
            @$teacher_email = $_GET["teacher_email"] ?? null;
            @$teacher_phone = $_GET["teacher_phone"] ?? null;
            @$course_ids = $_GET["course_ids"] ?? null;
            @$school_id = $_GET["school_id"] ?? null;

            $message = ""; $status = false; $final = array();
            
            if(empty($teacher_lname)){
                $message = "Please provide the lastname of the teacher";
            }elseif(empty($teacher_oname) || is_null($teacher_oname)){
                $message = "Please provide the othername(s) of the teacher";
            }elseif(empty($teacher_gender) || is_null($teacher_gender)){
                $message = "Please select the gender of the teacher";
            }elseif(empty($teacher_phone) || is_null($teacher_phone)){
                $message = "Please provide a phone number";
            }elseif(strlen($teacher_phone) != 10){
                $message = "Please enter a valid 10 digit phone number";
            }elseif(array_search(substr($teacher_phone, 0, 3), $phoneNumbers) === false){
                $message = "Mobile network could not be detected. Please make sure your phone number is valid";
            }elseif(empty($teacher_email) || is_null($teacher_email)){
                $message = "Please provide an email";
            }elseif(empty($course_ids) || is_null($course_ids)){
                $message = "Please assign the teacher at least one course and a class";
            }elseif(empty($school_id) || is_null($school_id)){
                $message = "No school selected. Please check and try again";
            }else{
                try {
                    $sql = "INSERT INTO teachers (lname, oname, gender, email, phone_number, school_id, joinDate)
                        VALUES (?,?,?,?,?,?,NOW())";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("sssssi", $teacher_lname, $teacher_oname, $teacher_gender, $teacher_email, $teacher_phone, $school_id);
                    if($stmt->execute()){
                        //insert into login
                        $teacher_id = $stmt->insert_id;

                        include_once("$rootPath/sms/sms.php");

                        if(!is_null($teacher_id) && !empty($teacher_id)){
                            $connect2->query("INSERT INTO teacher_login (user_id) VALUES ($teacher_id)");
                            
                            //teacher courses and classes are in the format [program_id|course_id] [program_id|course_id]
                            $parts = explode(' ', $course_ids);
                            if(is_array($parts)){
                                if(end($parts) === ""){
                                    array_pop($parts);
                                }
                                foreach($parts as $part){
                                    $part = trim($part,"[]");
                                    if(!empty($part)){
                                        if(strpos($part,'|') !== false){
                                            $part = explode("|",$part);
                                            if(is_array($part) && count($part) == 3){
                                                $pid = $part[0];
                                                $cid = $part[1];
                                                $yid = $part[2];

                                                // sql syntax would go here
                                                $detailsExist = fetchData1("COUNT(teacher_id) AS total","teacher_classes", "school_id=$user_school_id AND program_id=$pid AND course_id=$cid AND class_year=$yid");
                                                if(intval($detailsExist["total"]) < 1){
                                                    $sql = "INSERT INTO teacher_classes (school_id, teacher_id, program_id, course_id, class_year) VALUES (?,?,?,?,?)";
                                                    $stmt = $connect2->prepare($sql);
                                                    $stmt->bind_param("iiiii", $user_school_id, $teacher_id, $pid, $cid, $yid);

                                                    $stmt->execute();
                                                }else{
                                                    $detailsExist = fetchData1("t.lname","teachers t JOIN teacher_classes tc ON t.teacher_id=tc.teacher_id","tc.course_id=$cid AND tc.program_id=$pid");
                                                    if(is_array($detailsExist)){
                                                        $message = "Teacher added, but subject addition was halted halfway as ".$detailsExist["lname"]." already handles ".formatItemId($cid,"SID")." for Year $yid";
                                                    }else{
                                                        $message = "Teacher responsible for ".formatItemId($cid,"SID")." has been deleted, but details of him exist. Contact superadmin for help";
                                                    }
                                                    break;
                                                }

                                            }else{
                                                $message = "Class and subject is not properly separated. Process discontinued";
                                                break;
                                            }
                                        }else{
                                            $message = "An invalid split format was rejected";
                                            break;
                                        }
                                    }else{
                                        $message = "An empty detail was rejected";
                                        break;
                                    }
                                }
                            }else{
                                $message = "Invalid class and subject format projected. Process terminated";
                            }
                            
                            if(empty($message)){
                                $message = "Teacher has been added";
                                $status = true;
                            }else{
                                $status = false;

                                //display the message 
                                if(isset($_REQUEST["system_message"])){
                                    if(strtolower($message) != "teacher has been added"){
                                        $sms_message = $_REQUEST["system_message"];
                                        unset($_REQUEST["system_message"]);
                                    }                                    
                                }
                            }                            
                        }else{
                            $message = "Teacher added, but teacher cannot login. Please contact the administrator for help";
                        }
                    }else{
                        $message = "Error occured while creating an account for the teacher";
                    }
                } catch (\Throwable $th) {
                    $status = false;
                    $message = $th->getMessage();
                }
            }

            $final = [
                "message" => $message,
                "status" => $status,
                "sms-message" => $sms_message ?? "no-message"
            ];

            header("Content-Type: application/json");
            echo json_encode($final);
        }elseif($submit == "addProgram" || $submit == "addProgram_ajax"){
            $message = ""; $status = false; $final = array();

            $program_name = $_GET["program_name"];
            $school_id = $_GET["school_id"];
            $course_ids = $_GET["course_ids"];
            $short_form = $_GET["short_form"];
            $associate_program = $_GET["associate_program"];
            
            if(empty($program_name)){
                $message = "Please provide the name of the program";
            }elseif(empty($school_id) || $school_id < 1){
                $message = "Your desired school has not been specified";
            }elseif(empty($course_ids)){
                $message = "Please select the courses/subjects held in this program";
            }else{
                try {
                    $sql = "INSERT INTO program (school_id, program_name, short_form, course_ids, associate_program) VALUES (?,?,?,?,?)";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("issss", $school_id, $program_name, $short_form, $course_ids, $associate_program);

                    if($stmt->execute()){
                        $message = "The program has been saved.";
                        $status = true;
                    }else{
                        $message = "The program could not be saved. Please try again.";
                    }
                } catch (\Throwable $th) {
                    $message = $th->getMessage();
                    $status = false;
                }
            }

            $final = [
                "message" => $message,
                "status" => $status
            ];

            header("Content-Type: application/json");
            echo json_encode($final);
        }elseif($submit == "delete_item" || $submit == "delete_item_ajax"){
            $item_id = $_GET["item_id"];
            $table_name = $_GET["table_name"];
            $column_name = $_GET["column_name"];
            $db = $_GET["db"] ?? "shsdesk";

            if($db == "shsdesk" || empty($db)){
                $db = $connect;
            }else{
                $db = $connect2;
            }

            if(in_array(strtolower($table_name), ["results"])){
                if(str_contains($item_id, "-")){
                    $items = explode("-", $item_id);
                    $item_id = $items[0];
                }
            }

            if(in_array(strtolower($table_name), ["teachers"])){
                $sql = "UPDATE $table_name SET status=-1 WHERE $column_name=?";
            }else{
                $sql = "DELETE FROM $table_name WHERE $column_name=?";
            }

            try{
                $stmt = $db->prepare($sql);
                $stmt->bind_param("s", $item_id);
                if($stmt->execute()){
                    //delete every detail about this teacher from the system
                    if($table_name == "teachers"){
                        $db->query("DELETE FROM teacher_classes WHERE teacher_id=$item_id");  //remove all classes handled by teacher
                        $db->query("DELETE FROM saved_results WHERE teacher_id=$item_id");    //delete all saved records by teacher
                        $db->query("DELETE FROM teacher_login WHERE user_id=$item_id");       //delete login details of teacher
                    }elseif($table_name == "recordapproval"){
                        $db->query("DELETE FROM results WHERE result_token='$item_id'");
                    }elseif($table_name == "results" && isset($items)){
                        assignPositions(end($items));
                    }
                    echo "success";
                }else{
                    echo "Failed to delete data";
                }
            }catch(Throwable $th){
                echo throwableMessage($th);
            }
        }elseif($submit == "getProgram" || $submit == "getProgram_ajax"){
            $message = null; $status = false; $final = array();

            $program_id = $_REQUEST["program_id"];

            $message = fetchData1("*","program","program_id=$program_id");

            if(is_array($message)){
                $status = true;
            }else{
                $status = false;
            }

            $final = [
                "status" => $status,
                "results" => $message
            ];

            header("Content-Type: application/json");
            echo json_encode($final);
        }elseif($submit == "updateProgram" || $submit == "updateProgram_ajax"){
            $message = ""; $status = false; $final = array();

            $program_name = $_GET["program_name"];
            $program_id = $_GET["program_id"];
            $course_ids = $_GET["course_ids"];
            $short_form = $_GET["short_form"];
            $associate_program = $_GET["associate_program"];
            
            if(empty($program_id) || intval($program_id) < 0){
                $message = "Class could not be selected. Please refresh the page and try again";
            }elseif(empty($program_name)){
                $message = "Please provide the name of the class";
            }elseif(empty($course_ids)){
                $message = "Please select the courses/subjects held in this class";
            }else{
                try {
                    $sql = "UPDATE program SET program_name=?, short_form=?, course_ids=?, associate_program = ? WHERE program_id=?";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("ssssi", $program_name, $short_form, $course_ids, $associate_program, $program_id);

                    if($stmt->execute()){
                        $message = "The program has been updated";
                        $status = true;
                    }else{
                        $message = "The program could not be saved. Please try again.";
                    }
                } catch (\Throwable $th) {
                    $status = false;
                    $message = $th->getMessage();
                }                
            }

            $final = [
                "message" => $message,
                "status" => $status
            ];

            header("Content-Type: application/json");
            echo json_encode($final);
        }elseif($submit == "getItem" || $submit == "getItem_ajax"){
            $message = null; $status = false; $final = array();
            
            $item_id = $_REQUEST["item_id"];
            $item_table = $_REQUEST["item_table"];
            $item_table_col = $_REQUEST["item_table_col"];
            $isTeacher = $_REQUEST["isTeacher"] ?? null;
            
            $item_id = intval($item_id) ? $item_id : "'$item_id'";
            $sql = "SELECT * FROM $item_table WHERE $item_table_col=$item_id";
            
            if($result = $connect2->query($sql)){
                $message = $result->fetch_all(MYSQLI_ASSOC);
                if($isTeacher === true || strtolower($isTeacher) === "true"){
                    $message[0]["course_id"] = stringifyClassIDs(fetchData1("program_id, course_id, class_year", "teacher_classes","teacher_id={$message[0]['teacher_id']}",0));
                    $message[0]["course_names"] = stringifyClassNames(fetchData1("p.program_name, p.short_form as short_p, c.course_name, c.short_form as short_c",
                    "teacher_classes t JOIN program p ON t.program_id = p.program_id JOIN courses c ON c.course_id=t.course_id",
                    "t.teacher_id={$message[0]['teacher_id']}", 0));
                }
                
                $status = true;
            }else{
                $message = "Data could not be retrieved";
            }

            $final = [
                "results" => $message,
                "status" => $status
            ];

            header("Content-Type: application/json");
            echo json_encode($final);
        }elseif($submit == "updateCourse" || $submit == "updateCourse_ajax"){
            @$course_name = $_GET["course_name"];
            @$course_alias = $_GET["course_alias"];
            @$course_credit = $_GET["course_credit"];
            @$course_id = intval($_GET["course_id"]);

            $message = ""; $status = false; $final = array();

            if(empty($course_name)){
                $message = "Course Name was not provided";
            }elseif(empty($course_id) || $course_id <= 0){
                $message = "No course/subject has been selected. Please check and try again";
            }else{
                try{
                    $sql = "UPDATE courses SET course_name=?, short_form=?, credit_hours=? WHERE course_id=?";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("ssii", $course_name, $course_alias, $course_id, $course_credit);

                    if($stmt->execute()){
                        $message = !empty($course_alias) ? $course_alias : $course_name;
                        $message .= " has been updated";
                        $status = true;
                    }else{
                        $message = !empty($course_alias) ? $course_alias : $course_name;
                        $message .= " could not be updated";
                    }
                }catch(\Throwable $th){
                    $status = false;
                    $message = $th->getMessage();
                }
            }

            $final = [
                "message" => $message,
                "status" => $status
            ];

            header("Content-Type: application/json");
            echo json_encode($final);
        }elseif($submit == "updateTeacher" || $submit == "updateTeacher_ajax"){
            @$teacher_lname = $_GET["teacher_lname"];
            @$teacher_oname = $_GET["teacher_oname"];
            @$teacher_gender = $_GET["teacher_gender"];
            @$teacher_email = $_GET["teacher_email"];
            @$teacher_phone = $_GET["teacher_phone"];
            @$course_ids = $_GET["course_ids"];
            @$class_ids = $_GET["class_ids"];
            @$teacher_id = $_GET["teacher_id"];

            $message = ""; $status = false; $final = array();

            if(empty($teacher_lname)){
                $message = "Please provide the lastname of the teacher";
            }elseif(empty($teacher_oname)){
                $message = "Please provide the othername(s) of the teacher";
            }elseif(empty($teacher_gender)){
                $message = "Please select the gender of the teacher";
            }elseif(empty($teacher_phone)){
                $message = "Please provide a phone number";
            }elseif(strlen($teacher_phone) != 10){
                $message = "Please enter a valid 10 digit phone number";
            }elseif(array_search(substr($teacher_phone, 0, 3), $phoneNumbers) === false){
                $message = "Mobile network could not be detected. Please make sure your phone number is valid";
            }elseif(empty($teacher_email)){
                $message = "Please provide an email";
            }elseif(empty($course_ids) || strtolower($course_ids) === "wrong array data"){
                $message = "Please assign the teacher at least one course";
            }elseif(empty($teacher_id)){
                $message = "No teacher selected. Please check and try again";
            }else{
                try {
                    $sql = "UPDATE teachers SET lname=?, oname=?, gender=?, email=?, phone_number=? WHERE teacher_id=?";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("sssssi", $teacher_lname, $teacher_oname, $teacher_gender, $teacher_email, 
                        $teacher_phone, $teacher_id);
                    if($stmt->execute()){
                        //delete details about teacher's class from db
                        if($connect2->query("DELETE FROM teacher_classes WHERE teacher_id=$teacher_id")){
                            //insert into login
                            if(!is_null($teacher_id) && !empty($teacher_id)){                                
                                $parts = explode(' ', $course_ids);
                                if(is_array($parts)){
                                    if(end($parts) === ""){
                                        array_pop($parts);
                                    }
                                    foreach($parts as $part){
                                        $part = trim($part,"[]");
                                        if(!empty($part)){
                                            if(strpos($part,'|') !== false){
                                                $part = explode("|",$part);
                                                if(is_array($part) && count($part) == 3){
                                                    $pid = $part[0];
                                                    $cid = $part[1];
                                                    $yid = $part[2];

                                                    // sql syntax would go here
                                                    $detailsExist = fetchData1("COUNT(teacher_id) AS total","teacher_classes", "school_id=$user_school_id AND program_id=$pid AND course_id=$cid AND class_year=$yid");
                                                    if(intval($detailsExist["total"]) < 1){
                                                        $sql = "INSERT INTO teacher_classes (school_id, teacher_id, program_id, course_id, class_year) VALUES (?,?,?,?,?)";
                                                        $stmt = $connect2->prepare($sql);
                                                        $stmt->bind_param("iiiii", $user_school_id, $teacher_id, $pid, $cid, $yid);

                                                        $stmt->execute();
                                                    }else{
                                                        $detailsExist = fetchData1("t.lname","teachers t JOIN teacher_classes tc ON t.teacher_id=tc.teacher_id","tc.course_id=$cid AND tc.program_id=$pid");
                                                        if(is_array($detailsExist)){
                                                            $message = "Teacher data updated, but subject addition was halted halfway as ".$detailsExist["lname"]." already handles ".formatItemId($cid,"SID")." for Year $yid";
                                                        }else{
                                                            $message = "Teacher responsible for ".formatItemId($cid,"SID")." has been deleted, but details of him exist. Contact superadmin for help";
                                                        }
                                                        break;
                                                    }
                                                }else{
                                                    $message = "Class and subject is not properly separated. Process discontinued";
                                                    break;
                                                }
                                            }else{
                                                $message = "An invalid split format was rejected";
                                                break;
                                            }
                                        }else{
                                            $message = "An empty detail was rejected";
                                            break;
                                        }
                                    }
                                }else{
                                    $message = "Invalid class and subject format projected. Process terminated";
                                }
                                
                                if(empty($message)){
                                    $message = "Teacher data has been updated";
                                    $status = true;
                                }else{
                                    $status = false;
                                }
                            }else{
                                $message = "Teacher's unique identity missing. Please refresh your page and try again";
                            }
                        }
                        
                        if(empty($message) || is_null($message)){
                            $message = "Details for $teacher_lname has been updated";
                            $status = true;
                        }else{
                            $status = false;
                        }                        
                    }else{
                        $message = "Teacher could not be added";
                    }
                } catch (\Throwable $th) {
                    $message = $th->getMessage();
                    $status = false;
                }
                
            }

            $final = [
                "message" => $message,
                "status" => $status
            ];

            header("Content-Type: application/json");
            echo json_encode($final);
        }elseif($submit === "result_status_change" || $submit === "result_status_change_ajax"){
            $record_status = $_POST["record_status"] ?? null;
            $record_token = $_POST["record_token"] ?? null;
            $message = ""; $status = false; $response = array();
            
            if(empty($record_status) || is_null($record_status)){
                $message = "Process was broken, cannot determine the status of this record";
            }elseif(empty($record_token) || is_null($record_token)){
                $message = "This record is considered broken or invalid: Token error. Please contact the admin for help";
            }else{
                try{
                    $sql = "UPDATE recordapproval SET result_status=? WHERE result_token=?";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("ss",$record_status, $record_token);

                    if($stmt->execute()){
                        //mark student records as completed or rejected
                        $accept_status = intval($record_status === "accepted");
                        $sql = "UPDATE results SET accept_status=$accept_status WHERE result_token='$record_token'";
                        if($connect2->query($sql)){
                            $status = true;
                            $message = "success";
                        }else{
                            $message = "Students could not be approved successfully";
                        }
                    }else{
                        $message = "The selected record could not be updated. Please try again at a later time";
                    }
                }catch(\Throwable $th){
                    $message = $th->getMessage();
                    $status = false;
                }                
            }

            $response = [
                "message" => $message,
                "status" => $status,
                "rec_stat" => $record_status ?? false
            ];

            header("Content-Type: application/json");
            echo json_encode($response);
        }elseif($submit === "change_admin_mode"){
            if(!isset($_GET["admin_mode"]) || is_null($_GET["admin_mode"]) || empty($_GET["admin_mode"])){
                $message = "No mode has been selected";
            }else{
                $_SESSION["admin_mode"] = $_GET["admin_mode"];

                if($_GET["admin_mode"] == "admission"){
                    $_SESSION["nav_point"] = "dashboard";
                }else{
                    $_SESSION["nav_point"] = "students";
                }
                $message = "true";
            }

            echo $message;
        }elseif($submit === "update_result_type"){
            $school_result = $_GET["school_result"] ?? null;
            @$school_id = $_GET["school_id"];

            if(empty($school_result) || is_null($school_result)){
                $message = "Please select the type of result to proceed";
            }elseif(empty($school_id) || is_null(intval($school_id)) || is_null($school_id)){
                $message = "Seems your school was not selected";
            }else{
                try{
                    $sql = "UPDATE admissiondetails SET school_result=? WHERE schoolID=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("si", $school_result, $school_id);
                    if($stmt->execute()){
                        $message = true;
                    }else{
                        $message = "Update failed due to a database server error. Please try again later";
                    }
                }catch(\Throwable $th){
                    $message = $th->getMessage();
                }
            }
            
            echo $message;
        }elseif($submit == "make_announcement" || $submit == "make_announcement_ajax"){
            //retrieve needed data
            $title = $_REQUEST["title"];
            $message = htmlentities($_REQUEST["message"], ENT_QUOTES);
            $audience = $_REQUEST["audience"];

            $const_audience = ["all","teachers","students"];

            //get details from session
            if(isset($_SESSION['user_login_id']) && $_SESSION['user_login_id'] != null){
                $school_id = $user_school_id;
            }else{
                echo "You are not logged in";
                exit(1);
            }

            if($title == "" || $title == null || empty($title)){
                echo "no-title";
                exit(1);
            }elseif($message == "" || $message == null || empty($message)){
                echo "no-message";
                exit(1);
            }elseif(array_search(strtolower($audience), $const_audience) === false){
                echo "no-audience-provided";
                exit(1);
            }

            $sql = "INSERT INTO announcement (school_id, heading, body, audience, date) VALUES(?,?,?,?,NOW())";
            $res = $connect2->prepare($sql);
            $res->bind_param("isss",$school_id, $title, $message, $audience);
            
            if($res->execute()){
                if($submit == "make_announcement"){
                    //redirect to previous page
                    $location = $_SERVER["HTTP_REFERER"];

                    header("location: $location");
                }else{
                    echo "success";
                }                
            }else{
                echo "error making announcement";
            }
        }elseif($submit == "send_sms" || $submit == "send_sms_ajax"){
            $group = $_GET["group"] ?? null;
            $individuals = $_GET["individuals"] ?? null;
            $sms_text = $_GET["message"];
            
            if(is_null($group) || empty($group)){
                $message = "Group of recepients not specified";
            }elseif(is_null($individuals) || empty($individuals)){
                $message = "Individuals for the message not specified";
            }elseif(is_null($sms_text) || empty($sms_text)){
                $message = "Please provide the message text to be sent";
            }elseif(is_null($user_school_id) || empty($user_school_id)){
                $message = "Your school could not be specified. Please refresh the page and try again";
            }else{
                if(strpos($individuals, ",") !== false){
                    //check if there is a space after the comma separators
                    $offset = 0;

                    while($pos = strpos($individuals, ",", $offset)){
                        if(strpos($individuals, ", ", $offset) === false){
                            $message = "Please make sure there is a comma and a space after the name of the comma to separate different individual ids";
                            break;
                        }

                        $offset = $pos + 1;
                    }
                }

                if(empty($message)){
                    // print_r($_REQUEST); return;
                    include_once("$rootPath/sms/sms.php");
                }
                
            }

            if(!empty($message)){
                echo $message;
            }else{
                echo $_REQUEST["system_message"];
                unset($_REQUEST["system_message"]);
            }            
        }elseif($submit == "add_update_sms" || $submit == "add_update_sms_ajax"){
            $sms_id = $_POST["sms_id"] ?? null;

            if(is_null($sms_id) || empty($sms_id)){
                $message = "Please provide your school's ssid to proceed";
            }elseif(strtolower($sms_id) === "not set"){
                $message = "USSD not provided. Please provide the school's ssid to continue";
            }elseif(strlen($sms_id) > 11){
                $message = "Invalid USSD length. Your SSID can take up to 11 characters";
            }else{
                try {
                    $checkUSSD = fetchData1("sms_id","school_ussds","school_id=$user_school_id");
                    if(!is_array($checkUSSD)){
                        $sql = "INSERT INTO school_ussds (school_id, sms_id) VALUES (?,?)";
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("is", $user_school_id, $sms_id);
                        $message = "USSD could not be added. Please try again later";
                    }else{
                        $sql = "UPDATE school_ussds SET sms_id=?, status='pending' WHERE school_id=?";
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("si", $sms_id, $user_school_id);
                        $message = "USSD could not be updated. Please try again later";
                    }

                    if($stmt->execute()){
                        $message = "change-complete";
                    }
                } catch (\Throwable $th) {
                    $message = $th->getMessage();
                }
            }
            echo $message;
        }elseif($submit == "search_name" || $submit == "search_name_ajax"){
            $keyword = $_GET["keyword"] ?? null;
            $type = $_GET["type"] ?? null;
            
            if(is_null($keyword) || empty($keyword)){
                $status = false; $message = "";
            }elseif(is_null($type) || empty($type)){
                $status = false; $message = "";
            }else{
                if($type == "student"){
                    $sql = "SELECT Lastname, Othernames, indexNumber FROM students_table WHERE school_id = $user_school_id AND (Lastname LIKE '%$keyword%' OR Othernames LIKE '%$keyword%')";
                }else{
                    $sql = "SELECT lname AS Lastname, oname AS Othernames, teacher_id AS indexNumber FROM teachers WHERE school_id=$user_school_id AND (lname LIKE '%$keyword%' OR oname LIKE '%$keyword%')";
                }
                $result = $connect2->query($sql);

                if($result->num_rows > 0){
                    $status = true;
                    $message = array();

                    while($row = $result->fetch_assoc()){
                        if($type == "teacher"){
                            $row["indexNumber"] = formatItemID($row["indexNumber"],"TID");
                        }
                        array_push($message, $row);
                    }
                }else{
                    $status = true; $message = "no-result";
                }
            }

            header("Content-Type: application/json");
            echo json_encode(["status"=> $status ?? false, "message"=> $message ?? ""]);
        }elseif($submit == "access_payment" || $submit == "access_payment_ajax"){
            $transaction_id = $_POST["transaction_id"] ?? null;
            $phone = $_POST["phone"] ?? null;
            $email = $_POST["email"] ?? null;
            $amount = $_POST["amount"] ?? null;
            $recipients = $_POST["recipients"] ?? null;

            if(is_null($transaction_id) || empty($transaction_id)){
                $message = "Your transaction reference has not been captured. Process stopped but payment was completed";
            }elseif(is_null($phone) || empty($phone)){
                $message = "No phone number presented";
            }elseif(strlen($phone) != 10){
                $message = "Invalid phone number length provided. Please provide a valid phone number";
            }elseif(array_search(substr($phone, 0, 3), $phoneNumbers) === false){
                $message = "Network operator defined is invalid. Please make sure your number is correct";
            }elseif(is_null($email) || empty($email)){
                $message = "Email has not been provided";
            }elseif(is_null($amount) || empty($amount)){
                $message = "No amount has been provided";
            }elseif(floatval($amount) < 0.00){
                $message = "Invalid amount parsed through. Please provide an amount greater than GHC 0";
            }elseif(is_null($recipients) || empty($recipients)){
                $message = "No recipient provided. Please provide the people you are making payment for";
            }elseif(intval($recipients) && intval($recipients) === 0){
                $message = "Invalid recipient index provided. Please make sure you have selected a button";
            }else{
                $datePurchased = date("Y-m-d H:i:s");
                $expiryDate = date("Y-m-d 23:59:59",strtotime($datePurchased." +4 months +1 day"));
                $deduction = number_format($amount * (1.95/100), 2);
                $amount -= $deduction;

                //insert into transaction table
                $sql = "INSERT INTO transaction (transactionID, school_id, price, deduction, phoneNumber, email) VALUES (?,?,?,?,?,?)";
                $insert_stmt = $connect2->prepare($sql);
                $insert_stmt->bind_param("siddss",$transaction_id, $user_school_id, $amount, $deduction, $phone, $email);
                
                if($insert_stmt->execute()){
                    $transaction_insert = true;
                }else{
                    $transaction_insert = false;
                }

                if((intval($recipients) !== false && intval($recipients) <= 3) || strtolower($recipients) == "all"){
                    if(strtolower($recipients) == "all"){
                        $sql = "SELECT indexNumber FROM students_table WHERE school_id=$user_school_id";
                    }else{
                        $sql = "SELECT indexNumber FROM students_table WHERE school_id=$user_school_id AND studentYear=$recipients";
                    }
                    $query = $connect2->query($sql);

                    if($query->num_rows > 0){
                        $total = 0;
                        $fail = 0;
                        while($row = $query->fetch_assoc()){
                            //insert individual data where the need be
                            $isFound = fetchData1("indexNumber, expiryDate","accesstable","indexNumber='{$row['indexNumber']}' ORDER BY datePurchased DESC");
                            $insertData = false;
                            if(is_array($isFound)){
                                if($datePurchased > date("Y-m-d H:i:s", strtotime($isFound["expiryDate"]))){
                                    $insertData = true;
                                }
                            }else{
                                $insertData = true;
                            }

                            if($insertData === true){
                                do{
                                    $accessToken = generateToken(rand(1,9), $user_school_id);
                                }while(is_array(fetchData1("accessToken","accesstable","accessToken='$accessToken'")));
                                $sql = "INSERT INTO accesstable (indexNumber, accessToken, school_id, datePurchased, expiryDate, transactionID, status) VALUES (?,?,?,?,?,?,1)";
                                $stmt = $connect2->prepare($sql);
                                $stmt->bind_param("ssisss",$row["indexNumber"], $accessToken, $user_school_id, $datePurchased,$expiryDate, $transaction_id);

                                if($stmt->execute()){
                                    $total++;
                                }else{
                                    $fail++;
                                }
                                $message = "success";
                            }else{
                                continue;
                            }
                        }
                    }
                }elseif(strpos($recipients, ",") !== false){
                    //check if there is a space after the comma separators
                    $offset = 0;

                    while($pos = strpos($recipients, ",", $offset)){
                        if(strpos($recipients, ", ", $offset) === false){
                            $message = "Please make sure there is a comma and a space after the name of the comma to separate different individual ids";
                            break;
                        }

                        $offset = $pos + 1;
                    }

                    $total = 0;
                    $fail = 0;

                    $recipients = explode(", ", $recipients);
                    foreach($recipients as $recipient){
                        //insert individual data where the need be
                        $isFound = fetchData1("indexNumber, expiryDate","accesstable","indexNumber='$recipient' ORDER BY datePurchased DESC");
                        $insertData = false;
                        if(is_array($isFound)){
                            if($datePurchased > date("Y-m-d H:i:s", strtotime($isFound["expiryDate"]))){
                                $insertData = true;
                            }
                        }else{
                            $insertData = true;
                        }

                        if($insertData === true){
                            do{
                                $accessToken = generateToken(rand(1,9), $user_school_id);
                            }while(is_array(fetchData1("accessToken","accesstable","accessToken='$accessToken'")));
                            $sql = "INSERT INTO accesstable (indexNumber, accessToken, school_id, datePurchased, expiryDate, transactionID, status) VALUES (?,?,?,?,?,?,1)";
                            $stmt = $connect2->prepare($sql);
                            $stmt->bind_param("ssisss",$recipient, $accessToken, $user_school_id, $datePurchased,$expiryDate, $transaction_id);

                            if($stmt->execute()){
                                $total++;
                            }else{
                                $fail++;
                            }
                            $message = "success";
                        }else{
                            continue;
                        }
                    }
                }
            }
            $response = [
                "message" => $message ?? "No entry was made",
                "success" => $total,
                "fail" => $fail,
                "trans_insert" => $transaction_insert
            ];

            header("Content-Type: application/json");

            echo json_encode($response);
        }elseif($submit == "access_check" || $submit == "access_check_ajax"){
            $fullname = $_REQUEST["fullname"] ?? null;
            $phone = $_REQUEST["phone"] ?? null;
            $email = $_REQUEST["email"] ?? null;
            $amount = $_REQUEST["amount"] ?? null;
            $recipients = $_REQUEST["recipients"] ?? null;

            if(is_null($fullname) || empty($fullname)){
                $message = "Your fullname has not been specified";
            }elseif(is_null($phone) || empty($phone)){
                $message = "No phone number presented";
            }elseif(strlen($phone) != 10){
                $message = "Invalid phone number length provided. Please provide a valid phone number";
            }elseif(array_search(substr($phone, 0, 3), $phoneNumbers) === false){
                $message = "Network operator defined is invalid. Please make sure your number is correct";
            }elseif(is_null($email) || empty($email)){
                $message = "Email has not been provided";
            }elseif(is_null($amount) || empty($amount)){
                $message = "No amount has been provided";
            }elseif(floatval($amount) < 0.00){
                $message = "Invalid amount parsed through. Please provide an amount greater than GHC 0";
            }elseif(is_null($recipients) || empty($recipients)){
                $message = "No recipient provided. Please provide the people you are making payment for";
            }elseif(intval($recipients) && intval($recipients) === 0){
                $message = "Invalid recipient index provided. Please make sure you have selected a button | $recipients";
            }elseif(strpos($recipients, ",") !== false){
                //check if there is a space after the comma separators
                $offset = 0;

                while($pos = strpos($recipients, ",", $offset)){
                    if(strpos($recipients, ", ", $offset) === false){
                        $message = "Please make sure there is a comma and a space after the name of the comma to separate different individuals";
                        break;
                    }else{
                        $message = "success";
                    }

                    $offset = $pos + 1;
                }
            }else{
                $message = "success";
            }

            echo $message;
        }elseif($submit=="change_access" || $submit=="change_access_ajax"){
            $default_price = $_POST["default_price"] ?? null;
            $school_price = $_POST["school_price"] ?? null;
            $total_price = $_POST["total_price"] ?? null;

            if(is_null($default_price) || empty($default_price)){
                $message = "Default price has not been provided";
            }elseif(is_null($school_price) || empty($school_price)){
                $message = "School Price has not been provided";
            }elseif(is_null($total_price) || empty($total_price)){
                $message = "The total cost was not provided";
            }elseif(strpos(strtolower($default_price), "ghc ") === false){
                $message = "Default price format is invalid";
            }elseif(strpos(strtolower($school_price), "ghc ") === false){
                $message = "School price format is invalid";
            }elseif(strpos(strtolower($total_price), "ghc ") === false){
                $message = "Total price format is invalid";
            }else{
                //split into actual values
                $default_price = floatval(explode("ghc ", strtolower($default_price))[1]);
                $school_price = floatval(explode("ghc ", strtolower($school_price))[1]);
                $total_price = floatval(explode("ghc ", strtolower($total_price))[1]);

                if($default_price != 6){
                    $message = "The default value has been tempered with. Please ensure you have provided the right value";
                }elseif($school_price > 4.0){
                    $message = "Your profit per individual cannot exceed GHC 4.00";
                }elseif($total_price > 10.0){
                    $message = "Your total price cannot exceed GHC 10.00";
                }elseif($total_price != ($default_price + $school_price)){
                    $message = "Your total price does not add up to the sum of default price and school profit";
                }else{
                    $priceExists = fetchData1("access_price","accesspay","school_id=$user_school_id");
                    if($priceExists == "empty"){
                        $sql = "INSERT INTO accesspay (school_id, access_price, active) VALUES (?,?,1)";
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("id",$user_school_id, $total_price);
                    }else{
                        $sql = "UPDATE accesspay SET access_price=?, last_update=current_update, current_update=NOW() WHERE school_id=?";
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("di",$total_price, $user_school_id);
                    }

                    if($stmt->execute()){
                        $message = "success";
                    }else{
                        $message = "An error occured while applying updates. Please try again";
                    }
                }
            }

            echo $message;
        }elseif($submit == "view_results" || $submit == "view_results_ajax"){
            $token_id = $_GET["token_id"] ?? null;
            
            if(is_null($token_id) || empty($token_id)){
                $message = "No token id provided";
            }else{
                $sql = "SELECT r.indexNumber, r.class_mark, r.exam_mark, r.mark, r.id, r.position, r.academic_year, (CONCAT(s.Lastname,' ',s.Othernames)) AS fullname
                    FROM results r JOIN students_table s ON r.indexNumber = s.indexNumber
                    WHERE r.result_token='$token_id' ORDER BY r.position ASC";
                $results = $connect2->query($sql);

                if($results->num_rows > 0){
                    $exam_type = fetchData("school_result","admissiondetails","schoolID=$user_school_id")["school_result"];
                    if(empty($exam_type) || is_null($exam_type)){
                        $message = "Your results type has not been revised or provided. Please revise it to get grade marks";
                    }else{
                        $counter = 0;
                        $current_academic_year = getAcademicYear(now(), false);
                    
                        while($row = $results->fetch_assoc()){
                            $message[$counter] = $row;
                            $message[$counter]["grade"] = giveGrade($row["mark"],$exam_type);
                            $message[$counter]["position"] = positionFormat($row["position"]);
                            $message[$counter]["can_remove"] = $row["academic_year"] == $current_academic_year;

                            $counter++;
                        }
                        $error = false;
                    }
                }else{
                    $message = "No results were found for this token";
                }
            }

            $response = ["error"=>$error ?? true, "message"=>$message];
            header("Content-Type: application/json");
            
            echo json_encode($response);
        }elseif($submit == "add_split" || $submit == "add_split_ajax"){
            $school_id = $_POST["school_id"] ?? null;
            $admin_number = $_POST["admin_number"] ?? null;
            $head_number = $_POST["head_number"] ?? null;
            $admin_account_type = $_POST["admin_account_type"] ?? null;
            $head_account_type = $_POST["head_account_type"] ?? null;
            $admin_bank = $_POST["admin_bank"] ?? null;
            $head_bank = $_POST["head_bank"] ?? null;

            $telecoms = ["airteltigo", "glo", "mtn","vodafone"];
            $message = "";

            if(is_null($school_id) || empty($school_id)){
                $message = "No school was selected";
            }elseif(!is_null($admin_account_type) && !empty($admin_account_type)){
                if(is_null($admin_bank) || empty($admin_bank)){
                    $message = "No admin account vendor was selected. Please check and try again";
                }elseif(is_null($admin_number) || empty($admin_number)){
                    $message = "Please provide the account number for the admin";
                }elseif(strtolower($admin_account_type) == "bank" && (strlen($admin_number) < 10 || strlen($admin_number) > 20)){
                    $message = "The bank account number for the admin is of invalid standard length";
                }elseif(strtolower($admin_account_type) == "mobile" && strlen($admin_number) != 10){
                    $message = "Please provide a valid 10 digit phone number for the admin";
                }elseif(strtolower($admin_account_type) == "mobile" && is_numeric($admin_number) === false){
                    $message = "Phone number for the admin must be only numbers";
                }elseif(strtolower($admin_account_type) == "mobile" && array_search(strtolower($admin_bank),$telecoms) === false){
                    $message = "You have provided an invalid account vendor for the admin";
                }elseif(strtolower($admin_account_type) == "mobile" && array_search(substr($admin_number, 0, 3), $phoneNumbers1[strtolower($admin_bank)]) === false){
                    $message = "Admin's phone number does not match the specified service provider";
                }
            }elseif(!is_null($head_account_type) && !empty($head_account_type)){
                if(is_null($head_bank) || empty($head_bank)){
                    $message = "No head account vendor was selected. Please check and try again";
                }elseif(is_null($head_number) || empty($head_number)){
                    $message = "Please provide the account number for the head";
                }elseif(strtolower($head_account_type) == "bank" && (strlen($head_number) < 10 || strlen($head_number) > 20)){
                    $message = "The bank account number for the head is of invalid standard length";
                }elseif(strtolower($head_account_type) == "mobile" && strlen($head_number) != 10){
                    $message = "Please provide a valid 10 digit phone number for the head";
                }elseif(strtolower($head_account_type) == "mobile" && is_numeric($head_number) === false){
                    $message = "Phone number for the head must be only numbers";
                }elseif(strtolower($head_account_type) == "mobile" && array_search(strtolower($head_bank),$telecoms) === false){
                    $message = "You have provided an invalid account vendor for the head";
                }elseif(strtolower($head_account_type) == "mobile" && array_search(substr($head_number, 0, 3), $phoneNumbers1[strtolower($head_bank)]) === false){
                    $message = "Head's phone number does not match the specified service provider";
                }
            }elseif((is_null($admin_number) || empty($admin_number)) && (is_null($head_number) || empty($head_number))){
                if(is_null($admin_number)){
                    $message = "Please provide the detail of the school head to continue";
                }else{
                    $message = "Provide at least the detail of the admin or the school head";
                }
            }
            
            if(empty($message)){
                try {
                    $sql = "INSERT INTO transaction_splits (schoolID, admin_bank, admin_number, head_bank, head_number) VALUES (?,?,?,?,?)";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("issss",$school_id, $admin_bank, $admin_number, $head_bank, $head_number);

                    if($stmt->execute()){
                        $message = "success";
                    }else{
                        $message = "An error occured while details were been sent into the database";
                    }
                } catch (\Throwable $th) {
                    $message = $th->getMessage();
                }            
            }
            echo $message;
        }elseif($submit == "change_access_setting" || $submit == "change_access_setting_ajax"){
            $current = $_POST["current"] ?? null;
            $change = $_POST["change"] ?? null;

            if(is_null($current) || is_numeric($current) === false){
                $message = "Current value not idenfied";
            }elseif(is_null($change) || is_numeric($change) === false){
                $message = "Setting chosen is neither enable or disable";
            }elseif($current === $change){
                $message = "Load: No change was detected";
            }else{
                $is_present = fetchData1("COUNT(school_id) as total","accesstable","school_id=$user_school_id")["total"];

                if(!$is_present){
                    $sql = "INSERT INTO accesspay(school_id, active) VALUES(?,?)";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("ii",$user_school_id, $change);
                }else{
                    $sql = "UPDATE accesspay SET active=? WHERE school_id=?";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("ii",$change,$user_school_id);
                }

                if($stmt->execute()){
                    $message = "success";
                }else{
                    $message = "Error occured while processing the results. Try again later";
                }
            }

            echo $message;
        }elseif($submit == "record_date" || $submit == "record_date_ajax"){
            $start_date = $_POST["start_date"] ?? null;
            $end_date = $_POST["end_date"] ?? null;

            if(empty($start_date) || is_null($start_date)){
                $message = "The starting date was not provided";
            }elseif(empty($end_date) || is_null($end_date)){
                $message = "The final submission date was not provided";
            }elseif(empty($user_school_id)){
                $message = "Your login period has expired.";
            }elseif(!strtotime($start_date)){
                $message = "Invalid start date provided";
            }elseif(!strtotime($end_date)){
                $message = "Invalid final submission provided";
            }elseif(strtotime($start_date) >= strtotime($end_date)){
                $message = "Your starting date must be lower than your end date";
            }else{
                //format the dates
                $start_date = date("Y-m-d H:i:s",strtotime($start_date));
                $end_date = date("Y-m-d H:i:s",strtotime($end_date));

                //check if school already has a detail
                $hasEntry = fetchData1("school_id","record_dates","school_id=$user_school_id");
                if(is_array($hasEntry)){
                    $sql = "UPDATE record_dates SET start_date=?, end_date=? WHERE school_id=?";
                }else{
                    $sql = "INSERT INTO record_dates (start_date, end_date, school_id) VALUES (?,?,?)";
                }

                $stmt = $connect2->prepare($sql);
                $stmt->bind_param("ssi", $start_date, $end_date, $user_school_id);

                if($stmt->execute()){
                    $message = "success";
                }else{
                    $message = "Failed to set a new records timestream";
                }
            }

            echo $message;
        }elseif($submit == "resolve_students"){
            $displaced_studs = $connect->query(
                "SELECT studentGender, indexNumber, boardingStatus 
                    FROM house_allocation 
                    WHERE schoolID=$user_school_id AND current_data = 1 AND NOT EXISTS (
                        SELECT 1 
                        FROM houses 
                        WHERE houses.id = house_allocation.houseID 
                        AND houses.schoolID = $user_school_id
                    )"
            )->fetch_all(MYSQLI_ASSOC);

            $houses = decimalIndexArray(fetchData(...[
                "columns" => ["id","maleHeadPerRoom", "maleTotalRooms", "femaleHeadPerRoom", "femaleTotalRooms"],
                "table" => "houses",
                "where" => ["schoolID=$user_school_id"],
                "limit" => 0,
            ]));

            //add seconds to current time
            $sec = 0;
            foreach($displaced_studs as $student){
                $student_house = setHouse($student["studentGender"], $user_school_id, $student["indexNumber"], $houses, $student["boardingStatus"], false);
                $now = date("Y-m-d H:i:s", strtotime("+".$sec++." seconds"));

                // update student data
                try {
                    // $connect->query("UPDATE house_allocation SET houseID=$student_house, updated_at='$now' WHERE indexNumber='{$student['indexNumber']}'");
                    $sql = "UPDATE house_allocation SET houseID=?, updated_at=? WHERE indexNumber=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("iss", $student_house, $now, $student["indexNumber"]);
                    $response = $stmt->execute();

                    if(!$response){
                        exit("{$student['indexNumber']} could not be processed. Error: ".$connect->error);
                    }
                } catch (\Throwable $th) {
                    exit(throwableMessage($th));
                }
            }

            echo "success";
        }elseif($submit == "resolve_issues"){
            $school_id = $_POST["school_id"] ?? $user_school_id;

            if(is_null($school_id) || empty($school_id)){
                $message = "School Identifier is invalid or empty";
            }else{
                //grab all students with the issues
                $students = decimalIndexArray(fetchData(
                    [
                        "c.indexNumber", "e.jhsName", "e.birthdate", "h.houseID",
                        "c.Gender", "c.boardingStatus", "h.indexNumber AS h_indexNumber",
                        "c.Lastname", "c.Othernames"
                    ], 
                    [
                        ["join" => "enrol_table cssps", "on" => "indexNumber indexNumber", "alias" => "e c"],
                        ["join" => "enrol_table house_allocation", "on" => "indexNumber indexNumber", "alias" => "e h"]
                    ],
                    ["c.schoolID=$user_school_id", "c.current_data=TRUE", "c.Enroled=FALSE"], 
                    0, "AND", "left"));
                
                //school houses
                $houses = decimalIndexArray(fetchData(...[
                    "columns" => ["id","maleHeadPerRoom", "maleTotalRooms", "femaleHeadPerRoom", "femaleTotalRooms"],
                    "table" => "houses",
                    "where" => ["schoolID=$user_school_id"],
                    "limit" => 0,
                ]));
                
                if($students){
                    foreach($students as $student){
                        list(
                            "indexNumber" => $indexNumber, "jhsName" => $jhsName, 
                            "birthdate" => $birthdate, "houseID" => $houseID, "Gender" => $gender,
                            "boardingStatus" => $boarding_status, "h_indexNumber" => $h_indexNumber,
                            "Lastname" => $lname, "Othernames" => $oname
                        ) = $student;

                        try {
                            // mark student as enroled
                            $enrol_sql = "UPDATE cssps SET jhsAttended=?, dob=?, enroled=TRUE WHERE indexNumber=?";
                            $enrol_stmt = $connect->prepare($enrol_sql);
                            $enrol_stmt->bind_param("sss",$jhsName, $birthdate, $indexNumber);

                            //get student's house if it hasnt been made
                            if(empty($houseID) || is_null($houseID)){
                                $house_id = setHouse($gender, $user_school_id, $indexNumber, $houses, $boarding_status);

                                if(empty($h_indexNumber)){
                                    $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus) 
                                        VALUES (?,?,?,?,?,?,?)";
                                    $stmt = $connect->prepare($sql);
                                    $stmt->bind_param("sississ", $indexNumber,$user_school_id, $lname, $oname, $house_id, $gender, $boarding_status);
                                }else{
                                    $sql = "UPDATE house_allocation SET houseID=?, updated_at=NOW() WHERE indexNumber=?";
                                    $stmt = $connect->prepare($sql);
                                    $stmt->bind_param("is",$house_id, $indexNumber);
                                }

                                $response = $stmt->execute();

                                if(!$response){
                                    exit("'$indexNumber' could not have a valid house detail. Error: ".$connect->error);
                                }
                            }
                            
                            // parse sql with the enroled status
                            $response = $enrol_stmt->execute();

                            // send an error message if things are false
                            if(!$response){
                                exit("'$indexNumber' could not be marked as enroled. Error: ".$connect->error);
                            }

                            $message = "success";
                        } catch (\Throwable $th) {
                            $message = throwableMessage($th);
                        }
                    }
                }else{
                    $message = "No issues were found";
                }
            }

            echo $message;
        }elseif($submit == "reset_admission"){
            $sql = "UPDATE cssps SET current_data = FALSE WHERE schoolID=$user_school_id AND current_data = TRUE;
                UPDATE enrol_table SET current_data = FALSE WHERE shsID=$user_school_id AND current_data = TRUE;
                UPDATE house_allocation SET current_data = FALSE WHERE schoolID=$user_school_id AND current_data = TRUE;
                UPDATE transaction SET current_data = FALSE WHERE schoolBought = $user_school_id AND current_data = TRUE;
            ";
            $response = $connect->multi_query($sql);

            if($response === true){
                $message = "Admission has been reset. $connect->affected_rows results were updated";
            }else{
                $message = "Error: $connect->error";
            }

            echo json_encode(["status" => $response, "message" => $message]);
        }elseif($submit == "clean_data"){
            $sql = "DELETE FROM cssps WHERE schoolID=$user_school_id AND enroled = FALSE AND current_data = FALSE";
            $response = $connect->query($sql);

            $message = $response ? "$connect->affected_rows records have been cleaned" : "Error: $connect->error";

            echo json_encode(["status" => $response, "message" => $message]);
        }elseif($submit == "process_code"){
            $index_number = $_POST["index_number"] ?? null;
            $transaction_id = $_POST["transaction_id"] ?? null;
            $purchaseDate = $_POST["purchase_date"] ?? now();
            $school_id = $_POST["school_id"] ?? null;

            if(empty($index_number) || is_null($index_number)){
                $message = "Index Number is required";
            }elseif(empty($transaction_id) || is_null($transaction_id)){
                $message = "Transaction ID is required";
            }elseif(empty($school_id) || is_null($school_id)){
                $message = "School not defined. Refresh page to retry";
            }else{
                $valid_index = is_array(fetchData1("*", "students_table", "indexNumber='$index_number'"));

                if($valid_index){
                    try {
                        $active_index = is_array(fetchData1("*", "accesstable", ["indexNumber='$index_number'", "status = TRUE"]));

                        if($active_index){
                            $message = "An active code for this account is already in use";
                        }else{
                            do{
                                $accessToken = generateToken(rand(1,9), $school_id);
                            }while(is_array(fetchData1("accessToken","accesstable","accessToken='$accessToken'")));
        
                            $expiryDate = date("Y-m-d 23:59:59",strtotime($purchaseDate." +4 months +1 day"));
        
                            $sql = "INSERT INTO accesstable(indexNumber, accessToken, school_id, datePurchased, expiryDate, transactionID, status) VALUES (?,?,?,?,?,?,1)";
                            $stmt = $connect2->prepare($sql);
                            $stmt->bind_param("ssisss", $index_number, $accessToken, $school_id, $purchaseDate, $expiryDate, $transaction_id);
        
                            if($stmt->execute()){
                                $status = true;
                                $message = "success";
                            }else{
                                $status = false;
                                $message = "Student token was not captured appropriately. Try again";
                            }
                        }
                    } catch (\Throwable $th) {
                        $message = $th->getMessage();
                    }
                }else{
                    $message = "Index Number '$index_number' is invalid";
                }
            }

            echo $message;
        }elseif($submit == "search_class_result" || $submit == "search_subject_result"){
            $program_id = $_GET["program_id"] ?? null;
            $academic_year = $_GET["academic_year"] ?? null;
            $year_level = $_GET["year_level"] ?? null;
            $semester = $_GET["semester"] ?? null;
            $course_id = $_GET["course_id"] ?? null;
            $search_by_subject = $submit == "search_subject_result";

            if(is_null($program_id) || empty($program_id)){
                $message = "Class was not specified";
            }elseif(is_null($academic_year) || empty($academic_year)){
                $message = "Academic year was not specified";
            }elseif(!is_numeric($program_id)){
                $message = "Invalid class specified";
            }elseif(empty($year_level) || is_null($year_level)){
                $message = "Form level is required";
            }elseif(!is_numeric($year_level)){
                $message = "Invalid form level specified";
            }elseif(empty($semester) || is_null($semester)){
                $message = "Semester was not specified";
            }elseif(!is_numeric($semester)){
                $message = "Invalid semester value provided";
            }elseif($search_by_subject && (is_null($course_id) || empty($course_id))){
                $message = "Subject has not been selected";
            }elseif($search_by_subject && !is_numeric($course_id)){
                $message = "Invalid subject value provided";
            }else{
                $sql_c = "SELECT r.indexNumber, c.course_name, c.short_form, SUM(r.mark) as total_mark
                    FROM results r JOIN courses c ON c.course_id = r.course_id
                    WHERE r.program_id = $program_id AND r.exam_year = $year_level AND r.semester = $semester AND r.academic_year = '$academic_year'
                    AND accept_status = TRUE
                    GROUP BY r.indexNumber, c.course_name, c.short_form
                ";
                $sql_s = "SELECT r.indexNumber, c.course_name, r.class_mark, r.exam_mark, r.mark as total, CONCAT(t.lname, ' ', t.oname) as teacher_name
                    FROM results r JOIN courses c ON c.course_id = r.course_id
                    JOIN teachers t ON t.teacher_id = r.teacher_id
                    WHERE r.program_id = $program_id AND r.exam_year = $year_level AND r.semester = $semester AND r.academic_year = '$academic_year'
                    AND r.course_id = $course_id AND accept_status = TRUE
                ";

                $results = $connect2->query($search_by_subject ? $sql_s : $sql_c)->fetch_all(MYSQLI_ASSOC);

                if($results){
                    $status = true;
                    $thead = [];

                    if(!$search_by_subject){
                        foreach($results as $result){
                            $course_name = str_replace(" ", "_", strtolower($result["short_form"] ?? $result["course_name"]));
                            $message[$result["indexNumber"]][$course_name] = $result["total_mark"];
                            $message[$result["indexNumber"]]["total"] = floatval($message[$result["indexNumber"]]["total"] ?? 0) + $result["total_mark"];

                            if(!in_array($course_name, $thead))
                                $thead[] = $course_name;
                        }
                    }else{
                        $thead = ["class_score", "exam_score"];
                        foreach($results as $result){
                            $message[$result["indexNumber"]]["class_score"] = $result["class_mark"];
                            $message[$result["indexNumber"]]["exam_score"] = $result["exam_mark"];
                            $message[$result["indexNumber"]]["total"] = $result["total"];
                        }
                    }

                    // arrange in descending order
                    uasort($message, function($a, $b) {
                        return $b['total'] <=> $a['total'];
                    });

                    $thead = array_unique($thead);
                    $thead[] = "total";

                    $message = ["thead" => $thead, "data" => $message];

                    if($search_by_subject){
                        $message["course_name"] = $results[0]["course_name"];
                        $message["teacher_name"] = $results[0]["teacher_name"];
                    }
                }else{
                    $message = "No results for this search";
                }
            }

            header("Content-type: application/json");
            echo json_encode([
                "status" => $status ?? false, "data" => $message
            ]);
        }else{
            echo "Procedure for submit value '$submit' was not found";
        }
    }else{
        echo "no-submission";
    }
?>