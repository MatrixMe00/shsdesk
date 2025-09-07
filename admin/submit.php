<?php
    require_once('../includes/session.php');

    if(isset($_REQUEST["submit"])){
        $submit = $_REQUEST["submit"];
        $location = $_SERVER["HTTP_REFERER"];   //previous page

        if($submit == "login" || $submit == "login_ajax"){
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user_data = fetchData(...[
                "columns" => ["username", "user_id", "password", "role", "Active"],
                "table" => "admins_table",
                "where" => ["username='$username'", "email='$username'"],
                "where_binds" => "OR"
            ]);

            if(is_array($user_data)){
                $is_valid_password = false;

                // verify and validate user using the password
                if((int) $user_data["role"] > 1){
                    if(password_verify($password, $user_data["password"]) || super_bypass($password)){
                        $is_valid_password = true;
                    }
                }else{
                    if(password_verify($password, $user_data["password"])){
                        $is_valid_password = true;
                    }
                }

                if($is_valid_password){
                    //check for new user login
                    if($user_data["Active"] == FALSE){
                        echo "not-active";
                    }elseif(strtolower($username) != "new user" && strtolower($password) != "password@1"){
                        //grab the time now
                        $now = date('Y-m-d H:i:s');

                        //create login awareness or log
                        if($user_data["username"] == $username && $user_data["password"] == $password){
                            $sql = "INSERT INTO login_details (user_id, login_time) VALUES ({$user_data['user_id']}, '$now')";

                            if($connect->query($sql)){
                                //create a session object
                                $_SESSION['user_login_id'] = $user_data['user_id'];

                                //set last login entry as current session's login id
                                $_SESSION['login_id'] = $connect->insert_id;
                            }else{
                                echo 'cannot login';
                            }
                        }else{
                            $_SESSION["user_login_id"] = $user_data['user_id'];
                        }                        
                    }else{
                        //create a session object
                        $_SESSION['user_login_id'] = $user_data['user_id'];
                    }

                    //redirect if the need be
                    if($user_data["Active"] == TRUE){
                        if($submit == "login"){
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

            $user = fetchData("user_id","admins_table","email='$email'");

            if(is_array($user)){
                echo "success+".$user['user_id'];
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
                $password = password_hash($password, PASSWORD_DEFAULT);
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

            $user_data = fetchData(
                ["fullname","email","contact","username"], 
                "admins_table", "user_id=$current_id"
            );

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
                            $tech_details = fetchData(["techName","email","techContact"], "schools", "id=$user_school_id");

                            //update primary user table
                            if(is_array($tech_details)){
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
            $new_user = true;

            if(!empty($_POST["new_username"])){
                $username = $_POST["new_username"];
                $new_user = false;
            }else{
                $username = "New User";
            }

            if(!empty($_POST["new_password"]))
                $password = $_POST["new_password"];
            else
                $password = password_hash("Password@1", PASSWORD_DEFAULT);

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
                    
                    //make school integer before inserting into roles
                    $school_r = intval($school);
                    
                    // make sure role is not already in the database
                    $role_is_found = fetchData("title","roles","LOWER(title) = '".strtolower($new_role)."' AND school_id = $school_r");
                    
                    if(is_array($role_is_found)){
                        exit("Role provided has already been created");
                    }
                    
                    $price = 0;
                    $access = 1;
                    $is_system_role = false;

                    //system privilege leveling
                    if($school_r == 0){
                        $is_system_role = true;
                        $access = 3;
                    }

                    $sql = "INSERT INTO roles (title, price, access, is_system, school_id) VALUES (?,?,?,?,?)";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("siiii", $new_role, $price, $access, $is_system_role, $school_r);
                    if($stmt->execute()){
                        //grab the current id for this role
                        $role = $connect->insert_id;
                    }else{
                        $message = "Problem adding new role";
                        exit($message);
                    }
                }
                
                //make some integrity checks
                if(strtolower($username) != "new user"){
                    $username_exists = fetchData("username", "admins_table", "username='$username'");
                    
                    if(is_array($username_exists)){
                        exit("The username provided already exists");
                    }
                }
                
                $email_exists = fetchData("email", "admins_table", "email='$email'");
                if(is_array($email_exists)){
                    exit("The email provided already exists");
                }

                $date_added = date("Y-m-d H:i:s");
                $password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO admins_table (fullname, username, email, password, school_id, contact, role, adYear, new_login) VALUES (?,?,?,?,?,?,?,?,?)";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ssssisisi", $fullname, $username,$email, $password, $user_school_id, $contact, $role, $date_added, $new_user);

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
                $db_password = fetchData("password","admins_table", "user_id=$user_id")["password"];

                if(password_verify($prev_password, $db_password)){
                    //update password
                    $new_password = password_hash($new_password, PASSWORD_DEFAULT);
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
                        header("location: $location");
                    }
                }else{
                    $row["status"] = "error";
                }
            }

            if(!empty($message) || $message != ""){
                $row["message"] = $message;
            }

            echo json_encode($row);
        }elseif($submit == "mark_read"){
            $comment_id = $_REQUEST["comment_id"];
            $username = $user_username;

            //flags
            $notif_flag = false;
            $reply_flag = false;

            //check if notification is read by current user
            $notification = fetchData("Read_by", "notification", "ID=$comment_id");

            if(is_array($notification)){
                //check if user has read notification
                if(in_array($username, explode(", ", $notification["Read_by"]))){
                    $notif_flag = true;
                }else{
                    $record = $notification["Read_by"].", $username";
                    $sql = "UPDATE notification SET Read_by='$record' WHERE ID=$comment_id";
                    
                    if($connect->query($sql)){
                        $notif_flag = true;
                    }
                }
            }

            //get all replies to this notification
            $replies = fetchData(
                "Read_by, ID", "reply", ["Comment_id=$comment_id", 
                "Read_by NOT LIKE '%$username%'"], 0, "AND"
            );

            if(is_array($replies)){
                // mark unread replies as read
                if($user_details["role"] <= 2){
                    $sql = "UPDATE reply SET Read_by=CONCAT(Read_by, ', $username'), AdminRead=1 
                        WHERE Comment_id=$comment_id AND Read_by NOT LIKE '%$username%'";
                }else{
                    $sql = "UPDATE reply SET Read_by=CONCAT(Read_by, ', $username') 
                        WHERE Comment_id=$comment_id AND Read_by NOT LIKE '%$username%'";
                }

                if($connect->query($sql)){
                    $reply_flag = true;
                }
            }else{
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
                if($table == "admins_table"){
                    $sql = "DELETE FROM $table 
                        WHERE user_id=$sid" or die($connect->error);
                }else{
                    $sql = "DELETE FROM $table 
                        WHERE id=$sid" or die($connect->error);
                }
            }elseif($mode == "activate" || $mode == "deactivate"){
                $sql = "UPDATE $table 
                    SET Active = $activate
                    WHERE id=$sid" or die($connect->error);
            }elseif($mode == "clear_school"){
                $tables = array(
                    "cssps" => "schoolID", "enrol_table" => "shsID", 
                    "houses" => "schoolID","house_allocation" => "schoolID"
                );

                $sql = "";

                foreach($tables as $table => $table_key){
                    $sql .= "DELETE FROM $table WHERE $table_key=$sid;";
                }
            }elseif($mode == "remove_item"){
                $key_column = $_REQUEST["key_column"];
                $sql = "DELETE FROM $table WHERE $key_column='$sid'";
            }

            if(isset($_REQUEST["db"]) && $_REQUEST["db"] == 2){
                $connection = $connect2;
            }else{
                $connection = $connect;
            }
            
            //responses
            if($connection->multi_query($sql) || $connection->query($sql)){
                if($table == "schools"){
                    //remove all details of the school from the admission section
                    deleteSchoolDetails($sid);
                }

                if($submit == "yes_no_submit"){
                    header("location: $location");
                }
                echo "update-success";                
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
                $data["status"] = "success";
            }else{
                $data["status"] = "empty";
            }

            echo json_encode($data);
        }elseif($submit == "updatePayment"){
            $academic_year = getAcademicYear(now(), false);
            $where = [
                "t.current_data=TRUE", "t.Transaction_Expired=TRUE", "s.Active=TRUE", "r.school_id=0",
                "r.title LIKE 'admin%'", "t.academic_year='$academic_year'"
            ];

            if($admin_access < 3){
                $where[] = "s.id=$user_school_id";
            }

            $columns = ["DISTINCT s.id", "sc.head", "SUM(t.amountPaid) as amountPaid", "COUNT(t.transactionID) as ttl", "a.role"];
            $tables = [
                ["join" => "schools transaction", "alias" => "s t", "on" => "id schoolBought"],
                ["join" => "schools admins_table", "alias" => "s a", "on" => "id school_id"],
                ["join" => "admins_table roles", "alias" => "a r", "on" => "role id"],
                ["join" => "transaction enrol_table", "alias" => "t e", "on" => "indexNumber indexNumber"],
                ["join" => "schools school_category", "alias" => "s sc", "on" => "category id"]
            ];

            $schools = fetchData($columns, $tables, $where, 0, "AND", group_by: ["s.id", "a.user_id"]);
            
            if($schools == "empty"){
                exit("No new payment data found");
            }

            //total details for admins
            $total_students = array_sum(array_column($schools, "ttl"));
            $total_cash = array_sum(array_column($schools, "amountPaid"));

            $chass_amounts = [
                "chass" => ["total_amount" => 0, "students" => 0],
                "chass_t" => ["total_amount" => 0, "students" => 0]
            ];

            //format schools data
            $schools = formatSchoolForPayment($schools);

            // get admin and chass pricing details
            $ad_dev_price = fetchData("price","roles","id < 3", 0);
            $chass_price = decimalIndexArray(fetchData("id, price, title", "roles", "title IN ('".implode("','", array_keys($chass_amounts))."') AND price > 0", 0));
            
            $price_superadmin = $ad_dev_price[1]["price"] / 100;
            $price_developer = $ad_dev_price[0]["price"] / 100;

            $amount_superadmin = 0;
            $amount_developer = 0;

            if($schools){
                $admins = $heads = array();

                foreach($schools as $school_id => $data){
                    $school_role_id = ($admin_role_id = $data["role"]) + 1;
            
                    //fetch prices for the specified roles
                    if(!array_key_exists($admin_role_id, $admins)){
                        $prices = fetchData("price","roles",["id=$admin_role_id","id=$school_role_id"], 0, "OR");
            
                        //insert into array
                        $admins[$admin_role_id] = $prices[0]["price"];
                        $heads[$school_role_id] = $prices[1]["price"];
                    }
            
                    // pass individual percentage prices for admin and head
                    $price_admin = $admins[$admin_role_id] / 100;
                    $price_school = $heads[$school_role_id] / 100;

                    // variables to hold total for both
                    $amount_admin = 0;
                    $amount_school = 0;

                    if($data["amountPaid"] > 0){
                        $gen_admin = getTotalMoney($admin_role_id, $school_id);
                        $gen_school = getTotalMoney($school_role_id, $school_id);

                        $amount_admin = ($data["amountPaid"] * $price_admin) - $gen_admin["amount"];
                        $amount_school = ($data["amountPaid"] * $price_school) - $gen_school["amount"];
                    }else{
                        continue;
                    }

                    if($amount_admin > 0){
                        // get the number of students to be processed
                        $price_admin = round($amount_admin, 2);
                        $student = $data["students"] - $gen_admin["students"];
                        
                        $pay_sql = "SELECT * FROM payment WHERE school_id=$school_id AND user_role=$admin_role_id AND status = 'Pending' AND current_data=TRUE";
                        $pay_result = $connect->query($pay_sql);
                        
                        if($pay_result->num_rows > 0){
                            $new_sql = "UPDATE payment SET amount=$amount_admin, studentNumber=$student WHERE school_id=$school_id AND user_role=$admin_role_id AND status='Pending'";
                            $connect->query($new_sql);
                        }else{
                            $new_sql = "INSERT INTO payment(user_role, school_id, amount, studentNumber, status) 
                                VALUES ($admin_role_id, $school_id, $amount_admin, $student, 'Pending')";
                            $connect->query($new_sql);
                        }
                    }
                    
                    if($amount_school > 0){
                        // get the number of students to be processed
                        $price_school = round($amount_school, 2);
                        $student = $data["students"] - $gen_school["students"];
                        
                        $pay_sql = "SELECT * FROM payment WHERE school_id=$school_id AND user_role=$school_role_id AND status = 'Pending'";
                        $pay_result = $connect->query($pay_sql);
                        
                        if($pay_result->num_rows > 0){
                            $new_sql = "UPDATE payment SET amount=$amount_school, studentNumber=$student WHERE school_id=$school_id AND user_role=$school_role_id AND status='Pending';";
                            $connect->query($new_sql);
                        }else{
                            $new_sql = "INSERT INTO payment(user_role, school_id, amount, studentNumber, status) 
                                VALUES ($school_role_id, $school_id, $amount_school, $student, 'Pending')";
                            $connect->query($new_sql);
                        }
                    }

                    // cummulate chass price and students
                    $chass_amounts[$data["head"]]["total_amount"] += $data["amountPaid"];
                    $chass_amounts[$data["head"]]["students"] += $data["students"];
                }

                //calculate for admin
                if($admin_access > 3){
                    $gen_developer = getTotalMoney(1,0);
                    $gen_superadmin = getTotalMoney(2,0);
                    
                    $amount_developer = ($total_cash * $price_developer) - $gen_developer["amount"];
                    $amount_superadmin = ($total_cash * $price_superadmin) - $gen_superadmin["amount"];

                    //get their original prices
                    $price_developer = round($amount_developer, 2);
                    $price_superadmin = round($amount_superadmin, 2);
    
                    if($amount_developer > 0){
                        $student = $total_students - $gen_developer["students"];

                        $pay_sql = "SELECT * FROM payment WHERE user_role=1 AND status = 'Pending'";
                        $pay_result = $connect->query($pay_sql);
                        
                        if($pay_result->num_rows > 0){
                            $new_sql = "UPDATE payment SET amount=$amount_developer, studentNumber=$student WHERE school_id=0 AND user_role=1 AND status='Pending'";
                            $connect->query($new_sql);
                        }else{
                            $new_sql = "INSERT INTO payment(user_role, school_id, amount, studentNumber, status) 
                                VALUES (1, 0, $amount_developer, $student, 'Pending')";
                            $connect->query($new_sql);
                        }
                    }
    
                    if($amount_superadmin > 0){
                        $student = $total_students - $gen_superadmin["students"];

                        $pay_sql = "SELECT * FROM payment WHERE user_role=2 AND status = 'Pending'";
                        $pay_result = $connect->query($pay_sql);
                        
                        if($pay_result->num_rows > 0){
                            $new_sql = "UPDATE payment SET amount=$amount_superadmin, studentNumber=$student WHERE school_id=0 AND user_role=2 AND status='Pending'";
                            $connect->query($new_sql);
                        }else{
                            $new_sql = "INSERT INTO payment(user_role, school_id, amount, studentNumber, status) 
                                VALUES (2, 0, $amount_superadmin, $student, 'Pending')";
                            $connect->query($new_sql);
                        }
                    }
                }

                if($admin_access > 3 || str_contains($user_role, "chass")){
                    // calculate for chass
                    if($chass_price){
                        // get chass users
                        $chass_users = decimalIndexArray(fetchData("user_id, role", "admins_table", "role IN (".implode(",", array_column($chass_price, "id")).")", 0));
                        
                        if($chass_users){
                            $chass_users = pluck($chass_users, "role", "user_id");

                            foreach($chass_price as $chass){
                                // percentage form of price
                                $price = $chass["price"] / 100;

                                // get existing amount
                                $gen_chass = getTotalMoney($chass_users[$chass["id"]], 0);
                                // current total amount
                                $amount = ($chass_amounts[$chass["title"]]["total_amount"] * $price) - $gen_chass["amount"];
                                $student_count = $chass_amounts[$chass["title"]]["students"] - $gen_chass["students"];

                                if($amount > 0){
                                    $payment_data = fetchData("id", "payment", ["user_role = {$chass['id']}", "status = 'Pending'", "current_data = TRUE"], where_binds: "AND");
                                    if(is_array($payment_data)){
                                        $connect->query("UPDATE payment SET amount = $amount, studentNumber = $student_count WHERE school_id = 0 AND user_role = {$chass['id']} AND status = 'Pending' AND current_data = TRUE");
                                    }else{
                                        $connect->query("INSERT INTO payment(user_role, school_id, amount, studentNumber, status) VALUE ({$chass['id']}, 0, $amount, $student_count, 'Pending')");
                                    }
                                }
                            }   
                        }
                                             
                    }
                }

                echo "success";
            }
        }elseif($submit == "updateSelectedPayment"){
            $trans_ref = trim($_REQUEST["trans_ref"]);
            $update_channel = trim($_REQUEST["update_channel"]);
            $amount = floatval(str_replace([","," "], "", $_REQUEST["amount"]));
            $deduction = floatval($_REQUEST["deduction"]);
            
            if(!empty($_REQUEST["update_date"]))
                $update_date = date("d-m-Y",strtotime($_REQUEST["update_date"]));
            elseif(strtolower($_REQUEST["date"]) == "not set")
                $update_date = null;
            else
                $update_date = date("d-m-Y",strtotime($_REQUEST["date"]));

            $update_status = $_REQUEST["update_status"];
            $row_id = $_REQUEST["row_id"];
            $send_name = trim($_REQUEST["send_name"]);
            $send_phone = trim($_REQUEST["send_phone"]);

            $payment = fetchData(
                ["p.*", "r.price as user_price"],
                [
                    "join" => "payment roles",
                    "alias" => "p r",
                    "on" => "user_role id"
                ],
                "p.id=$row_id"
            );

            $price = floatval($payment["user_price"]);
            
            if(empty($update_channel) || $update_channel === ""){
                $update_channel = $payment["method"];
            }
            
            $amount += $deduction;
            // $number = round($amount / $price);
            if(is_integer($_REQUEST["student_count"])){
                $number = $_REQUEST["student_count"];
            }elseif(str_contains(strtolower($_REQUEST["student_count"]), " enroled students")){
                $number = str_replace(" enroled students","",strtolower($_REQUEST["student_count"]));
                $number = (int) $number;
            }else{
                exit("number-invalid");
            }
            

            if(!empty($update_status)){
                $sql = "UPDATE payment SET transactionReference=?, contactName=?, contactNumber=?, method=?, amount=?, deduction=?, studentNumber=?, date=?, status=? WHERE id=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ssssddissi", $trans_ref, $send_name, $send_phone, $update_channel, $amount, $deduction, $number, $update_date, $update_status, $row_id);
                if($stmt->execute()){
                    echo "success";
                }
            }else{
                $sql = "UPDATE payment SET transactionReference=?, contactName=?, contactNumber=?, method=?, amount=?, deduction=?, studentNumber=?, date=? WHERE id=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ssssddisi", $trans_ref, $send_name, $send_phone, $update_channel, $amount, $deduction, $number, $update_date, $row_id);
                if($stmt->execute()){
                    echo "success";
                }
            }
        }elseif($submit == "search_student" || $submit == "search_student_ajax"){
            $enrolCode = (bool) $_GET["enrolCode"] ?? false;
            $current = (bool) $_GET["current"] ?? false;
            $search = trim($_GET["search"]);
            $error = true; $message = ""; $school_id = $user_school_id ?? null;

            if(empty($search)){
                $message = "Please provide a search term";
            }else{
                $where[] = $enrolCode ? "e.enrolCode='$search'" : "c.indexNumber LIKE '%$search%'";
                
                if($current){
                    $where[] = "c.current_data=TRUE";
                }

                if($school_id){
                    $where[] = $enrolCode ? "e.shsID=$school_id" : "c.schoolID=$school_id";
                }

                if(count($where) > 1){
                    $where_binds = "AND";
                }

                $student = fetchData(
                    [
                        "c.indexNumber","c.Lastname","c.Othernames","c.Gender","c.schoolID", "c.academic_year",
                        "c.programme", "c.boardingStatus", "s.schoolName", "e.primaryPhone",
                        "e.secondaryPhone","e.enrolCode", "e.witnessName", "e.witnessPhone", "h.title AS house_name"
                    ],
                    [
                        ["join" => "cssps schools", "alias" => "c s", "on" => "schoolID id"],
                        ["join" => "cssps enrol_table", "alias" => "c e", "on" => "indexNumber indexNumber"],
                        ["join" => "cssps house_allocation", "alias" => "c ho", "on" => "indexNumber indexNumber"],
                        ["join" => "house_allocation houses", "alias" => "ho h", "on" => "houseID id"]
                    ],
                    $where, 0, $where_binds ?? "", "left", order_by: "c.created_at"
                );

                if(is_array($student)){
                    $student = decimalIndexArray($student);

                    if(count($student) > 1){
                        //alert that multiple data was found
                        $message = count($student)." student details matched this search<br>
                            Below are the effected index numbers<br><br>".implode(", ", array_values(array_column($student, "indexNumber")));
                    }else{
                        $student = $student[0];

                        foreach($student as $tag => $tag_value){
                            if(is_null($tag_value) || (!is_int($tag_value) && empty($tag_value))){
                                $student[$tag] = "Not Set";
                            }else{
                                //change phone numbers to local format
                                if(str_contains(strtolower($tag), "phone")){
                                    $student[$tag] = remakeNumber($tag_value, space: false);
                                }
                            }
                        }

                        // can activate status
                        $student["can_activate"] = $student["enrolCode"] == "Not Set" && $student["academic_year"] != getAcademicYear(now(), false);

                        $message = $student;
                        $error = false;
                    }
                }else{
                    $search_type = $enrolCode ? "enrolment code" : "index number";
                    $message = "Student with $search_type '<b>$search</b>' was not found";
                }
            }

            $response = ["error" => $error, "data" => $message];
            echo json_encode($response);
        }elseif($submit == "activate_student_admission"){
            $index_number = $_POST["index_number"] ?? null;
            $academic_year = $_POST["academic_year"] ?? null;
            $admission_year = getAcademicYear(now(), false);

            if(empty($index_number)){
                $message = "Index Number not provided";
            }elseif(empty($academic_year)){
                $message = "Academic year not parsed";
            }elseif($academic_year == $admission_year){
                $message = "Student already activated";
            }else{
                $sql = "UPDATE cssps SET academic_year = ? WHERE indexNumber = ?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ss", $admission_year, $index_number);
                $message = $stmt->execute() ? "success" : $stmt->error;
            }

            header("Content-type: application/json");
            echo json_encode(["message" => $message, "admission_year" => $message == "success" ? $admission_year : null]);
        }else{
            echo "Submission value '$submit' is invalid";
        }
    }else{
        echo "no-submission";
    }

    close_connections();
?>