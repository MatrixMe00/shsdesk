<?php 
include_once("../../includes/session.php");

//call autoload
require($rootPath."/PhpSpreadsheet/autoload.php");

//load IOFactory class
use PhpOffice\PhpSpreadsheet\IOFactory;

if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != null){
    $submit = $_REQUEST["submit"];

    $defaults = [
        "attendance_list" => [
            "first"=> "index number",
            "last"=> "total  attendance"
        ],
        "students_list" => [
            "first"=> "index number",
            "last"=> "guardian contact"
        ]
    ];

    if($submit == "upload_document" || $submit == "upload_document_ajax"){
        $document_type = $_REQUEST["document_type"] ?? null;

        if(empty($document_type) || is_null($document_type)){
            echo "Please select the document type";
            return;
        }

        if(isset($_FILES["document_file"]) && !empty($_FILES["document_file"]["tmp_name"])){
            //retrieve file extension
            $ext = strtolower(fileExtension("document_file"));

            if($ext == "xls" || $ext == "xlsx"){
                //identify file type Automatically
                $file_type = IOFactory::identify($_FILES["document_file"]["tmp_name"]);

                //create a reader
                $reader = IOFactory::createReader('Xlsx');

                //create a spreadsheet instance
                $spreadsheet = $reader->load($_FILES["document_file"]["tmp_name"]);
                
                //get the working sheet
                $sheet = $spreadsheet->getActiveSheet();
                
                //get maximum cell number
                $max_column = $sheet->getHighestDataColumn();
                $max_row = $sheet->getHighestDataRow();
                $row_start = 2;

                //required first and last cells
                $required_first = $defaults[$document_type]["first"];
                $required_last = $defaults[$document_type]["last"];

                //current document first and last cells
                $current_first1 = $sheet->getCell("A1");
                $current_first1 = $current_first1->getValue();
                $current_first2 = $sheet->getCell("A2");
                $current_first2 = $current_first2->getValue();

                $current_last1 = $sheet->getCell($max_column."1");
                $current_last1 = $current_last1->getValue();
                $current_last2 = $sheet->getCell($max_column."2");
                $current_last2 = $current_last2->getValue();

                $hasFirst = false; $hasLast = false;

                if((strtolower($current_first1) == $required_first) || (strtolower($current_first2) == $required_first)){
                    $hasFirst = true;
                    
                    if(strtolower($current_first2) == $required_first){
                        $row_start = 3;
                    }
                }

                if(((strtolower($current_last2) === $required_last) || strtolower($current_last1) === $required_last)){
                    $hasLast = true;
                    
                    if(strtolower($current_last2) == $required_last){
                        $row_start = 3;
                    }
                }

                // echo "Current: $current_last1 | $current_last2 | required: $required_last";


                if($hasFirst && $hasLast){
                    //create a general column header
                    $all_col_names = array("A","B","C","D","E","F",
                                            "G","H","I","J","K","L",
                                            "M","N","O","P","Q","R",
                                            "S","T","U","V","W","X",
                                            "Y","Z");
                    
                    //create columns for cell number operations
                    $current_col_names = array();

                    //count the headers
                    $headerCounter = 0;

                    //set a variable ready for values beyond z, aa - zz
                    $beyond_z = 0;

                    while($all_col_names[$headerCounter%count($all_col_names)]){
                        if($headerCounter <= count($all_col_names)){
                            $current_col_names[$headerCounter] = $all_col_names[$headerCounter%count($all_col_names)];

                            //break here
                            if($current_col_names[$headerCounter] == $max_column){
                                break;
                            }
                        }else{
                            $current_col_names[$headerCounter] = $all_col_names[$beyond_z].$all_col_names[$headerCounter%count($all_col_names)];
                            
                            //increment left label only if the right label is z
                            if($all_col_names[$headerCounter%count($all_col_names)] == end($all_col_names)){
                                $beyond_z++;
                            }

                            //break here
                            if($current_col_names[$headerCounter] == $max_column){
                                break;
                            }
                        }

                        $headerCounter++;
                    }

                    //function to check for merged cells
                    function checkMergedCells($sheet, $cell){
                        foreach ($sheet->getMergeCells() as $row){
                            if($cell->isInRange($row)){
                                //cell is merged
                                return "true";
                            }
                        }

                        //cell is by default defined as not merged
                        return "false";
                    }

                    if($document_type == "attendance_list"){
                        for($row=$row_start; $row <= $max_row; $row++){
                            $skip_row = 0;
                            
                            //grab columns
                            for($col = 0; $col <= $headerCounter; $col++){
                                $cellValue = $sheet->getCell($current_col_names[$col].$row)->getValue();
                                
                                switch ($col) {
                                    case 0:
                                        $skip_row = empty($cellValue) ? $skip_row + 1 : 0;
                                        $indexNumber = $cellValue;
                                        break;
                                    case 1:
                                        $skip_row = empty($cellValue) ? $skip_row + 1 : $skip_row - 1;
                                        break;
                                    case 3:
                                        $formLevel = intval($cellValue);
                                        break;
                                    case 4:
                                        $semester = intval($cellValue);
                                        break;
                                    case 5:
                                        $student_attendance = intval($cellValue);
                                        break;
                                    case 6:
                                        $attendance_total = intval($cellValue);
                                        break;  
                                    default:
                                        if($col > array_search($max_column, $current_col_names)){
                                            echo "Buffer count is beyond expected input count";
                                            exit(1);
                                        }
                                }

                                if($skip_row >= 2){
                                    break;
                                }
                            }

                            if($skip_row >= 2){
                                continue;
                            }

                            //insert into database
                            try {
                                $studentExist = fetchData1("indexNumber","attendance","indexNumber='$indexNumber' AND student_year=$formLevel AND semester=$semester");
                                if($studentExist == "empty"){
                                    $sql = "INSERT INTO attendance (school_id, indexNumber, student_year, semester,student_attendance, attendance_total, date)
                                        VALUES(?,?,?,?,?,?, NOW())";
                                    $stmt = $connect2->prepare($sql);
                                    $stmt->bind_param("isiiii",$user_school_id, $indexNumber, $formLevel, $semester, $student_attendance, $attendance_total);

                                    if($stmt->execute()){
                                        if($row == $max_row){
                                            echo "success";
                                        }
                                    }else{
                                        echo "An unforseen error found";
                                    }
                                }
                            } catch (\Throwable $th) {
                                echo $th->getMessage();
                            }
                        }
                    }elseif($document_type == "students_list"){
                        for($row=$row_start; $row <= $max_row; $row++){
                            $skip_row = 0;
                            //grab columns
                            for($col = 0; $col <= $headerCounter; $col++){
                                $cellValue = $sheet->getCell($current_col_names[$col].$row)->getValue();
                                
                                switch ($col) {
                                    case 0:
                                        $skip_row = empty($cellValue) ? $skip_row + 1 : 0;
                                        $indexNumber = empty($cellValue) ? generateIndexNumber($user_school_id) : $cellValue;
                                        break;
                                    case 1:
                                        $skip_row = empty($cellValue) ? $skip_row + 1 : $skip_row - 1;
                                        $Lastname = ucfirst($cellValue);
                                        break;
                                    case 2:
                                        $Othernames = ucwords($cellValue);
                                        break;
                                    case 3:
                                        $Gender = ucfirst($cellValue);
                                        if($Gender != "Male" && $Gender != "Female"){
                                            echo "Gender for <b>$indexNumber</b> was invalid. Process has been terminated";
                                            exit(1);
                                        }
                                        break;
                                    case 4:
                                        $studentYear = intval($cellValue);
                                        break;
                                    case 5:
                                        $house = strtolower($cellValue);
                                        $houseID = fetchData("id","houses","LOWER(title)='$house' AND schoolID=$user_school_id AND (gender='$Gender' OR gender='Both')");
                                        if(is_array($houseID)){
                                            $houseID = $houseID["id"];
                                        }else{
                                            $houseID = 0;
                                        }
                                        break;
                                    case 6:
                                        $boardingStatus = ucfirst($cellValue);
                                        if($boardingStatus !== "Boarder" && $boardingStatus !== "Day"){
                                            echo "Boarding Status for <b>$indexNumber</b> is invalid. Process execution has been terminated";
                                            exit(1);
                                        }
                                        break;
                                    case 7:
                                        $programme = formatName($cellValue);
                                        break;
                                    case 8:
                                        $program = strtolower($cellValue);
                                        $program_id = fetchData1("program_id","program","school_id=$user_school_id AND (program_name='$program' OR short_form='$program')");
                                        $program_id = is_array($program_id) ? $program_id["program_id"] : 0;
                                        break;
                                    case 9:
                                        if(empty($cellValue) || ctype_digit($cellValue) === false){
                                            $guardianContact = null;
                                        }else{
                                            $guardianContact = remakeNumber($cellValue, false, false);
                                        }
                                        break; 
                                    default:
                                        "Buffer count is beyond expected input count";
                                        exit(1);
                                }

                                if($skip_row >= 2){
                                    break;
                                }
                            }

                            if($skip_row >= 2){
                                continue;
                            }
    
                            try {
                                //check if index number exists and insert data into database
                                $index = fetchData1("indexNumber, program_id","students_table","indexNumber='$indexNumber'");
        
                                if($index == "empty"){
                                    $sql = "INSERT INTO students_table (indexNumber, Lastname, Othernames, Gender, houseID, school_id, studentYear, guardianContact, programme, program_id, boardingStatus)
                                        VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                                    $stmt = $connect2->prepare($sql);
                                    $stmt->bind_param("ssssiiissis",$indexNumber, $Lastname, $Othernames, $Gender, $houseID, $user_school_id, $studentYear,
                                        $guardianContact,$programme, $program_id, $boardingStatus);
                                    if($stmt->execute()){
                                        if($row == $max_row){
                                            echo "success";
                                        }
                                    }elseif(strtolower($boardingStatus) != "day" || strtolower($boardingStatus) != "boarder"){
                                        echo "Detail for <b>$indexNumber</b> not written. Boarding Status should either be Day or Boarder<br>";
                                    }elseif(strtolower($Gender) != "male" || strtolower($Gender) != "female"){
                                        echo "Detail for $indexNumber not written. Gender must either be Male or Female";
                                    }                                
                                }elseif($index["program_id"] == 0 || empty($index["program_id"])){
                                    $sql = "UPDATE students_table SET program_id=? WHERE indexNumber=?";
                                    $stmt = $connect2->prepare($sql);
                                    $stmt->bind_param("is",$program_id,$indexNumber);

                                    if($stmt->execute()){
                                        if($row == $max_row){
                                            echo "success";
                                        }
                                    }
                                }else{
                                    echo "Candidate with index number <b>$indexNumber</b> already exists. Candidate data was not written<br>";
                                }     
                            } catch (\Throwable $th) {
                                echo $th->getMessage();
                            }                          
                        }
                    }
                }else{
                    echo "The columns of this file cannot be found";
                    return;
                }

            }else{
                echo "Please provide a valid .xls or .xlsx excel file";
            }
        }else{
            echo "Please provide a file";
        }
    }else{
        echo "Submit not found";
    }
}
?>