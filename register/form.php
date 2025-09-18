<?php include_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");
    $connect->begin_transaction();
    try{ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        body{
            height: 100vh;
            border: 0;
            padding: 0;
            background-color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        #container{
            background-color: #fff;
            border: 1px solid grey;
            flex: 0.5 1 80vw;
            text-align: center;
            padding: 1%;
            overflow: auto;
            max-height: 95vh;
            border-radius: 10px;
            box-shadow: 0 0 5px 0 grey, 0 0 10px 1px lightgrey;
        }

        @media screen and (max-width:748px){
            #container{
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <div id="container">
<?php
    require_once "$rootPath/includes/functions.php";

    if(isset($_POST['submit']) && $_POST['submit'] == "register_school"){
        //take details
        $form_id = $_POST["form_id"];

        //check if the form is not already submitted
        $res = fetchData("*", "schools", "id=$form_id");

        if(is_array($res)){
            echo "<p>This form has already been submitted\n</p>";
            echo "<p>Click <a href=\"$url/admin\">here</a> to login</p>";

            exit(1);
        }

        //school and technical's details
        $school_name = formatName($_POST["school_name"]);
        $abbreviation = strtoupper($_POST["abbreviation"]);
        $head_name = formatName($_POST["head_name"]);
        $technical_name = formatName($_POST["technical_name"]);
        $technical_phone = $_POST["technical_phone"];
        
        //address
        $school_email = $_POST["school_email"];
        $postal_address = formatName($_POST["postal_address"]);
        
        //school's description
        $description = htmlentities($_POST["description"]);

        //other details of school
        $category = $_POST["category"];
        $residence_status = $_POST["residence_status"];
        $sector = $_POST["sector"];
        $autoHousePlace = $_POST["autoHousePlace"];

        //admission letter
        $admission_letter_head = $_POST["admission_letter_head"];
        $admission_letter = htmlentities($_POST["admission_letter"]);

        if($autoHousePlace == "true" || $autoHousePlace == "on"){
            $autoHousePlace = true;
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
        }elseif(fetchData("email","schools","email='$school_email'") != "empty"){
            echo "<p>The email provided is already in use by another school admin. Please provide your proper school email</p>";
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

        //check if email already exists
        if(is_array(fetchData("email", "schools", "email = '$school_email'"))){
            echo "<p>Email already exists. Please review your email and try again</p>";
            exit(1);
        }

        if(isset($_POST["letter_prefix"])){
            // check for necessary fields
            $text = $_POST["prefix_text"];
            $year = $_POST["prefix_year"];
            $separator = $_POST["prefix_separator"];

            if(empty($text)){
                echo "<p>Letter prefix text cannot be empty</p>"; exit;
            }elseif(is_array(fetchData("prefix_text", "admissiondetails", "prefix_text = '$text' AND schoolID != $school_id"))){
                echo "<p>Letter prefix text already exists for another school. Please choose another prefix text</p>"; exit;
            }elseif(!empty($year) && !in_array($year, ["YY", "YYYY"])){
                echo "<p>Invalid year format selected for letter prefix</p>"; exit;
            }elseif(empty($separator)){
                echo "<p>Letter prefix separator cannot be empty</p>"; exit;
            }else{
                $letter_prefix = json_encode([
                    "text" => $text,
                    "year" => $year,
                    "separator" => $separator
                ], JSON_UNESCAPED_SLASHES);
            }
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

        /*//allow user to upload prospectus
        if(isset($_FILES["prospectus"]) && !empty($_FILES["prospectus"]["tmp_name"])){
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
        }else{
            echo "<p>Please provide your prospectus</p>";
            
            echo "<p>Please go back and complete the form</p>";
            exit(1);
        }*/

        // prospectus type
        $multi_prospectus = isset($_POST["multi_prospectus"]);
        $prospectusData = [];

        //allow user to upload prospectus
        if ($multi_prospectus) {
            // multiple prospectus case
            $prospectusData["type"] = "multiple";
            $prospectusData["files"] = [];

            if(empty($_FILES["male_prospectus"]["tmp_name"]) || empty($_FILES["female_prospectus"]["tmp_name"] || empty($_FILES["day_prospectus"]["tmp_name"]))){
                echo "<p>Both prospectus documents for males and females are required</p>";
                exit(1);
            }

            // male
            if (isset($_FILES["male_prospectus"]) && !empty($_FILES["male_prospectus"]["tmp_name"])) {
                $ext = strtolower(fileExtension("male_prospectus"));
                if ($ext == "pdf") {
                    $file_input_name = "male_prospectus";
                    $local_storage_directory = "$rootPath/admin/admin/assets/files/prospectus/";
                    $maleDirectory = getFileDirectory($file_input_name, $local_storage_directory);

                    // remove root path
                    $maleDirectory = explode("$rootPath/", $maleDirectory)[1];
                    $prospectusData["files"]["male"] = $maleDirectory;
                } else {
                    echo "<p>Boarding Male prospectus must be a PDF</p>";
                    exit(1);
                }
            }

            // female
            if (isset($_FILES["female_prospectus"]) && !empty($_FILES["female_prospectus"]["tmp_name"])) {
                $ext = strtolower(fileExtension("female_prospectus"));
                if ($ext == "pdf") {
                    $file_input_name = "female_prospectus";
                    $local_storage_directory = "$rootPath/admin/admin/assets/files/prospectus/";
                    $femaleDirectory = getFileDirectory($file_input_name, $local_storage_directory);

                    // remove root path
                    $femaleDirectory = explode("$rootPath/", $femaleDirectory)[1];
                    $prospectusData["files"]["female"] = $femaleDirectory;
                } else {
                    echo "<p>Boarding Female prospectus must be a PDF</p>";
                    exit(1);
                }
            }

            // day students
            if (isset($_FILES["day_prospectus"]) && !empty($_FILES["day_prospectus"]["tmp_name"])) {
                $ext = strtolower(fileExtension("day_prospectus"));
                if ($ext == "pdf") {
                    $file_input_name = "day_prospectus";
                    $local_storage_directory = "$rootPath/admin/admin/assets/files/prospectus/";
                    $dayDirectory = getFileDirectory($file_input_name, $local_storage_directory);

                    // remove root path
                    $dayDirectory = explode("$rootPath/", $dayDirectory)[1];
                    $prospectusData["files"]["day"] = $dayDirectory;
                } else {
                    echo "<p>Day students prospectus must be a PDF</p>";
                    exit(1);
                }
            }
        } else {
            // single prospectus case
            if (isset($_FILES["prospectus"]) && !empty($_FILES["prospectus"]["tmp_name"])) {
                $ext = strtolower(fileExtension("prospectus"));
                if ($ext == "pdf") {
                    $file_input_name = "prospectus";
                    $local_storage_directory = "$rootPath/admin/admin/assets/files/prospectus/";
                    $prospectusDirectory = getFileDirectory($file_input_name, $local_storage_directory);

                    // remove root path
                    $prospectusDirectory = explode("$rootPath/", $prospectusDirectory)[1];

                    $prospectusData = [
                        "type" => "single",
                        "files" => $prospectusDirectory
                    ];
                } else {
                    echo "<p>File provided for prospectus is not a PDF</p>";
                    exit(1);
                }
            } else {
                echo "<p>Please provide your prospectus</p>";
                exit(1);
            }
        }

        $template_dir = null;
        if(isset($_FILES["admission_template"]) && !empty($_FILES["admission_template"]["tmp_name"])){
            //get file extension
            $ext = strtolower(fileExtension("admission_template"));

            if($ext =="pdf"){
                $file_input_name = "admission_template";
                $local_storage_directory = "$rootPath/admin/admin/assets/files/admission templates/";

                if(!is_dir($local_storage_directory)){
                    mkdir($local_storage_directory, recursive: true);
                }

                $template_dir = getFileDirectory($file_input_name, $local_storage_directory);

                //remove rootPath
                $template_dir = explode("$rootPath/", $template_dir);
                $template_dir = $template_dir[1];
            }else{
                echo "<p>File provided for admission template is not a PDF</p>";
                echo "<p>Please provide a valid document</p>";
            }
        }

        //remove the root path for image
        $image_directory = explode("$rootPath/", $image_directory);
        $image_directory = $image_directory[1];

        // encode prospectus data
        $prospectusData = json_encode($prospectusData, JSON_UNESCAPED_SLASHES);

        //query the database
        $query = "INSERT INTO schools (logoPath, prospectusPath, admissionPath, admissionHead, schoolName, postalAddress, abbr, 
            headName, techName, techContact, email, description, category, residence_status, sector, 
            autoHousePlace, admission_template) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)" or die($connect->error);

        //prepare query for entry into database
        $result = $connect->prepare($query);
        $result->bind_param("ssssssssssssissis", $image_directory, $prospectusData, $admission_letter, $admission_letter_head,
        $school_name, $postal_address, $abbreviation, $head_name, $technical_name, $technical_phone, $school_email, $description, 
        $category, $residence_status, $sector, $autoHousePlace, $template_dir);

        //execute the results
        if($result->execute()){
            // take school id
            $school_id = $connect->insert_id;

            //set the default password
            $default_password = password_hash("Password@1", PASSWORD_DEFAULT);

            $date_now = date("d-m-Y H:i:s");

            //create a usable login session for user
            $sql = "INSERT INTO admins_table (fullname, email, password, school_id, contact, role, adYear) 
                    VALUES (?,?,?,?,?,?,?)";

            //prepare the insert statement
            $res = $connect->prepare($sql);
            $role = 3;
            
            //bind necessary parameters
            $res->bind_param('sssisis',$technical_name,$school_email, $default_password,$school_id, $technical_phone, $role, $date_now);

            if($res->execute()){
                //providing academic year according to a calculated algorithm
                $admission_year = date("Y");
                $academic_year = getAcademicYear(now());

                //insert data into admission details table
                if(isset($_POST["letter_prefix"])){
                    $sql = "INSERT INTO admissiondetails (schoolID, headName, admissionYear, academicYear, prefix_text, letter_prefix) 
                        VALUES ($school_id,'$head_name', '$admission_year', '$academic_year', '{$_POST['prefix_text']}', '$letter_prefix')";
                    $connect->query($sql);
                }else{
                    $sql = "INSERT INTO admissiondetails (schoolID, headName, admissionYear, academicYear) 
                    VALUES ($school_id,'$head_name', '$admission_year', '$academic_year')";
                    $connect->query($sql);
                }
                

                // save all changes to database
                $connect->commit();

                echo "<p>Your data has been recorded successfully!</p>";
                if($autoHousePlace !== true){
                    echo "<p>Student house allocation has not been set to automatic<br>
                        Click <a href=\"$url/admin/admin/assets/files/default files/house_allocation.xlsx\">here</a> to download 
                        required file to manually place students in required houses</p>";
                }

                $message_detail = "<p>
                   <u>Default Login Details</u><br><br>
                   <b>Username</b>: New User<br>
                   <b>Password</b>: Password@1<br><br>
                   
                   Upon registration, you shall be asked for your full name [$technical_name] and email [$school_email]. 
                   Please do well to provide the right details to activate your account
                </p>";
                echo $message_detail;
                echo "<p>Click <a href=\"$url/admin/\">here</a> to log into your portal</p>";

                $email_message = <<< HTML
                <p>Welcome to the SHSDesk platform. We are pleased to have you on board. </p>
                $message_detail<br>
                Best regards,<br>
                SHSDesk Team
                HTML;
                send_email($email_message, "Welcome to SHSDesk", $school_email);
            }
        }else{
            echo "<p>An unexpected error occured! Please go back and resubmit the form again</p>";
        }
    }else{
        echo "<p>Unidentified submission made! Please go back and make a submission";
    }
?>
    </div>
</body>
</html>
<?php 
    }catch(Throwable $th){
        $connect->rollback();
        echo throwableMessage($th);
    }
    close_connections();
?>