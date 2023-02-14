<?php 
    require_once("includes/session.php");

    //call autoload
    require($rootPath."/PhpSpreadsheet/autoload.php");

    //load IOFactory class
    use PhpOffice\PhpSpreadsheet\IOFactory;

    //load spreadsheet class
    use PhpOffice\PhpSpreadsheet\Spreadsheet;

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

                    $acceptable = ["K","F","H","G","I"];

                    //check if right file is sent
                    if($max_column == "J" || $max_column == "F" || $max_column == "I"){
                        //grab the detail in the first cell
                        $cell =  $sheet->getCell("A1");
                        $cellValue = $cell->getValue();

                        if(strtolower($cellValue) != "index number" || strval(strpos(strtolower($cellValue), "index")) == "false"){
                            //display error if the first column isnt the index number
                            echo "First column not identified as 'index number'. Please follow the format directed in the documents above";
                            exit(1);
                        }
                    }elseif($max_column == "H" || $max_column == "G"){
                        //grab the detail in the first cell
                        $cell = $sheet->getCell("A2");
                        $cellValue = $cell->getValue();

                        if(strval(strpos(strtolower($cellValue), "index")) == "false"){
                            echo "The file you sent cannot be defined. Please send a valid file format. Make sure it begins with 'index'";
                            exit(1);
                        }
                    }elseif(!array_search($max_column, $acceptable)){
                        echo "Invalid file detected. Please send the correct file to continue";
                        exit(1);
                    }

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
                    
                    //display content
                    //--for placement
                    if($max_column == "J"){
                        for($row=2; $row <= $max_row; $row++){
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
                                        "Buffer count is beyond expected input count";
                                        exit(1);
                                }
                            }
    
                            //check if index number exists and insert data into database
                            $index = fetchData("indexNumber","cssps","indexNumber='$indexNumber'");
    
                            if($index == "empty"){
                                $sql = "INSERT INTO cssps(indexNumber,Lastname,Othernames,Gender,boardingStatus,programme,aggregate,jhsAttended,dob,trackID,schoolID)
                                    VALUES (?,?,?,?,?,?,?,?,?,?,?)
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
                            }else{
                                echo "Candidate with index number <b>$indexNumber</b> already exists. Candidate data was not written<br>";
                            }                            
                        }
                    }elseif($max_column == "H" || $max_column == "G"){
                        //make it end at G
                        $headerCounter = 6;
                        
                        for($row=3; $row <= $max_row; $row++){
                            //grab columns [reject last column, column H]
                            for($col = 0; $col <= $headerCounter; $col++){
                                $cellValue = $sheet->getCell($current_col_names[$col].$row)->getValue();
                                
                                switch ($col) {
                                    case 0:
                                        $indexNumber = $cellValue;
                                        break;
                                    case 1:
                                        //extract lastname and othernames
                                        $name = explode(' ', formatName($cellValue));

                                        if(is_array($name)){
                                            $Lastname = $name[0];
                                            $Othernames = "";
                                            for($i = 1; $i < count($name); $i++) {
                                                if($Othernames != ""){
                                                    $Othernames .= " ".$name[$i];
                                                }else{
                                                    $Othernames = $name[$i];
                                                }
                                            }
                                        }else{
                                            echo "Student's name could not be retrieved. Check your file and try again later, else report to admin for help";
                                            exit(1);
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
                                        if(strtolower($boardingStatus) == "boarding"){
                                            $boardingStatus = 'Boarder';
                                        }
                                        break;
    
                                    default:
                                        "Buffer count is beyond expected input count";
                                        exit(1);
                                }
                            }
    
                            //check if index number exists and insert data into database
                            if(!is_null($indexNumber)){
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
                                echo "Operation exited by meeting an empty column. All preceding data received successfully";
                                break;
                            }             
                        }
                    }elseif($max_column == "I"){
                        for($row=2; $row <= $max_row; $row++){
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
                                        $studentYear = intval($cellValue);
                                        break;
                                    case 5:
                                        $house = strtolower($cellValue);
                                        $houseID = fetchData("id","houses","LOWER(title)='$house' AND schoolID=$user_school_id");
                                        break;
                                    case 6:
                                        $boardingStatus = formatName($cellValue);
                                        break;
                                    case 7:
                                        $programme = formatName($cellValue);
                                        break;
                                    case 8:
                                        $guardianContact = remakeNumber($cellValue, true);
                                        break;    
                                    default:
                                        "Buffer count is beyond expected input count";
                                        exit(1);
                                }
                            }
    
                            //check if index number exists and insert data into database
                            $index = fetchData1("indexNumber","students_table","indexNumber='$indexNumber'");
    
                            if($index == "empty"){
                                $sql = "INSERT INTO students_table (indexNumber, Lastname, Othernames, Gender, houseID, school_id, studentYear, guardianContact, programme, boardingStatus)
                                    VALUES (?,?,?,?,?,?,?,?,?,?)";
                                $stmt = $connect2->prepare($sql);
                                $stmt = $connect2->prepare($sql);
                                $stmt->bind_param("ssssiiisss",$indexNumber, $Lastname, $Othernames, $Gender, $houseID, $user_school_id, $studentYear,
                                    $guardianContact, $programme, $boardingStatus);
                                if($stmt->execute()){
                                    if($row == $max_row){
                                        echo "success";
                                    }
                                }elseif(strtolower($boardingStatus) != "day" || strtolower($boardingStatus) != "boarder"){
                                    echo "Detail for <b>$indexNumber</b> not written. Boarding Status should either be Day or Boarder<br>";
                                }elseif(strtolower($Gender) != "male" || strtolower($Gender) != "female"){
                                    echo "Detail for $indexNumber not written. Gender must either be Male or Female";
                                }                                
                            }else{
                                echo "Candidate with index number <b>$indexNumber</b> already exists. Candidate data was not written<br>";
                            }                            
                        }
                    }else{
                        for($row=2; $row <= $max_row; $row++){
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
                }
            }else{
                echo "no-file";
                exit(1);
            }
        }
    }
?>