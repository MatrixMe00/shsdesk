<?php 
    require_once("../includes/session.php");

    //call autoload
    require("../PhpSpreadsheet/autoload.php");

    if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != null){
        $submit = $_REQUEST["submit"];
    }else{
        if(isset($_REQUEST["response_type"]) && $_REQUEST["response_type"] == "json"){
            header("Content-type: application/json");
            exit(json_encode([
                "status" => $status ?? false, "data" => "Invalid request received"
            ]));
        }else{
            exit("Invalid request received");
        }
        exit("Invalid request received");
    }

    //load spreadsheet class

use PhpOffice\PhpSpreadsheet\Cell\DataType;
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
    
    if($submit == "get_class_list" || $submit == "get_class_list_ajax"){
        $program_id = $_POST["program"] ?? null;
        $course_id = $_POST["course"] ?? null;
        $class_year = $_POST["class_year"] ?? null;

        try{
            if(is_null($program_id) || empty($program_id)){
                $message = "Please select the class";
            }elseif(is_null($course_id) || empty($course_id)){
                $message = "Please select the subject";
            }elseif(is_null($class_year) || empty($class_year)){
                $message = "Please select the year";
            }else{
                $students = decimalIndexArray(fetchData1(
                    "indexNumber, CONCAT(Lastname,' ',Othernames) as fullname",
                    "students_table",
                    "program_id=$program_id AND studentYear=$class_year",0, order_by: ["Lastname", "Othernames", "indexNumber"]
                ));
                
                //reject if
                if(!$students){
                    $message = "Error: No students were found with the specified filter";
                }
                
                if(is_array($class_name = fetchData1("program_name","program","program_id=$program_id"))){
                    $class_name = $class_name["program_name"];
                }else{
                    $message = "Error: Selected class not identified.";
                }
    
                //stop execution if there is an error
                if(!empty($message)){
                    echo $message; exit();
                }
    
                $heading = [
                    "Index Number","Full Name", "Class Mark (30)", "Exam Score (70)", "Total Score"
                ];
    
                //get the column names
                $current_col_names = createColumnHeader(count($heading));
    
                //enter headers
                $headerCounter = 0;
                foreach ($heading as $row => $value){
                    $cellName = $current_col_names[$headerCounter]."1";
    
                    //enter value into cells
                    $sheet->setCellValue($cellName, formatName(separateNames($value)));
    
                    //move to next header
                    $headerCounter++;
                }
    
                //fill the sheet with student data
                $rowCounter = 2;
                foreach($students as $student){
                    for($count = 0; $count < count($current_col_names); $count++){
                        $cellName = $current_col_names[$count].$rowCounter;
    
                        //set values
                        switch($count){
                            case 0:
                                $value = $student["indexNumber"];
                                break;
                            case 1:
                                $value = $student["fullname"];
                                break;
                            case 4:
                                // insert a formula into the total cell
                                $c_cell = $current_col_names[2].$rowCounter;
                                $e_cell = $current_col_names[3].$rowCounter;
                                $value = "=IF(OR($c_cell > 30, $c_cell < 0), \"class mark is invalid\", IF(OR($e_cell > 70, $e_cell < 0), \"exam mark is invalid\", $c_cell + $e_cell))";
                                break;
                            default:
                                $value = 0;
                        }
    
                        //pass values into sheet's cells
                        $sheet->setCellValue($cellName, $value);
                    }
    
                    //push row
                    $rowCounter++;
                }
    
                //automatically size up columns
                for($column = "A"; $column <= $sheet->getHighestColumn(); $column++){
                    $sheet->getColumnDimension($column)->setAutoSize(TRUE);
                }
    
                $filename = "$class_name - Class List - Year $class_year";
    
                //set header to accept excel
                header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    
                //define file name
                header("Content-Disposition: attachment;filename=\"$filename.xlsx\"");
    
                //create an IOFactory to ask for location for save file
                $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
    
                //save to php output
                $writer->save("php://output");
            }

            //stop execution if there is an error
            if(!empty($message)){
                echo "Error: $message"; exit();
            }
        }catch(\Throwable $th){
            $message = "Error: ";
            if($developmentServer){
                $message .= $th->getTraceAsString();
            }else{
                $message .= $th->getMessage();
            }
        }
    }elseif($submit == "upload_result" || $submit == "upload_result_ajax"){
        $semester = $_POST["semester"] ?? null;
        $academic_year = formatAcademicYear($_POST["academic_year"] ?? null, false) ?? getAcademicYear(now(), false);
        $program_id = $_POST["program"] ?? null;
        $course_id = $_POST["course"] ?? 0;
        $class_year = $_POST["class_year"] ?? 0;

        if(is_null($semester) || empty($semester)){
            $message = "Please select the semester";
        }elseif(empty($program_id)){
            $message = "Class has not been selected";
        }elseif(empty($course_id)){
            $message = "Subject has not been selected";
        }elseif(empty($class_year)){
            $message = "Form year has not been selected";
        }else{
            if(isset($_FILES['document_file']) && $_FILES["document_file"]["tmp_name"] != NULL){
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
    
                    $acceptable = ["E"];
    
                    //check if right file is sent
                    if($max_column == "E"){
                        //grab the detail in the first cell
                        $cell =  $sheet->getCell("A1");
                        $cellValue = $cell->getValue();
    
                        if(strtolower($cellValue) != "index number" && str_contains(strtolower($cellValue), "index") === false){
                            $cell =  $sheet->getCell("A2");
                            $cellValue = $cell->getValue();
    
                            $row_start = 3;
    
                            if(strtolower($cellValue) != "index number" || !str_contains($cellValue, "index")){
                                //display error if the first column isnt the index number
                                $message = "First column not identified as 'index number'. Please follow the format directed in the documents above";
                            }
                        }
                    }elseif(!in_array($max_column, $acceptable)){
                        $message = "Invalid file detected. Please send the correct file to continue";
                    }

                    $teacher_data_valid = decimalIndexArray(fetchData1("id", "teacher_classes", ["teacher_id={$teacher['teacher_id']}", "program_id=$program_id", "course_id=$course_id", "class_year=$class_year"], where_binds: "AND"));
                    $result_exists = decimalIndexArray(fetchData1("result_token","recordapproval", ["teacher_id={$teacher['teacher_id']}", "program_id=$program_id", "course_id=$course_id", "exam_year=$class_year", "semester=$semester", "academic_year='$academic_year'"], where_binds: "AND"));
                    
                    if(!$teacher_data_valid){
                        $message = "Teacher does not seem to teach this class or form level";
                    }elseif($result_exists){
                        $message = "$academic_year Semester $semester results already exist for the selected class and form";
                    }else{
                        // retrieve all records
                        $records = [];
                        $current_col_names = createColumnHeader(5);
                        $keys = ["student_index", "student_name", "class_mark", "exam_mark", "mark"];

                        for($row = $row_start; $row <= $max_row; $row++){
                            $record = [];
                            for($col = 0; $col < count($current_col_names); $col++){
                                $record[] = $sheet->getCell($current_col_names[$col].$row)->getCalculatedValue();
                            }

                            $records[] = array_combine($keys, $record);
                        }

                        $message = [
                            "records" => $records,
                            "program_id" => $program_id,
                            "course_id" => $course_id, "semester" => $semester,
                            "exam_year" => $class_year, "academic_year" => $academic_year
                        ];
                        $status = true;
                    }
                }else{
                    $message = "File extension '$ext' is not accepted";
                }
            }
        }

        header("Content-type: application/json");
        echo json_encode([
            "status" => $status ?? false, "data" => $message ?? "No file uploaded"
        ]);
    }else{
        if(isset($_REQUEST["response_type"]) && $_REQUEST["response_type"] == "json"){
            header("Content-type: application/json");
            echo json_encode([
                "status" => false, "data" => "Submission value '$submit' not found"
            ]);
        }else{
            echo "Submission value '$submit' not found";
        }
    }
?>
<?php close_connections() ?>