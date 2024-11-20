<?php 
include_once("../includes/session.php");

if(isset($_REQUEST["submit"])){
    $submit = $_REQUEST["submit"];

    if($submit == "getPrograms"){
        $school_id = $_REQUEST["school_id"] ?? null;

        if(is_null($school_id) || empty($school_id)){
            $response = "School was not specified";
        }else{
            $response = fetchData("DISTINCT programme","cssps","schoolID=$school_id",0);

            if(!is_array($response)){
                if($response == "empty"){
                    $response = "No student programs have been uploaded yet. Please try again later";
                }
            }else{
                $response = numericIndexArray($response);
                $success = true;
            }
        }

        $response = [
            "response" => $response, "success"=> $success ?? false
        ];
        header("Content-Type: application/json");
        echo json_encode($response);
    }elseif($submit == "getClasses"){
        $school_id = $_REQUEST["school_id"] ?? null;
        $program = $_REQUEST["program"] ?? null;

        if(is_null($school_id) || empty($school_id)){
            $response = "No school has been selected";
        }elseif(is_null($program) || empty($program)){
            $response = "Select the programme to proceed";
        }else{
            $programs = fetchData1(
                "DISTINCT s.program_id, p.program_name",
                "students_table s JOIN program p ON s.program_id=p.program_id",
                "s.programme='$program' AND s.school_id=$school_id", 0
            );

            if(is_array($programs)){
                $response = decimalIndexArray($programs);
                $success = true;
            }else{
                if($programs == "empty"){
                    $response = "No student has been assigned classes yet for this programme";
                }else{
                    $response = $programs;
                }
            }
        }

        $response = [
            "response"=>$response, "success"=>$success ?? false
        ];
        header("Content-Type: application/json");
        echo json_encode($response);
    }elseif($submit == "getStudents"){
        $program_id = $_GET["program_id"] ?? null;
        $year_id = $_GET["year_id"] ?? null;

        if(is_null($program_id) || empty($program_id)){
            $response = "Class was not selected";
        }elseif(is_null($year_id) || empty($year_id)){
            $response = "Invalid year value provided";
        }else{
            $students = fetchData1(
                "indexNumber, CONCAT(Lastname,' ',Othernames) as fullname",
                "students_table",
                "program_id=$program_id AND studentYear=$year_id", 0
            );

            if(is_array($students)){
                $response = decimalIndexArray($students);
                $success = true;
            }else{
                if($students == "empty"){
                    $response = "No student data fits the filter";
                }else{
                    $response = $students;
                }                
            }
        }
        $response = ["response"=>$response, "success"=>$success ?? false];

        header("Content-Type: application/json");
        echo json_encode($response);
    }elseif($submit == "get_results" || $submit == "get_results_ajax"){
        $index_number = $_REQUEST["index_number"] ?? null;
        $mode = $_REQUEST["mode"] ?? null;
        $school = $_REQUEST["school"] ?? null;
        $program = $_REQUEST["program"] ?? null;
        $class = $_REQUEST["class"] ?? null;
        $program_year = $_REQUEST["program_year"] ?? null;

        if(is_null($index_number) || empty($index_number)){
            $message = "Index Number not found. Please provide one";
        }elseif(is_null($mode) || empty($mode)){
            $message = "Form mode was not confirmed. Please refresh the page and try again";
        }elseif($mode != "filter" && $mode != "index"){
            $message = "Untrusted form mode identified";
        }elseif($mode == "filter" && (is_null($school) || empty($school))){
            $message = "Please select the school";
        }elseif($mode == "filter" && (is_null($program) || empty($program))){
            $message = "Please select the programme of study";
        }elseif($mode == "filter" && (is_null($class) || empty($class))){
            $message = "Select the class of the student";
        }elseif($mode == "filter" && (is_null($program_year) || empty($program_year))){
            $message = "Please select a year";
        }else{
            $student = fetchData1("count(indexNumber) as total", "students_table","indexNumber='$index_number'")["total"];
            
            if($student == 1){
                $message = "success";
                $_SESSION["student_id"] = $index_number;
            }else{
                $message = "Student not found. Please check the index number and try again";
            }
        }

        echo $message;
    }elseif($submit === "report_search" || $submit === "stat_search" || $submit === "report_search_ajax" || $submit === "stat_search_ajax"){
        $report_year = $_GET["report_year"];
        $report_term = $_GET["report_term"];
        $index_number = $_GET["index_number"];
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

            // if($distinct){$distinct = "DISTINCT r.exam_type";}
            // else{$distinct = "r.exam_type";}
            $distinct = "DISTINCT r.exam_type";

            if($submit == "report_search" || $submit == "report_search_ajax"){
                $sql = "SELECT $distinct, c.course_name, r.class_mark, r.exam_mark, r.mark ";
            }else{
                $sql = "SELECT $distinct, c.course_name, r.mark ";
            }

            $sql .= "FROM results r JOIN courses c ON r.course_id=c.course_id
                WHERE indexNumber='$index_number' AND exam_year=$report_year AND semester=$report_term AND accept_status=1
                ORDER BY r.exam_type ASC";
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

                $sql = "INSERT INTO transaction (transactionID, school_id, price, deduction, phoneNumber, email) VALUES (?,?,?,?,?,?)";
                $stmt = $connect2->prepare($sql);
                $stmt->bind_param("siddss", $transaction_id, $school_id, $price, $deduction, $phoneNumber, $email);

                if($stmt->execute()){
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

                if(isset($_SESSION["system_message"]) && $_SESSION["system_message"] != ""){
                    $message = "Details were successful but an sms could not be sent. SMS Error: {$_SESSION['system_message']}";
                }
            } catch (\Throwable $th) {
                $message = "Error: ".$th->getMessage();
            }
            
            echo $message;
        }
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
        echo "No submit of value '$submit' has been programmed";
    }
}else{
    echo "No submit request was made";
}
close_connections();
?>