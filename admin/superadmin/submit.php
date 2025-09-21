<?php
    include_once("page_parts/session.php");

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
            $item_desc = htmlentities($_POST["item_desc"]);
            $item_button = $_POST["item_button"] ?? null;
            $button_text = $_POST["real_button_text"];
            $button_url = $_POST["button_url"];
            $activate = $_POST["activate"] ?? 0;
            
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
            $semicolons = explode(";",$query);
            $semicolons = count($semicolons)-1;
            $operation = strtolower(explode(" ", $query)[0]);

            if($semicolons == 0){
                echo "Please break your statement(s) with semicolon(s)";
            }elseif($operation != "select" && $operation != "retrieve" && $operation != "delete" && $operation != "update"){
                echo "Please provide an operation name. This could be SELECT, RETRIEVE, DELETE, UPDATE or CREATE";
                exit();
            }elseif(!strpbrk(strtolower($query), "from")){
                echo "Please provide the FROM clause.";
            }elseif($semicolons > 1 && $connect->multi_query($query)){
                echo "success";
            }elseif($semicolons == 1 && $result=$connect->query($query)){
                if($operation != "select" && $operation != "retrieve"){
                    echo "successOperation was successful";
                }else{
                    echo "<table class='full'>";

                    //generate the headers
                    $headers = array_keys($result->fetch_assoc());
                    echo "<tr>";
                    foreach($headers as $key){
                        echo "<td>$key</td>";
                    }
                    echo "</tr>";

                    //results
                    foreach($result as $key=>$value){
                        foreach($value as $key1=>$value1){
                            if($key1 == $headers[0] || $key1 == 0){
                                echo "<tr>";
                            }
                            echo "<td>$value1</td>";
                            if($key1 == end($headers) || $key1 == count($headers)){
                                echo "</tr>";
                            }
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
            $amount = empty($_REQUEST["pay_price"]) ? $_REQUEST["pay_price"] : $system_usage_price;
            $deduction = round(((1.95 / 100) * $amount), 2, PHP_ROUND_HALF_UP);
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
                $academic_year = getAcademicYear(now(), false);

                if($result->num_rows > 0){
                    $message = "Transaction ID already exists in system";
                }else{
                    $sql = "INSERT INTO transaction (transactionID, contactNumber, schoolBought, amountPaid, contactName, contactEmail, Deduction, Transaction_Date, academic_year) 
                    VALUES(?,?,?,?,?,?,?,?, '$academic_year')";
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
            $academic_year = getAcademicYear(now(), false);
            $response = array();
            $sql = "SELECT COUNT(transactionID) as total FROM transaction WHERE academic_year='$academic_year'";
            $result = $connect->query($sql);
            
            //total transaction count
            $response = array(
                "trans_received" => $result->fetch_assoc()["total"]
            );

            $sql = "SELECT COUNT(transactionID) as total FROM transaction
                WHERE academic_year='$academic_year' AND Transaction_Expired=TRUE";
            $result = $connect->query($sql);
            
            //total transaction expired
            $response += array(
                "trans_expired" => $result->fetch_assoc()["total"]
            );

            $sql = "SELECT COUNT(transactionID) as total FROM transaction
                WHERE academic_year='$academic_year' AND Transaction_Expired=FALSE";
            $result = $connect->query($sql);
            
            //total transaction not expired
            $response += array(
                "trans_left" => $result->fetch_assoc()["total"]
            );

            echo json_encode($response);
        }elseif($submit == "search_transaction"){
            $search = $_REQUEST['txt_search'];
            $contactSearch = $_REQUEST["searchByContact"];
            $academic_year = getAcademicYear(now(), false);

            if($contactSearch == "true"){
                $sql = "SELECT transactionID, contactName, contactNumber, contactEmail, schoolBought, Transaction_Date, Transaction_Expired, Transaction_Date
                    FROM transaction WHERE academic_year='$academic_year' AND contactNumber LIKE '%$search%'";
            }else{
                $sql = "SELECT transactionID, contactName, contactNumber, contactEmail, schoolBought, Transaction_Date, Transaction_Expired, Transaction_Date
                FROM transaction WHERE transactionID LIKE '%$search%' AND academic_year='$academic_year'";
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
        }elseif($submit == "update_ussd_status" || $submit == "submit_ussd_status_ajax"){
            $value = $_GET["value"] ?? null;
            $school_id = $_GET["school_id"] ?? null;

            if(is_null($value) || empty($value)){
                $message = "Process was stopped since status could not be determined";
            }elseif(is_null($school_id) || empty($school_id)){
                $message = "Process was stopped since target school could not be determined";
            }else{
                $sql = "UPDATE school_ussds SET status=? WHERE school_id=?";
                $stmt = $connect2->prepare($sql);
                $stmt->bind_param("si", $value, $school_id);

                if($stmt->execute()){
                    $message = "update-success";
                }else{
                    $message = "There was an error which occured while changing the status. Please try again later";
                }
            }

            echo $message;
        }elseif($submit == "add_split" || $submit == "add_split_ajax"){
            $school_id = $_POST["school_id"] ?? null;
            $split_code_admission = $_POST["split_code_admission"] ?? null;
            $split_code_management = $_POST["split_code_management"] ?? null;
            $admin_number = $_POST["admin_number"] ?? null;
            $head_number = $_POST["head_number"] ?? null;
            $admin_account_type = $_POST["admin_account_type"] ?? null;
            $head_account_type = $_POST["head_account_type"] ?? null;
            $admin_bank = $_POST["admin_bank"] ?? null;
            $head_bank = $_POST["head_bank"] ?? null;

            $telecoms = ["airteltigo", "glo", "mtn","vodafone"];
            $message = "";

            if(is_null($school_id) || empty($school_id)){
                $message = "No school was selected";
            }elseif(is_null($split_code_admission) || empty($split_code_admission)){
                $message = "Please provide the split code for admission";
            }elseif(str_contains(strtolower($split_code_admission),"spl_") === false){
                $message = "Split code provided for admission has the wrong format";
            }elseif(!is_null($split_code_management) && !empty($split_code_management) && str_contains(strtolower($split_code_management),"spl_") === false){
                $message = "Split code provided for management has the wrong format";
            }elseif(!is_null($admin_account_type) && !empty($admin_account_type)){
                if(is_null($admin_bank) || empty($admin_bank)){
                    $message = "No admin account vendor was selected. Please check and try again";
                }elseif(is_null($admin_number) || empty($admin_number)){
                    $message = "Please provide the account number for the admin";
                }elseif(strtolower($admin_account_type) == "bank" && (strlen($admin_number) < 10 || strlen($admin_number) > 20)){
                    $message = "The bank account number for the admin is of invalid standard length";
                }elseif(strtolower($admin_account_type) == "mobile" && strlen($admin_number) != 10){
                    $message = "Please provide a valid 10 digit phone number for the admin";
                }elseif(strtolower($admin_account_type) == "mobile" && is_numeric($admin_number) === false){
                    $message = "Phone number for the admin must be only numbers";
                }elseif(strtolower($admin_account_type) == "mobile" && array_search(strtolower($admin_bank),$telecoms) === false){
                    $message = "You have provided an invalid account vendor for the admin";
                }elseif(strtolower($admin_account_type) == "mobile" && array_search(substr($admin_number, 0, 3), $phoneNumbers1[strtolower($admin_bank)]) === false){
                    $message = "Admin's phone number does not match the specified service provider";
                }
            }elseif(!is_null($head_account_type) && !empty($head_account_type)){
                if(is_null($head_bank) || empty($head_bank)){
                    $message = "No head account vendor was selected. Please check and try again";
                }elseif(is_null($head_number) || empty($head_number)){
                    $message = "Please provide the account number for the head";
                }elseif(strtolower($head_account_type) == "bank" && (strlen($head_number) < 10 || strlen($head_number) > 20)){
                    $message = "The bank account number for the head is of invalid standard length";
                }elseif(strtolower($head_account_type) == "mobile" && strlen($head_number) != 10){
                    $message = "Please provide a valid 10 digit phone number for the head";
                }elseif(strtolower($head_account_type) == "mobile" && is_numeric($head_number) === false){
                    $message = "Phone number for the head must be only numbers";
                }elseif(strtolower($head_account_type) == "mobile" && array_search(strtolower($head_bank),$telecoms) === false){
                    $message = "You have provided an invalid account vendor for the head";
                }elseif(strtolower($head_account_type) == "mobile" && array_search(substr($head_number, 0, 3), $phoneNumbers1[strtolower($head_bank)]) === false){
                    $message = "Head's phone number does not match the specified service provider";
                }
            }elseif((is_null($admin_number) || empty($admin_number)) && (is_null($head_number) || empty($head_number))){
                $message = "Provide at least the detail of the admin or the school head";
            }
            
            if(empty($message)){
                try {
                    $split_code_admission = strtoupper($split_code_admission);
                    $split_code_management = strtoupper($split_code_management);

                    $proceed = true;

                    if(intval(fetchData("COUNT(split_code_admission) as total","transaction_splits","split_code_admission='$split_code_admission'")["total"]) > 0){
                        $proceed = false;
                    }else{
                        $message = "Admission Split Code already exists. Processing was terminated";
                    }

                    if(!empty($split_code_management)){
                        if(intval(fetchData("COUNT(split_code_management) as total","transaction_splits","split_code_management='$split_code_management'")["total"]) > 0){
                            $proceed = false;

                            //send an sms to the admin
                            $user_school_id = $school_id;
                            include_once("$rootPath/sms/sms.php");
                        }else{
                            $message = "Management Split Code already exists. Processing was terminated";
                        }
                    }

                    if($proceed === true){
                        $sql = "INSERT INTO transaction_splits (schoolID, split_code_admission, split_code_management, admin_bank, admin_number, head_bank, head_number) VALUES (?,?,?,?,?,?,?)";
                        $stmt = $connect->prepare($sql);
                        $stmt->bind_param("issssss",$school_id, $split_code_admission, $split_code_management, $admin_bank, $admin_number, $head_bank, $head_number);

                        if($stmt->execute()){
                            $message = "success";
                        }else{
                            $message = "An error occured while details were been sent into the database";
                        }
                    }
                } catch (\Throwable $th) {
                    $message = $th->getMessage();
                }                
            }
            echo $message;
        }elseif($submit == "approve_split"){
            $status = $_POST["status"] ?? null;
            $school_id = $_POST["school_id"] ?? null;
            $m_code = $_POST["m_code"] ?? null;
            $a_code = $_POST["a_code"] ?? null;

            if(is_null($status) || empty($status) && intval($status) < 0){
                $message = "Status key is empty $status";
            }elseif(is_null($school_id) || empty($school_id)){
                $message = "No school was selected";
            }elseif(boolval($status) === true && (is_null($a_code) || empty($a_code))){
                $message = "Admission split code has not been provided";
            }elseif(boolval($status) === true && 
                (str_contains(strtolower($a_code),"spl_") === false || 
                (!empty($m_code) && !is_null($m_code) && str_contains(strtolower($m_code),"spl_") === false))){
                $message = "Please check that your split codes are in the right format";
            }else{
                if(boolval($status) === false){
                    $sql = "UPDATE transaction_splits SET status=? WHERE schoolID=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("ii", $status, $school_id);
                }else{
                    $sql = "UPDATE transaction_splits SET split_code_admission=?, split_code_management=?, status=? WHERE schoolID=?";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("ssii", $a_code, $m_code, $status, $school_id);
                }
                
                if($stmt->execute()){
                    $message = "success";
                }else{
                    $message = "An error occured while sending data into database. Please try again later";
                }
            }

            echo $message;
        }elseif($submit == "send_sms_admin" || $submit == "send_sms_admin_ajax"){
            $message = $_POST["message"] ?? null;
            $recipients = $_POST["recipients"] ?? null;
            $response = "";

            if(is_null($recipients) || empty($recipients)){
                $response = "Please provide at least one recipient";
            }elseif(is_null($message) || empty($message)){
                $response = "Please provide the sms text to be sent";
            }elseif(strlen($message) > 160){
                $response = "Your message exceeds the maximum length of 160 characters";
            }else{
                //make sure number is in good shape
                $recipients = str_replace(" ","",$recipients);
                if(str_contains($recipients,",")){
                    $recipients = explode(",",$recipients);
                }else{
                    $recipients = array($recipients);
                }

                foreach($recipients as $key => $recipient){
                    if(strlen($recipient) != 10){
                        $response = "'$recipient' is not up to 10 digits. Please check and try again";
                        break;
                    }elseif(!is_numeric($recipient)){
                        $response = "'$recipient' has an invalid character. Please check and try again";
                        break;
                    }elseif(array_search(substr($recipient, 0, 3), $phoneNumbers) === false){
                        $response = "'$recipient' does not match any network operator in the system";
                        break;
                    }else{
                        $recipients[$key] = remakeNumber($recipient, true, false);
                    }
                }

                if(empty($response)){
                    //avoid repeated numbers
                    $recipients = array_unique($recipients);

                    include_once($_SERVER["DOCUMENT_ROOT"]."/sms/sms.php");

                    $response = $_REQUEST["system_message"];
                }
            }

            echo $response;
        }elseif($submit == "send_sms_broadcast" || $submit == "send_sms_broadcast_ajax"){
            $message = $_POST["message"];

            // Prepare the upsert statement
            $sql = "
                INSERT INTO system_variables (`name`, `value`) 
                VALUES ('sms_broadcast', ?) 
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)
            ";

            if ($stmt = $connect->prepare($sql)) {
                // Bind the parameter
                $stmt->bind_param("s", $message);

                // Execute the statement
                if ($stmt->execute()) {
                    echo "Broadcast message saved successfully.";
                } else {
                    echo "Error executing query: " . $stmt->error;
                }

                // Close statement
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $connect->error;
            }

        }elseif($submit == "send_mail_admin"){
            $recipients = $_POST["recipients"];
            $template = $_POST["message"];
            $sender_name = $_POST["sendas"];
            $extra = $_POST["extra"];
            $subject = $_POST["subject"];

            if(empty($recipients) || is_null($recipients)){
                $message = "No recipients were specified";
            }elseif(empty($sender_name) || is_null($sender_name)){
                $message = "Sender name has not been provided";
            }elseif(empty($subject) || is_null($subject)){
                $message = "Your mail has no subject";
            }elseif(empty($template) || is_null($template)){
                $message = "No message was specified";
            }else{
                require "mailer.php";

                if($pass === $total){
                    $message = "email sent";
                }else{
                    $message = "$pass of $total mails sent";
                }
            }
            
            echo $message;
        }elseif ($submit == "check_school_students") {
            $school_id = $_POST["school_id"] ?? null;
            $response = ["has_students" => false];
        
            if ($school_id) {
                $count = fetchData1("COUNT(indexNumber) as total", "students_table", "school_id=$school_id")['total'];
        
                if ($count > 0) {
                    $programs = fetchData1(["program_id", "program_name", "short_form"], "program", "school_id=$school_id",0);
                    $response["has_students"] = true;
                    $response["programs"] = is_array($programs) ? pluck($programs, "program_id", "array", true) : [];
                }
            }
        
            header('Content-Type: application/json');
            echo json_encode($response);
        }elseif ($submit == "search_student_by_name") {
            $query = $_POST["term"] ?? null;
            $school_id = $_POST["school_id"] ?? null;
        
            $response = [
                "students" => [],
                "message" => "",
                "status" => false
            ];
        
            if (empty($query)) {
                $response["message"] = "No search term was provided";
            } else {
                $where = [
                    "(CONCAT(Othernames, ' ', Lastname) LIKE '%$query%' OR CONCAT(Lastname, ' ', Othernames) LIKE '%$query%')",
                    "NOT EXISTS (
                        SELECT 1
                        FROM accesstable a
                        WHERE a.indexNumber = students_table.indexNumber
                        AND a.status = 1
                    )"
                ];
        
                // Add school_id condition if present
                if (!empty($school_id)) {
                    $where[] = "school_id = $school_id";
                }
        
                // Default binder is AND
                $students = decimalIndexArray(fetchData1(
                    ["indexNumber", "Othernames", "Lastname", "program_id", "studentYear"], 
                    "students_table", 
                    $where, 0, "AND"
                ));
        
                if (is_array($students) && count($students) > 0) {
                    $response["students"] = $students;
                    $response["status"] = true;
                } else {
                    $response["message"] = "No student matched your search";
                }
            }
        
            header('Content-Type: application/json');
            echo json_encode($response);
        }elseif ($_POST['submit'] == 'create_access_code') {
            $school_id = $_POST['school_id'] ?? null;
            $students = $_POST['students'] ?? [];
        
            if (!$school_id || empty($students)) {
                echo json_encode([
                    'status' => false,
                    'message' => 'School or students missing.'
                ]);
                exit;
            }
        
            $transactions = generateTransactionID(count($students));
            $connect2->begin_transaction();
        
            foreach ($students as $position => $student) {
                $reference = $transactions[$position] ?? $transactions[0];
        
                $sql = "INSERT INTO transaction (transactionID, school_id, price, deduction, phoneNumber, email, index_number, pay_type)
                        VALUES (?, ?, 0, 0, '0249100268', 'successinnovativehub@gmail.com', ?, 'single')";
                $stmt = $connect2->prepare($sql);
                $stmt->bind_param("sis", $reference, $school_id, $student);
        
                // Rollback and return if INSERT fails
                if (!$stmt->execute()) {
                    $connect2->rollback();
                    echo json_encode([
                        'status' => false,
                        'message' => 'Failed to create transaction for student ID: ' . $student
                    ]);
                    exit;
                }
        
                // Now handle access code
                if (!activate_access_pay($student, $reference, $school_id)) {
                    $connect2->rollback();
                    echo json_encode([
                        'status' => false,
                        'message' => 'Failed to generate access code for student ID: ' . $student
                    ]);
                    exit;
                }
            }
        
            $connect2->commit();
            echo json_encode([
                'status' => true
            ]);
        }elseif($submit == "edit_user_role"){
            $role_id = $_POST["id"] ?? null;
            $price = $_POST["price"] ?? 0;

            if(is_null($role_id) || empty($role_id) || intval($role_id) <= 0){
                $message = "No role was selected";
            }elseif($price < 0){
                $message = "Please provide at least one permission for this role";
            }else{
                $sql = "UPDATE roles SET price=? WHERE id=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("di", $price, $role_id);

                if($stmt->execute()){
                    $message = "success";
                    $success = true;
                }else{
                    $message = "An error occured while sending data into database. Please try again later";
                }
            }

            echo json_encode(["success" => $success ?? false, "message" => $message]);
        }elseif($submit == "add_user_role"){
            $title = $_POST["title"] ?? null;
            $price = $_POST["price"] ?? 0;
            $access = $_POST["access"] ?? 3;
            $is_system = $_POST["system_role"] ?? 0;

            if(is_null($title) || empty($title)){
                $message = "No title was provided for the role";
            }elseif($price < 0){
                $message = "Invalid price value for this role";
            }elseif(decimalIndexArray(fetchData("title", "roles", ["title='$title'", "school_id = 0"], 0, "AND")) != false){
                $message = "Role title already exists. Please choose another title";
            }else{
                $sql = "INSERT INTO roles (title, price, access, is_system) VALUES (?,?,?,?)";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("sdii", $title, $price, $access, $is_system);

                if($stmt->execute()){
                    $message = "success";
                    $success = true;
                }else{
                    $message = "An error occured while sending data into database. Please try again later";
                }
            }

            echo json_encode(["success" => $success ?? false, "message" => $message]);
        }elseif($submit == "delete_user_role"){
            $role_id = $_POST["id"] ?? null;

            if(is_null($role_id) || empty($role_id) || intval($role_id) <= 0){
                $message = "No role was selected";
            }elseif(intval(fetchData("COUNT(user_id) as total", "admins_table", "role=$role_id")["total"]) > 0){
                $message = "Role is currently assigned to some users. You cannot delete it";
                $can_switch = true;
            }else{
                $sql = "DELETE FROM roles WHERE id=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("i", $role_id);

                if($stmt->execute()){
                    $message = "success";
                    $success = true;
                }else{
                    $message = "An error occured while sending data into database. Please try again later";
                }
            }

            echo json_encode(["success" => $success ?? false, "message" => $message, "can_switch" => $can_switch ?? false]);
        }elseif($submit == "switch_user_role"){
            $delete_id = $_POST["delete_id"] ?? null;
            $new_id = fetchData("id", "roles", ["title='{$_POST['switch_title']}'", "school_id = 0"], 1, "AND")["id"] ?? null;
            $should_delete = $_POST["should_delete"] ?? 0;
            
            if(is_null($delete_id) || empty($delete_id) || intval($delete_id) <= 0){
                $message = "No role was selected for deletion";
            }elseif(is_null($new_id) || empty($new_id) || intval($new_id) <= 0){
                $message = "No new role or an unidentified role was selected to switch to";
            }elseif($delete_id == $new_id){
                $message = "You cannot switch to the same role";
            }else{
                $connect->begin_transaction();
                $sql = "UPDATE admins_table SET role=? WHERE role=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ii", $new_id, $delete_id);

                if($stmt->execute()){
                    if(intval($should_delete) === 1){
                        $sql = "DELETE FROM roles WHERE id=?";
                        $stmt = $connect->prepare($sql);
                        $stmt->bind_param("i", $delete_id);

                        if($stmt->execute()){
                            $connect->commit();
                            $message = "success";
                            $success = true;
                        }else{
                            $connect->rollback();
                            $message = "An error occured while deleting the old role from database. Please try again later";
                        }
                    }else{
                        $connect->commit();
                        $message = "success";
                        $success = true;
                    }
                }else{
                    $connect->rollback();
                    $message = "An error occured while sending data into database. Please try again later";
                }
            }
            
            echo json_encode(["success" => $success ?? false, "message" => $message]);
        }else{
            echo "Submission value was not present";
        }
    }else{
        echo "no-submission";
    }

    //close connections
    close_connections();
?>