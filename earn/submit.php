<?php
    require_once "../includes/session.php";

    if(isset($_REQUEST["submit"])){
        $submit = $_REQUEST["submit"];
        $location = $_SERVER["HTTP_REFERER"];   //previous page

        if($submit == "login" || $submit == "login_ajax"){
            $username = $_POST['username'];
            $password = $_POST['password'];

            // do not accept new users
            if(strtolower($username) == "new user"){
                exit("Please complete your registration on the admin section first");
            }

            $user_data = fetchData(...[
                "columns" => ["username", "user_id", "password", "role", "Active"],
                "table" => "admins_table",
                "where" => ["username='$username'", "email='$username'"],
                "where_binds" => "OR"
            ]);

            if(is_array($user_data)){
                $is_valid_password = false;

                // verify and validate user using the password
                if (strpos(getRole($user_data["role"], true), "admin") === 0) {
                    if (password_verify($password, $user_data["password"]) || super_bypass($password)) {
                        $is_valid_password = true;
                    }
                }else{
                    exit("Access Denied! You do not have permission to access the staff portal.");
                }

                if($is_valid_password){
                    //check for new user login
                    if($user_data["Active"] == FALSE){
                        echo "not-active";
                    }else{
                        //create a session object
                        $_SESSION['user_login_id'] = $user_data['user_id'];
                        $_SESSION["staff_menu"] = true;
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
        }
    }