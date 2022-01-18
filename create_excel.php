<?php 
    require_once("includes/session.php");

    //call autoload
    require($rootPath."/PhpSpreadSheet/autoload.php");

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
        $sql = "SELECT e.*, s.schoolName, h.title AS houseName
            FROM enrol_table e JOIN schools s
            ON e.shsID = s.id
            JOIN house_allocation ho
            ON e.indexNumber = ho.indexNumber
            JOIN houses h
            ON h.id = ho.houseID
            WHERE e.shsID=$user_school_id";

            //exception headers, end it with an empty field
            $exception_headers = array("shsID","");

            //title
            $filename = "Enrolment Details | ";
    }elseif($submit == "houses"){
        $sql = "SELECT h.indexNumber, h.studentLname AS lastname, h.studentOname as `othername(s)`, h.studentYearLevel, h.studentGender, h.boardingStatus, h1.title AS `house name`
        FROM house_allocation h JOIN houses h1
        ON h.houseID=h1.id
        WHERE h.schoolID=$user_school_id";

        $exception_headers = array("");

        $filename = "House Allocation | ";
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
    $current_col_names = array();

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

    //Provide a title
    $title = strtoupper("List of Enroled Students");

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
    while($result=$query->fetch_assoc()){
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
    
?>