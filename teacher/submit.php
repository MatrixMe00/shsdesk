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
                    $teacher_id = strtoupper($teacher_id);
                    $teacher_id = str_replace(["TID"," "],"",$teacher_id);
                    $teacher_id = intval($teacher_id);

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
                            $sql = "SELECT t.* FROM teacher_login l JOIN teachers t ON l.user_id=t.teacher_id
                                WHERE l.user_id=? AND l.user_password=?";
                            $stmt = $connect2->prepare($sql);
                            $stmt->bind_param("is", $teacher_id, $password);
                            if($stmt->execute()){
                                $result = $stmt->get_result();

                                if($result->num_rows > 0){
                                    $error = false;
                                    $message = true;
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
            default:
                echo "cant find what you want";
        }
    }else{
        echo "No submit request delivered. No operation is performed.";
    }
?>