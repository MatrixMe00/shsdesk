<?php
    include_once("../includes/session.php");

    if(isset($_POST['submit']) && $_POST['submit'] == "register_school"){
        //take details
        $form_id = $connect->real_escape_string($_POST["form_id"]);

        //check if the form is not already submitted
        $res = $connect->query("SELECT * FROM schools WHERE id=$form_id") or die($connect->error);

        if($res->num_rows > 0){
            echo "<p>This form has already been submitted\n</p>";
            echo "<p>Click <a href=\"".$url."/admin\">here</a> to login</p>";

            exit(1);
        }

        //school and technical's details
        $school_name = $connect->real_escape_string($_POST["school_name"]);
        $abbreviation = $connect->real_escape_string($_POST["abbreviation"]);
        $head_name = $connect->real_escape_string($_POST["head_name"]);
        $technical_name = $connect->real_escape_string($_POST["technical_name"]);
        $technical_phone = $connect->real_escape_string($_POST["technical_phone"]);
        
        //address
        $school_email = $connect->real_escape_string($_POST["school_email"]);
        $postal_address = $connect->real_escape_string($_POST["postal_address"]);
        
        //school's description
        $description = htmlentities($_POST["description"]);

        //other details of school
        $category = $connect->real_escape_string($_POST["category"]);
        $residence_status = $connect->real_escape_string($_POST["residence_status"]);
        $sector = $connect->real_escape_string($_POST["sector"]);
        @$autoHousePlace = $connect->real_escape_string($_POST["autoHousePlace"]);
        $admission_letter = $connect->real_escape_string($_POST["admission_letter"]);

        if(@$autoHousePlace == "true" || @$autoHousePlace == "on"){
            @$autoHousePlace = true;
        }

        //prevent empty entries
        if(empty($school_name) || $school_name == null || $school_name == ""){
            echo "<p>No School name was provided</p>";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }elseif(empty($head_name) || $head_name == null || $head_name == ""){
            echo "<p>The name of the Head of the school has not being captured</p>";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }elseif(empty($technical_name) || $technical_name == null || $technical_name == ""){
            echo "<p>No technical support name has been provided</p>";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }elseif(empty($technical_phone) || $technical_phone == null || $technical_phone == ""){
            echo "<p>No technical support contact number has been provided";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }elseif(empty($school_email)){
            echo "<p>No email has been provided. Please check and try again";

            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }elseif(empty($description) || $description == null || $description == ""){
            echo "<p>Please provide a brief description of your school</p>";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }elseif(empty($category) || $category == null || $category == ""){
            echo "<p>You have not selected the category of your school</p>";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }elseif(empty($residence_status) || $residence_status == null || $residence_status == ""){
            echo "<p>Please provide the residence status of your school</p>";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }elseif(empty($sector) || $sector == null || $sector == ""){
            echo "<p>Please provide the sector to which your school belongs</p>";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }elseif(empty($admission_letter) || $admission_letter == null || $admission_letter == ""){
            echo "<p>Please provide a body for your admission letter</p>";

            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }

        //make editing on the profile picture chosen
        if(isset($_FILES['avatar']) && $_FILES["avatar"]["tmp_name"]){
            $image_input_name = "avatar";
            $local_storage_directory = "$rootPath/admin/admin/assets/images/schools/";
            $default_image_path = "$rootPath/admin/admin/assets/images/schools/default_user.png";

            $image_directory = getImageDirectory($image_input_name, $local_storage_directory,$default_image_path);
        }else{
            echo "<p>You have not uploaded your school's logo</p>";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }

        //allow user to upload prospectus
        if(isset($_FILES["prospectus"]) && $_FILES["prospectus"]["tmp_name"] !== null){
            //get file extension
            $ext = strtolower(fileExtension("prospectus"));

            if($ext =="pdf"){
                $file_input_name = "prospectus";
                $local_storage_directory = "$rootPath/admin/admin/assets/files/prospectus/";

                $prostectusDirectory = getFileDirectory($file_input_name, $local_storage_directory);
            }else{
                echo "<p>File provided for prospectus is not a PDF</p>";
                echo "<p>Please go back and provide valid document form</p>";
            }

            $prostectusDirectory = getFileDirectory($file_input_name, $local_storage_directory);
        }else{
            echo "<p>Please provide your prospectus</p>";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }

        //remove the root path
        $image_directory = explode("$rootPath/",$image_directory);
        $prostectusDirectory = explode("$rootPath/", $prostectusDirectory);

        //store only the direct file paths
        $image_directory = $image_directory[1];
        $prostectusDirectory = $prostectusDirectory[1];

        //query the database
        $query = "INSERT INTO schools (logoPath, prospectusPath, admissionPath, schoolName, postalAddress, abbr, 
            headName, techName, techContact, email, description, category, residence_status, sector, 
            autoHousePlace) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)" or die($connect->error);

        //prepare query for entry into database
        $result = $connect->prepare($query);
        $result->bind_param("sssssssssssissi", $image_directory, $prostectusDirectory, $admission_letter, 
        $school_name, $postal_address, $abbreviation, $head_name, $technical_name, $technical_phone, $school_email, $description, 
        $category, $residence_status, $sector, $autoHousePlace);

        //execute the results
        if($result->execute()){
            //take this school id
            $sql = $connect->query("SELECT id FROM schools WHERE schoolName = '$school_name'");

            //take the id in the form of array
            $row = $sql->fetch_array();

            //set the default password
            $default_password = MD5("Password@1");

            $date_now = date("d-m-Y H:i:s");

            //create a usable login session for user
            $sql = "INSERT INTO admins_table (fullname, email, password, school_id, contact, role, adYear) 
                    VALUES (?,?,?,?,?,?,?)";

            //prepare the insert statement
            $res = $connect->prepare($sql);

            $row_id = intval($row["id"]);
            $role = 3;
            
            //bind necessary parameters
            $res->bind_param('sssisis',$technical_name,$school_email, $default_password,$row_id, $technical_phone, $role, $date_now);

            if($res->execute()){
                //insert data into admission details table
                $sql = "INSERT INTO admissiondetails (schoolID, headName) 
                VALUES (".$row["id"].",$head_name)";
                $connect->query($sql);

                echo "<p>Your data has been recorded successfully!</p>";
                if(@$autoHousePlace != true){
                    echo "<p>Student house allocation has not been set to automatic<br>
                        Click <a href=\"$url/admin/admin/assets/files/default files/house_allocation.csv\">here</a> to download 
                        required file to manually place students in required houses</p>";
                }

                echo "<p>
                   <u>Default Login Details</u><br><br>
                   <b>Username</b>: New User<br>
                   <b>Password</b>: Password@1<br><br>
                   
                   Upon registration, you shall be asked for your full name [$technical_name] and email [$school_email]. Please do well to provide the right details
                </p>";
    
                echo "<p>Click <a href=\"".$url."/admin/\">here</a> to log into your portal</p>";
            }
        }else{
            echo "<p>An unexpected error occured! Please go back and resubmit the form again</p>";
        }
    }else{
        echo "<p>Unidentified submission made! Please go back and make a submission";
    }
?>