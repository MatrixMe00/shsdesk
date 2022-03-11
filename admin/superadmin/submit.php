<?php
    @include_once("../../includes/session.php");

    if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != NULL){
        $submit = $_REQUEST["submit"];

        if($submit == "document_upload" || $submit == "document_upload_ajax"){
            if(isset($_FILES['import']) && $_FILES["import"]["tmp_name"] != NULL){
                echo "okay";
            }else{
                echo "no-file";
                exit(1);
            }
        }elseif($submit == "page_item_upload" || $submit == "page_item_upload_ajax"){
            $image_alt = $connect->real_escape_string($_POST["image_alt"]);
            $item_page = $connect->real_escape_string($_POST["item_page"]);
            $item_type = $connect->real_escape_string($_POST["item_type"]);
            $item_head = $connect->real_escape_string($_POST["item_head"]);
            $item_desc = htmlentities($_POST["item_desc"]);
            $item_button = $connect->real_escape_string($_POST["item_button"]);
            $button_text = $connect->real_escape_string($_POST["real_button_text"]);
            $button_url = $connect->real_escape_string($_POST["button_url"]);
            $activate = $connect->real_escape_string($_POST["activate"]);
            
            //upload the image
            $image_input_name = "item_img";
            $local_storage_directory = "$rootPath/assets/images/";

            //separate storage directories for images
            switch ($item_type) {
                case "carousel":
                    $local_storage_directory .= "backgrounds/carousel/";
                    break;
                
                case "gallery":
                    $local_storage_directory .= "pictures/";
                    break;
                    
                default:
                    # code...
                    break;
            }

            $item_img = getImageDirectory($image_input_name, $local_storage_directory);

            //split the rootpath from the image url
            $item_img = explode("$rootPath/", $item_img);
            $item_img = $item_img[1];

            //check for errors in the upload
            if($item_page == ""){
                echo "Object page location has not being specified. Please try again";

                exit(1);
            }elseif(strlen($item_page) < 3){
                echo "Object page location is too short to be identified! Please try again";

                exit(1);
            }elseif($item_type == ""){
                echo "Object page type has not being specified. Please try again";

                exit(1);
            }elseif(strlen($item_type) < 4){
                echo "Object page type lenght is too short to be identified. Please try again";

                exit(1);
            }elseif($item_head == ""){
                echo "Object being specified a title. Please try again";

                exit(1);
            }elseif(strlen($item_head) < 5){
                echo "Title lenght is too short. Please get at least 5 characters long title";

                exit(1);
            }elseif($item_desc == ""){
                echo "Object description has not being specified. Please try again";

                exit(1);
            }elseif(strlen($item_desc) < 15){
                echo "Object description lenght is too short. Please try again with 15 or more characters";

                exit(1);
            }elseif($item_img == "wrong_format"){
                echo "Wrong image format was detected! only JPG, JPEG, PNG, GIF and JFIF files are allowed.";

                exit(1);
            }elseif($item_img == "upload_error"){
                echo "An upload error was identified! Please try again later";

                exit(1);
            }elseif($item_img == "file_error"){
                echo "Your file is either corrupted or does not meet all requirements for this server";

                exit(1);
            }else{
                //conver on strings to integers
                if($item_button == "on"){
                    $item_button = 1;
                }else{
                    $item_button = 0;
                }

                if($activate == "on"){
                    $activate = 1;
                }else{
                    $activate = 0;
                }

                //query into database
                $sql = "INSERT INTO pageitemdisplays(item_img,image_alt, item_page, item_type, item_head, 
                item_desc, item_url, item_button,button_text, active) VALUES(?,?,?,?,?,?,?,?,?,?)";

                //prepare the values for entry
                $result = $connect->prepare($sql);
                $result->bind_param("sssssssisi",$item_img,$image_alt, $item_page, $item_type, $item_head, $item_desc, $button_url, $item_button, $button_text, $activate);

                //execute command
                if($result->execute()){
                    //take previous reference and remove any additives
                    if($submit == "document_upload"){
                        $reference = explode("?",$_SERVER["HTTP_REFERER"]);
                        $reference = $reference[0];

                        header("location:$reference?nav_point=Index");
                    }else{
                        echo "success";
                    }                    
                }else{
                    echo "error";
                }
            }
        }elseif($submit == "make_announcement" || $submit == "make_announcement_ajax"){
            //retrieve needed data
            $title = $connect->real_escape_string($_REQUEST["title"]);
            $message = $connect->real_escape_string(htmlentities($_REQUEST["message"], ENT_QUOTES));
            $audience = $connect->real_escape_string($_REQUEST["audience"]);
            $notification_type = $connect->real_escape_string($_REQUEST["notification_type"]);

            //get details from session
            if(isset($_SESSION['user_login_id']) && $_SESSION['user_login_id'] != null){
                $school_id = $_REQUEST["school_id"];
                $sender_id = $user_id;
                $item_read = true;
                $read_by = $user_username;
            }else{
                echo "You are not logged in";
                exit(1);
            }

            if($title == "" || $title == null || empty($title)){
                echo "no-title";
                exit(1);
            }elseif($message == "" || $message == null || empty($message)){
                echo "no-message";
                exit(1);
            }elseif($notification_type == "" || $notification_type == null || empty($notification_type)){
                echo "notification-type-not-set";
            }
            
            if($audience == "Others" && !empty($_REQUEST["custom_audience"])){
                $audience = $_REQUEST["custom_audience"];
            }elseif($audience == "Others" && empty($_REQUEST["custom_audience"])){
                echo "no-custom-audience";
                exit(1);
            }elseif($audience != "All" && $audience != "Others"){
                echo "no-audience-provided";
                exit(1);
            }

            $date_now = date("d-m-Y H:i:s");
            $sql = "INSERT INTO notification (Sender_id, Audience, School_id, Notification_type, Title, Description, Item_Read, Read_by, Date) VALUES 
                    (?,?,?,?,?,?,?,?,?)" or die("Connection error");
            $res = $connect->prepare($sql);
            $res->bind_param("isisssiss",$sender_id,$audience,$school_id,$notification_type,$title,$message,$item_read,$read_by,$date_now);
            
            if($res->execute()){
                if($submit == "make_announcement"){
                    //redirect to previous page
                    $location = $_SERVER["HTTP_REFERER"];

                    header("location: $location");
                }else{
                    echo "success";
                }                
            }else{
                echo "error making announcement";
            }
        }
        elseif($submit == "fetchEdit"){
            $school_id = $_REQUEST["school_id"];
            $content_box = $_REQUEST["content_box"];

            $_REQUEST["user_id"] = $user_id;

            //determine display to provide
            if($school_id <= 0 || empty($school_id)){
                echo "School selection failed. Please check and try again";
            }elseif(empty($content_box)){
                echo "Content menu not described. Please describe the content to display to continue";
            }elseif($content_box == "details"){
                require($rootPath."/admin/admin/page_parts/admission.php");
            }elseif($content_box == "add_student"){
                require($rootPath."/admin/admin/page_parts/cssps.php");
            }elseif($content_box == "houses"){
                require($rootPath."/admin/admin/page_parts/houses.php");
            }elseif($content_box == "alloc"){
                require($rootPath."/admin/admin/page_parts/allocation.php");
            }
        }
    }else{
        echo "no-submission";
    }

    //close connections
    $connect->close();
?>