<?php 
    include_once("../includes/session.php");

    $submit = $_REQUEST["submit"];

    if($submit === "report_search" || $submit === "stat_search" || $submit === "report_search_ajax" || $submit === "stat_search_ajax"){
        @$report_year = $_GET["report_year"];
        @$report_term = $_GET["report_term"];
        @$index_number = $_GET["index_number"];
        @$distinct = $_GET["result_distinct"] ?? false;

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

            if($distinct){$distinct = "DISTINCT(r.exam_type)";}
            else{$distinct = "r.exam_type";}

            $sql = "SELECT $distinct, c.course_name, r.mark 
                FROM results r JOIN courses c ON r.course_id=c.course_id
                WHERE indexNumber='$index_number' AND exam_year=$report_year AND semester=$report_term
                ORDER BY r.exam_type ASC";
            $query = $connect2->query($sql);

            if($query->num_rows > 0){
                $message["error"] = false;
                $message["message"] = $query->fetch_all(MYSQLI_ASSOC);
            }else{
                $message["error"] = false;
                $message["message"] = "Results for this period has not been uploaded yet";
                $message["message"] = [
                    array("exam_type" => "exam", "course_name" => "Mathematics", "mark" => rand(20, 85)),
                    array("exam_type" => "mock", "course_name" => "English Language", "mark" => rand(20, 85)),
                    array("exam_type" => "exam", "course_name" => "Physics", "mark" => rand(20, 85)),
                    array("exam_type" => "mock", "course_name" => "Biology", "mark" => rand(20, 85)),
                    array("exam_type" => "exam", "course_name" => "Chemistry", "mark" => rand(20, 85)),
                    array("exam_type" => "mock", "course_name" => "Geography", "mark" => rand(20, 85)),
                    array("exam_type" => "exam", "course_name" => "Economics", "mark" => rand(20, 85)),
                    array("exam_type" => "mock", "course_name" => "History", "mark" => rand(20, 85))
                ];
            }            
        }else{
            $message["error"] = true;
        }

        header("Content-Type: application/json");
        echo json_encode($message);
    }elseif($submit === "studentLogin" || $submit === "studentLogin_ajax"){
        $user = $_POST["indexNumber"];
        $password = MD5($_POST["password"]);
        $message = "";

        if(empty($user)){
            $message = "Please enter a username or index number";
        }elseif(empty($password)){
            $message = "Please enter a password";
        }else{
            $sql = "SELECT * FROM students_table WHERE indexNumber='$user' OR (username 
                IS NOT NULL AND username = '$user') OR (email IS NOT NULL AND email = '$user')";
            
            $query = $connect2->query($sql);

            if($query->num_rows > 0){
                $sql = "SELECT indexNumber FROM students_table WHERE (indexNumber='$user' OR (username 
                    IS NOT NULL AND username = '$user') OR (email IS NOT NULL AND email = '$user')) AND 
                    password = '$password'";
                $query = $connect2->query($sql);

                if($query->num_rows == 1){
                    $_SESSION["student_id"] = $query->fetch_assoc()["indexNumber"];
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
        @$course_id = $_GET["cid"];
        @$school_id = $_GET["sid"];
        @$student_index = $_GET["stud_index"];
        $error = true; $message = null; $return = array();

        if(empty($course_id)){
            $message = "No course or program has been provided for query";
        }elseif(empty($school_id)){
            $message = "Session has been timed out. Please refresh the page and login again";
        }elseif(empty($student_index)){
            $message = "No index number provided. Please check if you are logged in to continue";
        }else{
            try {
                $sql = "SELECT * FROM results WHERE indexNumber=? AND school_id=? AND course_id=?";
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
                    $message = "No results were found. Please speak with your administrator for help.";
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
    }
?>