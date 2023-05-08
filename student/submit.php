<?php 
    include_once("../includes/session.php");

    $submit = $_REQUEST["submit"];

    if($submit === "stat_search" || $submit === "stat_search_ajax"){
        @$stat_year = $_GET["stat_year"];
        @$stat_term = $_GET["stat_term"];
        @$student_id = $_GET["student_id"];

        $message = array();

        if(empty($stat_year)){
            $message["message"] = "No year has been specified";
        }elseif(empty($stat_term)){
            $message["message"] = "No term has been specified";
        }elseif(empty($student_id)){
            $message["message"] = "No student identified or specified";
        }

        if(count($message) === 0){
            $sql = "SELECT r.*, c.course_name FROM results r JOIN courses c
                ON r.course_id = c.course_id 
                WHERE r.indexNumber='$student_id' AND r.exam_year=$stat_year AND r.semester=$stat_term";
            $query = $connect2->query($sql);

            if($query->num_rows > 0){
                $message["error"] = false;
                $message["message"] = $query->fetch_all(MYSQLI_ASSOC);
            }else{
                $message["error"] = true;
                $message["message"] = "Sorry, no results for the selected year or semester";
            }
        }else{
            $message["error"] = true;
        }

        header("Content-Type: application/json");
        echo json_encode($message);
    }elseif($submit === "report_search" || $submit === "report_search_ajax"){
        @$report_year = $_GET["report_year"];
        @$report_term = $_GET["report_term"];

        $message = array();

        if(empty($report_year)){
            $message["message"] = "No year has been specified";
        }elseif(empty($report_term)){
            $message["message"] = "No term has been specified";
        }

        if(count($message) === 0){
            $message["error"] = false;
            $message["message"] = "current requirements passed";
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