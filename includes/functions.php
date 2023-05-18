<?php
    /**
     * The purpose of this function is to retrieve user details from database
     * 
     * @param int $id This variable will take the user id for extraction
     * 
     * @return array The function returns an array of results
     * @return string The function returns an error in a string format
     */
    function getUserDetails($id){
        global $connect;

        $sql = "SELECT * FROM admins_table WHERE user_id=$id";
        $res = $connect->query($sql);

        if($res->num_rows > 0){
            $row = $res->fetch_array();
        }else{
            $row = "error";
        }

        return $row;
    }

    /**
     * This function will be used to retrieve the roles in the system
     * 
     * @param int $role_id This parameter receives the role id to be searched for
     * 
     * @return string The title of the role is retrieved
     */
    function getRole($role_id):string{
        global $connect;

        $sql = "SELECT title 
            FROM roles 
            WHERE id=$role_id" or die($connect->error);
        $res = $connect->query($sql);

        if($res->num_rows > 0){
            $row = $res->fetch_array()["title"];
        }else{
            $row = "error";
        }

        return $row;
    }

    /**
     * This function is responsible for storing an image into
     * a local storage location
     * 
     * @param string $image_input_name This is the name of the input file tag to retrieve data from
     * @param string $local_storage_directory This is the directory path where the image is stored
     * @param string $default_image_path This is the path of the default image to be used if there 
     * is no image provided
     * @param int $pic_quality This is the quality of the image to be compressed
     * 
     * @return string This function return the directory of the image stored
     */
    function getImageDirectory(string $image_input_name, string $local_storage_directory, string $default_image_path = "", $pic_quality = 40):string{
        if(isset($_FILES[$image_input_name]) && $_FILES[$image_input_name]["tmp_name"] != null){
            //compress the image to 40% quality
            $_FILES[$image_input_name]["tmp_name"] = compress($_FILES[$image_input_name]["tmp_name"], $_FILES[$image_input_name]["tmp_name"], $pic_quality);
            
            //get a local directory
            $image_directory = $local_storage_directory;

            //grab the target filename
            $image_name = $image_directory . basename($_FILES[$image_input_name]["name"]);

            //raise a flag for upload
            $uploadOk = 1;

            //get the image file type
            $file_type = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

            //checks to prevent fake image upload
            $check = getimagesize($_FILES[$image_input_name]["tmp_name"]);

            if($check !== false){
                $image_directory = $image_name;
                $uploadOk = 1;
            }else{
                echo "File uploaded is not an image<br>";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg" && $file_type != "gif" && $file_type != "jfif") {
                echo "Sorry, only JPG, JPEG, PNG, GIF and JFIF files are allowed.";
                $uploadOk = 0;

                $image_directory = "wrong_format";

                return $image_directory;
            }

            //if file already exists, try to create another filename
            if(file_exists($image_directory)){
                //split into directories
                $image_directory = explode("/", $image_directory);

                //get file directory path
                $file_directory = "";

                foreach ($image_directory as $row){
                    if($row != end($image_directory))
                        $file_directory .= "$row/";
                }

                $filename = end($image_directory);

                //split file name into name and extension
                $filename = explode(".", $filename);

                //reset the path
                $image_directory = $file_directory.$filename[0].".".$filename[1];

                //set a counter to count image number for new name
                $counter = 1;

                //create a new image name till unique name is formed
                while(file_exists($image_directory)){
                    $image_directory = $file_directory.$filename[0]."_$counter".".".$filename[1];
                    
                    $counter++;
                }

                //when loop is over, let the image be prepared for upload
                $uploadOk = 1;
            }

            //now upload the file
            if($uploadOk == 1){
                if(move_uploaded_file($_FILES[$image_input_name]["tmp_name"], $image_directory)){
                    #process complete
                }else{
                    $image_directory = "upload_fail";
                }
            }else{
                $image_directory = "file_error";
            }
        }else{
            //if no image is uploaded, provide a default image directory
            $image_directory = $default_image_path;
        }

        return $image_directory;
    }

    /**
     * This function is responsible for compressing the file size of image files.
     * 
     * @param string $source This is the name of the image file to be compressed.
     * @param string $destination This is the name of the storage directory for the image file
     * @param integer $quality This is the quality of the image
     * 
     * @return string The return value is the destination of the compressed image
     */
    function compress($source, $destination, $quality):string {
        $image = null;
        $info = getimagesize($source);
    
        if ($info['mime'] == 'image/jpeg') 
            $image = imagecreatefromjpeg($source);
    
        elseif ($info['mime'] == 'image/gif') 
            $image = imagecreatefromgif($source);
    
        elseif ($info['mime'] == 'image/png') 
            $image = imagecreatefrompng($source);
    
        imagejpeg($image, $destination, $quality);
    
        return $destination;
    }

    /**
     * This function is responsible for storing any file into
     * a local storage location
     * 
     * @param string $file_input_name This is the name of the input file tag to retrieve data from
     * @param string $local_storage_directory This is the default directory path where the file is stored
     * 
     * @return string This function return the directory of the file stored
     */
    function getFileDirectory(string $file_input_name, string $local_storage_directory):string{
        if(isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]["tmp_name"] != null){
            //get a local directory
            $base_directory = $local_storage_directory;

            //grab the target filename
            $file_name = $base_directory . basename($_FILES[$file_input_name]["name"]);

            //raise a flag for upload
            $uploadOk = 1;

            //if file already exists, try to create another filename
            if(file_exists($file_name)){
                $counter = 1;

                //split the pathname and extension
                $temp_file_name = explode("/",$file_name);

                $file_directory = "";

                foreach ($temp_file_name as $row){
                    if($row != end($temp_file_name)){
                        $file_directory .= "$row/";
                    }
                }
                $filename = end($temp_file_name);
                
                //divide filename into name and extension
                $filename = explode(".", $filename);

                //loop through until unique file is found
                while(file_exists($file_name)){
                    //set the filename path into a new filename path
                    $file_name = $file_directory.$filename[0]."_".$counter++.".".$filename[1];
                }

                //when loop is over, let the image be prepared for upload
                $uploadOk = 1;
            }

            //now upload the file
            if($uploadOk == 1){
                if(move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $file_name)){
                    $file_name = $file_name;
                }
            }else{
                echo "<p>Upload failed</p>";
            }
        }else{
            $file_name = "error";
        }

        return $file_name;
    }

    /** 
    * Function to retrieve file extension
    * @param string $file_input_name Receives input field name for file
    *
    * @return string extension of file
    */
    function fileExtension(string $file_input_name):string{
        $name = $_FILES[$file_input_name]["name"];
        $name = explode(".", $name);

        return end($name);
    }

    /**
     * This function will be used to check if the specified person is a new user or not
     * 
     * @param int $user_id This parameter receives the index of the user
     * @return bool|string The return value is either a true (for new user) or a false (for not new user)
     */
    function checkNewUser($user_id) {
        global $connect;
        
        $sql = "SELECT new_login 
            FROM admins_table 
            WHERE user_id=$user_id" or die($connect->error);

        $res = $connect->query($sql);

        if($res->num_rows > 0){
            $row = $res->fetch_array();

            return $row['new_login'];
        }else{
            return "invalid-user";
        }
    }

    /**
     * This function is responsible for retrieving data about schools in the system
     * 
     * @param mixed $key This parameter can either be a string with the name of the school
     * or an integer holding the school's id
     * @param bool $all This parameter is a  boolean responsible for revealing if an
     * array should return or not. It is false by default
     * 
     * @return string A string (school name) is returned when the key is an integer
     * @return int An integer is return when the key is a string
     * @return array An array is returned when the all parameter is set to true
     */
    function getSchoolDetail($key, bool $all = false){
        global $connect;

        if(intval($key) > 0 && !$all){
            $sql = "SELECT schoolName
                    FROM schools
                    WHERE id=$key";
        }elseif(!intval($key) && !$all){
            $sql = "SELECT id
                    FROM schools
                    WHERE schoolName='$key'";
        }elseif(intval($key) > 0 && $all){
            $sql = "SELECT * 
                    FROM schools
                    WHERE id=$key";
        }elseif(!intval($key) && $all){
            ///sql is responsible for storing the sql statement
            $sql = "SELECT *
                    FROM schools
                    WHERE schoolName='$key'";
        }

        $result = $connect->query($sql);

        if($result->num_rows > 0){
            $row = $result->fetch_array();

            return $row;
        }else{
            return "error";
        }
    }

    /**
     * The purpose of this function is to allow number of details to be found for notifications
     * 
     * @param string $audience This is the receives the kind of audience to reveal. It is defaulted as all
     * @param string $type This is the variable for taking the type of notification to count. It is defaulted as all
     * @param boolean $read This is for checking if user is requesting user read or unread messages
     *
     *  @return int returns total number of notifications requested
     */
    function notificationCounter($audience = "all", $type = "all", $read = false):int{
        global $connect;

        //variables that will be returned
        $total = 0;

        //get username
        if(isset($_SESSION['user_login_id'])){
            $user = getUserDetails($_SESSION['user_login_id']);
            $username = $user["username"];
            // $school_id = $user["school_id"];
            $role = $user["role"];
            
            $sql = "SELECT ID 
                    FROM notification
                    WHERE ID > 0";

            if($audience != "all" && $type != "all"){
                $sql .= " AND Audience LIKE '%$audience%'
                        AND Notification_type = '$type'";
            }elseif($audience != "all" && $type == "all"){
                $sql .= " AND Audience LIKE '%$audience%'";
            }elseif($audience == "all" && $type != "all"){
                $sql .= " AND Notification_type = '$type'";
            }
            
            if($read == false){
                $sql .= " AND Read_by NOT LIKE '$username'";

                // //filter by school
                // if(!empty($school_id)){
                //     $sql .= " AND School_id = $school_id";
                // }
            }else{
                if($role > 2){
                    $sql .= " AND Read_by LIKE '$username'";
                }
                
            }

            //generate total number
            $res = $connect->query($sql);

            $total = $res->num_rows;
        }
        
        return $total;
    }

    /**
     * The purpose of this function is to allow number of details to be found for replies
     * 
     * @param int $comment_id This is the receives the id of the current comment box
     * 
     * @return int returns total number of replies to a notification
     */
    function replyCounter($comment_id = 0):int{
        global $connect;

        //variables that will be returned
        $total = 0;

        //get username
        if(isset($_SESSION['user_login_id'])){
            $user = getUserDetails($_SESSION['user_login_id']);
            $username = $user["username"];
            $role = $user["role"];

            if($comment_id == 0){
                //main purpose is for counting
                $sql = "SELECT ID
                        FROM reply
                        WHERE Read_by NOT LIKE '$username'";
                
                //check if user is a superadmin
                // if($role < 2){
                //     $sql .= " AND AdminRead = FALSE";
                // }else{
                //     $sql .= " AND Read_by NOT LIKE '$username'";
                // }
            }else{
                $sql = "SELECT ID 
                FROM reply
                WHERE Comment_id = '$comment_id'";
            }
            

            //generate total number
            $res = $connect->query($sql);

            $total = $res->num_rows;
        }
        
        return $total;
    }

    /**
     * Function to directly query database
     * 
     * @param string $columns This receives the roles to fetch
     * @param string $table Receives table name
     * @param string $where Receives a where clause command
     * @param int $limit Number of rows to deliver. Default is 1. Use 0 to fetch everything
     * 
     * @return string returns a string of data or error
     * @return array returns an array of data
     */
    function fetchData(string $columns, string $table, string $where, int $limit = 1){
        global $connect;

        $sql = "SELECT $columns
                FROM $table";
        
        if($where !== ""){
            $sql .= " WHERE $where";
        }
                

        //determine if it should set all or some
        if($limit > 0){
            $sql .= " LIMIT $limit";
        }
        
        try {
            $query = $connect->query($sql);

            if($query->num_rows > 0){
                if($query->num_rows == 1){
                    $result = $query->fetch_assoc();
                }else{
                    $result = $query->fetch_all(MYSQLI_ASSOC);
                }
                    
            }else{
                $result = "empty";
            } 
        } catch (\Throwable $th) {
            $result = $th->getMessage();
            echo $result;
        }

        return $result;
    }

    /**
     * Function to directly query connect2 database
     * 
     * @param string $columns This receives the roles to fetch
     * @param string $table Receives table name
     * @param string $where Receives a where clause command
     * @param int $limit Number of rows to deliver. Default is 1. Use 0 to fetch everything
     * 
     * @return string returns a string of data or error
     * @return array returns an array of data
     */
    function fetchData1(string $columns, string $table, string $where, int $limit = 1){
        global $connect2;

        $sql = "SELECT $columns
                FROM $table";
        
        if($where !== ""){
            $sql .= " WHERE $where";
        }
        
        //determine if it should set all or some
        if($limit > 0){
            $sql .= " LIMIT $limit";
        }
        
        try {
            $query = $connect2->query($sql);

            if($query->num_rows > 0){
                if($query->num_rows == 1){
                    $result = $query->fetch_assoc();
                }else{
                    $result = $query->fetch_all(MYSQLI_ASSOC);
                }
                    
            }else{
                $result = "empty";
            }    
        } catch (\Throwable $th) {
            $result = $th->getMessage();
            echo $result;
        }
           

        return $result;
    }

    /**
     * This is a function to format entry of names into proper nouns
     * 
     * @param string $name The name of the person to be formated
     * @return string formated name
     */
    function formatName($name){
        //separate them by spaces
        $new_name = explode(" ",$name);

        //reset the name variable
        $name = "";

        //make needed changes to name
        foreach ($new_name as $key => $value) {
            $value_size = strlen($value);

            //make formatting here
            for ($i=0; $i < $value_size; $i++) { 
                //capitalize only first alphabet or any value after a hyphen or dot
                if($i == 0 || $value[$i-1] == "-" || $value[$i-1] == "."){
                    $value[$i] = strtoupper($value[$i]);
                }else{
                    //make any other value lowercase
                    $value[$i] = strtolower($value[$i]);
                }
            }

            //push new value into name
            if(strlen($name) == 0){
                $name = $value;
            }else{
                $name .= " $value";
            }
        }

        //return name
        return $name;
    }

    /**
     * A function to separate names in camelCase
     * 
     * @param string $name This is the name to be separated
     * @return string returns the separated name
     */
    function separateNames(string $name){
        $temp_name = "";
        for($i=0; $i < strlen($name); $i++){
            if($name[$i] != strtoupper($name[$i])){
                $temp_name .= $name[$i];
            }elseif(($i-1 >= 0) && $name[$i] == strtoupper($name[$i]) && $name[$i-1] == strtoupper($name[$i-1])){
                $temp_name .= $name[$i];
            }else{
                //separate with space
                $temp_name .= " ".$name[$i];
            }
        }

        return $temp_name;
    }
    
    /**
     * This function is used to add a suffix to numbers
     * @param $value This receives the value to be changed
     * 
     * @return string It returns a string representation of the value
     */
    function numberShortner($value){
        $value = intval($value);
        $divisor = array(
            0 => array(
                "div" => 1000000,
                "val" => "M"
            ),
            1 => array(
                "div" => 1000,
                "val" => "K"
            ),
            2 => array(
                "div" => 10,
                "val" => ""
            )
        );
    
        $final = "";
    
        for($i=0; $i < count($divisor);$i++){
            $divide = $value / $divisor[$i]["div"];
    
            if($value >= 1000)
                $divide = round($divide,1);
            else
                $divide = intval($divide) * $divisor[$i]["div"];
    
            if($divide >= 1){
                $final = $divide.$divisor[$i]["val"];
                break;
            }
        }
    
        return $final;
    }
    
    /**
     * Function to automatically set houses for students
     * 
     * @param string $gender This receives the gender of the student
     * @param integer $shs_placed This receives the id of the school placed
     * @param string $ad_index This is the index number of the student to be allocated a house
     * @param array $house Receives an array of house ids
     * @param string $boardingStatus Receives the boarding status to be checked on, defaults on boarder
     * 
     * @return integer|null Returns the integer value for next room to be allocated to student or null if no room could be allocated
    */
    function setHouse($gender, $shs_placed, $ad_index, $house, $boardingStatus){
        global $connect;
        $next_house = null;
    
        $total = count($house);
    
        //get last index number to successfully register
        $last_student = fetchData("e.indexNumber",
            "cssps c JOIN enrol_table e ON c.indexNumber = e.indexNumber",
            "e.gender='$gender' AND e.indexNumber != '$ad_index' AND e.shsID=$shs_placed AND c.boardingStatus = '$boardingStatus' ORDER BY e.enrolDate DESC"
        );
        // $last_student = fetchData("indexNumber","enrol_table","shsID=$shs_placed AND gender='$ad_gender' AND indexNumber != '$ad_index' ORDER BY enrolDate DESC");
        
        if(is_array($last_student)){
            $last_student = $last_student["indexNumber"];
            
            //search for last house allocation entry for this gender
            $sql = "SELECT houseID
                FROM house_allocation
                WHERE indexNumber='$last_student'";
            $result = $connect->query($sql);
    
            $hid = $result->fetch_assoc()["houseID"];
            
            if(is_null($hid) || $hid == "empty"){
                //look for the last correctly placed student
                $sql = "SELECT houseID FROM house_allocation 
                    WHERE schoolID=$shs_placed AND studentGender='$gender' AND houseID IS NOT NULL 
                    AND boardingStatus='$boardingStatus' ORDER BY indexNumber DESC LIMIT 1";
                $result = $connect->query($sql);
                
                $hid = $result->fetch_assoc()["houseID"];
            }
    
            if(!is_null($hid)){
                //retrieve last house id given out
                $id = $hid;
                $next_house = 0;
                
                for($i = 0; $i < $total; $i++){                    
                    //try choosing the next, previous or current house
                    //start at the last house given out
                    if($house[$i]["id"] == $id){
                        $ttl = null;
                        $check_count = 0;       //this variable would be used check if all houses have been checked at most once
                        
                        //select a house and check its availability
                        while($next_house < 1){
                            if(strtolower($boardingStatus) === "boarder"){
                                $house_pointer = 0;     //this is a pointer to the house array provided
                                if($check_count == $total){
                                    //forcefully exit the function if all houses are full
                                    return null;
                                }elseif($i+1 == $total){
                                    //current pointer equals last house in array, pick first house in array for checking
                                    $house_pointer = 0;
                                }elseif($i+1 < $total && $i >= 0){
                                    //next house is not at the end of the array for checking
                                    $house_pointer = $i + 1;
                                }

                                //get house id, total epected membership and current total membership
                                $nid = $house[$house_pointer]["id"];
                                $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$nid AND studentGender='$gender' AND boardingStatus='Boarder'")["total"];
                                $cur_ttl = $house[$house_pointer]["totalHeads"];
                                
                                //check immediate available houses
                                if($i+1 == $total && $ttl < $cur_ttl){
                                    //Give boarder candidate first house in array
                                    $next_house = $house[0]["id"];
                                }elseif($i+1 < $total && $i >= 0 && $ttl < $cur_ttl){
                                    //Give boarder candidate a next house
                                    $next_house = $house[$house_pointer]["id"];
                                }

                                //keep track of the number of houses checked
                                ++$check_count;
                            }elseif(strtolower($boardingStatus) === "day"){
                                //check immediate available houses
                                if($i+1 == $total){
                                    //Give day candidate current house in array
                                    $next_house = $house[0]["id"];
                                }elseif($i+1 < $total && $i >= 0){
                                    //Give day candidate a next house
                                    $next_house = $house[$i+1]["id"];
                                }
                            }
                            
                            
                            //increment i
                            $i++;
    
                            //start from 0 if end is reached but no house
                            if($i+1 > $total && $next_house < 1){
                                $i = 0;
                            }
                        }
                    }
                    
                    //break the house checking if a house has been allocated
                    if($next_house > 0){
                        break;
                    }
                }
            }else{
                //this means it is the first entry
                $next_house = $house[0]["id"];
            }
        }elseif(strtolower($last_student) === "empty"){
            $next_house = $house[0]["id"];
        }
    
        return $next_house;
    }

    /**
     * The purpose of this function is to get the total amount of money made by a school
     * 
     * @param integer $user_role This takes the role of the user or admin
     * @param integer $schoolID This is the ID of the school of the user
     * 
     * @return float returns the total amount of money made by the user
     */
    function getTotalMoney($user_role,$schoolID):float{
        global $connect;
        $amount = 0;

        $sql = "SELECT SUM(amount) as amountSum FROM payment WHERE user_role = $user_role AND school_id = $schoolID AND status = 'Sent'";
        $res = $connect->query($sql);

        if($res->num_rows > 0){
            $amount = $res->fetch_assoc()["amountSum"];
        }

        if(is_null($amount)){
            $amount = 0;
        }

        return $amount;
    }

    /**
     * This function is used to format numbers into international and local formats
     * 
     * @param string $number This is the number to be formatted
     * @param bool $international This determines if it should be in international format or local format
     * @param bool $space This determines if it should be spaced
     * 
     * @return string returns a string of the formatted number
     */
    function remakeNumber(string $number, bool $international = false, $space = true){
        if($international){     //add +233
            //remove any space in it
            $number = str_replace(" ","",$number);

            //make sure it begins with 0
            if($number[0] != "0" && strlen($number) < 10){
                $number = "0".$number;
            }

            //replace the zero at the beginning
            if(strlen($number) < 13){
                $number = substr_replace($number,"233", 0, 1);
            }
        }else{      //remove +233
            if(strlen($number) >= 12)
                $number = str_replace("+233", "0", $number);
            
            //insert spaces
            if(strlen($number) < 12){
                $number = str_split($number, 3);

                //set number in xxx xxx xxxx
                $number = $number[0]." ".$number[1]." ".$number[2].$number[3];
            }
        }
        
        if(!$space)
            $number = str_replace(" ", "", $number);

        return $number;
    }
    
    /**
     * The function is used to format the id of a program into the form PID XXXX
     * @param string|int $subject_id This is the id to be converted
     * @param string $prefix The prefix is used to provide the pre-text of the identifier
     * @param bool $reverse This tells if it should convert the item id to integer
     * @return string|int Returns the formatted program id (string or int)
     */
    function formatItemId($subject_id, $prefix, $reverse = false){
        if(!$reverse){
            $subject_id = str_pad($subject_id, 4, "0", STR_PAD_LEFT);

            $subject_id = "$prefix $subject_id";
        }else{
            $subject_id = strtoupper($subject_id);
            $subject_id = str_replace([$prefix," "],"",$subject_id);
            $subject_id = intval($subject_id);
        }

        return $subject_id;
    }

    /**
     * This function is used to present a greeting message based on the time
     * @return string a greeting
     */
    function showGreeting():string{
        $time = date("H");

        if($time < 12){
            $greet = "Good Morning";
        }elseif($time < 16){
            $greet = "Good Afternoon";
        }else{
            $greet = "Good Evening";
        }

        return $greet;
    }

    /**
     * This function is used to provide an index number for a candidate
     * @param int $school_id This is the school id of the user logged in
     * @return string returns a formated index number
     */
    function generateIndexNumber(int $school_id):string{
        global $connect;

        $school_id = str_pad($school_id, 3, "0", STR_PAD_LEFT);
        $current_year = date("y");
        $student_number = str_pad(((rand() % 9999) + 1),4,"0",STR_PAD_LEFT);
        
        return "$school_id$current_year$student_number";
    }
    
    /**
     * 
     */

    /**
     * This function would be used to generate tokens for results posted by teachers
     * @param int $teacher_id This is the id of the teacher
     * @param int $school_id This is the unique index of the teachers school
     * @return string returns a unique token
     */
    function generateToken($teacher_id, $school_id):string{
        $token = "";
    
        //generate three random values
        for($i = 1; $i <= 3; $i++){
            $token .= chr(rand(65,90));
        }
    
        //add teacher id
        $token .= str_pad(strval($teacher_id), 3, "0", STR_PAD_LEFT);
    
        $token = str_shuffle($token);
    
        //random characters
        $token .= chr(rand(65,90)). str_pad($school_id,2,"0",STR_PAD_LEFT);
        $token = substr(str_shuffle($token.uniqid()), 0, 8);
        $token .= date("y");
    
        return strtolower($token);
    }

    /**
     * This function is used to provide the grade of any school
     * @param float $mark This is the mark of the student
     * @param string $exam_type This is the type of exam grading system
     */
    function giveGrade($mark, $exam_type="wassce") {
        $grade = "";
    
        switch($exam_type) {
            case NULL:
            case "wassce":
                if($mark >= 80) {$grade = "A1";}
                elseif($mark >= 70) {$grade = "B2";}
                elseif($mark >= 65) {$grade = "B3";}
                elseif($mark >= 60) {$grade = "C4";}
                elseif($mark >= 55) {$grade = "C5";}
                elseif($mark >= 50) {$grade = "C6";}
                elseif($mark >= 45) {$grade = "D7";}
                elseif($mark >= 40) {$grade = "E8";}
                else {$grade = "F9";}
                
                break;
            case "ctvet":
                if($mark >= 80) {$grade = "D";}
                elseif($mark >= 60) {$grade = "C";}
                elseif($mark >= 40) {$grade = "P";}
                else {$grade = "F";}
                
                break;
        }
    
        return $grade;
    }

    /**
     * This function is for the records model to retrive the data of the classes and records of the teacher
     * @param array $arrayData This is the data of array which holds the program id n course id
     * @return string returns a string which compiles all arrays into one string in the format [pid|cid] 
     */
    function stringifyClassIDs($arrayData){
        $newString = "";
        if(!is_array($arrayData[0])){
            return "wrong array data";
        }else{
            foreach($arrayData as $data){
                $newString .= "[{$data['program_id']}|{$data['course_id']}] ";
            }
        }

        return $newString;
    }

    /**
     * This function is for the records model to retrive the data of the classes and records of the teacher
     * @param array $arrayData This is the data of array which holds the program strings n course strings
     * @return string returns a string which compiles all arrays into one string in the format [pid|cid] 
     */
    function stringifyClassNames($arrayData){
        $newString = "";
        if(!is_array($arrayData[0])){
            return "wrong array data";
        }else{
            foreach($arrayData as $data){
                $newString .= "[";
                $newString .= empty($data["short_p"]) ? $data["program_name"] : $data["short_p"];
                $newString .= "|";
                $newString .= empty($data["short_c"]) ? $data["course_name"] : $data["short_c"];
                $newString .= "],";
            }
        }

        return $newString;
    }
?>