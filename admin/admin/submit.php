<?php
    @include_once("../../includes/session.php");

    if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != NULL){
        $submit = $_REQUEST["submit"];

        if($submit == "upload" || $submit == "upload_ajax"){
            if(isset($_FILES['import']) && $_FILES["import"]["tmp_name"] != NULL){
                echo "okay";
            }else{
                echo "no-file";
                exit(1);
            }
        }elseif($submit == "new_user_update" || $submit == "new_user_update_ajax"){
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

                //insert data into CSSPS table
                $sql = "INSERT INTO cssps (indexNumber,Lastname,Othernames,Gender,
                        boardingStatus,programme, aggregate, jhsAttended, dob, trackID, schoolID) 
                        VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ssssssisssi",$student_index,$lname,$oname,$gender,$boarding_status,$student_course,
                    $aggregate,$jhs,$dob,$track_id,$school_id);
                $stmt->execute();

                $message = "success";
            }

            echo $message;
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
                $sql = "INSERT INTO houses (title, schoolID, totalRooms, headPerRoom, gender)
                    VALUES (?,?,?,?,?)";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("sisss",$house_name, $user_school_id, $house_room_total, $head_per_room, $gender);
                $stmt->execute();

                $message = "success";
            }

            echo $message;
            
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
        }
    }else{
        echo "no-submission";
    }
?>