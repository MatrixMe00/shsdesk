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
        }elseif($submit == "getHeaders"){
            $table_name = $_REQUEST["table_name"];
            $query = $connect->query("SELECT * FROM $table_name LIMIT 1");
            
            if($query->num_rows >= 0){
                echo "<tr>";

                //extract the keys
                $fields = array_keys($query->fetch_assoc());

                //display the keys
                foreach($fields as $field){
                    echo "<td>$field</td>";
                }

                echo "</tr>";
            }else{
                echo "Table was not found. Please check the name of the table and try again";
            }
        }elseif($submit == "databaseQuery"){
            $query = $_REQUEST["query"];

            if(!strpbrk(strtolower($query), "select") || !strpbrk(strtolower($query), "retrieve") || !strpbrk(strtolower($query), "delete") ||
                !strpbrk(strtolower($query), "update")){
                echo "Please provide an operation name. This could be SELECT, RETRIEVE, DELETE, UPDATE or CREATE";
                exit();
            }elseif(strpbrk(strtolower($query), "from")){
                echo "Please provide the FROM clause.";
            }elseif($result = multi_query($query) || $result = $connect->query($query)){
                if(strpbrk(strtolower($query), "update") || !strpbrk(strtolower($query), "delete")){
                    echo "success";
                }else{
                    echo "<table>";

                    //generate the headers
                    $headers = array_keys($result);
                    echo "<tr>";
                    foreach($headers as $key){
                        echo "<td>$key</td>";
                    }
                    echo "</tr>";

                    //results
                    foreach($result as $key=>$value){
                        if($key == $headers[0] || $key == 0){
                            echo "<tr>";
                        }
                        echo "<td>$value</td>";
                        if($key == end($headers) || $key == count($headers) - 1){
                            echo "</tr>";
                        }
                    }

                    echo "</table>";
                }
            }
        }elseif($submit == "new_transaction"){
            $trans_id = $_REQUEST["trans_id"];
            $cont_number = $_REQUEST["cont_number"];
            $cont_email = $_REQUEST["cont_email"];
            $school = $_REQUEST["school"];
            $amount = 30.00;
            $deduction = 0.59;
            $cont_name = $_REQUEST["cont_name"];
            $trans_date = date("Y-m-d H:i:s");

            //input validation
            if($trans_id === ""){
                $message = "Please enter a transaction ID";
            }elseif(strlen($trans_id) < 14){
                $message = "Transaction id provides is invalid. Check the length";
            }elseif($trans_id[0] != "T"){
                $message = "Valid transaction IDs begin with T.";
            }elseif($cont_name === ""){
                $message = "Please provide the contact's mumber";
            }elseif($cont_number === ""){
                $message = "Please provide the contact's number";
            }elseif(strlen($cont_number) < 10){
                $message = "Please provide a valid phone number";
            }elseif($school === ""){
                $message = "Please select the school which was bought";
            }else{
                //check if transaction is already in system
                $sql = "SELECT transactionID FROM transaction WHERE transactionID = '$trans_id'";
                $result = $connect->query($sql);

                if($result->num_rows > 0){
                    $message = "Transaction ID already exists in system";
                }else{
                    $sql = "INSERT INTO transaction (transactionID, contactNumber, schoolBought, amountPaid, contactName, contactEmail, Deduction, Transaction_Date) 
                    VALUES(?,?,?,?,?,?,?,?)";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("ssidssds", $trans_id, $cont_number, $school, $amount, $cont_name, $cont_email, $deduction, $trans_date);
                    if($stmt->execute()) {
                        $message = "success";
                    }else{
                        $message = "Error occured while writing data into database. Try again later";
                    }
                }
            }

            echo $message;
        }elseif($submit == "currentTransactionCount"){
            $response = array();
            $sql = "SELECT COUNT(transactionID) as total FROM transaction";
            $result = $connect->query($sql);
            
            //total transaction count
            $response = array(
                "trans_received" => $result->fetch_assoc()["total"]
            );

            $sql = "SELECT COUNT(transactionID) as total FROM transaction
                WHERE Transaction_Expired=TRUE";
            $result = $connect->query($sql);
            
            //total transaction expired
            $response += array(
                "trans_expired" => $result->fetch_assoc()["total"]
            );

            $sql = "SELECT COUNT(transactionID) as total FROM transaction
                WHERE Transaction_Expired=FALSE";
            $result = $connect->query($sql);
            
            //total transaction not expired
            $response += array(
                "trans_left" => $result->fetch_assoc()["total"]
            );

            echo json_encode($response);
        }elseif($submit == "search_transaction"){
            $search = $_REQUEST['txt_search'];
            $contactSearch = $_REQUEST["searchByContact"];

            if($contactSearch == "true"){
                $sql = "SELECT transactionID, contactName, contactNumber, contactEmail, schoolBought, Transaction_Date, Transaction_Expired, Transaction_Date
                    FROM transaction WHERE contactNumber LIKE '%$search%'";
            }else{
                $sql = "SELECT transactionID, contactName, contactNumber, contactEmail, schoolBought, Transaction_Date, Transaction_Expired, Transaction_Date
                FROM transaction WHERE transactionID LIKE '%$search%'";
            }

            $sql .= " ORDER BY Transaction_Expired ASC";
            
            $result = $connect->query($sql);

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
?>
        <form action="<?php echo $url?>/admin/superadmin/submit.php" method="post" name="showTransaction">
            <div class="head">
                <h3>Transaction for <?php echo $row["transactionID"]?></h3>
            </div>
            <div class="body">
                <div class="message_box <?php
                    if($row["Transaction_Expired"] == TRUE){
                        echo "error";
                        $mess_display = "Transaction ID has been used";
                    }else{
                        echo "success";
                        $mess_display = "Transaction ID available for use";
                    }
                ?>">
                    <span class="message"><?php echo $mess_display; ?></span>
                </div>
                <div class="joint">
                    <label for="trans_id">
                        <input type="text" class="text_input" id="trans_id" name="trans_id" placeholder="Transaction ID"
                        title="Enter the received transaction ID" disabled value="<?php echo $row["transactionID"] ?>" />
                    </label>
                    <label for="cont_name">
                        <input type="text" class="text_input tel" id="cont_name" name="cont_name" disabled
                        title="Enter the contact's name [name of the person who bought it]" placeholder="Contact Name"
                        value="<?php echo $row["contactName"] ?>" />
                    </label>
                    <label for="cont_number">
                        <input type="text" class="text_input tel" id="cont_number" name="cont_number" disabled
                        title="Enter the contact's number [number of the person who bought it]" placeholder="Contact number"
                        value="<?php echo $row["contactNumber"] ?>" />
                    </label>
                    <label for="cont_email">
                        <input type="text" class="text_input" id="cont_email" name="cont_email" placeholder="Contact's email"
                        title="Contact's email address. You can leave it blank if none was provided during transaction" 
                        disabled value="<?php echo $row["contactEmail"] ?>"/>
                    </label>
                </div>
                <div class="joint">
                    <label for="school">
                        <select name="school" id="school" disabled>
                            <option value="<?php echo $row["schoolBought"]?>"><?php echo getSchoolDetail(intval($row["schoolBought"]))["schoolName"]?></option>
                        </select>
                    </label>
                    <label for="bought">
                        <input type="text" name="bought" id="bought" value="Paid on <?php echo date("jS M, Y \a\\t H:i:s", strtotime($row["Transaction_Date"]))?>"
                        disabled>
                    </label>
                </div>
                
                <input type="hidden" name="amount" value="30">
                <input type="hidden" name="deduction" value="0.59">
            </div>
        </form>
<?php
                }
            }else{
                echo "not-found";
            }
        }
    }else{
        echo "no-submission";
    }

    //close connections
    $connect->close();
?>