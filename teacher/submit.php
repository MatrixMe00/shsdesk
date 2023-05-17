<?php
    include_once("../includes/session.php");

    if(isset($_REQUEST["submit"])){
        $submit = $_REQUEST["submit"];

        switch($submit){
            case "user_login":
                $teacher_id = $_REQUEST["teacher_id"] ?? null;
                $step = $_REQUEST["step"] ?? null;
                $message = ""; $error = true;

                if(is_null($teacher_id) || empty($teacher_id)){
                    $message = "Teacher ID not found. Please provide one";
                }elseif(!str_contains(strtoupper($teacher_id),"TID")){
                    $message = "Teacher ID is invalid or has the wrong format";
                }elseif(is_null($step) || empty($step)){
                    $message = "Process has broken down. Location of processing couldn't be established";
                }else{
                    $teacher_id = formatItemId($teacher_id, "TID", true);

                    if($step == 1){
                        $sql = "SELECT user_username FROM teacher_login WHERE user_id=?";
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("i", $teacher_id);
                        if($stmt->execute()){
                            $result = $stmt->get_result();

                            if($result->num_rows > 0){
                                $error = false;
                                $message = true;
                            }else{
                                $message = "ID was not found. Please check your id and try again";
                            }
                        }else{
                            $message = "There is an internal sql error. Please try again later";
                        }
                    }else{
                        $password = MD5($_POST["password"]) ?? null;

                        if(is_null($password) || empty($password)){
                            $message = "Please provide a password";
                        }else{
                            $sql = "SELECT t.teacher_id FROM teacher_login l JOIN teachers t ON l.user_id=t.teacher_id
                                WHERE l.user_id=? AND l.user_password=?";
                            $stmt = $connect2->prepare($sql);
                            $stmt->bind_param("is", $teacher_id, $password);
                            if($stmt->execute()){
                                $result = $stmt->get_result();

                                if($result->num_rows > 0){
                                    $error = false;
                                    $message = true;

                                    //start the session
                                    $_SESSION["teacher_id"] = $result->fetch_assoc()["teacher_id"];
                                }else{
                                    $message = "Incorrect password delivered. Please check and try again";
                                }
                            }else{
                                $message = "There was a problem with the mysql server. Please try again later";
                            }                            
                        }                        
                    }
                }
                $response = [
                    "error"=>$error,
                    "message"=>$message
                ];

                header("Content-Type: application/json");
                echo json_encode($response);

                break;
            case "new_user_update":
            case "new_user_update_ajax":
                $teacher_id = $_POST["teacher_id"] ?? null;
                $lname = $_POST["lname"] ?? null;
                $oname = $_POST["oname"] ?? null;
                $email = $_POST["email"] ?? null;
                $old_email = $_POST["old_email"];
                $new_username = $_POST["new_username"] ?? null;
                $new_password = $_POST["new_password"] ?? null;
                $phone_number = $_POST["phone_number"] ?? null;
                $gender = $_POST["gender"] ?? null;

                if(is_null($teacher_id) || empty($teacher_id)){
                    $message = "Teacher ID not provided. Process was terminated";
                }elseif(strlen($teacher_id) < 7 || strlen($teacher_id) > 8){
                    $message = "Teacher ID provided is invalid or is in the wrong format.";
                }elseif(is_null($lname) || empty($lname)){
                    $message = "Please provide your lastname";
                }elseif(is_null($oname) || empty($oname)){
                    $message = "Please provide your other name(s)";
                }elseif(is_null($email) || empty($email)){
                    $message = "Please provide your email";
                }elseif(is_null($new_username) || empty($new_username)){
                    $message = "Please provide your username";
                }elseif(strtolower($new_username) === "new user"){
                    $message = "You are not permitted to use the same username. Please provide a new one";
                }elseif(is_null($new_password) || empty($new_password)){
                    $message = "Please provide your new password";
                }elseif(strtolower($new_password) === "password@1"){
                    $message = "Password cannot be same as the old one. Please provide a new password";
                }elseif(is_null($phone_number) || empty($phone_number)){
                    $message = "Please provide your Phone number";
                }elseif(strlen($phone_number) < 10 || strlen($phone_number) > 16){
                    $message = "invalid phone number lenght detected";
                }elseif(is_null($gender) || empty($gender)){
                    $message = "Please select your gender";
                }else{
                    $teacher_id = formatItemId($teacher_id, "TID", true);

                    try {
                        //check username and email
                        $sql = "SELECT t.email, l.user_username FROM teacher_login l JOIN teachers t ON l.user_id=t.teacher_id
                            WHERE l.user_username=? OR t.email=?";
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("ss", $user_username, $email);
                        if($stmt->execute()){
                            $result = $stmt->get_result();

                            if($result->num_rows > 0 && strtolower($email) != strtolower($old_email)){
                                while($row = $result->fetch_assoc()){
                                    if($row["email"] == $email){
                                        $message = "This email already exists. Please provide a new one else use the old one";
                                    }elseif($row["username"] == $new_username){
                                        $message = "This username already exists. Please use a new one";
                                    }
                                }
                            }else{
                                // Prepare the first update statement
                                $new_password = MD5($new_password);
                                $sql1 = "UPDATE teacher_login SET user_username=?, user_password=? WHERE user_id=?";
                                $stmt1 = $connect2->prepare($sql1);
                                $stmt1->bind_param("ssi", $new_username, $new_password, $teacher_id);

                                // Prepare the second update statement
                                $sql2 = "UPDATE teachers SET lname=?,oname=?,gender=?,email=?, phone_number=? WHERE teacher_id = ?";
                                $stmt2 = $connect2->prepare($sql2);
                                $stmt2->bind_param("sssssi", $lname, $oname, $gender, $email, $phone_number, $teacher_id);

                                // Begin the transaction
                                $connect2->begin_transaction();

                                try {
                                    // Execute the first update statement
                                    $stmt1->execute();

                                    // Execute the second update statement
                                    $stmt2->execute();

                                    // Commit the transaction
                                    $connect2->commit();

                                    $message =  "Update successful!";
                                    $status = true;
                                } catch (Exception $e) {
                                    // An error occurred, rollback the transaction
                                    $connect2->rollback();

                                    $message = "Update failed: " . $e->getMessage();
                                }
                            }
                        }else{
                            $message = "Process was broken on u&e check. Please contact admin for help";
                        }
                    } catch (\Throwable $th) {
                        $message = $th->getMessage();
                    }
                }

                $response = [
                    "status"=>$status ?? false,
                    "message"=>$message
                ];

                header("Content-Type: application/json");
                echo json_encode($response);
                break;
            default:
                echo "cant find what you want";
        }
    }else{
        echo "No submit request delivered. No operation is performed.";
    }
?>