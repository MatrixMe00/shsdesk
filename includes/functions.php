<?php
    // for database purposes

    use PHPMailer\PHPMailer\PHPMailer;

    require "database_functions.php";

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
     * @param ?string $name [Optional] The name of the file
     * @param bool $replace [optional] Used to define if a file should be replaced if its the same name or should be given an index
     * 
     * @return string This function return the directory of the file stored
     */
    function getFileDirectory(string $file_input_name, string $local_storage_directory, ?string $name = null, bool $replace = false): string {
        if(isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]["tmp_name"] != null) {
            // Get a local directory
            $base_directory = $local_storage_directory;
    
            // Grab the target filename
            $original_file_name = basename($_FILES[$file_input_name]["name"]);
    
            // If $name is provided, use it instead of the original filename
            if($name !== null) {
                // Extract the original file extension
                $extension = pathinfo($original_file_name, PATHINFO_EXTENSION);
                $file_name = "$base_directory/$name.$extension";
            } else {
                $file_name = $base_directory . $original_file_name;
            }
    
            // Raise a flag for upload
            $uploadOk = 1;
    
            // If file already exists, try to create another filename
            if(file_exists($file_name) && !$replace) {
                $counter = 1;
    
                // Split the pathname and extension
                $temp_file_name = explode("/", $file_name);
    
                $file_directory = "";
    
                foreach ($temp_file_name as $row) {
                    if($row != end($temp_file_name)) {
                        $file_directory .= "$row/";
                    }
                }
    
                $filename = end($temp_file_name);
                
                // Divide filename into name and extension
                $filename = explode(".", $filename);
    
                // Loop through until unique file is found
                while(file_exists($file_name)) {
                    // Set the filename path into a new filename path
                    $file_name = $file_directory . $filename[0] . "_" . $counter++ . "." . $filename[1];
                }
    
                // When loop is over, let the image be prepared for upload
                $uploadOk = 1;
            }
    
            // Now upload the file
            if($uploadOk == 1) {
                if(move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $file_name)) {
                    $file_name = trim($file_name);
                }else{
                    exit("File move failed");
                }
            } else {
                echo "<p>Upload failed</p>";
            }
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
     * This function is used to generate an sql query
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
     * @param array $multiple_table Takes a list of tables that can appear multiple times during joins
     * 
     * @return string
     */
    function create_query_string(string|array $columns, string|array $table, 
        string|array $where = "", int $limit = 1, string|array $where_binds = "",
        string $join_type = "", string|array $group_by = "", string|array $order_by = "", bool $asc = true,
        array $multiple_table = []
    ):string{
        $columns = stringifyColumn($columns);
        $table = stringifyTable($table, $join_type, $multiple_table);
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

        return $sql;
    }

    /**
     * Used to close the connections for a specified script
     */
    function close_connections(){
        global $connect, $connect2;

        if($connect)
            $connect->close();

        if($connect2)
            $connect2->close();
    }

    /**
     * This function queries the database, usually for select statements
     * 
     * @param mysqli $adapter The sql connection adapter
     * @param string $sql The sql string
     * @param mixed $error_value Optional value to be displayed when results are false. Default is empty
     * @return mixed
     */
    function fetch_query(mysqli $adapter, string $sql, $error_value = "empty"){
        $query = $adapter->query($sql);

        if($query->num_rows > 0){
            $result = $query->num_rows == 1 ? $query->fetch_assoc() : $query->fetch_all(MYSQLI_ASSOC);
        }else{
            $result = $error_value;
        }

        return $result;
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
     * @param array $multiple_table Takes a list of tables that can appear multiple times during joins.
     * Do something like [table_name => max_occurences] If the table must have multiple occurences for a fixed number of times
     * 
     * @return string|array returns a(n) array|string of data or error
     */
    function fetchData(string|array $columns, string|array $table, 
        string|array $where = "", int $limit = 1, string|array $where_binds = "",
        string $join_type = "", string|array $group_by = "", string|array $order_by = "", bool $asc = true,
        array $multiple_table = []
    ){
        global $connect;

        // generate an sql
        $sql = create_query_string(
            $columns, $table, $where, $limit, $where_binds, 
            $join_type, $group_by, $order_by, $asc, $multiple_table
        );

        try{
            $result = fetch_query($connect, $sql);
        }catch(Throwable $th){
            $result = throwableMessage($th, $sql);
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
     * @param array $multiple_table Takes a list of tables that can appear multiple times during joins
     * 
     * @return string|array returns a(n) array|string of data or error
     */
    function fetchData1(string|array $columns, string|array $table, string|array $where = "", int $limit = 1, 
        string|array $where_binds = "", string $join_type = "", string|array $group_by = "", string|array $order_by = "", bool $asc = true,
        array $multiple_table = []
    ){
        global $connect2;

        // generate an sql
        $sql = create_query_string(
            $columns, $table, $where, $limit, $where_binds, 
            $join_type, $group_by, $order_by, $asc, $multiple_table
        );

        try{
            $result = fetch_query($connect2, $sql);
        }catch(Throwable $th){
            $result = throwableMessage($th, $sql);
        }

        return $result;
    }

    /**
     * This is a function to format entry of names into proper nouns
     * 
     * @param ?string $name The name of the person to be formated
     * @return string formated name
     */
    function formatName(?string $name) :string{
        if(is_null($name) || empty($name)){
            return "Not Defined";
        }
        //separate them by spaces
        $new_name = explode(" ",strtolower($name));

        //reset the name variable
        $name = [];

        //make needed changes to name
        foreach ($new_name as $key => $value) {
            $name[] = ucfirst($value);
        }

        //return name
        return trim(implode(" ", $name));
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

        if(empty($boardingStatus)){
            return $next_house;
        }
    
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
                    "where" => ["ho.schoolID=$shs_placed","ho.studentGender='$gender'", 
                        "ho.boardingStatus='Boarder'", "ho.current_data=TRUE"],
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
     * @return array returns an array with the total amount of money and students assigned
     */
    function getTotalMoney($user_role,$schoolID):array{
        global $connect;
        $amount = 0;

        $sql = "SELECT SUM(amount) as amountSum, SUM(studentNumber) AS students FROM payment WHERE user_role = $user_role AND school_id = $schoolID AND status = 'Sent' AND current_data=TRUE";
        $res = $connect->query($sql);

        if($res->num_rows > 0){
            $data = $res->fetch_assoc();
            $amount = $data["amountSum"];
            $students = $data["students"];
        }

        return ["amount" => $amount ?? 0, "students" => $students ?? 0];
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
        $number = str_replace([" ", "+"],"", $number);

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
     * This provides a hashed format of the index number
     * @param string $index_number The index number to be hased
     * @return string|false
     */
    function hash_index_number(string $index_number) :string|false{
        if (is_numeric($index_number) && strlen($index_number) === 12) {
            // Retain the first three digits, mask the middle five with asterisks, and keep the last four
            return substr($index_number, 0, 3) . '*****' . substr($index_number, -4);
        }

        return false;
    }

    /**
     * This is used to check if an index number is similar to its hash
     * @param string $hashed_index The hashed index number
     * @param string $index_number The index number
     * @return bool
     */
    function verify_index_number_hash(string $hashed_index, string $index_number) :bool{
        $index_number = hash_index_number($index_number);
        $response = false;

        if($index_number && $hashed_index == $index_number){
            $response = true;
        }

        return $response;
    }

    /**
     * Logs a throwable error
     * @param Throwable $throwable The throwable message
     * @param ?string $additional Additional message to be added
     */
    function logThrowable(Throwable $throwable, ?string $additional = null) {
        // Define the path to the logs directory
        $logDir = $_SERVER["DOCUMENT_ROOT"] . '/logs/'. date('F_Y');
        
        // Check if the logs directory exists, create it if it doesn't
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
    
        // Define the log file name based on the current month and year
        $logFile = $logDir . '/log_' . date('d_m_Y') . '.log';
    
        // Gather error details
        $timestamp = date('Y-m-d H:i:s');
        $errorType = get_class($throwable);   // Get the exception/error class name
        $errorCode = $throwable->getCode();    // Get the error code (if any)
        $errorMessage = $throwable->getMessage();
        $errorFile = $throwable->getFile();
        $errorLine = $throwable->getLine();
        $errorTrace = $throwable->getTraceAsString() ?: "No stack trace available."; // Handle empty stack trace
    
        // Format the log entry
        $logEntry = "[$timestamp] Error Type: $errorType\n";
        $logEntry .= "Error Code: $errorCode\n";
        $logEntry .= "Message: $errorMessage\n";
        $logEntry .= "File: $errorFile (Line $errorLine)\n";
        $logEntry .= "Stack Trace:\n$errorTrace\n";

        if($additional){
            $logEntry .= "Additional Message: $additional\n";
        }

        $logEntry .= str_repeat("-", 80) . "\n"; // Separator for readability
    
        // Append the log entry to the log file
        file_put_contents($logFile, $logEntry, FILE_APPEND);
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
        $newString = null;
        if(is_array($arrayData) && !array_key_exists(0,$arrayData)){
            $newString = "";
            $newString .= "[";
            $newString .= empty($arrayData["short_p"]) ? $arrayData["program_name"] : $arrayData["short_p"];
            $newString .= "|";
            $newString .= empty($arrayData["short_c"]) ? $arrayData["course_name"] : $arrayData["short_c"];
            $newString .= "]";
        }elseif(is_array($arrayData[0])){
            $newString = [];
            foreach($arrayData as $data){
                $string = "";
                $string .= "[";
                $string .= empty($data["short_p"]) ? $data["program_name"] : $data["short_p"];
                $string .= "|";
                $string .= empty($data["short_c"]) ? $data["course_name"] : $data["short_c"];
                $string .= "]";

                $newString[] = $string;
            }

            $newString = implode(",", $newString);
        }else{
            return "wrong array data";
        }

        return $newString;
    }

    /**
     * This function is used to insert teacher class information
     * @param string $data The teacher data
     * @param int $teacher_id The id of the teacher
     * @return array
     */
    function insert_teacher_classes(string $data, int $teacher_id){
        global $connect2, $user_school_id;

        // get all the programs and courses for the school
        $programs = fetchData1("program_id as id", "program", "school_id = $user_school_id", 0);
        $courses = fetchData1("course_id as id", "courses", "school_id = $user_school_id", 0);
        $skip = false;

        if(is_array($programs) && is_array($courses)){
            $programs = array_column(decimalIndexArray($programs), "id");
            $courses = array_column(decimalIndexArray($courses), "id");

            $parts = explode(" ", $data);
            if(is_array($parts)){
                // remove trailing empty part
                if(end($parts) === ""){
                    array_pop($parts);
                }
                foreach($parts as $part){
                    $part = trim($part,"[]");
                    if(!empty($part)){
                        if(strpos($part,'|') !== false){
                            $part = explode("|",$part);
                            if(is_array($part) && count($part) == 3){
                                $pid = $part[0];
                                $cid = $part[1];
                                $yid = $part[2];

                                // if the program or course is not found, skip
                                if(!in_array($pid, $programs) || !in_array($cid, $courses)){
                                    $skip = true;
                                    continue;
                                }

                                // sql syntax would go here
                                $detailsExist = fetchData1("COUNT(teacher_id) AS total, id","teacher_classes", "school_id=$user_school_id AND program_id=$pid AND course_id=$cid AND class_year=$yid");
                                if(intval($detailsExist["total"]) < 1){
                                    $sql = "INSERT INTO teacher_classes (school_id, teacher_id, program_id, course_id, class_year) VALUES (?,?,?,?,?)";
                                    $stmt = $connect2->prepare($sql);
                                    $stmt->bind_param("iiiii", $user_school_id, $teacher_id, $pid, $cid, $yid);

                                    $stmt->execute();
                                }else{
                                    $detailsExist = fetchData1("t.lname, t.teacher_id","teachers t JOIN teacher_classes tc ON t.teacher_id=tc.teacher_id","tc.id = {$detailsExist['id']}");
                                    if(is_array($detailsExist)){
                                        $message = "No changes have been applied as ".$detailsExist["lname"]." (". formatItemId($detailsExist["teacher_id"], "TID") .") already handles ".formatItemId($cid,"SID")." for Year $yid";
                                    }else{
                                        $message = "Teacher responsible for ".formatItemId($cid,"SID")." has been deleted, but details of him exist. Contact superadmin for help";
                                    }
                                    break;
                                }
                            }else{
                                $message = "Class and subject is not properly separated. Process discontinued";
                                break;
                            }
                        }else{
                            $message = "An invalid split format was rejected";
                            break;
                        }
                    }else{
                        $message = "An empty detail was rejected";
                        break;
                    }
                }
            }else{
                $message = "Invalid class and subject format projected. Process terminated";
            }

            if(empty($message) || !isset($message)){
                $status = true;
                $message = $skip ? "Anomaly has been fixed. Reopen modal to see changes" : "success";
            }
        }

        return ["status" => $status ?? false, "message" => $message ?? "No classes have been provided"];
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
     * @param int $program_id The program id
     * @param string $exam_type This is an optional parameter which checks the type of exam
     * 
     * @return string returns the position in a string form
     */
    function getStudentPosition($index_number, $year, $semester, $program_id, $exam_type="exam"):string{
        global $connect2;

        //make sure student exists
        $studentData = fetchData1("school_id, program_id", "students_table", "indexNumber='$index_number'");
        if($studentData === "empty"){
            return "no-student";
        }else{
            $sql = "SELECT SUM(mark) as totalMark, indexNumber 
                FROM results 
                WHERE school_id={$studentData['school_id']} AND exam_year=$year AND semester=$semester 
                    AND exam_type='$exam_type' AND accept_status=1 AND program_id=$program_id
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
     * @param bool $spaced Verifies if there should be spaces between
     * @return string the academic year
     */
    function getAcademicYear($date, $spaced = true){
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

        return $spaced ? "$prev_year / $next_year" : "$prev_year/$next_year";
    }

    /**
     * formats the academic year
     * @param string $academic_year The academic year to format
     * @param bool $spaced If there should be spaces
     * @return ?string
     */
    function formatAcademicYear($academic_year, $spaced = true):?string{
        if(is_null($academic_year) || !str_contains($academic_year, "/")){
            return null;
        }
        $academic_year = str_replace(" ", "", $academic_year);

        return $spaced ? str_replace("/", " / ", $academic_year) : $academic_year;
    }

    /**
     * This function determines what message should be displayed in a try/catch throwable block
     * @param Throwable $throwable This takes the throwable variable
     * @param ?string $additional_message An additional message to be logged into the error log file
     * @return string
     */
    function throwableMessage(Throwable $throwable, ?string $additional_message = null):string{
        global $developmentServer;
        
        $message = "";
        logThrowable($throwable, $additional_message);
        if($developmentServer){
            if(str_contains($_SERVER["SERVER_NAME"], ".local") === true)
                $message = $throwable->getMessage()." in ".$throwable->getFile()." on line ".$throwable->getLine();
            else
                $message = $throwable->getMessage();
        }else{
            $message = $throwable->getMessage();
        }

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
        if(is_array($array) && !empty($array)){
            return array_key_exists(0,$array) ? $array : [$array];
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
    function validate_email(string|array $recipients, &$message = null){
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
     * Used to format school details in form ["school_id" => "admin_user_role"]
     * @param array $schools The schools details in the form [0 => [school_id, admin_user_role]]
     * @return array|false the new formated data or false if not an array
     */
    function formatSchoolForPayment(array $schools){
        if(is_array($schools)){
            //rearrange into form ["school_id" => "admin_role_id"]
            $schools = decimalIndexArray($schools);
            $schools_new = pluck($schools, "id", "array", true, [
                "ttl" => "students"
            ]);
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
            return in_array(substr($phone_number,0,3), $phoneNumbers);
        }else{
            return in_array(substr($phone_number, 0, 3), $phoneNumbers1[strtolower($network_provider)]);
        }        
    }

    /**
     * Present a dumped data in a more readable format
     * @param mixed $data The data to be checked
     */
    function formatDump(...$data) {
        echo "<pre>";
        foreach ($data as $item) {
            var_dump($item);
        }
        echo "</pre>";
    }

    /**
     * This reverses a year (usually academic year) value to its original state
     * @param ?string $year_value The year value
     */
    function reverseYearURL(?string $year_value) :?string{
        return !is_null($year_value) && !empty($year_value) ? str_replace("_", "/", $year_value) : null;
    }

    /**
     * This is used to get special case values for an array. Typically used for the admin settings page
     * @param array $subject The subject array
     * @param string|int $column_name The column name in the string
     * @param bool $unique This will return the specified column without making the results unique
     * @return array
     */
    function settingsArrayConvert(array $subject, string|int $column_name, bool $unique = true) :array{
        return $unique ? array_unique(array_values(array_column($subject,$column_name))) : 
                array_values(array_column($subject,$column_name));
    }

    /**
     * This is used to provide the subject positions to students
     * 
     * @param string $token The result token
     */
    function assignPositions(string $token) {
        global $connect2;

        $results = decimalIndexArray(fetchData1(["id", "indexNumber", "mark as total"], "results", "result_token='$token'", 0));

        if($results){
            // Sort scores in descending order
            usort($results, function ($a, $b) {
                return $b['total'] <=> $a['total'];
            });

            // Assign positions based on sorted scores
            $current_position = 0;
            $last_grade = -1;
            $success = $failed = 0;

            // set positions
            foreach ($results as $key => $result) {
                if ($result['total'] != $last_grade) {
                    $last_grade = $result['total'];
                    $current_position = $key + 1;
                }
                
                $sql = "UPDATE results SET position=$current_position WHERE id={$result['id']}";
                $status = $connect2->query($sql);

                if($status)
                    $success += 1;
                else
                    $failed += 1;
            }

            return [
                "token" => $token, "initial" => count($results),
                "success" => $success, "failed" => $failed
            ];
        }
    }

    /**
     * Used to set up the output buffer
     */
    function flush_php_start(){
        // Turn off output buffering
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);

        // Disable implicit flush
        ini_set('implicit_flush', true);
        ob_implicit_flush(true);

        // Set a higher time limit if necessary
        set_time_limit(0);
    }

    /**
     * This flushes an output
     */
    function flush_output(){
        ob_flush();
        flush();
    }

    /**
     * Get the current date and time
     * @param string $date Custom datetime or leave at now for current date
     * @param string $format The format to be used 
     * @param ?string $timezone The timezone to be used
     */
    function now(string $date = "now", string $format = "d-m-Y H:i:s", ?string $timezone = null){
        // set the timezone
        $timezone = $timezone ? new DateTimeZone($timezone) : null;
        $date = new DateTime($date, $timezone);
        return $date->format($format);
    }

    /**
     * Used to check merged cells
     * @param $sheet The sheet
     * @param object $cell The cell object
     */
    function checkMergedCells($sheet, $cell){
        foreach ($sheet->getMergeCells() as $row){
            if($cell->isInRange($row)){
                //cell is merged
                return true;
            }
        }

        //cell is by default defined as not merged
        return false;
    }

    /**
     * Creates a column header
     * @param int|string $maxCols The total headings
     * @return array
     */
    function createColumnHeader($maxCols) {
        // If the parameter is a column letter, convert it to a number
        if (!is_numeric($maxCols)) {
            $maxCols = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCols);
        }
    
        $columns = [];
        for ($i = 1; $i <= $maxCols; $i++) {
            $columns[] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
        }
        return $columns;
    }

    /**
     * This is used inside the pluck to trim the keys in an array
     * @param array $array The array to be checked
     */
    function trim_array_keys(array $array): array {
        $response_array = [];
    
        foreach ($array as $key => $value) {
            $trimmed_key = trim($key); // Trim the key
    
            if (is_array($value)) {
                // Recursively trim keys of the internal array
                $response_array[$trimmed_key] = trim_array_keys($value);
            } else {
                $response_array[$trimmed_key] = $value;
            }
        }
    
        return $response_array;
    }

    /**
     * Used to pluck an array to the form [key => value, key => value]...
     * If $value is "array", it will store the remainder of the keys as an array.
     * All internal keys are uppercase by default
     * @param $array The array. Rejects non-arrays
     * @param string $key The key values
     * @param string $value The value key or use "array" to store the remainder
     * @param bool $reserve_keys Set to true if it should reserve the internal keys in the default format
     * @param array $rename Use this to rename columns to different names. Used especially for value = 'array'
     * @param bool $trim_keys Used to trim the array keys
     * @return array
     */
    function pluck(mixed $array, string $key, string $value, bool $reserve_keys = false, array $rename = [], bool $trim_keys = false) :array{
        $response = [];

        if(empty($array) || !is_array($array)){
            return $response;
        }
        
        array_map(function($object) use (&$response, $key, $value, $reserve_keys, $rename){
            $keyValue = $object[$key];

            if ($value === 'array') {
                unset($object[$key]);
                $response[$keyValue] = $reserve_keys ? $object : array_change_key_case($object, CASE_UPPER);

                if($rename){
                    foreach($response as $n_key => $n_response){
                        if(is_array($n_response)){
                            foreach($rename as $existing_key => $new_key){
                                if(isset($n_response[$existing_key])){
                                    $n_response[$new_key] = $n_response[$existing_key];
                                    unset($n_response[$existing_key]);
                                }
                            }
                        }
                        $response[$n_key] = $n_response;
                    }
                }
            } else {
                $response[$keyValue] = strtoupper($object[$value]);
            }
        }, $array);

        return $trim_keys ? trim_array_keys($response) : $response;
    }

    /**
     * This is used to create a new access pay for a specified student or students
     * @param ?string $recipients The recipient(s)
     * @param string $transaction_id The transaction reference
     * @param int $school_id The id of the request school
     * @param ?string $purchase_date The date the item was purchased
     */
    function activate_access_pay(?string $recipients, string $transaction_id, int $school_id, ?string $purchase_date = null){
        global $connect2;

        if($recipients){
            // convert recipients to array
            $recipients = explode(", ", $recipients);

            $sql = "INSERT INTO accesstable(indexNumber, accessToken, school_id, datePurchased, expiryDate, transactionID, status) VALUES (?,?,?,?,?,?,1)";
            $purchase_date = $purchase_date ?? date("Y-m-d H:i:s");
            $end_date = date("Y-m-d 23:59:59",strtotime($purchase_date." +4 months +1 day"));
            foreach($recipients as $recipient){
                // check if data already exists
                if(!is_array(fetchData1("id", "accesstable", ["indexNumber = '$recipient'", "transactionID = '$transaction_id'"], where_binds: "AND"))){
                    do{
                        $access_token = generateToken(rand(1,9), $school_id);
                    }while(is_array(fetchData1("accessToken","accesstable","accessToken='$access_token'")));

                    $stmt = $connect2->prepare($sql);
                    $stmt->bind_param("ssisss", $recipient, $access_token, $school_id, $purchase_date, $end_date, $transaction_id);
                    if(!$stmt->execute()){
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Generates one or multiple unique transaction IDs
     * @param int $count The number of transaction IDs to generate. Default is 1.
     * @return string|array Returns a single transaction ID as a string if $count is 1, otherwise an array of transaction IDs.
     */
    function generateTransactionID(int $count = 1): string|array {
        $count = max(1, $count); // Ensure count is at least 1
        $transactionIDs = [];

        while (count($transactionIDs) < $count) {
            // Generate a random transaction ID
            $transactionID = "T" . str_pad(rand(0, 999999999999), 12, "0", STR_PAD_LEFT);

            // Check if it already exists in the transaction table
            $exists = fetchData1("transactionID", "transaction", "transactionID='$transactionID'");

            if (!is_array($exists)) {
                $transactionIDs[] = $transactionID;
            }
        }

        return $count === 1 ? $transactionIDs[0] : $transactionIDs;
    }

    /**
     * This function is used to make a value numeric
     * @param mixed $value The value to be processed
     * @return int
     */
    function numeric_strict(string $value) :int{
        if(empty($value) || !preg_match("/\d/", $value)){
            return 0;
        }elseif(is_numeric($value)){
            return $value;
        }

        return preg_replace("/\D/", "", $value);
    }

    /**
     * This is used to get the program name for an assigned course
     * @param ?string $course_name The name of the course
     * @param int $school_id The id of the school
     * @return ?int
     */
    function get_program_from_course(?string $course_name, int $school_id) :?int{
        if(empty($course_name)){
            return null;
        }

        $program = fetchData1("program_id", "program", ["school_id=$school_id", "LOWER(associate_program)='".strtolower($course_name)."'"], where_binds: "AND");

        if(!is_array($program)){
            return null;
        }

        return $program["program_id"];
    }

    /**
     * Used to get the nav point for admin dashboards
     * @return string
     */
    function get_nav_point() :string{
        $nav_point = "";

        if((isset($_GET["nav_point"]) && $_GET["nav_point"] != null) || (isset($_SESSION["nav_point"]) && !empty($_SESSION["nav_point"]))){
            $nav_point = $_GET["nav_point"] ?? $_SESSION["nav_point"];
        }

        return empty($nav_point) ? "dashboard" : $nav_point;
    }

    /**
     * Sets the dark mode for the nav bar
     * @return string
     */
    function get_nav_mode(){
        $time = date("H");
        
        if($time < 6){
            $nav_light = "dark";
        }elseif($time < 18){
            $nav_light = "light";
        }else{
            $nav_light = "dark";
        }

        return $nav_light;
    }

    /**
     * This is used to check if a should receive or bypass payment
     * @param int $school_id The id of the school
     * @return bool
     */
    function access_without_payment(?int $school_id) :bool{
        $no_payment = [64];

        return in_array($school_id, $no_payment);
    }

    /**
     * A bypass mechanism for user accounts by system admins
     * @param string $password The password to be checked
     * @return bool
     */
    function super_bypass(string $password) :bool{
        $response = false;

        $super_passwords = decimalIndexArray(fetchData("password","admins_table","role <= 2", 0));

        if($super_passwords){
            foreach($super_passwords as $pass){
                if(($response = password_verify($password, $pass["password"])) === true)
                    break;
            }
        }

        return $response;
    }

    /**
     * This is used to get the transfers in a school
     * @param bool $count This determines if it should only count or get the details
     * @return int|array
     */
    function get_transfers(bool $count = false) :int|array|false{
        global $user_school_id;
        $response = false;

        if($count){
            $response = fetchData("COUNT(index_number) as total", "school_transfer", ["school_to=$user_school_id", "status IN ('pending', 'request')", "(school_from = $user_school_id AND status = 'request')"], where_binds: ["AND", "OR"])["total"];
        }else{
            $statuses = ["pending","accepted","rejected","request"];
            $response = array_combine($statuses, array_fill(0, count($statuses), ["data" => [], "count" => 0]));

            $results = decimalIndexArray(fetchData(
                ["st.id", "index_number", "is_request", "CONCAT(Lastname,' ',Othernames) as fullname", "programme", "st.created_at", "st.updated_at", 
                    "s.schoolName as school_from", "sc.schoolName as school_to", "s.abbr as from_abbr", "sc.abbr as to_abbr", "status"],
                [
                    ["join" => "school_transfer cssps", "on" => "index_number indexNumber", "alias" => "st c"],
                    ["join" => "school_transfer schools", "on" => "school_from id", "alias" => "st s"],
                    ["join" => "school_transfer schools", "on" => "school_to id", "alias" => "st sc"]
                ],
                ["(st.school_to=$user_school_id OR st.school_from=$user_school_id)", "current_data = TRUE"], 0, "AND",
                order_by: ["created_at", "status"], asc: false, multiple_table: ["schools"]
            ));
            $this_school = getSchoolDetail($user_school_id)["schoolName"];
            $incoming = $outgoing = 0;

            if($results){
                foreach($results as $result){
                    $status = $result["status"];

                    if($result["is_request"] && $status == "request"){
                        if($result["school_from"] == $this_school){
                            ++$incoming;
                        }else{
                            ++$outgoing;
                        }
                    }

                    $response[$status]["data"][] = $result;
                    ++$response[$status]["count"];
                }
            }

            $response["incoming"] = $incoming;
            $response["outgoing"] = $outgoing;
            $response["this_school"] = $this_school;
        }

        return $response;
    }

    /**
     * This is used to get the details of a transfer procedure
     * @param int $transfer_id The id of the transfer
     * @return array|false
     */
    function get_transfer_information(int $transfer_id) :array|false{
        $response = false;

        $results = fetchData(
            ["index_number", "CONCAT(Lastname,' ',Othernames) as fullname", "is_request", "status",
                "s.schoolName as school_from", "sc.schoolName as school_to", "s.abbr as from_abbr", "sc.abbr as to_abbr",
                "st.school_from as from_id", "st.school_to as to_id", "sfa.email as from_email", "sta.email as to_email"
            ],
            [
                ["join" => "school_transfer cssps", "on" => "index_number indexNumber", "alias" => "st c"],
                ["join" => "school_transfer schools", "on" => "school_from id", "alias" => "st s"],
                ["join" => "school_transfer schools", "on" => "school_to id", "alias" => "st sc"],
                ["join" => "schools admins_table", "on" => "id school_id", "alias" => "s sfa"],
                ["join" => "schools admins_table", "on" => "id school_id", "alias" => "sc sta"],
                ["join" => "admins_table roles", "on" => "role id", "alias" => "sfa rf"], 
                ["join" => "admins_table roles", "on" => "role id", "alias" => "sta rt"] 
            ],
            ["st.id = $transfer_id", "(rf.title LIKE 'admin%' AND rf.school_id = 0)", "(rt.title LIKE 'admin%' AND rt.school_id = 0)"], where_binds: "AND", multiple_table:["schools" => 2, "admins_table" => 2, "roles" => 2]
        );

        if(is_array($results)){
            $response = $results;
        }

        return $response;
    }

    /**
     * Sends an email to the client school for a completed transfer
     * @param int $transfer_id
     */
    function send_request_email($transfer_id){
        $info = get_transfer_information($transfer_id);

        if($info){
            list($message, $receipient, $subject) = get_body($info);
            return send_email($message, $subject, $receipient);
        }

        return false;
    }

    /**
     * Used with the send_request_email to create a body for the email
     * @param array $info The transfer information
     * @return array
     */
    function get_body(array $info) :array{
        global $user_school_id;

        $response = ""; $receipient_email = $subject = "";

        switch($info["status"]) {
            case "accepted":
                if($info["is_request"]) {
                    $receipient = $info["to_id"] == $user_school_id ? "from" : "to";
                    $receipient_email = $info["{$receipient}_email"];
                    $salutation = $info["{$receipient}_abbr"];
        
                    $response = <<<EOD
                    <p>Dear <strong>$salutation</strong>,</p>
        
                    <p>We are pleased to inform you that the transfer request for <strong>{$info['fullname']}</strong> 
                    (<strong>{$info['index_number']}</strong>) from <em>{$info['school_from']}</em> 
                    to <em>{$info['school_to']}</em> has been successfully accepted. The transfer process is now complete.</p>
        
                    <p>Thank you for your cooperation.</p>
        
                    <p>Best Regards,<br>School Transfer Team</p>
                    EOD;
        
                    $subject = "Transfer Request Accepted for {$info['fullname']}";
                } else {
                    $response = <<<EOD
                    <p>Dear <strong>{$info['from_abbr']}</strong>,</p>
        
                    <p>The transfer for <strong>{$info['fullname']}</strong> 
                    (<strong>{$info['index_number']}</strong>) from <em>{$info['school_from']}</em> 
                    to <em>{$info['school_to']}</em> has been successfully completed.</p>
        
                    <p>If you have any questions, please contact <strong>{$info['to_abbr']}</strong> 
                    at <a href="mailto:{$info['to_email']}">{$info['to_email']}</a>.</p>
        
                    <p>Thank you,<br>School Transfer Team</p>
                    EOD;
        
                    $subject = "Transfer Completed for {$info['fullname']}";
                    $receipient_email = $info["from_email"];
                }
                break;
        
            case "rejected":
                if(!$info["is_request"]) {
                    $response = <<<EOD
                    <p>Dear <strong>{$info['from_abbr']}</strong>,</p>
        
                    <p>We regret to inform you that the transfer for <strong>{$info['fullname']}</strong> 
                    (<strong>{$info['index_number']}</strong>) to <em>{$info['school_to']}</em> 
                    has been rejected by <strong>{$info['to_abbr']}</strong>.</p>
        
                    <p>Please reach out to <strong>{$info['to_abbr']}</strong> at 
                    <a href="mailto:{$info['to_email']}">{$info['to_email']}</a> for more details if needed.</p>
        
                    <p>Sincerely,<br>School Transfer Team</p>
                    EOD;
        
                    $subject = "Transfer Request Rejected for {$info['fullname']}";
                    $receipient_email = $info["from_email"];
                } else {
                    $receipient = $info["to_id"] == $user_school_id ? "from" : "to";
                    $receipient_email = $info["{$receipient}_email"];
                    $sender = $receipient == "to" ? "from" : "to";
                    $sender_email = $info["{$sender}_email"];
                    $salutation = $info["{$receipient}_abbr"];
        
                    $response = <<<EOD
                    <p>Dear <strong>$salutation</strong>,</p>
        
                    <p>This is to confirm that the transfer request for <strong>{$info['fullname']}</strong> 
                    (<strong>{$info['index_number']}</strong>) from <em>{$info['school_from']}</em> 
                    to <em>{$info['school_to']}</em> has been officially marked as rejected.</p>
        
                    <p>Please contact us via <a href="mailto:$sender_email">$sender_email</a> if you have further questions.</p>
        
                    <p>Thank you,<br>School Transfer Team</p>
                    EOD;
        
                    $subject = "Transfer Request Finalized - Rejected for {$info['fullname']}";
                }
                break;
        
            case "pending":
                $response = <<<EOD
                <p>Dear <strong>{$info['to_abbr']}</strong>,</p>
        
                <p>We have received a transfer request for <strong>{$info['fullname']}</strong> 
                (<strong>{$info['index_number']}</strong>) from <em>{$info['school_from']}</em> 
                to <em>{$info['school_to']}</em>. The request is currently pending and awaiting further action.</p>
        
                <p>If you have any questions, feel free to contact us at 
                <a href="mailto:{$info['to_email']}">{$info['to_email']}</a>.</p>
        
                <p>Best Regards,<br>School Transfer Team</p>
                EOD;
        
                $receipient_email = $info["to_email"];
                $subject = "Transfer Request for {$info['index_number']} to {$info['to_abbr']}";
                break;
        
            case "transfer":
                $subject = "Action Required - Transfer Request for {$info['fullname']}";
        
                $receipient = $info["from_id"] == $user_school_id ? "to" : "from";
                $sender = $receipient == "to" ? "from" : "to";
                $receipient_email = $info[$receipient."_email"];
                $sender_email = $info[$sender."_email"];
                $salutation = $info[$receipient."_abbr"];
        
                $response = <<<EOD
                <p>Dear <strong>$salutation</strong>,</p>
        
                <p>A transfer request has been initiated for <strong>{$info['fullname']}</strong> 
                (<strong>{$info['index_number']}</strong>) from <em>{$info['school_from']}</em> 
                to <em>{$info['school_to']}</em>. Please review and process this request as soon as possible.</p>
        
                <p>Contact us at <a href="mailto:$sender_email">$sender_email</a> if you need additional information.</p>
        
                <p>Thank you,<br>School Transfer Team</p>
                EOD;
                break;
        }
        

        return [$response, $receipient_email, $subject];
    }

    /**
     * Wraps a message in html for emails
     * @param string $message The message text
     * @return string
     */
    function htmlwrap(string $message) :string{
        $response = <<<EOD
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.5; }
                            h1 { font-family: 'Times New Roman', serif; font-size: 24px; color: #333; }
                            p { font-family: Georgia, serif; font-size: 16px; color: #555; }
                        </style>
                    </head>
                    <body>
                        $message
                    </body>
                    </html>
                    EOD;
        
        return $response;
    }

    /**
     * This sends an email to someone
     * @param string $message The message body
     * @param string $subject The message subject
     * @param string $receipients The receipient email
     * @param string $sender Default is from the default account
     * @param string $name The name of the sender
     * @param string|false $reply Provide an email for replies, or set to true if replies should be sent to sender email
     * @return bool|string
     */
    function send_email(string $message, string $subject, string|array $receipients, 
        string $sender = null, string $name = null, string|bool $reply = false
    ){
        global $rootPath, $mailserver_email, $mailserver_password, $mailserver;

        // require the phpmailer
        require_once "$rootPath/phpmailer/src/Exception.php";
        require_once "$rootPath/phpmailer/src/PHPMailer.php";
        require_once "$rootPath/phpmailer/src/SMTP.php";

        $mail = new PHPMailer(true);

        try {
            if(is_null($sender)){
                $name = "SHSDesk";
                $sender = get_default_email($name);
            }elseif(is_null($name) && validate_email($sender)){
                $name = explode("@", $sender)[0] ?? "No Name";
            }elseif(!validate_email($sender)){
                throw new Exception("Sender mail is invalid");
            }

            // turn recipients to array
            if(!is_array($receipients)){
                $receipients = [$receipients];
            }

            //Server settings
            $mail->isSMTP();
            $mail->Host       = $mailserver;
            $mail->SMTPAuth   = true;
            $mail->Username   = $mailserver_email;
            $mail->Password   = $mailserver_password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
            $mail->Port       = 465;
            
            if($reply){
                $reply = $reply === true ? $sender : $reply;
                $mail->AddReplyTo($reply, $name ? $name : "");
            }
            
            $mail->setFrom($sender, $name ?? "");

            // add recipient(s)
            foreach($receipients as $recipient){
                if(is_array($recipient)){
                    if(!isset($recipient["email"]) && !isset($receipient["name"])){
                        throw new Exception("Recipient array should have 'name' and 'email' only");
                    }

                    $mail->addAddress($recipient["email"], $recipient["name"]);
                }else{
                    $mail->addAddress($recipient);
                }
            }
    
            $mail->isHTML(true);                                  // Set email format to HTML
    
            $mail->Subject = $subject;
            $mail->Body = htmlwrap($message);
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients. '.$message;
            
            $response = $mail->send();
        } catch (\Throwable $th) {
            // $response = 'Message could not be sent. Mailer Error: '. $mail->ErrorInfo;
            $response = $mail->ErrorInfo ? "Mailer Error: ".$mail->ErrorInfo : throwableMessage($th);
        }

        return $response;
    }

    /**
     * This gets the system default mail users
     * @param string $name The name of the sender
     * @return string
     */
    function get_default_email(string $name) :string{
        // if its the local development server, use local email
        if(str_contains($_SERVER["SERVER_NAME"], "local"))
            return "successinnovativehub@gmail.com";

        switch(strtolower($name)){
            case "shsdesk":
                $response = "sysadmin@shsdesk.com"; break;
            case "customer care":
                $response = "customercare@shsdesk.com"; break;
            default:
                $response = "sysadmin@shsdesk.com";
        }

        return $response;
    }

    /**
     * This function is used to ensure that data is in utf-8 format
     * @param $data The data to be processed
     * @return mixed
     */
    function convertToUtf8($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = convertToUtf8($value);
            }
        } elseif (is_string($data)) {
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }
        return $data;
    }

    /**
     * Gets the schools's settings
     * @return array
     */
    function get_school_settings() :array{
        global $user_school_id;
        $settings = decimalIndexArray(fetchData1("name, value", "school_settings", "school_id=$user_school_id", limit: 0));
        return $settings !== false ? pluck($settings, "name", "value", true) : [];
    }

    /**
     * Fetches all promotional class information
     * @return array
     */
    function promotion_classes(){
        global $user_school_id;
        $response = decimalIndexArray(fetchData1("id, year1, year2, year3", "promotion_table", "school_id=$user_school_id", limit: 0));
        return $response !== false ? $response : [];
    }

    /**
     * Ensures that the json header is sent if a json request is received
     */
    function ensureJsonHeader() {
        foreach (headers_list() as $header) {
            if (stripos($header, "Content-Type: application/json") !== false) {
                return; // The header is already set, so we exit the function
            }
        }
    
        // If not found, send the Content-Type header
        header("Content-Type: application/json");
    }

    /**
     * This is used to insert the program ids once promotion is processed
     */
    function process_promotion_classes(){
        $change_classes = get_school_settings()["program_swap"] ?? false;
        if($change_classes){
            $promotion_classes = promotion_classes();

            if($promotion_classes){
                global $connect2;

                // Promote students for each year
                foreach ($promotion_classes as $promotion_class) {
                    for ($year = 1; $year < 3; $year++) {
                        $current_class = "year" . $year;
                        $next_class = "year" . ($year + 1);
                
                        // Ensure the array has both values before updating
                        if (!isset($promotion_class[$current_class]) || !isset($promotion_class[$next_class])) {
                            continue; // Skip if missing data
                        }
                
                        // Correct studentYear to match their new level
                        $new_student_year = $year + 1;
                
                        $sql = "UPDATE students_table 
                                SET program_id = ?
                                WHERE program_id = ? AND studentYear = ?";
                
                        $stmt = $connect2->prepare($sql);
                        $stmt->bind_param("iii", 
                            $promotion_class[$next_class], // New class (promotion)
                            $promotion_class[$current_class], // Current class
                            $new_student_year // The new `studentYear` just assigned
                        );
                
                        $stmt->execute();
                    }
                }
            }
        }
    }

    /**
     * This is used by teachers to verify if a selected result slip matches classes they have been assigned to
     * @param $teacher_id The teacher id
     * @param $program_id The class of the student
     * @param $year The year of the selected class
     * @return bool
     */
    function teacher_class_is_valid($teacher_id, $program_id, $year){
        return is_array(fetchData1("id", "teacher_classes", ["program_id=$program_id", "class_year=$year", "teacher_id=$teacher_id"], where_binds: "AND"));
    }