<?php 
    include_once("../../includes/session.php");
    
    //call autoload
    require($rootPath."/PhpSpreadsheet/autoload.php");

    if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != null){
        $submit = $_REQUEST["submit"];
    }else{
        echo "Invalid request received";
        exit();
    }

    //load spreadsheet class
    use PhpOffice\PhpSpreadsheet\Spreadsheet;

    //load IOFactory class
    use PhpOffice\PhpSpreadsheet\IOFactory;

    //make a spreadsheet object
    $spreadsheet = new Spreadsheet();

    //select first sheet
    $sheet = $spreadsheet->getActiveSheet();

    //create a general column header
    $all_col_names = array("A","B","C","D","E","F",
                      "G","H","I","J","K","L",
                      "M","N","O","P","Q","R",
                      "S","T","U","V","W","X",
                      "Y","Z");
    $error = false; $message = "";
    
    if($submit == "student_list"){
        $program_name = $_REQUEST["program_name"] ?? null;
        $program_year = $_REQUEST["program_year"] ?? null;
        $gender = $_REQUEST["gender"] ?? null;

        if(empty($program_name) || is_null($program_name)){
            $message = "No program name provided. Please provide one to continue";
            $error = true;
        }elseif(empty($program_year) || is_null($program_year)){
            $message = "Please provide the year of the programme";
            $error = true;
        }elseif(empty($gender) || is_null($gender)){
            $message = "Please provide the gender(s) involved";
            $error = true;
        }else{
            $sql = "SELECT indexNumber, Lastname, Othernames, Gender, studentYear, houseID, boardingStatus, programme, program_id AS `Class Name` 
                FROM students_table WHERE school_id=$user_school_id";
            if(strtolower($program_name) != "all"){
                $sql .= " AND programme='$program_name'";   
            }
            if(strtolower($program_year) != "all"){
                $sql .= " AND studentYear=$program_year";
            }

            if(strtolower($gender) != "all"){
                $sql .= " AND Gender='$gender'";
            }

            $sql .= " ORDER BY programme ASC";

            $results = $connect2->query($sql);
            
            if($results->num_rows < 1){
                $message = "There are no results for this filter";
                $error = true;
            }
        }
    }elseif($submit == "attendance_list"){
        $program_id = $_REQUEST["program_id"] ?? null;
        $program_year = $_REQUEST["program_year"] ?? null;
        $semester = $_REQUEST["semester"] ?? null;

        if(empty($program_id) || is_null($program_id)){
            $message = "No class has been selected";
            $error = true;
        }elseif(empty($program_year) || is_null($program_year)){
            $message = "Please select the class' year level";
            $error = true;
        }elseif(empty($semester) || is_null($semester)){
            $message = "Please select the semester";
            $error = true;
        }else{
            $sql = "SELECT indexNumber FROM students_table WHERE school_id=$user_school_id";
        }
    }

    if($error === true){
        echo $message; exit(1);
    }
?>