<?php 
    require_once("includes/session.php");

    //call autoload
    require($rootPath."/PhpSpreadsheet/autoload.php");

    //load IOFactory class
    use PhpOffice\PhpSpreadsheet\IOFactory;

    //load spreadsheet class
    use PhpOffice\PhpSpreadsheet\Spreadsheet;

    try {
        if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != null){
            $submit = $_REQUEST["submit"];
    
            if($submit == "upload" || $submit == "upload_ajax" || $submit == "upload_students" || $submit == "upload_students_ajax"){
                if(isset($_FILES['import']) && $_FILES["import"]["tmp_name"] != NULL){
                    //retrieve file extension
                    $ext = strtolower(fileExtension("import"));
    
                    if($ext == "xls" || $ext == "xlsx"){
                        //identify file type Automatically
                        $file_type = IOFactory::identify($_FILES["import"]["tmp_name"]);
    
                        //create a reader
                        $reader = IOFactory::createReader('Xlsx');
    
                        //create a spreadsheet instance
                        $spreadsheet = $reader->load($_FILES["import"]["tmp_name"]);
                        
                        //get the working sheet
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        //get maximum cell number
                        $max_column = $sheet->getHighestDataColumn();
                        $max_row = $sheet->getHighestDataRow();
                        $row_start = 2;
    
                        $acceptable = ["K","F","H","G","I"];
    
                        //check if right file is sent
                        if($max_column == "J" || $max_column == "F" || $max_column == "I"){
                            //grab the detail in the first cell
                            $cell =  $sheet->getCell("A1");
                            $cellValue = $cell->getValue();
    
                            if(strtolower($cellValue) != "index number" && str_contains(strtolower($cellValue), "index") === false){
                                $cell =  $sheet->getCell("A2");
                                $cellValue = $cell->getValue();
    
                                $row_start = 3;
    
                                if(strtolower($cellValue) != "index number" || !str_contains($cellValue, "index")){
                                    //display error if the first column isnt the index number
                                    exit("First column not identified as 'index number'. Please follow the format directed in the documents above");
                                }
                            }
                        }elseif($max_column == "H" || $max_column == "G"){
                            //grab the detail in the first cell
                            $cell = $sheet->getCell("A2");
                            $cellValue = $cell->getValue();
    
                            $row_start = 3;
    
                            if(!str_contains(strtolower($cellValue), "index")){
                                $cell =  $sheet->getCell("A1");
                                $cellValue = $cell->getValue();
    
                                $row_start = 2;
    
                                if(!str_contains(strtolower($cellValue), "index")){
                                    exit("The file you sent cannot be defined. Please send a valid file format. Make sure it begins with 'index'");
                                }
                            }
                        }elseif(!in_array($max_column, $acceptable)){
                            exit("Invalid file detected. Please send the correct file to continue");
                        }
    
                        //last heading
                        $last_heading = ucwords($sheet->getCell($max_column.($row_start-1))->getValue());
                        
                        //create columns for cell number operations
                        $current_col_names = createColumnHeader($max_column);
                        $headerCounter = count($current_col_names);
                        
                        // display content
                        // for placement provided by system
                        if($max_column == "J" && $last_heading != "Guardian Contact"){
                            $academic_year = $_REQUEST["academic_year"] ?? getAcademicYear(now(), false);
                            $academic_year = formatAcademicYear($academic_year, false);
                            for($row=$row_start; $row <= $max_row; $row++){
                                //grab columns
                                for($col = 0; $col <= $headerCounter; $col++){
                                    $cellValue = $sheet->getCell($current_col_names[$col].$row)->getValue();
                                    
                                    switch ($col) {
                                        case 0:
                                            $indexNumber = $cellValue;
                                            break;
                                        case 1:
                                            $Lastname = formatName($cellValue);
                                            break;
                                        case 2:
                                            $Othernames = formatName($cellValue);
                                            break;
                                        case 3:
                                            $Gender = formatName($cellValue);
                                            break;
                                        case 4:
                                            $boardingStatus = formatName($cellValue);
    
                                            if(str_contains($boardingStatus, "board")){
                                                $boardingStatus = "Boarder";
                                            }
                                            break;    
                                        case 5:
                                            $programme = formatName($cellValue);
                                            break;
                                        case 6:
                                            $aggregate = $cellValue;
                                            break;
                                        case 7:
                                            if(!empty($cellValue)){
                                                $jhsAttended = formatName($cellValue);
                                            }else{
                                                $jhsAttended = null;
                                            }                                        
                                            break;
                                        case 8:
                                            if(!empty($cellValue)){
                                                $val = PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cellValue);
                                                $dob = date("Y-m-d", $val);
                                            }else{
                                                $dob = null;
                                            }                                        
                                            break;
                                        case 9:
                                            $trackID = $cellValue;
                                            break;
        
                                        default:
                                            exit("Buffer count is beyond expected input count");
                                    }
                                }

                                // insert data but ignore any existing student
                                $sql = "INSERT IGNORE INTO cssps(indexNumber,Lastname,Othernames,Gender,boardingStatus,programme,aggregate,jhsAttended,dob,trackID,schoolID, academic_year)
                                        VALUES (?,?,?,?,?,?,?,?,?,?,?,'$academic_year')
                                    ";
                                $stmt = $connect->prepare($sql);
                                $stmt->bind_param("ssssssisssi",$indexNumber,$Lastname,$Othernames,$Gender,$boardingStatus,$programme,$aggregate,$jhsAttended,$dob,$trackID,$user_school_id);
                                if($stmt->execute()){
                                    if($row == $max_row){
                                        echo "success";
                                    }
                                }elseif(strtolower($boardingStatus) != "day" || strtolower($boardingStatus) != "boarder"){
                                    echo "Detail for <b>$indexNumber</b> not written. Boarding Status should either be Day or Boarder<br>";
                                }elseif(strtolower($Gender) != "male" || strtolower($Gender) != "female"){
                                    echo "Detail for $indexNumber not written. Gender must either be Male or Female";
                                }                         
                            }
                        }elseif($max_column == "H" || $max_column == "G" || $max_column == "I"){
                            //make it end at G
                            $headerCounter = 6;
                            
                            for($row=$row_start; $row <= $max_row; $row++){
                                //grab columns [reject last column, column H]
                                for($col = 0; $col <= $headerCounter; $col++){
                                    $cellValue = $sheet->getCell($current_col_names[$col].$row)->getValue();
                                    switch ($col) {
                                        case 0:
                                            $indexNumber = $cellValue;

                                            if(empty($indexNumber)){
                                                exit("Empty index number at cell ".$current_col_names[$col].$row.". Data before this cell have been saved");
                                            }
                                            break;
                                        case 1:
                                            //extract lastname and othernames
                                            $name = explode(' ', formatName($cellValue), 2);
    
                                            if(is_array($name)){
                                                $Lastname = $name[0];
                                                $Othernames = $name[1] ?? "";

                                                if(empty($Othernames)){
                                                    $cellValue = $sheet->getCell($current_col_names[$col].$row)->getCalculatedValue();
                                                    $name = explode(' ', formatname($cellValue), 2);
                                                    if(is_array($name)){
                                                        $Lastname = $name[0];
                                                        $Othernames = $name[1] ?? "";
                                                    }
                                                    
                                                    if(empty($Othernames)){
                                                        exit("Student's name for '$indexNumber' at ".$current_col_names[$col].$row." could not be well formated");
                                                    }
                                                }
                                            }else{
                                                exit("Student's name for '$indexNumber' could not be retrieved. Check your file and try again later, else report to admin for help");
                                            }
                                            
                                            break;
                                        case 2:
                                            $Gender = formatName($cellValue);
                                            break;
                                        case 3:
                                            $aggregate = $cellValue;
                                            break;
                                        case 4:
                                            $programme = formatName($cellValue);
                                            break;    
                                        case 5:
                                            $trackID = formatName($cellValue);
                                            break;
                                        case 6:
                                            $boardingStatus = formatName($cellValue);
    
                                            //convert to enum value
                                            if(str_contains(strtolower($boardingStatus), "board")){
                                                $boardingStatus = 'Boarder';
                                            }
                                            break;
        
                                        default:
                                            exit("Buffer count is beyond expected input count");
                                    }
                                }
        
                                //check if index number exists and insert data into database
                                if(!is_null($indexNumber) && !empty($indexNumber)){
                                    $index = fetchData("indexNumber","cssps","indexNumber='$indexNumber'");
        
                                    if($index == "empty"){
                                        $sql = "INSERT INTO cssps(indexNumber,Lastname,Othernames,Gender,boardingStatus,programme,aggregate,trackID,schoolID)
                                            VALUES (?,?,?,?,?,?,?,?,?)
                                        ";
                                        $stmt = $connect->prepare($sql);
                                        $stmt->bind_param("ssssssisi",$indexNumber,$Lastname,$Othernames,$Gender,$boardingStatus,$programme,$aggregate,$trackID,$user_school_id);
                                        if($stmt->execute()){
                                            if($row == $max_row){
                                                echo "success";
                                            }
                                        }elseif(strtolower($boardingStatus) != "day" || strtolower($boardingStatus) != "boarder"){
                                            echo "Detail for <b>$indexNumber</b> not written. Boarding Status should either be Day or Boarder<br>";
                                        }elseif(strtolower($Gender) != "male" || strtolower($Gender) != "female"){
                                            echo "Detail for <b>$indexNumber</b> not written. Gender must either be Male or Female";
                                        }           
                                    }else{
                                        echo "Candidate with index number <b>$indexNumber</b> already exists. Candidate data was not written<br>";
                                    }      
                                }else{
                                    $cellName = $current_col_names[$col].$row;
                                    echo "Operation exited by meeting an empty column ($cellName). All preceding data received successfully";
                                    break;
                                }             
                            }
                        }elseif($max_column == "J" && $last_heading == "Guardian Contact"){
                            $message = ""; $insert_count = 0; $houses = [];
                            $house_data = fetchData("id, title", "houses", "schoolID=$user_school_id", 0);
                            $houses = $house_data == "empty" ? [] : array_change_key_case(pluck($house_data, "title", "id"));
                            $program_data = fetchData1("program_id, program_name", "program", "school_id=$user_school_id", 0);
                            $programs = $program_data == "empty" ? [] : array_change_key_case(pluck($program_data, "program_name", "program_id"));
                            
                            for($row=$row_start; $row <= $max_row; $row++){
                                $skip_row = 0;
                                //grab columns
                                for($col = 0; $col < $headerCounter; $col++){
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
                                            $houseID = $houses[$house] ?? 0;
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
                                            $program_id = $programs[$program] ?? 0;
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
                                    if($row == $max_row && $insert_count > 0){
                                        $message = "success";
                                    }elseif($row == $max_row){
                                        $message = "No student was inserted";
                                    }
                                    continue;
                                }
        
                                try {
                                    //check if index number exists and insert data into database
                                    $index = fetchData1("indexNumber, program_id","students_table","indexNumber='$indexNumber' OR (Lastname='$Lastname' AND Othernames='$Othernames')");
            
                                    if($index == "empty"){
                                        $sql = "INSERT INTO students_table (indexNumber, Lastname, Othernames, Gender, houseID, school_id, studentYear, guardianContact, programme, program_id, boardingStatus)
                                            VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                                        $stmt = $connect2->prepare($sql);
                                        $stmt->bind_param("ssssiiissis",$indexNumber, $Lastname, $Othernames, $Gender, $houseID, $user_school_id, $studentYear,
                                            $guardianContact,$programme, $program_id, $boardingStatus);
                                        if($stmt->execute()){
                                            if($row == $max_row){
                                                $message = "success";
                                            }

                                            ++$insert_count;
                                        }elseif(strtolower($boardingStatus) != "day" || strtolower($boardingStatus) != "boarder"){
                                            $message = "Detail for <b>$indexNumber</b> not written. Boarding Status should either be Day or Boarder<br>";
                                        }elseif(strtolower($Gender) != "male" && strtolower($Gender) != "female"){
                                            $message = "Detail for $indexNumber not written. Gender must either be Male or Female";
                                        }
                                    }elseif($index["program_id"] == 0 || empty($index["program_id"])){
                                        $sql = "UPDATE students_table SET program_id=? WHERE indexNumber=?";
                                        $stmt = $connect2->prepare($sql);
                                        $stmt->bind_param("is",$program_id,$indexNumber);
    
                                        if($stmt->execute()){
                                            if($row == $max_row){
                                                $message = "success";
                                            }
                                            ++$insert_count;
                                        }
                                    }elseif($row == $max_row){
                                        $message = "success";
                                    }
                                } catch (\Throwable $th) {
                                    $message = $th->getMessage();
                                }
                            }

                            echo $message;
                        }else{
                            for($row=$row_start; $row <= $max_row; $row++){
                                //grab columns
                                for($col = 0; $col <= $headerCounter; $col++){
                                    $cellValue = $sheet->getCell($current_col_names[$col].$row)->getValue();
                                    
                                    switch ($col) {
                                        case 0:
                                            $indexNumber = $cellValue;
                                            break;
                                        case 1:
                                            $studentLname = formatName($cellValue);
                                            break;
                                        case 2:
                                            $studentOname = formatName($cellValue);
                                            break;
                                        case 3:
                                            $houseName = formatName($cellValue);
                                            break;
                                        case 4:
                                            $studentGender = formatName($cellValue);
                                            break;
                                        case 5:
                                            $boardingStatus = formatName($cellValue);
                                            break;    
        
                                        default:
                                            "Buffer count is beyond expected input count";
                                            exit(1);
                                    }
                                }
        
                                //check if index number exists and insert data into database
                                $index = fetchData("indexNumber","house_allocation","indexNumber='$indexNumber'");
        
                                if($index == "empty"){
                                    $sql = "INSERT INTO house_allocation(indexNumber,schoolID,studentLname,studentOname,houseID,studentGender,boardingStatus)
                                        VALUES (?,?,?,?,?,?,?)
                                    ";
    
                                    //set house id
                                    $houseID = fetchData("id","houses","title='$houseName' AND schoolID=$user_school_id AND gender='$studentGender'");
    
                                    //do not break when user data is null
                                    if($houseID == "empty"){
                                        echo "Candidate with index number <b>$indexNumber</b> could not be allocated a defined house";
                                        $houseID = 0;
                                    }else{
                                        $houseID = intval($houseID["id"]);
                                    }
    
                                    $stmt = $connect->prepare($sql);
                                    $stmt->bind_param("sississ",$indexNumber,$user_school_id,$studentLname,$studentOname,$houseID,$studentGender,$boardingStatus);
                                    if($stmt->execute()){
                                        if($row == $max_row){
                                            echo "success";
                                        }
                                    }elseif(strtolower($boardingStatus) != "day" || strtolower($boardingStatus) != "boarder"){
                                        echo "Detail for <b>$indexNumber</b> not written. Boarding Status should either be Day or Boarder";
                                    }elseif(strtolower($studentGender) != "male" || strtolower($studentGender) != "female"){
                                        echo "Detail for $indexNumber not written. Gender should either be Male or Female";
                                    }                    
                                }else{
                                    echo "user-exist".$index["indexNumber"];
                                    exit(1);
                                }
                                
                            }
                        }
                    }else{
                        $message = "extension-error";
                        exit($message);
                    }
                }else{
                    exit("no-file");
                }
            }else{
                exit("Submission method '{$_REQUEST['submit']}' is invalid");
            }
        }else{
            $message = print_r((array) file_get_contents("php://input"));
            exit("No submission was detected " .$message);
        }
    } catch (\Throwable $th) {
        echo throwableMessage($th);
    }
?>