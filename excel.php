<?php 
    require_once("includes/session.php");

    //call autoload
    require($rootPath."/PhpSpreadSheet/autoload.php");

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
    $school_id = 3;

    $sql = "SELECT c.* FROM cssps c JOIN schools s
        ON c.schoolID=s.ID
        JOIN houses h
        ON  WHERE c.schoolID=$school_id";
    $query = $connect->query($sql);

    //take field names
    $field_names = array_keys($query->fetch_assoc());

    //exception headers, end it with an empty field
    $exception_headers = array("schoolID","");
    
    //Take number of columns
    $number_of_columns = count($field_names) - (count($exception_headers) - 1);

    //fill current columns with column names
    $current_col_names = array();

    for($i=0; $i < $number_of_columns; $i++){
        $current_col_names[$i] = $all_col_names[$i];
    }

    //Provide a title
    $title = strtoupper("List of Enroled Students");

    //merge cells to take school name
    $merged_cells = $current_col_names[0]."1:".end($current_col_names)."1";
    $sheet->mergeCells($merged_cells);

    //counters
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
            $sheet->setCellValue($cellName, $value);

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
                $sheet->setCellValue($cellName, formatName($result[$value]));
    
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
    for($column = "A"; $column <= $sheet->getHighestColumn(); $column++){
        $sheet->getColumnDimension($column)->setAutoSize(TRUE);
    }

    //set header to accept excel
    header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    //define file name
    header("Content-Disposition: attachment;filename=\"sqlTestSchoolName.xlsx\"");

    //create an IOFactory to ask for location for save file
    $writer = IOFactory::createWriter($spreadsheet, "Xlsx");

    //save to php output
    $writer->save("php://output");
    
?>