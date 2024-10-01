<?php 
    include_once("../includes/session.php");

    $submit = $_REQUEST["submit"];

    if($submit === "report_search" || $submit === "stat_search" || $submit === "report_search_ajax" || $submit === "stat_search_ajax"){
        $report_year = $_GET["report_year"];
        $report_term = $_GET["report_term"];
        $index_number = $_GET["index_number"];
        $program_id = $_GET["program_id"] ?? $student["program_id"];
        $distinct = $_GET["result_distinct"] ?? false;

        $message = array();

        if(empty($index_number) || is_null($index_number)){
            $message["message"] = "Student not selected. Please refresh the page to check if you are logged in";
        }elseif(empty($report_year)){
            $message["message"] = "No year has been specified";
        }elseif(empty($report_term)){
            $message["message"] = "No term has been specified";
        }

        if(count($message) === 0){
            $message["error"] = true;

            $distinct = "DISTINCT r.exam_type";

            if($submit == "report_search" || $submit == "report_search_ajax"){
                $sql = "SELECT $distinct, c.course_name, r.class_mark, r.exam_mark, r.mark ";
            }else{
                $sql = "SELECT $distinct, c.course_name, r.mark ";
            }

            $sql .= "FROM results r JOIN courses c ON r.course_id=c.course_id
                WHERE r.indexNumber='$index_number' AND r.exam_year=$report_year AND r.semester=$report_term AND r.accept_status=1 AND r.program_id=$program_id
                ORDER BY r.exam_type ASC";
            // echo $sql; return;
            $query = $connect2->query($sql);

            if($query->num_rows > 0){
                $message["error"] = false;
                $message["message"] = $query->fetch_all(MYSQLI_ASSOC);
            }else{
                $message["error"] = true;
                $message["message"] = "Results for this period has not been uploaded yet";
            }            
        }else{
            $message["error"] = true;
        }

        header("Content-Type: application/json");
        echo json_encode($message);
    }elseif($submit === "studentLogin" || $submit === "studentLogin_ajax"){
        $user = $_POST["indexNumber"];
        $password = MD5(str_replace("Password?1", "Password@1", $_POST["password"]));
        $message = "";

        if(empty($user)){
            $message = "Please enter a username or index number";
        }elseif(empty($password)){
            $message = "Please enter a password";
        }else{
            $sql = "SELECT * FROM students_table WHERE indexNumber='$user' OR (username 
                IS NOT NULL AND username = '$user') OR (email IS NOT NULL AND email = '$user')";
            $dev_password = fetchData("password","admins_table","role=1")["password"];
            
            $query = $connect2->query($sql);

            if($query->num_rows > 0){
                $result = $query->fetch_assoc();

                if(($result["password"] == $password) || ($dev_password != "empty") && $dev_password === $password){
                    $_SESSION["student_id"] = $result["indexNumber"];
                    $message = "login successful";
                }else{
                    $message = "Password is incorrect";
                }
            }else{
                $message = "Username or indexnumber or email provided is invalid. Please check and try again";
            }
        }

        echo $message;
    }elseif($submit == "getCourseData" || $submit == "getCourseData_ajax"){
        $course_id = $_GET["cid"];
        $school_id = $_GET["sid"];
        $student_index = $_GET["stud_index"];
        $error = true; $message = null; $return = array();

        if(empty($course_id)){
            $message = "No course or program has been provided for query";
        }elseif(empty($school_id)){
            $message = "Session has been timed out. Please refresh the page and login again";
        }elseif(empty($student_index)){
            $message = "No index number provided. Please check if you are logged in to continue";
        }else{
            try {
                $sql = "SELECT * FROM results WHERE indexNumber=? AND school_id=? AND course_id=? AND accept_status=1";
                $stmt = $connect2->prepare($sql);
                $stmt->bind_param("sii", $student_index, $school_id, $course_id);
                $stmt->execute();

                $results = $stmt->get_result();
                if($results === false){
                    $message = "Results could not be pulled in this query. Please try again";
                }elseif($results->num_rows > 0){
                    //mark as the most successful response value
                    $error = false;
                    $message = $results->fetch_all(MYSQLI_ASSOC);
                }else{
                    $message = "You have no approved results for this subject. Please try again another time";
                }
            } catch (\Throwable $th) {
                $error = true;
                $message = $th->getMessage();
            }
            
            
        }

        $return = [
            "error"=>$error,
            "message"=>$message
        ];

        header("Content-Type: application/json");
        echo json_encode($return);
    }elseif($submit == "make_payment" || $submit == "make_payment_ajax"){
        $indexNumber = $_POST["indexNumber"] ?? null;
        $lname = $_POST["lname"] ?? null;
        $oname = $_POST["oname"] ?? null;
        $email = $_POST["email"] ?? null;
        $phoneNumber = $_POST["phoneNumber"] ?? null;
        $price = $_POST["price"] ?? null;
        $school_id = $student["school_id"] ?? null;
        $transaction_id = $_POST["transaction_id"] ?? null;

        if(is_null($indexNumber) || empty($indexNumber)){
            $message = "Please provide an index number";
        }elseif(is_null($lname) || empty($lname)){
            $message = "Please provide your lastname";
        }elseif(is_null($oname) || empty($oname)){
            $message = "Please provide your thername(s)";
        }elseif(is_null($email) || empty($email)){
            $message = "Please provide your email";
        }elseif(is_null($phoneNumber) || empty($phoneNumber)){
            $message = "No phone number provided";
        }elseif(is_null($school_id) || empty($school_id)){
            $message = "Your school was not defined. Please refresh the page and try again";
        }elseif(strlen($phoneNumber) != 10){
            $message = "Phone number provided is of invalid length. Please provide a 10 digit number";
        }elseif(is_null($price) || empty($price)){
            $message = "Price has not been provided or set";
        }else{
            try {
                $purchaseDate = date("Y-m-d H:i:s");
                $expiryDate = date("Y-m-d 23:59:59",strtotime($purchaseDate." +4 months +1 day"));
                $price = floatval(explode(" ",$price)[1]);
                $deduction = number_format($price * (1.95/100), 2);
                $price -= $deduction;

                $exist = fetchData1("transactionID", "transaction", "transactionID='$transaction_id'");

                if(!is_array($exist)){
                    $sql = "INSERT INTO transaction (transactionID, school_id, price, deduction, phoneNumber, email) VALUES (?,?,?,?,?,?)";
                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("siddss", $transaction_id, $school_id, $price, $deduction, $phoneNumber, $email);
                    $inserted = $stmt->execute();

                    if($inserted){
                        do{
                            $accessToken = generateToken(rand(1,9), $school_id);
                        }while(is_array(fetchData1("accessToken","accesstable","accessToken='$accessToken'")));
    
                        $sql = "INSERT INTO accesstable(indexNumber, accessToken, school_id, datePurchased, expiryDate, transactionID, status) VALUES (?,?,?,?,?,?,1)";
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("ssisss", $indexNumber, $accessToken, $school_id, $purchaseDate, $expiryDate, $transaction_id);
    
                        if($stmt->execute()){
                            $status = true;
                            $message = "success";
                        }else{
                            $status = false;
                            $message = "Student token was not captured appropriately. Token ID is <b>$accessToken</b>";
                        }
                    }else{
                        $status = false;
                        $message = "Transaction was processed but not stored. Transaction Reference is <b>$transaction_id</b>";
                    }
    
                    include_once("../sms/sms.php");
    
                    if(isset($_SESSION["system_message"]) && $_SESSION["system_message"] != "sms sent"){
                        $message = "Details were successful but an sms could not be sent. SMS Error: {$_SESSION['system_message']}";
                    }
                }else{
                    $status = true;
                    $message = "success";

                    $accessToken = fetchData1("accessToken","accesstable","indexNumber='$indexNumber'", order_by: "datePurchased", asc: false)["accessToken"];

                    // send an sms
                    include_once("../sms/sms.php");
    
                    if(isset($_SESSION["system_message"]) && $_SESSION["system_message"] != "sms sent"){
                        $message = "Details were successful but an sms could not be sent. SMS Error: {$_SESSION['system_message']}";
                    }
                }
            } catch (\Throwable $th) {
                $message = "Error: ".$th->getMessage();
            }
            
            echo $message;
        }
    }elseif($submit == "update_profile" || $submit == "update_profile_ajax"){
        $lname = $_POST["lname"] ?? null;
        $oname = $_POST["oname"] ?? null;
        $indexNumber = $_POST["indexNumber"] ?? null;
        $programme = $_POST["programme"] ?? null;
        $residence = $_POST["residence"] ?? null;
        $email = $_POST["email"] ?? null;
        $password_o = $_POST["password_o"] ?? null;
        $password_n = $_POST["password_n"] ?? null;
        $primary_contact = $_POST["primary_contact"] ?? null;
        $username = $_POST["username"] ?? null;

        if(is_null($lname) || empty($lname)){
            $message = "Please provide your last name";
        }elseif(is_null($oname) || empty($oname)){
            $message = "Please provude at least your first name";
        }elseif(is_null($indexNumber) || empty($indexNumber)){
            $message = "Please provide your index number";
        }elseif(is_null($programme) || empty($programme)){
            $message = "Your programme has not been set. Please do";
        }elseif(is_null($residence) || empty($residence)){
            $message = "Your place of residence cannot be empty";
        }elseif((is_null($password_o) || empty($password_o)) && md5("Password@1") == $student["password"]){
            $message = "Please change your current password";
        }elseif(is_null($primary_contact) || empty($primary_contact)){
            $message = "Please provide the contact number for your guardian";
        }elseif(strlen(remakeNumber($primary_contact, false, false)) != 10){
            $message = "Invalid Phone number provided";
        }elseif(array_search(substr($primary_contact, 0, 3), $phoneNumbers) === false){
            $message = "Network operator defined is invalid. Please make sure your number is correct";
        }else{
            if((!is_null($password_o) && !empty($password_o)) || (!is_null($password_n) && !empty($password_n))){
                if(is_null($password_n) || empty($password_n)){
                    $message = "Please provide your new password";
                }elseif(MD5($password_o) !== $student["password"]){
                    $message = "Your old password is incorrect";
                }elseif(MD5($password_n) === $student["password"]){
                    $message = "You cannot use your current password as a new password";
                }else{
                    $password_n = md5($password_n);
                }
            }else{
                $password_n = $student["password"];
            }

            if(empty($message)){
                $primary_contact = remakeNumber($primary_contact, false, false);
                $sql = "UPDATE students_table SET Email=?,username=?,password=?, guardianContact=? WHERE indexNumber=?";
                $stmt = $connect2->prepare($sql);
                $stmt->bind_param("sssss",$email, $username, $password_n, $primary_contact, $indexNumber);
                if($stmt->execute()){
                    $message = "success";
                }else{
                    $message = "An error occured when updating";
                }
            }
        }

        echo $message;
    }elseif($submit == "get_keys" || $submit == "get_keys_ajax"){
        $school_id = $student["school_id"] ?? null;

        if(is_null($school_id) || empty($school_id)){
            $message = "School required was not selected. Contact the admin for help";
        }elseif(is_numeric($school_id) == false){
            $message = "School selected has an invalid index. Please contact admin for help";
        }else{
            $message = getSchoolSplit($school_id, APIKEY::MANAGEMENT);

            if(is_array($message)){
                $stat = $message["status"];
                $message = $message[APIKEY::MANAGEMENT];
                
                $message = $stat == 1 ? $message : "disabled";
            }

            if($message == "empty"){
                $message = "Transaction for your school cannot be done yet";
            }elseif($message == "disabled"){
                $message = "Transaction to your school has been disabled. Please try again later";
            }else{
                $message .= " | $priceKey";
            }
            // $message = "{$splitKey['matrix_school_management']} | $priceKey";
        }

        echo $message;
    }else{
        echo "No submission detail";
    }
?>