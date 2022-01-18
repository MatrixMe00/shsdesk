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
            $sql = "SELECT user_id, username, password, email FROM admins_table WHERE email=? AND fullname=?";
            $res = $connect->prepare($sql);
            $res->bind_param("ss", $email,$fullname);
            
            //execute statement
            $res->execute();

            //get results
            $res = $res->get_result();

            if($res->num_rows > 0){
                $row = $res->fetch_assoc();
                $old_username = $row["username"];
                $old_password = $row["password"];

                if($new_username == $old_username){
                    echo "same-username";
                }elseif($new_password == $old_password){
                    echo "same-password";
                }else{
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
                }
            }else{
                echo "wrong-email-fullname";
            }
        }elseif($submit == "adminAddStudent" || $submit == "adminAddStudent_ajax"){
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
            }elseif(empty($jhs)){
                $message = "no-jhs-set";
            }elseif(empty($dob)){
                $message = "no-dob";
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
            }elseif(empty($jhs)){
                $message = "no-jhs-set";
            }elseif(empty($dob)){
                $message = "no-dob";
            }elseif(empty($track_id)){
                $message = "no-track-id";
            }else{
                //format date
                $dob = date("Y-m-d", strtotime($dob));

                //insert data into CSSPS table
                $sql = "UPDATE cssps SET indexNumber=?, Lastname=?, Othernames=?, Gender=?, boardingStatus=?,
                        programme=?, aggregate=?, jhsAttended=?, dob=?, trackID=?, schoolID=? 
                        WHERE indexNumber=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ssssssisssis",$student_index,$lname,$oname,$gender,$boarding_status,$student_course,
                    $aggregate,$jhs,$dob,$track_id,$school_id, $student_index);
                $stmt->execute();

                $message = "success";
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
        }elseif($submit == "addHouse" || $submit == "addHouse_ajax"){
            $house_name = $_REQUEST["house_name"];
            $gender = $_REQUEST["gender"];
            $house_room_total = $_REQUEST["house_room_total"];
            $head_per_room = $_REQUEST["head_per_room"];

            $message = "";

            if(empty($house_name)){
                $message = "no-house-name";
            }elseif(empty($gender)){
                $message = "no-gender";
            }/*elseif(empty($house_room_total)){
                $message = "room-total-empty";
            }elseif(intval($house_room_total) <= 0){
                $message = "room-zero";
            }elseif(empty($head_per_room)){
                $message = "head-total-empty";
            }elseif(intval($head_per_room) <= 0){
                $message = "head-zero";
            }*/else{
                //query into database
                // $sql = "INSERT INTO houses (title, schoolID, totalRooms, headPerRoom, gender)
                //     VALUES (?,?,?,?,?)";
                $sql = "INSERT INTO houses (title, schoolID, gender)
                    VALUES (?,?,?)";
                $stmt = $connect->prepare($sql);
                // $stmt->bind_param("sisss",$house_name, $user_school_id, $house_room_total, $head_per_room, $gender);
                $stmt->bind_param("sis", $house_name, $user_school_id, $gender);
                $stmt->execute();

                $message = "success";
            }

            echo $message;
            
        }elseif($submit == "updateHouse" || $submit == "updateHouse_ajax"){
            $house_name = $_REQUEST["house_name"];
            $gender = $_REQUEST["gender"];
            $house_room_total = $_REQUEST["house_room_total"];
            $head_per_room = $_REQUEST["head_per_room"];
            $id = $_REQUEST["id"];

            $message = "";

            if(empty($house_name)){
                $message = "no-house-name";
            }elseif(empty($gender)){
                $message = "no-gender";
            }elseif(empty($house_room_total)){
                $message = "room-total-empty";
            }elseif(intval($house_room_total) <= 0){
                $message = "room-zero";
            }elseif(empty($head_per_room)){
                $message = "head-total-empty";
            }elseif(intval($head_per_room) <= 0){
                $message = "head-zero";
            }else{
                //query into database
                $sql = "UPDATE houses SET title=?, totalRooms=?, headPerRoom=?, gender=?
                WHERE id=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ssssi",$house_name, $house_room_total, $head_per_room, $gender,$id);
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
            }else{
                //validate index number
                $sql = "SELECT houseID FROM house_allocation WHERE indexNumber=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("s",$student_index);
                $stmt->execute();

                $result = $stmt->get_result();

                if($result->num_rows > 0){
                    //format dates
                    $exeat_date = date("Y-m-d", strtotime($exeat_date));
                    $return_date = date("Y-m-d", strtotime($return_date));

                    //get house id
                    $column = "houseID";
                    $table = "house_allocation";
                    $where = "indexNumber='$student_index'";

                    $data = fetchData($column, $table, $where);
                    
                    //parse data into database
                    $sql = "INSERT INTO exeat (indexNumber,houseID,exeatTown,exeatDate,expectedReturn,exeatReason,exeatType,school_id)
                        VALUES (?,?,?,?,?,?,?)";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("sisssssi",$student_index, $data["houseID"],$exeat_town,$exeat_date,$return_date,$exeat_reason,$exeat_type, $user_school_id);
                    $stmt->execute();

                    #code to send letter to parents will go here

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

            //delete record
            if($indexNumber == "all"){
                $sql = "DELETE FROM cssps WHERE schoolID=$user_school_id";
            }else{
                $sql = "DELETE FROM cssps WHERE indexNumber='$indexNumber'";
            }
            if($connect->query($sql)){
                if($indexNumber == "all"){
                    $sql = "DELETE FROM enrol_table WHERE shsID=$user_school_id";
                }else{
                    $sql = "DELETE FROM enrol_table WHERE indexNumber='$indexNumber'";
                }
                if($connect->query($sql)){
                    if($indexNumber == "all"){
                        $sql = "DELETE FROM house_allocation WHERE schoolID=$user_school_id";
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
        }
    }else{
        echo "no-submission";
    }
?>