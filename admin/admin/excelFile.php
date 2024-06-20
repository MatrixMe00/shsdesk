<?php 
    include_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");
    
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
    
    //message variables
    $error = false; $message = "";
    
    //sheet global variables
    $included_headers = [];
    $title = ""; $filename = "";
    
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
            $sql = "SELECT indexNumber, Lastname, Othernames, Gender, studentYear, houseID as houseName, boardingStatus, programme, program_id AS className, guardianContact
                FROM students_table WHERE school_id=$user_school_id";
            $additions = ["className", "programme"];
            
            if(strtolower($program_name) != "all"){
                $sql .= " AND programme='$program_name'";   
            }
            if(strtolower($program_year) != "all"){
                $sql .= " AND studentYear=$program_year";
            }

            if(strtolower($gender) != "all"){
                $sql .= " AND Gender='$gender'";
            }

            $sql .= " ORDER BY ". implode(", ", $additions) ." ASC";

            $results = $connect2->query($sql);
            
            if($results->num_rows < 1){
                $message = "There are no results for this filter";
                $error = true;
            }

            $title = "Students List";
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
            $sql = "SELECT indexNumber, Lastname, Othernames, studentYear as formLevel FROM students_table WHERE school_id=$user_school_id";

            if($program_id != "all"){
                $sql .= " AND program_id=$program_id";
            }
            if($program_year != "all"){
                $sql .= " AND studentYear=$program_year";
            }

            $sql .= " ORDER BY formLevel ASC";

            $results = $connect2->query($sql);

            if($results->num_rows < 1){
                $message = "There are no results for this filter";
                $error = true;
            }

            $included_headers = ["Semester","Attendance","Total Attendance"];
            $title = "Attendance List";
        }
    }elseif($submit == "get_enrolment_data"){
        $academic_year = reverseYearURL($_REQUEST["academic_year"]);
        $error = true;

        if(is_null($academic_year)){
            $message = "No Year value provided";
        }else{
            $sql = "SELECT e.indexNumber, e.lastname, e.othername, e.enrolCode, e.aggregateScore, e.program, 
                        e.gender, e.jhsName, e.jhsTown, e.jhsDistrict, e.birthdate, e.birthPlace, e.fatherName,
                        e.fatherOccupation, e.motherName, e.motherOccupation, e.guardianName, e.residentAddress,
                        e.postalAddress, e.primaryPhone, e.secondaryPhone, e.interest, e.award, e.position, e.witnessName,
                        e.witnessPhone, ho.title AS 'House Name'
                    FROM enrol_table e JOIN cssps c ON e.indexNumber = c.indexNumber
                    LEFT JOIN house_allocation h ON h.indexNumber = c.indexNumber
                    LEFT JOIN houses ho ON ho.id = h.houseID
                    WHERE e.shsID = $user_school_id AND c.academic_year = '$academic_year'";
            $results = $connect->query($sql);
            
            $title = "Student Records $academic_year";
            $error = false;
        }
    }

    $results = $results->fetch_all(MYSQLI_ASSOC);

    if(count($results) == 0){
        $message = "No results were returned. File not created";
        $error = true;
    }
    
    if($error === true){
        echo "Error: $message"; return;
    }

    $field_names = array_keys($results[0]);

    $current_col_names = array();
    $number_of_columns = count($field_names) + count($included_headers);

    $headerCounter = 0;

    if($number_of_columns <= count($all_col_names)){
        for($i=0; $i < $number_of_columns; $i++){
            $current_col_names[$i] = $all_col_names[$i];
        }
    }else{
        $temp_number_of_columns = $number_of_columns;
        for($i=0; $i < intval($number_of_columns / count($all_col_names))+1; $i++){
            if($temp_number_of_columns > count($all_col_names)){
                $ttl = count($all_col_names);
                $temp_number_of_columns -= count($all_col_names);
            }else{
                $ttl = $number_of_columns % count($all_col_names);
            }

            for($j=0; $j < $ttl; $j++){
                if($i > 0){
                    $current_col_names[$headerCounter] = $all_col_names[$i-1].$all_col_names[$j];
                }else{
                    $current_col_names[$headerCounter] = $all_col_names[$j];
                }
                $headerCounter++;
            }
        }
    }

    //merge cells to take current title
    $merged_cells = $current_col_names[0]."1:".end($current_col_names)."1";
    $sheet->mergeCells($merged_cells);

    //set title
    $sheet->setCellValue("A1", $title);
    $sheet->getStyle("A1")->getFont()->setBold(true);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');
    $sheet->getStyle("A1")->getAlignment()->setVertical('center');

    //enter headers
    if(count($included_headers) > 0){
        $field_names = array_merge($field_names, $included_headers);
    }

    foreach ($field_names as $row => $value){
        $cellName = $current_col_names[$row]."2";

        //enter value into cells
        $sheet->setCellValue($cellName, formatName(separateNames($value)));
    }

    //start line for next rows
    $row = 3;

    //content checking variables
    $form_level = 0;

    foreach($results as $result){
        if($submit == "student_list"){
            if(empty($program)){
                $program = $result["programme"];
            }elseif($program != $result["programme"]){
                $program = $result["programme"];
                ++$row;
            }
        }elseif($submit == "attendance_list"){
            if($form_level == 0){
                $form_level = $result["formLevel"];
            }elseif($form_level != $result["formLevel"]){
                //set new form level
                $form_level = $result["formLevel"];
                ++$row;
            }
        }

        foreach($field_names as $key=>$field_name){
            if(array_search($field_name, $included_headers) === false){
                $cellName = $current_col_names[$key].$row;
    
                //enter value into cells
                if($submit == "student_list"){
                    if($field_name == "houseName"){
                        $cellValue = fetchData("title","houses","id={$result[$field_name]}");
                        $cellValue = is_array($cellValue) ? $cellValue["title"] : "no house";
                        $sheet->setCellValueExplicit($cellName, ucwords($cellValue), "s");
                    }elseif($field_name == "className" && !empty($result[$field_name])){
                        $cellValue = fetchData1("program_name","program","program_id=$result[$field_name]");
                        $cellValue = is_array($cellValue) ? $cellValue["program_name"] : "";
                        $sheet->setCellValueExplicit($cellName, ucwords($cellValue), "s");
                    }elseif($field_name == "guardianContact"){
                        if(!empty($result["guardianContact"]) && !is_null($result["guardianContact"])){
                            $cellValue = remakeNumber($result["guardianContact"], false, false);
                        }else{
                            $cellValue = "";
                        }
                        
                        $sheet->setCellValueExplicit($cellName, $cellValue, "s");
                    }else{
                        $sheet->setCellValueExplicit($cellName, formatName($result[$field_name]), "s");
                    }
                }else{
                    $sheet->setCellValueExplicit($cellName, formatName($result[$field_name]), "s");
                }

                //enter the semester value for the attendance list
                if($submit == "attendance_list" && $field_name == "formLevel"){
                    $cellName = $current_col_names[($key + 1)].$row;
                    
                    $sheet->setCellValue($cellName, ucwords($semester));
                }
                
            }
        }

        $row++;
    }

    //automatically size up columns
    for($column = "A"; $column != $sheet->getHighestColumn(); $column++){
        $sheet->getColumnDimension($column)->setAutoSize(TRUE);
    }

    //create an IOFactory to ask for location for save file
    $writer = IOFactory::createWriter($spreadsheet, "Xlsx");

    //set header to accept excel
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    //define file name
    header("Content-Disposition: attachment;filename=\"$title.xlsx\"");

    header('Cache-Control: max-age=0');

    //save to php output
    $writer->save("php://output");
?>