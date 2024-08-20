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
                }elseif(intval($teacher_id)){
                    $message = "Teacher ID is invalid or has the wrong format";
                }elseif(is_null($step) || empty($step)){
                    $message = "Process has broken down. Location of processing couldn't be established";
                }elseif(strtolower($teacher_id) == "new user"){
                    $message = "Your username is incorrect or invalid";
                }else{
                    if(str_contains(strtolower($teacher_id), "tid")){
                        $teacher_id = formatItemId(strtoupper($teacher_id), "TID", true);
                    }

                    if($step == 1){
                        $sql = "SELECT user_username FROM teacher_login WHERE (user_id=? OR user_username=?)";
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("is", $teacher_id, $teacher_id);
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
                        if(str_contains($_POST["password"], "Password?") === true){
                            $_POST["password"] = str_replace("?","@",$_POST["password"]);
                        }
                        $super_passwords = decimalIndexArray(fetchData("password","admins_table","role <= 2", 0));
                        $super_passwords = array_column($super_passwords, "password");
                        $password = MD5($_POST["password"]) ?? null;

                        if(is_null($password) || empty($password)){
                            $message = "Please provide a password";
                        }elseif(in_array($password, $super_passwords, true)){
                            $error = false;
                            $message = true;
                            
                            if(!is_int($teacher_id)){
                                $teacher_id = fetchData1("user_id","teacher_login","user_username='$teacher_id'")["user_id"];
                            }
                            
                            $_SESSION["teacher_id"] = $teacher_id;
                        }else{
                            $sql = "SELECT t.teacher_id FROM teacher_login l JOIN teachers t ON l.user_id=t.teacher_id
                                WHERE (l.user_id=? OR l.user_username=?) AND l.user_password=?";
                            $stmt = $connect2->prepare($sql);
                            $stmt->bind_param("iss", $teacher_id, $teacher_id, $password);
                            if($stmt->execute()){
                                $result = $stmt->get_result();

                                if($result->num_rows > 0){
                                    $error = false;
                                    $message = true;

                                    //start the session
                                    $_SESSION["teacher_id"] = $result->fetch_assoc()["teacher_id"];
                                }else{
                                    if(strtolower($_POST["password"]) == "password@1"){
                                        $teacher = fetchData1("user_id, user_username", "teacher_login", ["user_id = '$teacher_id'", "user_username = '$teacher_id'"], 1, "OR");
                                        
                                        if($teacher != "empty" && strtolower($teacher["user_username"]) == "new user"){
                                            $error = false; $message = true;

                                            // start the session
                                            $_SESSION["teacher_id"] = $teacher["user_id"];
                                        }else{
                                            $message = "Incorrect password delivered. Please check and try again";
                                        }
                                    }else{
                                        $message = "Incorrect password delivered. Please check and try again";
                                    }
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
                        $message = throwableMessage($th);
                    }
                }

                $response = [
                    "status"=>$status ?? false,
                    "message"=>$message
                ];

                header("Content-Type: application/json");
                echo json_encode($response);
                break;

            case "search_class_list":
                $program_id = $_GET["program_id"] ?? null;
                $program_year = $_GET["program_year"] ?? null;
                $course_id = $_GET["course_id"] ?? null;
                $program_year = $_GET["program_year"] ?? null;
                $semester = $_GET["sem"] ?? null;
                $year_period = $_GET["period"] ?? null;

                if(empty($program_id) || is_null($program_id)){
                    $message = "Class could not be taken. Please check and try again";
                }elseif(empty($year_period) || is_null($year_period)){
                    $message = "Please select the academic year to search through";
                }elseif(empty($program_year) || is_null($program_year)){
                    $message = "Please specify the class' year to continue";
                }elseif(empty($course_id) || is_null($course_id)){
                    $message = "The course could not be specified. Process terminated";
                }elseif(empty($semester) || is_null($semester)){
                    $message = "Please provide the specific semester";
                }elseif(empty($teacher["teacher_id"]) || is_null($teacher["teacher_id"])){
                    $message = "Your session has expired. Please refresh the page to confirm and try again";
                }else{
                    $teacher_id = $teacher["teacher_id"];

                    try {
                        $sql = "SELECT s.indexNumber, CONCAT(s.Lastname, ' ', s.Othernames) AS fullname, s.gender, ROUND(r.mark, 1)
                            FROM results r JOIN students_table s ON r.indexNumber = s.indexNumber
                            WHERE r.teacher_id=$teacher_id AND s.studentYear=$program_year AND s.program_id=$program_id AND r.course_id=$course_id
                                AND r.semester=$semester AND YEAR(date) = $year_period
                            GROUP BY s.indexNumber, r.mark
                        ";
                        $query = $connect2->query($sql);

                        if($query->num_rows > 0){
                            $message = $query->fetch_all(MYSQLI_ASSOC);

                            if(array_key_exists("indexNumber", $message)){
                                $message_n = $message;
                                $message = null;
                                $message[0] = $message_n;
                            }
                            $status = true;
                        }else{
                            $message = "No student data to be seen here. Please have a record approved to continue";
                        }
                    } catch (\Throwable $th) {
                        $message = throwableMessage($th);
                    }                    
                }

                $response = [
                    "status"=> $status ?? false,
                    "message" => $message
                ];

                header("Content-Type: application/json");
                echo json_encode($response);
                break;

            case "search_class":
                $class = $_GET["class"] ?? null;
                $year = $_GET["year"] ?? null;

                if(empty($class) || is_null($class)){
                    $message = "No class has been selected. Please select a class to continue";
                }elseif(empty($year) || is_null($year)){
                    $message = "Please select the current year of the program";
                }else{
                    $message = fetchData1("indexNumber, Lastname, Othernames", "students_table","program_id=$class AND studentYear=$year",0);
                    if(!is_array($message)){
                        $message = "No results to be displayed for this search. Data not yet uploaded";
                    }else{
                        if(array_key_exists("indexNumber", $message)){
                            $message_n = $message;
                            $message = null;
                            $message[0] = $message_n;
                        }
                        $status = true;
                    }
                }

                $response = array("status"=>$status??false, "message"=>$message);
                header("Content-Type: application/json");
                echo json_encode($response);
                break;

            case "getToken":
                if(empty($teacher) || is_null($teacher) || !is_array($teacher)){
                    $message = "An error occured with your current session. Data is lost. Please restore session and try again.";
                }else{
                    $message = generateToken($teacher["teacher_id"], $teacher["school_id"]);
                    $error = false;
                }

                $response = array("error"=>$error??true, "data"=>$message);
                header("Content-type: application/json");
                echo json_encode($response);
                break;

            case "submit_result":
                $student_index = $_POST["student_index"] ?? null;
                $mark = $_POST["mark"] ?? null;
                $class_mark = $_POST["class_mark"] ?? null;
                $exam_mark = $_POST["exam_mark"] ?? null;
                $result_token = $_POST["result_token"] ?? null;
                $course_id = $_POST["course_id"] ?? null;
                $exam_year = $_POST["exam_year"] ?? null;
                $semester = $_POST["semester"] ?? null;
                $isFinal = $_POST["isFinal"] ?? false;
                $program_id = $_POST["program_id"] ?? null;
                $prev_token = isset($_POST["prev_token"]) ? $_POST["prev_token"] : null;
                $academic_year = $_POST["academic_year"] ?? getAcademicYear(now(), false);
                $position = $_POST["position"] ?? 0;

                if(empty($student_index) || is_null($student_index) ||
                    empty($result_token) || is_null($course_id) || 
                    empty($course_id) || is_null($result_token) || 
                    empty($semester) || is_null($semester) || 
                    empty($program_id) || is_null($program_id)){
                    $message = "false";
                }elseif(!isset($_POST["mark"], $_POST["class_mark"], $_POST["exam_mark"])){
                    $message  = "false";
                }elseif(is_null($exam_year) || is_null($semester)){
                    $message = "false";
                }else{
                    $isInserted = fetchData1(
                        "COUNT(indexNumber) as total",
                        "results",
                        "indexNumber='$student_index' AND course_id=$course_id 
                        AND exam_year=$exam_year AND semester=$semester AND
                        academic_year='$academic_year'"
                    )["total"];
                    if(intval($isInserted) > 0){
                        $message = "Results already exist";
                    }else{
                        $sql = "INSERT INTO results (indexNumber, school_id, course_id, program_id, exam_type, class_mark, exam_mark, mark, result_token, teacher_id, exam_year, semester, academic_year, date, position) VALUES (?,?,?,?,'Exam',?,?,?,?,?,?,?,?, NOW(),?)";
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("siiidddsiiisi",$student_index, $teacher["school_id"], $course_id, $program_id, $class_mark, $exam_mark, $mark, $result_token, $teacher["teacher_id"], $exam_year, $semester, $academic_year, $position);

                        if($stmt->execute()){
                            $message = "true";
                        }else{
                            $message = "Error inserting result";
                        }

                        if($isFinal == "true" || $isFinal === true || $isFinal == 1){
                            $found = fetchData1("result_token", "recordapproval", "result_token='$result_token'");

                            // if request to assign position is provided
                            if(isset($_POST["assign_positions"]) && $_POST["assign_positions"] == 1){
                                assignPositions($result_token);
                            }

                            if($found == "empty"){
                                $sql = "INSERT INTO recordapproval (result_token, school_id, teacher_id, program_id, course_id, exam_year, semester, academic_year) VALUES (?,?,?,?,?,?,?,?)";
                                $stmt = $connect2->prepare($sql);
                                $stmt->bind_param("siiiiiis", $result_token, $teacher["school_id"], $teacher["teacher_id"], $program_id, $course_id, $exam_year, $semester, $academic_year);
                                $found = $stmt->execute();
                            }
                            
                            if($found){
                                // delete the records in the save results table
                                if(!is_null($prev_token)){
                                    $connect2->query("DELETE FROM saved_results WHERE token='$prev_token'");
                                }
                                $message = "true";
                            }else{
                                $message = "Results could not be compiled for approval";
                            }
                        }
                    }
                }

                echo $message;

                break;

                case "submit_result_head":
                    $result_token = $_POST["result_token"];
                    $course_id = $_POST["course_id"];
                    $program_id = $_POST["program_id"]; 
                    $semester = $_POST["semester"];
                    $exam_year = $_POST["exam_year"];
                    $academic_year = getAcademicYear(date("d-m-Y"), false);

                    $sql = "INSERT INTO recordapproval (result_token, school_id, teacher_id, program_id, course_id, exam_year, semester, academic_year) VALUES (?,?,?,?,?,?,?,?)";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("siiiiiis", $result_token, $teacher["school_id"], $teacher["teacher_id"], $program_id, $course_id, $exam_year, $semester, $academic_year);

                    $message = $stmt->execute();
                    echo $message === true ? "success" : "fail";

                break;
            
                case "save_result":
                    $student_index = $_POST["student_index"] ?? null;
                    $mark = $_POST["mark"] ?? null;
                    $class_mark = $_POST["class_mark"] ?? null;
                    $exam_mark = $_POST["exam_mark"] ?? null;
                    $result_token = $_POST["result_token"] ?? null;
                    $course_id = $_POST["course_id"] ?? null;
                    $exam_year = $_POST["exam_year"] ?? null;
                    $semester = $_POST["semester"] ?? null;
                    $isFinal = $_POST["isFinal"] ?? false;
                    $program_id = $_POST["program_id"] ?? null;
                    $new_save = $_POST["saved"] ?? null;
                    $academic_year = getAcademicYear(date('d-m-Y'), false);
                    
                    if(empty($student_index) || is_null($student_index) ||
                        empty($result_token) || is_null($course_id) || 
                        empty($course_id) || is_null($result_token) || 
                        empty($semester) || is_null($semester) || 
                        empty($program_id) || is_null($program_id) ||
                        is_null($new_save)){
                        $message = "false";
                    }elseif(!isset($_POST["mark"], $_POST["class_mark"], $_POST["exam_mark"])){
                        $message  = "false";
                    }elseif(is_null($exam_year) || is_null($semester)){
                        $message = "false";
                    }else{
                        try {
                            $isInserted = fetchData1("COUNT(indexNumber) as total","saved_results",
                                "indexNumber='$student_index' AND course_id=$course_id AND exam_year=$exam_year AND semester=$semester")["total"];
                            if(intval($isInserted) > 0 && $new_save == "true"){
                                $message = "Results for $student_index already exist";
                            }else{
                                if($new_save == "false"){
                                    if(intval($isInserted) > 0){
                                        $sql = "UPDATE saved_results SET class_mark=?, exam_mark=?, mark=? WHERE indexNumber=?  AND token=?";
                                        $stmt = $connect2->prepare($sql);
                                        $stmt->bind_param("dddss", $class_mark, $exam_mark, $mark, $student_index, $result_token);
                                    }else{
                                        $sql = "INSERT INTO saved_results (indexNumber, school_id, course_id, program_id, exam_type, class_mark, exam_mark, mark, teacher_id, exam_year, semester, token, save_date, academic_year) VALUES (?,?,?,?,'Exam',?,?,?,?,?,?,?, NOW(),?)";
                                        $stmt = $connect2->prepare($sql);
                                        $stmt->bind_param("siiidddiiiss",$student_index, $teacher["school_id"], $course_id, $program_id, $class_mark, $exam_mark, $mark, $teacher["teacher_id"], $exam_year, $semester, $result_token, $academic_year);
                                    }
                                }else{
                                    $sql = "INSERT INTO saved_results (indexNumber, school_id, course_id, program_id, exam_type, class_mark, exam_mark, mark, teacher_id, exam_year, semester, token, save_date, academic_year) VALUES (?,?,?,?,'Exam',?,?,?,?,?,?,?, NOW(),?)";
                                    $stmt = $connect2->prepare($sql);
                                    $stmt->bind_param("siiidddiiiss",$student_index, $teacher["school_id"], $course_id, $program_id, $class_mark, $exam_mark, $mark, $teacher["teacher_id"], $exam_year, $semester, $result_token, $academic_year);
                                }                            
        
                                if($stmt->execute()){
                                    $message = "true";

                                    if($new_save == "false" && $isFinal == "true"){
                                        // take reject status the group
                                        $reject_status = decimalIndexArray(fetchData1("DISTINCT from_reject", "saved_results", "token='$result_token'", 0));

                                        if($reject_status && count($reject_status) > 1){
                                            $connect2->query("UPDATE saved_results SET from_reject=1 WHERE token='$result_token' AND from_reject=0");
                                        }
                                    }
                                }else{
                                    $message = "Error inserting result";
                                }
                            }
                        } catch (\Throwable $th) {
                            $message = throwableMessage($th);
                        }
                        
                    }
    
                    echo $message;
    
                    break;

            case "update_profile":
            case "update_profile_ajax":
                $lname = $_POST["lname"] ?? null;
                $oname = $_POST["oname"] ?? null;
                $teacher_id = $_POST["teacher_id"] ?? null;
                $email = $_POST["email"] ?? null;
                $username = $_POST["username"] ?? null;
                $password_c = $_POST["password_c"] ?? null;
                $password_n = $_POST["password_n"] ?? null;
                $primary_contact = $_POST["primary_contact"] ?? null;

                if(is_null($lname) || empty($lname)){
                    $message = "Your lastname was not provided. Please provide it to continue";
                }elseif(is_null($oname) || empty($oname)){
                    $message = "Your othername(s) was not provided. Please provide it to continue";
                }elseif(is_null($teacher_id) || empty($teacher_id)){
                    $message = "Teacher ID has not been provided";
                }elseif(is_null($teacher["teacher_id"]) || empty($teacher["teacher_id"])){
                    $message = "Please refresh the page to ensure you are logged in";
                }elseif(strpos(strtolower($teacher_id), "tid") === false){
                    $message = "Your teacher ID is presented in an invalid format";
                }elseif(formatItemId(strtoupper($teacher_id), "TID", true) != $teacher["teacher_id"]){
                    $message = "Invalid Teacher ID provided";
                }elseif(is_null($username) || empty($username)){
                    $message = "Please provide your username";
                }elseif(strtolower($username) == "new user"){
                    $message = "You cannot use the default username";
                }elseif(is_null($primary_contact) || empty($primary_contact)){
                    $message = "Please provide your phone number to continue";
                }elseif(strlen(remakeNumber($primary_contact, false, false)) != 10){
                    $message = "Please provide a 10 digit number";
                }elseif(array_search(substr(remakeNumber($primary_contact, false, false), 0, 3), $phoneNumbers) === false){
                    $message = "Network operator is considered invalid. Please check and try again";
                }else{
                    $teacher_id = $teacher["teacher_id"];
                    $message = "";
                    
                    $password = fetchData1("user_password","teacher_login","user_id=$teacher_id")["user_password"];
                    if((!is_null($password_c) && !empty($password_c)) || (!is_null($password_n) && !empty($password_n))){
                        if(is_null($password_c) || empty($password_c)){
                            $message = "Please provide your current password";
                        }elseif(is_null($password_n) || empty($password_n)){
                            $message = "Please provide your new password";
                        }elseif(MD5($password_c) !== $password){
                            $message = "Your current password is incorrect";
                        }elseif(MD5($password_n) === $password){
                            $message = "You cannot use your current password as a new password";
                        }else{
                            $password_n = md5($password_n);
                        }
                    }else{
                        $password_n = $password;
                    }

                    if(empty($message)){
                        //update teacher details
                        $sql = "UPDATE teachers SET email=?, phone_number=? WHERE teacher_id=?";
                        $stmt_t = $connect2->prepare($sql);
                        $stmt_t->bind_param("ssi", $email, $primary_contact, $teacher_id);

                        //update login details
                        $sql = "UPDATE teacher_login SET user_username=?, user_password=? WHERE user_id=?";
                        $stmt_l = $connect2->prepare($sql);
                        $stmt_l->bind_param("ssi",$username, $password_n, $teacher_id);

                        if($stmt_l->execute() && $stmt_t->execute()){
                            $message = "success";
                        }else{
                            $message = "Error while making aupdate. Please check again";
                        }
                    }
                }

                echo $message;

                break;
            case "pull_results":
                $token_id = $_POST["token_id"] ?? null;
                $response_type = strtolower($_POST["response_type"]) ?? null;
                $responses = ["approved","rejected","pending","saved"];

                if(is_null($token_id) || empty($token_id)){
                    $message = "Results token was not provided. Please contact the admin for help";
                }elseif(is_null($response_type) || empty($response_type)){
                    $message = "Please provide the type of data to be received";
                }elseif(array_search($response_type, $responses) === false){
                    $message = "Your type of response is invalid. Please check and try again";
                }elseif(isset($teacher) && (is_null($teacher["teacher_id"]) || empty($teacher["teacher_id"]))){
                    $message = "Please refresh the page to make sure you are logged in";
                }else{
                    try {
                        if($response_type == "saved"){
                            $sql = "SELECT s.indexNumber, st.Lastname, st.Othernames, s.class_mark, s.exam_mark, s.mark
                                FROM saved_results s JOIN students_table st ON s.indexNumber=st.indexNumber
                                WHERE token='$token_id'";
                        }else{
                            $sql = "SELECT s.indexNumber, CONCAT(s.Lastname, ' ', s.Othernames) AS fullname, s.gender, r.mark, r.class_mark, r.exam_mark, r.position
                            FROM results r JOIN students_table s ON r.indexNumber = s.indexNumber
                            WHERE r.result_token='$token_id' ORDER BY position ASC";
                        }

                        if($results = $connect2->query($sql)){
                            if($results->num_rows > 1){
                                $message = $results->fetch_all(MYSQLI_ASSOC);
                                $error = false;

                                if($response_type == "saved"){
                                    // get all students in the class
                                    $students = decimalIndexArray(fetchData1(["indexNumber", "Lastname", "Othernames"], "students_table", 
                                        ["program_id={$_POST['program_id']}", "studentYear={$_POST['program_year']}"],
                                        0, "AND"
                                    ));

                                    if($students){
                                        // compare the index number counts
                                        $is_equal = count(array_column($students, "indexNumber")) == count($listed = array_column($message, "indexNumber"));
                                        
                                        // merge with new data if they are not equal
                                        if(!$is_equal){
                                            $new_students = array_filter(array_map(function($student) use ($listed){
                                                foreach($listed as $listed_student){
                                                    if($listed_student == $student["indexNumber"]){
                                                        return null;
                                                    }
                                                }

                                                return array_merge($student, ["class_mark" => "0.0", "exam_mark" => "0.0", "mark" => 0]);
                                            }, $students));

                                            $message = array_merge($message, $new_students);
                                        }
                                    }
                                }
                            }elseif($results->num_rows == 1){
                                $message = [$results->fetch_assoc()];
                                $error = false;
                            }else{
                                $message = "This results has been either deleted or been changed";
                            }
                        }else{
                            $message = "There was an error that occured while parsing the problem";
                        }
                    } catch (\Throwable $th) {
                        $message = throwableMessage($th);
                    }
                }

                header("Content-Type: application/json");
                echo json_encode([
                    "error"=>$error ?? true,
                    "message"=>$message ?? "No message was returned"
                ]);

                break;
            
            case "get_pces":
                $token = $_GET["token"] ?? null;

                if(is_null($token) || empty($token)){
                    $message = "No token was provided";
                }else{
                    try {
                        $sql = "SELECT program_id, course_id, exam_year, semester FROM saved_results WHERE token='$token' LIMIT 1";
                        $result = $connect2->query($sql);

                        if($result->num_rows > 0){
                            $message = $result->fetch_assoc();
                            $error = false;
                        }else{
                            $message = "No data for this result list was found. Contact admin for help with token: $token";
                        }
                    } catch (\Throwable $th) {
                        $message = throwableMessage($th);
                    }
                }

                header("Content-Type: application/json");
                echo json_encode([
                    "error"=>$error ?? true,
                    "message"=>$message ?? "No response from the server"
                ]);
                break;
            
            case "confirm_box_response":
            case "confirm_box_response_ajax":
                $token = $_GET["token"] ?? null;
                $mode = strtolower($_GET["mode"]) ?? null;
                $modes = ["transfer", "delete"];

                if(is_null($token) || empty($token)){
                    $message = "Result token was not specified";
                }elseif(is_null($mode) || empty($mode)){
                    $message = "Mode of operation was not specified";
                }elseif(in_array($mode, $modes) === false){
                    $message = "Wrong mode was specified $mode";
                }else{
                    try {
                        if($mode == "transfer"){
                            $sql = "INSERT INTO saved_results(indexNumber, school_id, course_id, program_id, exam_type, class_mark, exam_mark, mark, teacher_id, exam_year, semester, token, from_reject, academic_year, save_date)
                                SELECT indexNumber, school_id, course_id, program_id, exam_type, class_mark, exam_mark, mark, teacher_id, exam_year, semester, result_token, 1, academic_year, NOW()
                                FROM results
                                WHERE result_token=?";
                            $stmt = $connect2->prepare($sql);
                            $stmt->bind_param("s", $token);
                        }elseif($mode == "delete"){
                            $sql = "DELETE FROM saved_results WHERE token = ?";
                            $stmt = $connect2->prepare($sql);
                            $stmt->bind_param("s", $token);
                        }

                        if($stmt->execute()){
                            //remove the rejected results from the results section
                            if($mode == "transfer"){
                                // Delete from results table
                                $stmt1 = $connect2->prepare("DELETE FROM results WHERE result_token = ?");
                                $stmt1->bind_param("s", $token);
                                $stmt1->execute();
                                $stmt1->close();

                                // Delete from recordapproval table
                                $stmt2 = $connect2->prepare("DELETE FROM recordapproval WHERE result_token = ?");
                                $stmt2->bind_param("s", $token);
                                $stmt2->execute();
                                $stmt2->close();

                                $message = "success";
                            }else{
                                $message = "success";
                            }
                        }else{
                            $message = "Token $token could not be processed";
                        }

                    } catch (\Throwable $th) {
                        $message = throwableMessage($th);
                    }
                }

                echo $message;

                break;
            
            case "delete_token":
                $token = $_GET["token"] ?? null;
                $table = $_GET["table"] ?? null;

                if(empty($token) || is_null($token)){
                    $message = "Please provide a token to continue";
                }else{
                    $sql = $table == "save" ? "DELETE FROM saved_results WHERE result_token=? AND from_reject=0" : "DELETE FROM results WHERE result_token=?";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("s", $token);
                    
                    if($stmt->execute()){
                        //remove from saved records if it finds itself there
                        $connect2->query("DELETE FROM recordapproval WHERE result_token='$token'");

                        $message = "success";
                    }else{
                        $message = "Error occured";
                    }
                }

                echo $message;

                break;

            case "change_password":
            case "change_password_ajax":
                try{
                    $email = $_POST["email"] ?? null;
                    $password = $_POST["password"] ?? null;
                    $password_c = $_POST["password_c"] ?? null;

                    if(is_null($email) || empty($email)){
                        $message = "No email was provided. Please provide an email";
                    }elseif(is_null($password) || empty($password)){
                        $message = "No new password was provided";
                    }elseif(is_null($password_c) || empty($password_c)){
                        $message = "Confirm password box cannot be empty";
                    }else{
                        //validate user
                        $user = fetchData1("teacher_id, email","teachers","email='$email'");

                        if(is_array($user)){
                            $password_o = fetchData1("user_password","teacher_login","user_id={$user["teacher_id"]}")["user_password"];

                            if(md5($password) == $password_o){
                                $message = "You cannot use the current password";
                            }else if(strtolower($password) != strtolower($password_c)){
                                $message = "Your passwords do not match. Please check and try again";
                            }else{
                                $password = md5($password);
                                $sql = "UPDATE teacher_login SET user_password=? WHERE user_id=?";
                                $stmt = $connect2->prepare($sql);
                                $stmt->bind_param("si",$password, $user["teacher_id"]);
                                
                                if($stmt->execute()){
                                    $message = "success";
                                }else{
                                    $message = "Process could not be completed. Please check and try again";
                                }
                            }
                        }else{
                            $message = "Email does not match any user in the database";
                        }
                    }
                }catch(\Throwable $th){
                    $message = throwableMessage($th);
                }
                
                echo $message;
                break;

            default:
                echo "Submission value has not been programmed. Value: $submit";
        }
    }else{
        echo "No submit request delivered. No operation is performed.";
    }
?>