<?php
    //for database purposes
    require("database_functions.php");

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
            $row = $res->fetch_assoc();
        }else{
            $row = "error";
        }

        return $row;
    }

    /**
     * This function will be used to retrieve the roles in the system
     * 
     * @param int $role_id This parameter receives the role id to be searched for
     * @param bool $isTitle Determine if only the title should be displayed
     * 
     * @return string|array The title or full details of the role is retrieved
     */
    function getRole($role_id, $isTitle = true):string|array{
        global $connect;

        $title = $isTitle ? "title" : "*";

        $sql = "SELECT $title 
            FROM roles 
            WHERE id=$role_id" or die($connect->error);
        $res = $connect->query($sql);

        if($res->num_rows > 0){
            if($isTitle){
                $row = $res->fetch_assoc()["title"];
            }else{
                $row = formatRoleData($res->fetch_assoc());
            }
        }else{
            $row = "error";
        }

        return $row;
    }

    /**
     * This is used to format the results that come from the database for roles
     * @param array $role_data The data retrieved
     * @return array the formated data
     */
    function formatRoleData(array $role_data):array{
        foreach($role_data as $key => $value){
            switch($key){
                case "id":
                case "school_id":
                case "access":
                    $role_data[$key] = (int) $value; break;
                case "price":
                    $role_data[$key] = (float) $value; break;
                case "is_system":
                    $role_data[$key] = (bool) $value; break;
            }
        }
        return $role_data;
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
                    $file_name = trim($file_name);
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

            return boolval($row['new_login']);
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
     * @return array|string An array is returned when the all parameter is set to true
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
            $row = $result->fetch_assoc();

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
     * @param string|array $columns This receives the roles to fetch
     * @param string|array $table Receives table name
     * @param string|array $where Receives a where clause command
     * @param int $limit Number of rows to deliver. Default is 1. Use 0 to fetch everything
     * @param array|string $where_binds This is used to bind where conditions
     * @param string $join_type This is the type of join to be used in a table
     * @param string|array $group_by This is used in case there is a group function 
     * @param string|array $order_by order results by some columns
     * @param bool $asc order is in ascending order by default
     * 
     * @return string|array returns a(n) array|string of data or error
     */
    function fetchData(string|array $columns, string|array $table, 
        string|array $where = "", int $limit = 1, string|array $where_binds = "",
        string $join_type = "", string|array $group_by = "", string|array $order_by = "", bool $asc = true
    ){
        global $connect;

        try{
            $columns = stringifyColumn($columns);
            $table = stringifyTable($table, $join_type);
            $where = stringifyWhere($where, $where_binds);

            $sql = "SELECT $columns FROM $table";
            $sql .= !empty($where) ? " WHERE $where" : "";

            //automatically detect that know that all data is been fetched if where is empty
            if(empty($where)){
                $limit = 0;
            }else{
                if(!empty($group_by)){
                    $sql .=" GROUP BY ";
                    $sql .= is_array($group_by) ? implode(", ", $group_by) : $group_by;
                }
            }

            if(!empty($order_by)){
                $sql .= " ORDER BY ";
                $sql .= is_array($order_by) ? implode(", ", $order_by) : $order_by;

                if($asc){
                    $sql .= " ASC";
                }else{
                    $sql .= " DESC";
                }
            }

            //add the limit if the limit is set
            $sql .= $limit > 0 ? " LIMIT $limit" : "";

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
        }catch(Throwable $th){
            $result = throwableMessage($th);
        }

        return $result;
    }

    /**
     * Function to directly query connect2 database
     * 
     * @param string|array $columns This receives the roles to fetch
     * @param string|array $table Receives table name
     * @param string|array $where Receives a where clause command
     * @param int $limit Number of rows to deliver. Default is 1. Use 0 to fetch everything
     * @param string|array $where_binds Conditions to bind
     * @param string $join_type This is the type of join to be used in a table
     * @param string|array $group_by This is used in case there is a group function 
     * @param string|array $order_by order results by some columns
     * @param bool $asc order is in ascending order by default
     * 
     * @return string|array returns a(n) array|string of data or error
     */
    function fetchData1(string|array $columns, string|array $table, string|array $where = "", int $limit = 1, 
        string|array $where_binds = "", string $join_type = "", string|array $group_by = "", string|array $order_by = "", bool $asc = true
    ){
        global $connect2;

        try{
            $columns = stringifyColumn($columns);
            $table = stringifyTable($table, $join_type);
            $where = stringifyWhere($where, $where_binds);

            $sql = "SELECT $columns FROM $table";
            $sql .= !empty($where) ? " WHERE $where" : "";

            //automatically detect that know that all data is been fetched if where is empty
            if(empty($where)){
                $limit = 0;
            }else{
                if(!empty($group_by)){
                    $sql .=" GROUP BY ";
                    $sql .= is_array($group_by) ? implode(", ", $group_by) : $group_by;
                }
            }

            if(!empty($order_by)){
                $sql .= " ORDER BY ";
                $sql .= is_array($order_by) ? implode(", ", $order_by) : $order_by;

                if($asc){
                    $sql .= " ASC";
                }else{
                    $sql .= " DESC";
                }
            }

            //add the limit if the limit is set
            $sql .= $limit > 0 ? " LIMIT $limit" : "";

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
        }catch(Throwable $th){
            $result = throwableMessage($th);
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
                if($i == 0 || ($i > 0 && $value[$i-1] == "-") || $value[$i-1] == "."){
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
     * @param bool $is_new Check if the current entry is new or not
     * 
     * @return integer|null Returns the integer value for next room to be allocated to student or null if no room could be allocated
    */
    function setHouse($gender, $shs_placed, $ad_index, $house, $boardingStatus, $is_new = true){
        $next_house = null;

        //allow whole database details to be passed here
        $house = decimalIndexArray($house);

        if(!array_key_exists("totalHeads",$house[0])){
            $houses = $house;
            $house = array_map(function($data) use ($gender){
                $gender_room = strtolower($gender)."HeadPerRoom";
                $gender_total_room = strtolower($gender)."TotalRooms";

                return [
                    "id" => $data["id"],
                    "totalHeads" => intval($data[$gender_room]) * intval($data[$gender_total_room])
                ];
            }, $houses);
        }
    
        $total = count($house);

        $last_student_order = $is_new ? "e.enrolDate" : "h.updated_at";
    
        //get last index number to successfully register
        $last_student = fetchData(
            ["h.houseID"],
            [
                ["join" => "cssps enrol_table", "alias" => "c e", "on" => "indexNumber indexNumber"],
                ["join" => "cssps house_allocation", "alias" => "c h", "on" => "indexNumber indexNumber"]
            ],
            [
                "e.gender='$gender'", "e.indexNumber != '$ad_index'", "h.current_data=1", "e.shsID=$shs_placed", 
                "c.boardingStatus='$boardingStatus'", "h.houseID IS NOT NULL", "h.houseID > 0"
            ],
            where_binds: "AND", order_by: $last_student_order, asc: false
        );
        
        if(is_array($last_student)){
            $hid = (int) $last_student["houseID"];

            if(!empty($hid)){
                //retrieve last house id given out
                $id = $hid;
                $next_house = 0;

                //get the total of all houses
                $hs_ttl = decimalIndexArray(fetchData(...[
                    "columns" => ["h.id", "COUNT(ho.indexNumber) as total"],
                    "table" => [
                        "join" => "house_allocation houses",
                        "alias" => "ho h",
                        "on" => "houseID id"
                    ],
                    "where" => ["ho.schoolID=$shs_placed","ho.studentGender='$gender'", "ho.boardingStatus='Boarder'"],
                    "limit" => 0, "where_binds" => "AND", "group_by" => "h.id"
                ]));

                foreach($house as $house_value){
                    $found = false;
                    foreach($hs_ttl as $ttl){
                        if($ttl["id"] == $house_value["id"]){
                            $ttl_data[$house_value["id"]] = (int) $ttl["total"];
                            $found = true;
                        }
                    }
                
                    if(!$found){
                        $ttl_data[$house_value["id"]] = 0;
                    }
                }
                
                for($i = 0; $i < $total; $i++){                    
                    //try choosing the next, previous or current house
                    //start at the last house given out
                    if($house[$i]["id"] == $id){
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
                                $ttl = $ttl_data[$nid];
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
        }else{
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

        if(empty($amount) || is_null($amount)){
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
        //remove any space in number before editing
        $number = str_replace(" ","", $number);

        if($international){     //add +233
            if(substr($number, 0, 3) != "233"){
                //make sure it begins with 0
                if($number[0] != "0" && strlen($number) < 10){
                    $number = "0".$number;
                }

                //replace the zero at the beginning
                if(strlen($number) < 13){
                    $number = substr_replace($number,"233", 0, 1);
                }
            }

            if($space){
                $number = str_split($number, 3);

                $number = implode(" ", $number);
            }
        }else{      //remove +233
            //take first three
            $beginning = substr($number, 0, 3);

            if(str_contains($beginning, "+")){
                $number = str_replace("+233", "0", $number);
            }
            elseif($beginning == "233" || $beginning == "23"){
                $number = str_replace("233","0", $number);
            }

            //insert spaces
            if(strlen($number) < 12){
                $number = str_split($number, 3);

                //let the last 
                if(count($number) >= 4) {
                    $number[2] = $number[2].$number[3];
                    unset($number[3]);
                }

                //set number in xxx xxx xxxx
                $number = implode(" ", $number);
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
     * @param array $arrayData This is the data of array which holds the program id n course id n year level
     * @return string returns a string which compiles all arrays into one string in the format [pid|cid|yid] 
     */
    function stringifyClassIDs($arrayData){
        $newString = "";
        if(is_array($arrayData) && !array_key_exists(0,$arrayData)){
            $newString .= "[{$arrayData['program_id']}|{$arrayData['course_id']}|{$arrayData['class_year']}] ";
        }else if(is_array($arrayData[0])){
            foreach($arrayData as $data){
                $newString .= "[{$data['program_id']}|{$data['course_id']}|{$data['class_year']}] ";
            }
        }else{
            return "wrong array data";
        }

        return $newString;
    }

    /**
     * This function is for the records model to retrive the data of the classes and records of the teacher
     * @param array $arrayData This is the data of array which holds the program strings n course strings
     * @return string returns a string which compiles all arrays into one string in the format [pname|cname] 
     */
    function stringifyClassNames($arrayData){
        $newString = "";
        if(is_array($arrayData) && !array_key_exists(0,$arrayData)){
            $newString .= "[";
            $newString .= empty($arrayData["short_p"]) ? $arrayData["program_name"] : $arrayData["short_p"];
            $newString .= "|";
            $newString .= empty($arrayData["short_c"]) ? $arrayData["course_name"] : $arrayData["short_c"];
            $newString .= "],";
        }elseif(is_array($arrayData[0])){
            foreach($arrayData as $data){
                $newString .= "[";
                $newString .= empty($data["short_p"]) ? $data["program_name"] : $data["short_p"];
                $newString .= "|";
                $newString .= empty($data["short_c"]) ? $data["course_name"] : $data["short_c"];
                $newString .= "],";
            }
        }else{
            return "wrong array data";
        }

        return $newString;
    }

    /**
     * This function converts integers into positions
     * @param int $number This is the number to be converted
     * @return string returns the converted number as a string
     */
    function positionFormat($number):string{
        $number = intval($number);
        $suffix = "";

        switch($number % 10){
            case 1: $suffix = $number > 20 || $number < 10 ? "st" : "th"; break;
            case 2: $suffix = $number > 20 || $number < 10 ? "nd" : "th"; break;
            case 3: $suffix = $number > 20 || $number < 10 ? "rd" : "th"; break;
            default: $suffix = "th";
        }

        return "$number$suffix";
    }

    /**
     * This function would be used to check the overall position of a student
     * @param string $index_number This takes the index number which has its position to be found
     * @param int $year This takes the year been searched for
     * @param int $semester This takes the semester to which the exams was held
     * @param string $exam_type This is an optional parameter which checks the type of exam
     * 
     * @return string returns the position in a string form
     */
    function getStudentPosition($index_number, $year, $semester, $exam_type="exam"):string{
        global $connect2;

        //make sure student exists
        $studentData = fetchData1("school_id, program_id", "students_table", "indexNumber='$index_number'");
        if($studentData === "empty"){
            return "no-student";
        }else{
            $sql = "SELECT SUM(mark) as totalMark, indexNumber 
                FROM results 
                WHERE school_id={$studentData['school_id']} AND exam_year=$year AND semester=$semester 
                    AND exam_type='$exam_type' AND accept_status=1 AND program_id='{$studentData['program_id']}'
                GROUP BY indexNumber
                ORDER BY totalMark DESC";
            $results = $connect2->query($sql);

            if($results->num_rows > 0){
                $position = 0;
                while($row = $results->fetch_assoc()){
                    ++$position;

                    if($row["indexNumber"] === $index_number){
                        break;
                    }
                }

                return positionFormat($position);   
            }else{
                return "$sql";
            }
        }
    }

    /**
     * This function would be used to check for a student's position in a specific subject
     * @param string $index_number This is the index number of the student
     * @param int $course_id This receives the id of the course
     * @param int $year This is the year of the exam
     * @param int $semester This is the semester of the exam
     * @param string $exam_type This is the type of the exam
     * 
     * @return string returns the position of the student in that subject
     */
    function getSubjectPosition($index_number, $course_id, $year, $semester, $exam_type="exam"):string{
        global $connect2;

        //make sure student exists
        $studentData = fetchData1("school_id, program_id", "students_table", "indexNumber='$index_number'");
        if($studentData === "empty"){
            return "no-student";
        }else{
            $sql = "SELECT SUM(mark) as totalMark, indexNumber 
                FROM results 
                WHERE school_id={$studentData['school_id']} AND exam_year=$year AND semester=$semester 
                    AND exam_type='$exam_type' AND course_id=$course_id AND program_id={$studentData['program_id']}
                    AND accept_status=1
                GROUP BY indexNumber
                ORDER BY totalMark DESC";
            $results = $connect2->query($sql);

            if($results->num_rows > 0){
                $position = 0;
                while($row = $results->fetch_assoc()){
                    ++$position;

                    if($row["indexNumber"] === $index_number){
                        break;
                    }
                }

                return positionFormat($position);   
            }else{
                return "no-results";
            }
        }
    }

    /**
     * This function is used to generate the academic year for a selected period
     * @param string $date This takes the date of which academic calendar is presented
     * @return string the academic year
     */
    function getAcademicYear($date){
        //providing a value according to a calculated algorithm
        $this_year = date("Y", strtotime($date));

        //get the academic year
        $prev_year = null;
        $next_year = null;
        $this_date = date("Y-m-d", strtotime($date));

        if($this_date < date("$this_year-09-01")){
            $prev_year = intval($this_year) - 1;
            $next_year = intval($this_year);
        }else{
            $prev_year = intval($this_year);
            $next_year = intval($this_year) + 1;
        }

        return "$prev_year / $next_year";
    }

    /**
     * This function determines what message should be displayed in a try/catch throwable block
     * @param Throwable $throwable This takes the throwable variable
     * @return string Returns the string form of the desired error
     */
    function throwableMessage(Throwable $throwable):string{
        global $developmentServer;
        
        $message = "";
        /*if($developmentServer){
            $message = $throwable->getTraceAsString();
        }else{
            $message = $throwable->getMessage();
        }*/
        $message = $throwable->getMessage();

        return $message;
    }

    /**
     * This function would be used to get the admission year of a user
     * @param int $student_year This is the current year of the student
     * @param int $intended_year This is the year to present the academic year on
     * @return int the expected year
     */
    function getYear($student_year, $intended_year){
        $init_year = intval(date("Y")) - $student_year;

        return $init_year + $intended_year;
    }

    /**
     * This function is going to be used to convert arrays with a common key to numeric keys
     * @param array $array This is the array to be converted
     * @return array an index array profile
     */
    function numericIndexArray($array):array{
        if(is_array($array)){
            if(count($array) > 1){
                $newArray = array_map(function($item){
                    $key = array_keys($item);
                    return $item[$key[0]];
                }, $array);
            }else{
                $key = array_keys($array);
                $newArray = [$array[$key[0]]];
            }
        }else{
            $newArray = ["Invalid Array"];
        }
        
        return $newArray;
    }

    /**
     * This function is used to get the details of the split keys of a school
     * @param int $school_id This is the id of the school
     * @param APIKEY $key_type This receives the type of the api key needed
     * @return string|array the api key(s)
     */
    function getSchoolSplit($school_id, $key_type){
        $response = fetchData((string) $key_type.", status","transaction_splits","schoolID=$school_id");

        return $response;
    }

    /**
     * This function is used to make an array have decimal indexes
     * @param array $array The array to be processed
     * @return array|false returns a decimal indexed array or false if not an array
     */
    function decimalIndexArray($array){
        if(is_array($array)){
            if(array_key_exists(0,$array)){
                return $array;
            }else{
                return [$array];
            }
        }else{
            return false;
        }
    }

    /**
     * This function is used to get the number of subjects of a specified program
     * @param int $program_id This receives the id of the specified program
     * @return false|int returns false or the number of subjects 
     */
    function countProgramSubjects($program_id){
        $subject_ids = getSubjectIDs($program_id);

        return is_array($subject_ids) ? count($subject_ids) : false;
    }

    /**
     * This function is used to return only the course/subject ids of a program
     * @param int $program_id This takes the program's id
     * @return false|array Returns false if there is no result
     */
    function getSubjectIDs($program_id){
        $subject_ids = fetchData1("course_ids","program","program_id=$program_id");
        if($subject_ids == "empty"){
            return false;
        }else{
            $subjects = explode(" ", $subject_ids["course_ids"]);
            
            //remove any trailing space before counting
            if(empty(end($subjects)) || ctype_space(end($subjects)))
                array_pop($subjects);
            
            return $subjects;
        }   
    }
    
    /**
     * This function is used to get the specified course/subject details
     * @param int $program_id This takes the program id
     * @param string|array $columns This takes the columns to retrieve. It retrieves only full names by default. Use the string 'all' if you want all columns
     * @param bool $indexed_array This is true by default and returns an indexed array
     * @return false|string|array Returns false if there is no result or an (indexed) array of results or an error message
     */
    function getProgramSubjects($program_id, $columns, $indexed_array = true){
        global $connect2;

        //get course ids
        $subject_ids = getSubjectIDs($program_id);

        if(!is_array($subject_ids)){
            return false;
        }else{
            $subject_ids = count($subject_ids) == 1 ? $subject_ids[0] : implode("','",$subject_ids);
            $subject_ids = "'$subject_ids'";
        }

        if(is_array($columns)){
            $columns = implode(",", $columns);
        }elseif(strtolower($columns) === "all"){
            $columns = "*";
        }

        try {
            $sql = "SELECT $columns FROM courses WHERE course_id IN ($subject_ids)";
            $query = $connect2->query($sql);

            if($query->num_rows > 0){
                $results = $query->fetch_all(MYSQLI_ASSOC);
                return $indexed_array ? decimalIndexArray($results) : $results;
            }else{
                return false;
            }
        } catch (\Throwable $th) {
            return throwableMessage($th);
        }
    }

    /**
     * This is a function used to get a specified program's teachers full names (Lname Onames)
     * @param int $program_id This is the program id to search
     * @param string|array $column This receives the specified columns from course(c), teacher_classes(tc) and teacher(t) tables
     * @param int $indexed_array [optional] The return array should be indexed or not
     * @return string|array|false returns false if there is no result or a string if there is an error or an array of results 
     */
    function getProgramSubjectNTeachers($program_id, $column=[], $indexed_array = true){
        try{
            if(is_array($column) && count($column) > 1){
                $column = implode(",",$column);
            }elseif(!is_array($column) && strtolower($column) === "all"){
                $column = "*";
            }else{
                $column = "c.course_id, c.course_name, c.credit_hours, c.short_form, CONCAT(t.lname,' ', t.oname) as fullname";
            }

            $course_ids = getSubjectIDs($program_id);
            
            if(!is_array($course_ids)){
                return $course_ids;
            }

            $course_ids = implode("','",$course_ids);
            $course_ids = "'$course_ids'";

            $data = fetchData1(
                "DISTINCT $column",
                "courses c
                LEFT JOIN teacher_classes tc ON c.course_id = tc.course_id
                LEFT JOIN teachers t ON tc.teacher_id=t.teacher_id",
                "tc.program_id=$program_id AND c.course_id IN ($course_ids)",0
            );

            return $indexed_array ? decimalIndexArray($data) : $data;
        }catch(\Throwable $th){
            return throwableMessage($th);
        }
    }

    /**
     * This function is for holding an array of teacher names (lname,oname or fullname) or ids
     * @param int $program_id The program to checked
     * @param boolean $return_name This when false will return the ids of the teachers, else would return the fullname
     * @param string|array $columns It takes the columns to return, either lname, oname, separated, or fullname
     * @param boolean $indexed_array The array should be indexed by default
     * @return array|string|false returns an array of data or false if not found or a string for error message 
     */
    function getProgramTeachers($program_id, $return_name = true, $columns="fullname", $indexed_array=true){
        global $connect2;

        if($return_name === false){
            $columns = "teacher_id";
        }

        $index = "fullname";
        $isDefault = false;

        if(!is_array($columns)){
            if(strtolower($columns) == "fullname"){
                $columns = "CONCAT(lname, ' ', oname)";
                $isDefault = true;
            }
        }else{
            $columns = implode(",",$columns);
        }

        $search = ["lname","oname"];
        $replace = ["t.lname","t.oname"];

        if(str_contains($columns, "t.") === false){
            $columns = str_replace($search, $replace, $columns);

            if($isDefault){
                $columns .= " AS fullname";
            }
        }

        $course_ids = getSubjectIDs($program_id);
        
        $teachers = [];
        // $teachers = fetchData1($columns,"teacher_classes tc LEFT JOIN teachers t ON t.teacher_id=tc.teacher_id","tc.program_id=$program_id AND tc.course_id IN (".implode(",",$course_ids).")",0);
        // print_r($teachers);
        foreach($course_ids as $course_id){
            $result = fetchData1($columns,"teachers t JOIN teacher_classes tc ON t.teacher_id=tc.teacher_id","tc.program_id=$program_id AND tc.course_id=$course_id");
            
            if(is_array($result)){
                array_push($teachers, $result[$index]);
            }else{
                array_push($teachers, "none");
            }
        }
        
        return $indexed_array ? decimalIndexArray($teachers) : $teachers;
    }

    /**
     * This function is used to check if results entry mode is valid
     * @param int $school_id The school to check
     * @return bool True if its open and false if otherwise
     */
    function checkResultsEntry(int $school_id){
        $record_date = fetchData1("end_date, start_date","record_dates","school_id=$school_id");

        if(is_array($record_date)){
            list("end_date"=>$end_date, "start_date"=>$start_date) = $record_date;
            $now = date("Y-m-d H:i:s");

            if(strtotime($now) >= strtotime($end_date) || strtotime($start_date) > strtotime($now)){
                $response = false;
            }else{
                $response = true;
            }
        }else{
            $response = false;
        }

        return $response;
    }

    /**
     * Used to validate if a group of recipients are email addresses. Used for emailing
     * @param string|array $recipients The recipient data
     * @param null|mixed $message The message to be sent if there is an error
     * @return bool true if everything is fine or false if otherwise
     */
    function checkRecipients(string|array $recipients, &$message = null){
        $isValid = true;

        if(!is_array($recipients)){
            $recipients = [$recipients];
        }

        foreach($recipients as $recipient){
            if(!filter_var(trim($recipient), FILTER_VALIDATE_EMAIL)){
                $isValid = false;
                $message = "'$recipient' is not a valid email";
                break;
            }
        }

        return $isValid;
    }

    /**
     * This function is used to merge mails which are not in the database to the mailing list
     * @param array $original This is the original data from the enduser
     * @param array $db_data This is the data from the database
     * @return array Returns the merged results
     */
    function mergeRecipients(array $original, array $db_data){
        $found_emails = array_column($db_data, "email");
        $emails_not_found = array_diff($original, $found_emails);

        foreach($emails_not_found as $email){
            $db_data[] = [
                "email" => $email,
                "name" => "No name",
                "username" => "No username",
                "school" => "No school",
                "phone" => "No phone",
            ];
        }

        return $db_data;
    }

    /**
     * Used to format school details in form ["school_id" => "admin_user_role"]
     * @param array $schools The schools details in the form [0 => [school_id, admin_user_role]]
     * @return array|false the new formated data or false if not an array
     */
    function formatSchoolForPayment(array $schools){
        if(is_array($schools)){
            //rearrange into form ["school_id" => "admin_role_id"]
            $schools = decimalIndexArray($schools);
            $schools_new = [];
            foreach($schools as $school){
                $schools_new[$school["id"]] = [
                    "role" => (int) $school["role"],
                    "students" => (int) $school["total"]
                ];
            }
        }else{
            $schools_new = false;
        }

        return $schools_new;
    }

    /**
     * Delete every detail about a school from the database
     * @param int|string $school_id The school id
     */
    function deleteSchoolDetails(int $school_id){
        global $connect2, $connect;

        //for admission database
        $sql = "DELETE FROM admins_table WHERE school_id=$school_id; 
                DELETE FROM admissiondetails WHERE schoolID=$school_id; 
                DELETE FROM cssps WHERE schoolID=$school_id; 
                DELETE FROM enrol_table WHERE shsID=$school_id;
                DELETE FROM houses WHERE schoolID=$school_id;
                DELETE FROM house_allocation WHERE schoolID=$school_id;
                DELETE FROM transaction_splits WHERE schoolID=$school_id;";
        mysqli_multi_query($connect, $sql);

        $sql = array();

        // for records database
        $tables = [
            "accesspay","accesstable","announcement","attendance","courses","exeat",
            "program", "record_approval", "record_cleaning", "record_dates", "results",
            "saved_results","school_ussds", "students_table", "teachers", "teacher_classes"
        ];

        foreach($tables as $table){
            $sql[] = "DELETE FROM $table WHERE school_id=$school_id";
        }

        $sql = implode(";", $sql);

        mysqli_multi_query($connect2, $sql);
    }

    /**
     * Determine if the phone number provided is a valid phone number
     * @param string $phone_number The phone number to be validated
     * @param string|null $network_provider Optional network provider
     * @return bool returns true if phone number is valid and false if otherwise
     */
    function checkPhoneNumber(string $phone_number, ?string $network_provider = null) :bool{
        global $phoneNumbers;
        global $phoneNumbers1;

        $phone_number = remakeNumber($phone_number, false, false);

        if(is_null($network_provider)){
            return array_search(substr($phone_number,0,3), $phoneNumbers);
        }else{
            return array_search(substr($phone_number, 0, 3), $phoneNumbers1[strtolower($network_provider)]);
        }        
    }