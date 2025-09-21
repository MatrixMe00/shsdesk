<?php 
    require_once("includes/session.php");

    //call autoload
    require($rootPath."/PhpSpreadsheet/autoload.php");

    //load IOFactory class

    use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\RichText\RichText;
    use PhpOffice\PhpSpreadsheet\Shared\Date;

    //load spreadsheet class
    use PhpOffice\PhpSpreadsheet\Spreadsheet;

    /**
     * Validates the headers of the excel file
     * @param string $type The type
     * @param array $headers The headers of the file
     * @param array $acceptable_headers The array for the acceptable headers
     * @return array
     */
    function validateHeaders($type, $headers, $acceptable_headers) {
        if (!isset($acceptable_headers[$type])) {
            return [
                "status" => false,
                "error" => "Unknown type: $type"
            ];
        }
    
        $rules = $acceptable_headers[$type];
        $required = $rules['required'];
        $replace  = $rules['replace'];
    
        // Normalize headers (lowercase + trim)
        $normalizedHeaders = array_map('strtolower', array_map('trim', $headers));
    
        // Apply replacements
        foreach ($normalizedHeaders as &$header) {
            if (isset($replace[$header])) {
                $header = $replace[$header];
            }
        }
        unset($header);
    
        // Check required fields
        $missing = [];
        foreach ($required as $req) {
            if (!in_array(strtolower($req), $normalizedHeaders)) {
                $missing[] = $req;
            }
        }
    
        return [
            "status" => empty($missing),
            "missing" => $missing,
            "processed_headers" => $normalizedHeaders
        ];
    }

    try {
        if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != null){
            $submit = $_REQUEST["submit"];
            $type = $_REQUEST["type"] ?? "house";

            $acceptable_headers = $excel_acceptable_headers;
    
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
    
                        //check if right file is sent by grabbing first two cells in column A
                        $cell1 = strtolower(trim($sheet->getCell("A1")->getValue()));
                        $cell2 = strtolower(trim($sheet->getCell("A2")->getValue()));

                        if ($cell1 === "index number" || str_contains($cell1, "index")) {
                            $row_start = 2; // headers on row 1
                        } elseif ($cell2 === "index number" || str_contains($cell2, "index")) {
                            $row_start = 3; // headers on row 2
                        } else {
                            exit("The file you sent cannot be defined. Please send a valid file format. Make sure it begins with 'index'");
                        }
    
                        //last heading
                        $last_heading = null;
                        $test_max_column = $max_column;

                        do {
                            $last_heading = $sheet->getCell($test_max_column . ($row_start - 1))->getValue();
                            $last_heading = trim($last_heading);
                            
                            // Move left if empty
                            if (empty($last_heading)) {
                                $colIndex = Coordinate::columnIndexFromString($test_max_column);
                                $colIndex--;
                                $headerCounter = $colIndex;

                                if ($colIndex < 1) {
                                    exit("Couldn't get a valid column value");
                                }

                                $test_max_column = Coordinate::stringFromColumnIndex($colIndex);
                            }
                        } while (empty($last_heading));
                        $max_column = $test_max_column;

                        //create columns for cell number operations
                        $current_col_names = createColumnHeader($max_column);
                        $headerCounter = count($current_col_names);

                        // get the headings in the file
                        $headings = [];
                        for ($col = 0; $col < $headerCounter; $col++) {
                            $cellValue = $sheet->getCell($current_col_names[$col] . ($row_start - 1))->getValue();
                            $headings[] = strtolower(trim($cellValue));
                        }

                        // validate the headings
                        $validationResult = validateHeaders($type, $headings, $acceptable_headers);
                        if (!$validationResult['status']) {
                            $missingHeaders = implode(", ", $validationResult['missing']);
                            exit("Missing required headers: $missingHeaders");
                        }

                        $sheet_data = [];
                        for ($row = $row_start; $row <= $max_row; $row++) {
                            $rowData = [];

                            for ($col = 0; $col < $headerCounter; $col++) {
                                $columnLetter = $current_col_names[$col];
                                $cell = $sheet->getCell($columnLetter . $row);

                                // Prefer calculated value if formula, else raw value
                                $cellValue = $cell->getCalculatedValue();

                                // Handle rich text
                                if ($cellValue instanceof RichText) {
                                    $cellValue = $cellValue->getPlainText();
                                }

                                // Handle Excel dates
                                if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                                    $cellValue = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cell->getValue())->format('Y-m-d H:i:s');
                                }

                                // Optionally, handle hyperlinks (replace the cell value with the URL if you want)
                                $hyperlink = $cell->getHyperlink()->getUrl();
                                if (!empty($hyperlink)) {
                                    $cellValue = $hyperlink; // <-- comment this out if you want to keep original text instead
                                }

                                // --- Handle key replacement ---
                                $key = strtolower(trim($headings[$col])); // normalize heading
                                if (isset($acceptable_headers[$type]['replace'][$key])) {
                                    $key = $acceptable_headers[$type]['replace'][$key];
                                }

                                $rowData[$key] = $cellValue;
                            }

                            $sheet_data[] = $rowData;
                        }

                        if($type == "admission"){
                            $current_row = $row_start;
                            $academic_year = $_REQUEST["academic_year"] ?? getAcademicYear(now(), false);
                            $not_written = $new_data = 0;
                            
                            $sql = "INSERT IGNORE INTO cssps(indexNumber, hidden_index, Lastname, Othernames, Gender, dob, boardingStatus, `aggregate`, programme, jhsAttended, trackID, schoolID, guardian_contact, academic_year)
                                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
                                ";
                            $connect->begin_transaction();
                            try {
                                foreach($sheet_data as $row){
                                    ++$current_row;
    
                                    $hidden_index = null;
    
                                    $index_number = $row["index number"];
                                    $dob = $row["date of birth"] ?? null;
                                    $guardian_contact = $row["contact"] ?? null;
                                    $programme = $row["programme"];
                                    $gender = $row["gender"];
                                    $boarding_status = $row["boarding status"];
                                    $aggregate = $row["aggregate score"] ?? null;
                                    $jhs_attended = $row["jhs attended"] ?? null;
                                    $track_id = $row["track id"] ?? null;
    
                                    if(empty($index_number)){
                                        echo "Empty index number at row $current_row. Kindly rectify";
                                        continue;
                                    }elseif(str_contains($index_number, "*")){
                                        $hidden_index = $index_number;   // store the hashed index_number
                                        do {
                                            $index_number = generateIndexNumber($user_school_id);
                                        } while (is_array(fetchData("indexNumber","cssps","indexNumber='$index_number'")));
                                    }elseif(strlen($index_number) === 11){
                                        $index_number = "0$index_number";
                                    }

                                    if($dob){
                                        $dob = date("Y-m-d", strtotime($dob));
                                    }
    
                                    if(isset($row["name"])){
                                        $name = explode(' ', $row["name"], 2);
                                        $Lastname = $name[0];
                                        $Othernames = $name[1] ?? "";
                                    }elseif(isset($row["surname"]) && isset($row["othername"])){
                                        $Lastname = $row["surname"];
                                        $Othernames = $row["othername"];
                                    }else{
                                        echo "Student's name for '$index_number' could not be retrieved. Check your file and try again later, else report to admin for help";
                                        continue;
                                    }
    
                                    if(str_contains(strtolower($boarding_status), "board")){
                                        $boarding_status = 'Boarder';
                                    }
    
                                    if(!in_array(strtolower($boarding_status), ["day", "boarder"])){
                                        echo "Boarding Status for <b>$index_number</b> should either be Day or Boarder/Boarding<br>";
                                        continue;
                                    }
    
                                    if($guardian_contact){
                                        $guardian_contact = remakeNumber($guardian_contact, space: false);

                                        // add 0 to the front if its 9 characters
                                        if(strlen($guardian_contact) === 9){
                                            $guardian_contact = "0".$guardian_contact;
                                        }
                                    }
    
                                    if(!in_array(strtolower($gender), ["male", "female"])){
                                        echo "Gender for <b>$index_number</b> must either be Male or Female";
                                        continue;
                                    }
    
                                    if(!empty($index_number)){
                                        $index = null;
                                        if(!empty($hidden_index)){
                                            $index = fetchData("indexNumber", "cssps", ["hidden_index = '$hidden_index'", "Lastname = '$Lastname'", "Othernames = '$Othernames'", "programme = '$programme'", "schoolID = $user_school_id"], where_binds: "AND");
                                        }
            
                                        if(empty($index) || $index == "empty"){
                                            $stmt = $connect->prepare($sql);
                                            $stmt->bind_param("sssssssisssiss",
                                                $index_number, $hidden_index, $Lastname, $Othernames, $gender, $dob, $boarding_status, $aggregate, $programme, $jhs_attended, $track_id, $user_school_id, $guardian_contact, $academic_year
                                            );
                                            if($stmt->execute()){
                                                if($connect->affected_rows){
                                                    ++$new_data;
                                                }else{
                                                    $not_written++;
                                                }
    
                                                if(!empty($guardian_contact)){
                                                    add_cssps_guardian($index_number, $user_school_id, $guardian_contact);
                                                }
                                            }else{
                                                throw new Exception("Statment Error: $stmt->error");
                                            }
                                        }else{
                                            $not_written++;
                                        }     
                                    }
                                }

                                $connect->commit();

                                echo $not_written < 1 ? "success" : "$new_data new records have been added";
                            } catch (\Throwable $th) {
                                $connect->rollback();
                                echo "Error: ".throwableMessage($th);
                            }
                            
                        }elseif($max_column == "J" && $last_heading == "Guardian Contact"){
                            $message = ""; $insert_count = 0; $houses = [];
                            $house_data = fetchData("id, title", "houses", "schoolID=$user_school_id", 0);
                            $houses = $house_data == "empty" ? [] : array_change_key_case(pluck(decimalIndexArray($house_data), "title", "id"));
                            $program_data = fetchData1("program_id, program_name, short_form", "program", "school_id=$user_school_id", 0);
                            $programs = $program_data == "empty" ? [] : array_change_key_case(pluck(decimalIndexArray($program_data), "program_name", "program_id"));
                            $programs_ = $program_data == "empty" ? [] : array_change_key_case(pluck(decimalIndexArray($program_data), "short_form", "program_id"));
                            $programs = trim_array_keys(array_merge($programs, $programs_));
                            
                            $connect2->begin_transaction();
                            for($row=$row_start; $row <= $max_row; $row++){
                                $skip_row = 0;
                                //grab columns
                                for($col = 0; $col < $headerCounter; $col++){
                                    $cellValue = $sheet->getCell($current_col_names[$col].$row)->getValue() ?? "";
                                    $cellValue = trim($cellValue);

                                    switch ($col) {
                                        case 0:
                                            $skip_row = empty($cellValue) ? $skip_row + 1 : 0;
                                            $indexNumber = empty($cellValue) ? generateIndexNumber($user_school_id) : $cellValue;
                                            break;
                                        case 1:
                                            $skip_row = empty($cellValue) ? $skip_row + 1 : $skip_row - 1;
                                            $Lastname = ucfirst(strtolower($cellValue));
                                            break;
                                        case 2:
                                            $Othernames = ucwords(strtolower($cellValue));
                                            break;
                                        case 3:
                                            $Gender = ucfirst(strtolower($cellValue));
                                            if($Gender != "Male" && $Gender != "Female"){
                                                echo "Gender for <b>$Lastname $Othernames</b> was invalid. Process has been terminated";
                                                $connect2->rollback();
                                                exit(1);
                                            }
                                            break;
                                        case 4:
                                            $studentYear = numeric_strict($cellValue);
                                            if($studentYear > 3){
                                                echo "Invalid form level provided. From level ranges from 1 to 3";
                                                $connect2->rollback();
                                                exit(1);
                                            }
                                            break;
                                        case 5:
                                            $house = strtolower($cellValue);
                                            $houseID = $houses[$house] ?? 0;
                                            break;
                                        case 6:
                                            $boardingStatus = ucfirst(strtolower($cellValue));
                                            if($boardingStatus !== "Boarder" && $boardingStatus !== "Day"){
                                                echo "Boarding Status for <b>$indexNumber</b> is invalid. Process execution has been terminated";
                                                $connect2->rollback();
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
                                            if(empty($cellValue) || !is_numeric($cellValue)){
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
                                    $index = fetchData1("indexNumber, program_id, studentYear","students_table","indexNumber='$indexNumber' OR (Lastname='$Lastname' AND Othernames='$Othernames')");
            
                                    if($index == "empty"){
                                        $password = password_hash("Password@1", PASSWORD_DEFAULT);
                                        $sql = "INSERT INTO students_table (indexNumber, Lastname, Othernames, Gender, houseID, school_id, studentYear, guardianContact, programme, program_id, boardingStatus, password)
                                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
                                        $stmt = $connect2->prepare($sql);
                                        $stmt->bind_param("ssssiiississ",$indexNumber, $Lastname, $Othernames, $Gender, $houseID, $user_school_id, $studentYear,
                                            $guardianContact,$programme, $program_id, $boardingStatus, $password);
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
                                    }elseif($index["studentYear"] == 0 || empty($index["studentYear"])){
                                        $sql = "UPDATE students_table SET studentYear=? WHERE indexNumber=?";
                                        $stmt = $connect2->prepare($sql);
                                        $stmt->bind_param("is",$studentYear,$indexNumber);
    
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
                                    $connect2->rollback();
                                    $message = throwableMessage($th);
                                }
                            }
                            $connect2->commit();

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

    close_connections();
?>