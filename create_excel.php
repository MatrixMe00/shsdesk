<?php 
    require_once("includes/session.php");

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

    //sql statement
    if($submit == "enrolment"){
        $sql = "SELECT e.*, s.schoolName, h.title AS houseName, c.boardingStatus
            FROM enrol_table e JOIN schools s
            ON e.shsID = s.id
            JOIN house_allocation ho
            ON e.indexNumber = ho.indexNumber
            JOIN houses h
            ON h.id = ho.houseID
            JOIN cssps c
            ON e.indexNumber = c.indexNumber
            WHERE e.shsID=$user_school_id AND c.current_data=1
            ORDER BY e.enrolDate ASC";

            //exception headers, end it with an empty field
            $exception_headers = array("shsID","transactionID","schoolName","current_data");

            //title
            $filename = "Enrolment Details | ";
    }elseif($submit == "houses"){
        $sql = "SELECT h.indexNumber, h.studentLname AS lastname, h.studentOname as `othername(s)`, h.studentYearLevel, h.studentGender, h.boardingStatus, h1.title AS `house name`
        FROM house_allocation h JOIN houses h1
        ON h.houseID=h1.id
        WHERE h.schoolID=$user_school_id AND h.current_data=1
        ORDER BY h.boardingStatus DESC, h.houseID ASC, h.studentGender ASC";

        $exception_headers = array("");

        $filename = "House Allocation | ";
    }elseif($submit == "exeat"){
        $sql = "SELECT c.Lastname, c.Othernames, e.*,  h.title AS house 
        FROM exeat e JOIN cssps c
        ON e.indexNumber = c.indexNumber
        JOIN houses h
        on e.houseID = h.id
        WHERE e.school_id= $user_school_id
        ORDER BY e.houseID ASC";

        $exception_headers = array("id","houseID","school_id","");

        $filename = "Exeat Report | ";
    }

    //complete file name
    $filename .= getSchoolDetail($user_school_id)["schoolName"];
    
    $query = $connect->query($sql);

    //generate nothing if there are no rows
    if($query->num_rows <= 0){
        echo "There are no results to be displayed. No Document was generated";
        exit(1);
    }

    //take field names
    $field_names = array_keys($query->fetch_assoc());

    $current_col_names = array();

    $headerCounter = 0;
    
    //Take number of columns
    $number_of_columns = count($field_names) - (count($exception_headers) - 1);

    //fill current columns with column names
    $current_col_names = createColumnHeader($number_of_columns);

    //Provide a title
    if($submit == "enrolment"){
        $title = strtoupper("List of Enroled Students");
    }elseif($submit == "houses"){
        $title = strtoupper("House List of Enroled Students");
    }elseif($submit == "exeat"){
        $title = strtoupper("Exeat records in this year");
    }

    //merge cells to take school name
    $merged_cells = $current_col_names[0]."1:".end($current_col_names)."1";
    $sheet->mergeCells($merged_cells);

    //counter resets
    $exceptionCounter = 0;
    $headerCounter = 0;
    
    //*******************************************
    //              DATA ENTRY POINT            *
    //*******************************************
    //set title
    $sheet->setCellValue("A1", $title);
    $sheet->getStyle("A1")->getFont()->setBold("true");
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');
    $sheet->getStyle("A1")->getAlignment()->setVertical('center');

    //enter headers
    foreach ($field_names as $row => $value){
        if($value != $exception_headers[$exceptionCounter]){
            $cellName = $current_col_names[$headerCounter]."2";

            //enter value into cells
            $sheet->setCellValue($cellName, formatName(separateNames($value)));

            //move to next header
            $headerCounter++;
        }else{
            $exceptionCounter++;
        }
    }

    //reset counters
    $exceptionCounter = 0;
    $headerCounter = 0;
    $rowCounter = 3;

    //send value into selected cells
    $query = $connect->query($sql);
    
    //variable to check a house break
    $house_break = ""; $date_break = "";
    while($result=$query->fetch_assoc()){
        if($submit == "houses"){
            //check the former house name and provide space where the need be
            if($house_break != "" && $house_break != $result["house name"]){
                ++$rowCounter;
                
                //merge cells to take house title
                $merged_cells = $current_col_names[0].$rowCounter.":".end($current_col_names).$rowCounter;
                $sheet->mergeCells($merged_cells);
                
                //give a title
                $title = strtoupper("Members in ".$result["house name"]." [". $result["boardingStatus"]."]");
                $sheet->setCellValue("A".$rowCounter, $title);
                
                //format to center
                $sheet->getStyle("A".$rowCounter)->getAlignment()->setHorizontal('center');
                $sheet->getStyle("A".$rowCounter)->getAlignment()->setVertical('center');
                
                //next line
                ++$rowCounter;
            }elseif($house_break == ""){
                ++$rowCounter;
                //merge cells to take house title
                $merged_cells = $current_col_names[0]."4:".end($current_col_names)."4";
                $sheet->mergeCells($merged_cells);
                
                //give a title
                $title = strtoupper("Members in ".$result["house name"]." [". $result["boardingStatus"]."]");
                $sheet->setCellValue("A4", $title);
                
                //format to center
                $sheet->getStyle("A4")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("A4")->getAlignment()->setVertical('center');
                
                //next row
                ++$rowCounter;
            }
            
            //take house of current student
            $house_break = $result["house name"];
        }elseif($submit == "exeat" && $query->num_rows > 1){
            //check former house
            if($house_break != "" && $house_break != $result["house"]){
                ++$rowCounter;
                
                //merge cells to take create a space
                $merged_cells = $current_col_names[0].$rowCounter.":".end($current_col_names).$rowCounter;
                $sheet->mergeCells($merged_cells);
                
                //next line
                ++$rowCounter;

                $house_break = $result["house"];
            }
        }

        //specify if student has returned or not in exeat table
        if($submit == "exeat"){
            if($result["returnStatus"]){
                $result["returnStatus"] = "Returned";
            }else{
                $result["returnStatus"] = "Not Returned";
            }
        }

        //divide enroled candidates into months and years
        if($submit === "enrolment"){
            if($date_break !== ""){
                if(date("Y-m-j", strtotime($date_break)) !== date("Y-m-j", strtotime($result["enrolDate"]))){
                    ++$rowCounter;
                    $date_break = $result["enrolDate"];
                }
            }else{
                $date_break = $result["enrolDate"];
            }
        }
        
        foreach ($field_names as $row => $value){
            if($value != $exception_headers[$exceptionCounter]){
                $cellName = $current_col_names[$headerCounter].$rowCounter;
    
                //enter value into cells
                $sheet->setCellValueExplicit($cellName, formatName($result[$value]), "s");
    
                //move to next header
                $headerCounter++;
            }else{
                $exceptionCounter++;
            }
        }

        //reset counters
        $headerCounter = 0;
        $exceptionCounter = 0;

        //move to next row
        $rowCounter++;
    }

    //automatically size up columns
    for($column = "A"; $column != $sheet->getHighestColumn(); $column++){
        $sheet->getColumnDimension($column)->setAutoSize(TRUE);
    }

    //set header to accept excel
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    //define file name
    header("Content-Disposition: attachment;filename=\"$filename.xlsx\"");

    //create an IOFactory to ask for location for save file
    $writer = IOFactory::createWriter($spreadsheet, "Xlsx");

    //save to php output
    $writer->save("php://output");

    close_connections();
    
?>