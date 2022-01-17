<?php 
    require_once("includes/session.php");

    //call autoload
    require($rootPath."/PhpSpreadSheet/autoload.php");

    //load IOFactory class
    use PhpOffice\PhpSpreadsheet\IOFactory;

    //load spreadsheet class
    use PhpOffice\PhpSpreadsheet\Spreadsheet;

    if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != null){
        $submit = $_REQUEST["submit"];

        if($submit == "upload" || $submit == "upload_ajax"){
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

                    //check if enrolment or house is exact file
                    //enrolment end at J, house end at F
                    if($max_column != "J" && $max_column != "F"){
                        echo "Invalid file detected. Please send the correct file to continue";
                        exit(1);
                    }elseif($max_column == "J" || $max_column == "F"){
                        //grab the detail in the first cell
                        $cell =  $sheet->getCell("A1");
                        $cellValue = $cell->getValue();

                        if(strtolower($cellValue) != "index number"){
                            echo "Desired file was not received";
                            exit(1);
                        }
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
                                        $Lastname = $cellValue;
                                        break;
                                    case 2:
                                        $Othernames = $cellValue;
                                        break;
                                    case 3:
                                        $Gender = $cellValue;
                                        break;
                                    case 4:
                                        $boardingStatus = $cellValue;
                                        break;    
                                    case 5:
                                        $programme = $cellValue;
                                        break;
                                    case 6:
                                        $aggregate = $cellValue;
                                        break;
                                    case 7:
                                        $jhsAttended = $cellValue;
                                        break;
                                    case 8:
                                        $val = PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cellValue);
                                        $dob = date("Y-m-d", $val);
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
                                        $studentLname = $cellValue;
                                        break;
                                    case 2:
                                        $studentOname = $cellValue;
                                        break;
                                    case 3:
                                        $houseName = $cellValue;
                                        break;
                                    case 4:
                                        $studentGender = $cellValue;
                                        break;
                                    case 5:
                                        $boardingStatus = $cellValue;
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
                                }
                                
                            }else{
                                echo "user-exist".$index["indexNumber"];
                                exit(1);
                            }
                            
                        }
                    }

                    
                    /*//variable to write data from file
                    $writer = IOFactory::createWriter($spreadsheet, "Html");

                    //parse result into html
                    $result = $writer->save("php://output");

                    echo $result;*/
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