<?php 
    require_once("../includes/session.php");

    //call autoload
    require("../PhpSpreadsheet/autoload.php");

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
                    "program_id=$program_id AND studentYear=$class_year",0
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
                for($i=0; $i < count($heading); $i++){
                    $current_col_names[$i] = $all_col_names[$i];
                }
    
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

        if(is_null($semester) || empty($semester)){
            $message = "Please select the semester";
        }

        if(isset($_FILES['document_file']) && $_FILES["document_file"]["tmp_name"] != NULL){
            //retrieve file extension
            $ext = strtolower(fileExtension("document_file"));

            if($ext == "xls" || $ext == "xlsx"){

            }else{
                $message = "Improper file format was received. Please provide an 'xls' or 'xlsx' file";
            }
        }else{
            $message = "No file has been selected. Please select one to proceed";
        }
    }
?>