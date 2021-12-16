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
            $image_alt = $_POST["image_alt"];
            $item_page = $_POST["item_page"];
            $item_type = $_POST["item_type"];
            $item_head = $_POST["item_head"];
            $item_desc = $_POST["item_desc"];
            $item_button = $_POST["item_button"];
            $button_text = $_POST["real_button_text"];
            $button_url = $_POST["button_url"];
            $activate = $_POST["activate"];
            
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
                $sql = "INSERT INTO pageItemDisplays(item_img,image_alt, item_page, item_type, item_head, 
                item_desc, item_url, item_button,button_text, active) VALUES(?,?,?,?,?,?,?,?,?,?)";

                //prepare the values for entry
                $result = $connect->prepare($sql);
                $result->bind_param("sssssssisi",$item_img,$image_alt, $item_page, $item_type, $item_head, $item_desc, $button_url, $item_button, $button_text, $activate);

                //execute command
                if($result->execute()){
                    //take previous reference and remove any additives
                    $reference = explode("?",$_SERVER["HTTP_REFERER"]);
                    $reference = $reference[0];

                    header("location:$reference?nav_point=Index");

                    echo "success";
                }else{
                    echo "error";
                }
            }
        }elseif($submit == "yes_no_submit" || $submit == "yes_no_submit_ajax"){
            $sid = $_REQUEST["sid"];
            $mode = $_REQUEST["mode"];
            $table = $_REQUEST["table"];

            if($mode == "activate"){
                $activate = 1;
            }elseif($mode == "deactivate"){
                $activate = 0;
            }

            //sql statement
            if($mode == "delete"){
                $sql = "DELETE FROM $table 
                WHERE id=$sid" or die($connect->error);
            }elseif($mode == "activate" || $mode == "deactivate"){
                $sql = "UPDATE $table 
            SET Active = $activate
            WHERE id=$sid" or die($connect->error);
            }
            
            //responses
            if($connect->query($sql)){
                echo "update-success";
            }else{
                echo "update-error";
            }

        }
    }else{
        echo "no-submission";
    }

    //close connections
    $connect->close();
?>