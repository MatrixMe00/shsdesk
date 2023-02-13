<?php
    @include_once("../../includes/session.php");

    if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != NULL){
        $submit = $_REQUEST["submit"];

        if($submit == "new_user_update" || $submit == "new_user_update_ajax"){
            $new_username = strip_tags(stripslashes($_REQUEST["new_username"]));
            $new_password = strip_tags(stripslashes($_REQUEST["new_password"]));
            $fullname = strip_tags(stripslashes($_REQUEST["fullname"]));
            $email = strip_tags(stripslashes($_REQUEST["email"]));

            //search for email
            $sql = "SELECT user_id email FROM admins_table WHERE email=? AND fullname=?";
            $res = $connect->prepare($sql);
            $res->bind_param("ss", $email,$fullname);
            
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
                        //update the content
                        $sql = "UPDATE admins_table SET username=?, password=?, new_login=0 WHERE email=? AND fullname=?";
                        $res = $connect->prepare($sql);

                        //hash password
                        $new_password = MD5($new_password);
                        
                        $res->bind_param("ssss",$new_username,$new_password,$email,$fullname);

                        if($res->execute()){
                            //grab the time now
                            $now = date('Y-m-d H:i:s');

                            //create login awareness
                            $sql = "INSERT INTO login_details (user_id, login_time) VALUES (".$row['user_id'].", '$now')";

                            if($connect->query($sql)){
                                //update user session id
                                $_SESSION['user_login_id'] = $row['user_id'];

                                //get this login id
                                $sql = "SELECT MAX(id) AS id FROM login_details WHERE user_id=".$row['user_id'];
                                $res = $connect->query($sql);

                                //set as session's login id
                                $_SESSION['login_id'] = $res->fetch_assoc()['id'];
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

            if(empty($student_index)){
                $message = "index-number-empty";
            }elseif(empty($lname)){
                $message = "lastname-empty";
            }elseif(empty($oname)){
                $message = "no-other-name";
            }elseif(empty($gender)){
                $message = "gender-not-set";
            }elseif(empty($boarding_status)){
                $message = "boarding-status-not-set";
            }elseif(empty($student_course)){
                $message = "no-student-program-set";
            }elseif(empty($aggregate)){
                $message = "no-aggregate-set";
            }elseif(intval($aggregate) < 6 || intval($aggregate) > 81){
                $message = "aggregate-wrong";
            }elseif(empty($track_id)){
                $message = "no-track-id";
            }else{
                //format date
                $dob = date("Y-m-d", strtotime($dob));

                //verify if index number is unavailable
                $valid = fetchData("*","cssps","indexNumber='$student_index'");

                if($valid == "empty"){
                    //insert data into CSSPS table
                    $sql = "INSERT INTO cssps (indexNumber,Lastname,Othernames,Gender,
                            boardingStatus,programme, aggregate, jhsAttended, dob, trackID, schoolID) 
                            VALUES (?,?,?,?,?,?,?,?,?,?,?)";
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
        }elseif($submit == "adminUpdateStudent" || $submit == "adminUpdateStudent_ajax"){
            $student_index = strip_tags(stripslashes($_REQUEST["student_index"]));
            $lname = strip_tags(stripslashes($_REQUEST["lname"]));
            $oname = strip_tags(stripslashes($_REQUEST["oname"]));
            $gender = strip_tags(stripslashes($_REQUEST["gender"]));
            $boarding_status = strip_tags(stripslashes($_REQUEST["boarding_status"]));
            $student_course = strip_tags(stripslashes($_REQUEST["student_course"]));
            $aggregate = strip_tags(stripslashes($_REQUEST["aggregate"]));
            $jhs = strip_tags(stripslashes($_REQUEST["jhs"]));
            $dob = strip_tags(stripslashes($_REQUEST["dob"]));
            $track_id = strip_tags(stripslashes($_REQUEST["track_id"]));
            if(isset($_REQUEST["house"]))
                $house = strip_tags(stripslashes($_REQUEST["house"]));

            //variable to hold messages
            $message = "";

            if(empty($student_index)){
                $message = "index-number-empty";
            }elseif(empty($lname)){
                $message = "lastname-empty";
            }elseif(empty($oname)){
                $message = "no-other-name";
            }elseif(empty($gender)){
                $message = "gender-not-set";
            }elseif(empty($boarding_status)){
                $message = "boarding-status-not-set";
            }elseif(empty($student_course)){
                $message = "no-student-program-set";
            }elseif(empty($aggregate)){
                $message = "no-aggregate-set";
            }elseif(intval($aggregate) < 6 || intval($aggregate) > 81){
                $message = "aggregate-wrong";
            }elseif(empty($track_id)){
                $message = "no-track-id";
            }elseif(isset($_REQUEST['house']) && empty($house)){
                $message = "no-house";
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
                    //make update to house allocation
                    $sql = "UPDATE house_allocation SET studentLname=?, studentOname=?, houseID=?, studentGender=?, boardingStatus=? 
                        WHERE indexNumber=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("ssisss", $lname, $oname, $house, $gender, $boarding_status, $student_index);
                    if($stmt->execute()){
                        $message = "success";
                    }else{
                        $message = "Could not update details";
                    }
                }else{
                    $message = "Could not update details";
                }  
            }

            echo $message;
        }elseif($submit == "fetchStudentDetails"){
            $index_number = $_REQUEST["index_number"];
            $registered = $_REQUEST["registered"];

            if($registered == "true"){
                $sql = "SELECT c.*, h.houseID 
                    FROM cssps c JOIN house_allocation h
                    ON c.indexNumber = h.indexNumber 
                    WHERE c.indexNumber='$index_number'";
            }else{
                $sql = "SELECT * FROM cssps WHERE indexNumber='$index_number' AND enroled=FALSE";
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
                $result = array("status" => "error");
            }else{
                $result += array("status" => "success");
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
        }elseif($submit == "table_yes_no_submit"){
            $indexNumber = $_REQUEST["indexNumber"];
            $school_id = $_REQUEST["school_id"];

            //delete record
            if($_REQUEST["db"] === ""){
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
                            echo "success";
                        }else{
                            echo "Student detail could not be removed from house allocated";
                        }
                    }else{
                        echo "Could not remove student from your enrolment list";
                    }
                }else{
                    echo "Could not remove student from cssps";
                }
            }elseif($_REQUEST["db"] == "shsdesk2"){
                if($indexNumber == "all"){
                    //delete third years
                    $sql = "DELETE FROM students_table WHERE school_id=$school_id AND studentYear=3";
                }else{
                    $sql = "DELETE FROM students_table WHERE indexNumber = '$indexNumber'";
                }

                if($connect2->query($sql)){
                    //promote students
                    $sql = "UPDATE students_table SET studentYear=3 WHERE school_id=$school_id AND studentYear=2";
                    if($connect2->query($sql)){
                        $sql = "UPDATE students_table SET studentYear=2 WHERE school_id=$school_id AND studentYear=1";
                        if($connect2->query($sql)){
                            //report that this school has updated its records
                            $cleanDate = date("Y-m-d H:i:s");
                            $sql = "INSERT INTO record_cleaning (school_id, cleanDate) VALUES ('$school_id','$cleanDate')";
                            $connect2->query($sql);
                            
                            echo "success";
                        }else{
                            echo "Promotion from Year 1 to Year 2 failed";
                        }
                    }else{
                        echo "Promotion from Year 2 to Year 3 failed";
                    }
                }else{
                    if($indexNumber == "all"){
                        echo "Could not remove Year 3 students from records. Cleaning Failed";
                    }else{
                        echo "Could not remove student with index number '$indexNumber' from records";
                    }                    
                }
            }            
        }elseif($submit == "admissiondetails" ||  $submit == "admissiondetails_ajax"){
            $school_name = formatName($_REQUEST["school_name"]);
            $school_email = $_POST["school_email"];
            $postal_address = formatName($_POST["postal_address"]);
            $head_name = $_POST["head_name"];
            $head_title = $_POST["head_title"];
            $sms_id = $_POST["sms_id"];
            $reopening = $_POST["reopening"];
            $announcement = htmlentities($_POST["announcement"]);
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
            }elseif(empty($sms_id)){
                $message = "SMS ID field cannot be empty";
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

                $logo_mod = true;
            }else{
                $logo_mod = false;
            }

            //prospectus check
            if(isset($_FILES["prospectus"]) && $_FILES["prospectus"]["tmp_name"] !== null){
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

                $pros_mod = true;
            }else{
                $pros_mod = false;
            }

            if(@$autoHousePlace == "true" || @$autoHousePlace == "on"){
                @$autoHousePlace = true;
            }

            if($message == ""){
                if($logo_mod && $pros_mod){
                    $sql = "UPDATE schools SET logoPath=?, prospectusPath=?, admissionPath=?, admissionHead=?, schoolName=?, postalAddress=?, headName=?, email=?,
                            description=?, autoHousePlace=? WHERE id=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("sssssssssii",$image_directory,$prostectusDirectory, $admission, $admission_head, $school_name, $postal_address, $head_name,
                        $school_email, $description, $autoHousePlace, $school_id);
                }elseif($logo_mod){
                    $sql = "UPDATE schools SET logoPath=?, admissionPath=?, admissionHead=?, schoolName=?, postalAddress=?, headName=?, email=?,
                            description=?, autoHousePlace=? WHERE id=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("ssssssssii",$image_directory, $admission, $admission_head, $school_name, $postal_address, $head_name,
                        $school_email, $description, $autoHousePlace, $school_id);
                }elseif($pros_mod){
                    $sql = "UPDATE schools SET prospectusPath=?, admissionPath=?, admissionHead=?, schoolName=?, postalAddress=?, headName=?, email=?,
                            description=?, autoHousePlace=? WHERE id=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("ssssssssii",$prostectusDirectory, $admission, $admission_head, $school_name, $postal_address, $head_name,
                        $school_email, $description, $autoHousePlace, $school_id);
                }else{
                    $sql = "UPDATE schools SET admissionPath=?, admissionHead=?, schoolName=?, postalAddress=?, headName=?, email=?,
                            description=?, autoHousePlace=? WHERE id=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("sssssssii", $admission, $admission_head, $school_name, $postal_address, $head_name,
                        $school_email, $description, $autoHousePlace, $school_id);
                }

                if($stmt->execute()){
                    //providing a value according to a calculated algorithm
                    $this_year = date("Y");
                    $this_month = date("m");
                    $admission_year = $this_year;

                    /*if($this_month < 9){
                        $admission_year = $this_year - 1;
                    }*/

                    //get the academic year
                    $prev_year = null;
                    $next_year = null;
                    // $this_date = date("Y-m-1");
                    $this_date = date("Y-m-d");

                    // if($this_date < date("Y-09-01")){
                    /*if($this_date <= date("Y-m-01")){
                        $prev_year = date("Y") - 1;
                        $next_year = date("Y");
                    }else{
                        $prev_year = date("Y");
                        $next_year = date("Y") + 1;
                    }*/

                    $prev_year = date("Y");
                    $next_year = date("Y") + 1;

                    $academic_year = "$prev_year / $next_year";

                    //update admission details table
                    $sql = "UPDATE admissiondetails SET titleOfHead=?, headName=?, smsID=?, admissionYear=?, academicYear=?,
                    reopeningDate=?, announcement=? WHERE schoolID=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("sssssssi", $head_title, $head_name, $sms_id, $admission_year, $academic_year, $reopening, $announcement,
                        $school_id);
                    
                    $stmt->execute();
                    $message = "success";
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
        }
    }else{
        echo "no-submission";
    }
?>