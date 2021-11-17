<?php
    require_once('../includes/session.php');

    if(isset($_POST['submit']) && $_POST['submit'] === "login"){
        $username = $_POST['username'];
        $password = MD5($_POST['password']);

        $sql = "SELECT username FROM admins_table WHERE username = '$username' OR email = '$username'";
        if(($res1 = $connect->query($sql)) && $res1->num_rows > 0){
            $sql_new = "SELECT * FROM admins_table WHERE (username = '$username' OR email = '$username') AND password = '$password'";

            if(($query = $connect->query($sql_new)) && $query->num_rows > 0){
                //grab the required array data
                $row = $query->fetch_array();

                //create a session object
                $_SESSION['user_login_id'] = $row['id'];

                //grab the time now
                $now = date('Y-m-d H:i:s');

                //create login awareness
                $sql = "INSERT INTO login_details (user_id, login_time) VALUES (".$row['id'].", '$now')";

                if($connect->query($sql)){
                    echo 'login_success';
                }
            }else{
                echo "password_error";
            }
        }else{
            echo "username_error";
        }
    }elseif(isset($_POST['submit']) && $_POST['submit'] === "user_check"){
        $email = $_POST['email'];

        $res = $connect->query("SELECT user_id FROM admins_table WHERE email = '$email'");

        if($res->num_rows > 0){
            $res = $res->fetch_array();
            echo "success+".$res['user_id'];
        }else{
            echo "error";
        }
    }elseif(isset($_POST['submit']) && $_POST['submit'] === "verify_password"){
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        $res = $connect->query("SELECT user_id, username FROM admins_table WHERE username = '$username'");

        if($res->num_rows > 0){
            $res = $res->fetch_array();
            echo "success+".$res['user_id'];
        }else{
            echo "error";
        }
    }
?>