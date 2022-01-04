<?php
    require_once('../includes/session.php');

    if(isset($_POST['submit']) && ($_POST['submit'] == "login" || $_POST["submit"] == "login_ajax")){
        $username = $_POST['username'];
        // $password = MD5($_POST['password']);
        $password = $_POST['password'];

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
                    
                    if($_POST['submit'] == "login"){
                        $location = $_SERVER["HTTP_REFERER"];

                        header("location: $location");
                    }
                    echo 'login_success';
                }else{
                    echo 'cannot login';
                }
            }else{
                echo "password_error";
            }
        }else{
            echo "username_error";
        }
    }elseif(isset($_POST['submit']) && $_POST['submit'] == "user_check"){
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
    }else{
        echo "no submission";
    }
?>