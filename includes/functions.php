<?php
    /**
     * The purpose of this function is to retrieve user details from database
     * 
     * @param int $id This variable will take the user id for extraction
     * 
     * @return array|string The function returns an array of results
     */
    function getUserDetails($id):array|string{
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
            $row = $res->fetch_array();
            $row = $row['title'];
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
     * 
     * @return string This function return the directory of the image stored
     */
    function getImageDirectory(string $image_input_name, string $local_storage_directory, string $default_image_path = ""):string{
        if(isset($_FILES[$image_input_name]) && $_FILES[$image_input_name]["tmp_name"] != null){
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
                $counter = 1;

                //split the pathname and extension
                $temp_image_directory = explode(".",$image_directory);
                $filename_path = $temp_image_directory[0];
                $file_extension = $temp_image_directory[1];

                //loop through until unique file is found
                while(file_exists($image_directory)){
                    //set the filename path into a new filename path
                    $temp_image_directory = $filename_path."_".$counter++;
                    
                    //set the new path for checking
                    $image_directory = $temp_image_directory.".".$file_extension;
                }

                //when loop is over, let the image be prepared for upload
                $uploadOk = 1;
            }

            //now upload the file
            if($uploadOk == 1){
                if(move_uploaded_file($_FILES[$image_input_name]["tmp_name"], $image_directory)){
                    //compress the image to 40% quality
                    $image_directory = compress($image_directory, $image_directory, 40);
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
     * @param string $quality This is the quality of the image
     * 
     * @return string The return value is the destination of the compressed image
     */
    function compress($source, $destination, $quality):string {

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

            //get the file type
            $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            //if file already exists, try to create another filename
            if(file_exists($file_name)){
                $counter = 1;

                //split the pathname and extension
                $temp_file_name = explode(".",$file_name);
                $filename_path = $temp_file_name[0];
                $file_extension = $temp_file_name[1];

                //loop through until unique file is found
                while(file_exists($file_name)){
                    //set the filename path into a new filename path
                    $temp_file_name = $filename_path."_".$counter++;
                    
                    //set the new path for checking
                    $file_name = $temp_file_name.".".$file_extension;
                }

                //when loop is over, let the image be prepared for upload
                $uploadOk = 1;
            }

            // Allow certain file formats
            if($file_type != "pdf" && $file_type != "PDF") {
                echo "Sorry, only PDF files are allowed.";
                $uploadOk = 0;
                $file_name = "error";
            }

            //now upload the file
            if($uploadOk == 1){
                if(move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $file_name)){
                    return $file_name;
                }else{
                    echo "<p>Upload failed</p>";
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
     * This function will be used to check if the specified person is a new user or not
     * 
     * @param int $user_id This parameter receives the index of the user
     * @return bool The return value is either a true (for new user) or a false (for not new user)
     */
    function checkNewUser($user_id):bool {
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
     * @param string|int $key This parameter can either be a string with the name of the school
     * or an integer holding the school's id
     * @param bool $all This parameter is a  boolean responsible for revealing if an
     * array should return or not. It is false by default
     * 
     * @return string A string (school name) is returned when the key is an integer
     * @return int An integer is return when the key is a string
     * @return array An array is returned when the all parameter is set to true
     */
    function getSchoolDetail(int|string $key, bool $all = false):string|int|array{
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
    function notificationCounter($audience = "all", $type = "all", $read = 0):int{
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
    function replyCounter($comment_id = 0, $read = null):int{
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
?>