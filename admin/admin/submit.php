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
                        //update the login info
                        $_SESSION['user_login_id'] = $row["user_id"];

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
        }
    }else{
        echo "no-submission";
    }
?>