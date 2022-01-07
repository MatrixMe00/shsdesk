<?php
    require_once('../includes/session.php');

    if(isset($_REQUEST["submit"])){
        $submit = $_REQUEST["submit"];

        if($submit == "login" || $submit == "login_ajax"){
            $username = $_POST['username'];
            $password = MD5($_POST['password']);

            $sql = "SELECT username FROM admins_table WHERE username = ? OR email = ?";
            $res1 = $connect->prepare($sql);
            $res1->bind_param("ss",$username,$username);
            $res1->execute();

            $res1 = $res1->get_result();

            if($res1->num_rows > 0){
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

            if($fullname != $user_details["fullname"] || $email != $user_details["email"] ||
               $contact != $user_details["contact"] || $username != $user_details["username"] ){
                $sql = "UPDATE admins_table SET fullname=?, email=?, contact=?, username=? WHERE user_id = $user_id";
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
                            $sql = "UPDATE schools SET techName=?, email=?, techContact=? 
                            WHERE id=$user_school_id";
                            $stmt = $connect->prepare($sql);
                            $stmt->bind_param("sss", $fullname, $email, $contact);
                            $stmt->execute();
                        }
                    }
                    echo "success";
                }else{
                    echo "update-error";
                }
            }else{
                echo "no-change";
            }
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
        }
    }else{
        echo "no-submission";
    }
?>