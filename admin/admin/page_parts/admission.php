<?php include_once("../../../includes/session.php");

    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
    }else{
        //set nav_point session
        $_SESSION["nav_point"] = "admission";
    }
?>

    <form action="<?php echo $url?>/admin/admin/submit.php" method="post" name="admissiondetailsForm">
        <div class="body">
            <div class="message_box no_disp">
                <span class="message">Here is a test message</span>
                <div class="close"><span>&cross;</span></div>
            </div>

            <?php 
                $query = $connect->query("SELECT * FROM admissiondetails WHERE schoolID= $user_school_id");
                $row = $query->fetch_assoc();

                $schoolDetail = getSchoolDetail($user_school_id, true);
            ?>

            <div class="joint">
                <input type="hidden" name="school_id" value="<?php echo $user_school_id?>">
                <label for="school_name">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/user.png" alt="fullname_logo">
                    </span>
                    <input type="text" name="school_name" id="school_name" class="text_input" placeholder="Name of School" pattern="[a-zA-Z\s]{6,}[\.\-\']{0,}"
                    autocomplete="off" title="Update the name of your school" value="<?php echo $schoolDetail["schoolName"]?>">
                    
                </label>
                <label for="school_email">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/mail-outline.svg" alt="email_icon">
                    </span>
                    <input type="email" name="school_email" id="school_email" class="text_input" placeholder="School's email address"
                    autocomplete="off" title="Update your school email" value="<?php echo $schoolDetail["email"]?>">
                </label>
                <label for="postal_address">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/Sign Post.png" alt="postal">
                    </span>
                    <input type="text" name="postal_address" id="postal_address" class="text_input" placeholder="Postal Address*"
                    autocomplete="off" title="Update your postal address. It will be useful in details of the admission form"
                    value="<?php echo $schoolDetail["postalAddress"]?>">
                </label>
            </div>
            <div class="joint">
                <label for="head_name">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="head name">
                    </span>
                    <input type="text" name="head_name" id="head_name" class="text_input" placeholder="Name of Head of School*" 
                    autocomplete="off" value="<?php echo $row["headName"] ?>" title="Name of School Head">
                </label>
                <label for="head_title">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/bag-handle-outline.svg" alt="head title">
                    </span>
                    <input type="text" name="head_title" id="head_title" class="text_input" placeholder="Title of Head*" autocomplete="off" 
                    title="Enter the title of the head provided above. Eg. Head Master or Head Mistress" value="<?php echo $row["titleOfHead"] ?>">
                </label>
                <label for="sms_id">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/chatbox-outline.svg" alt="sms id">
                    </span>
                    <input type="text" name="sms_id" id="sms_id" class="text_input" placeholder="SMS Sender ID*" autocomplete="off" 
                    title="Provide the name to be seen when an sms is sent. It should not exceed 11 characters [including spaces]" 
                 value="<?php echo $row["smsID"] ?>" maxlength="11">
                </label>
            </div>
            
            <div class="joint">
                <label for="admission_year">
                    <span class="label_image" style="align-self: flex-start;">
                        <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="admission year">
                    </span>
                    <div class="flex flex-column" style="flex:1">
                        <input type="text" disabled name="admission_year" id="admission_year" class="text_input" placeholder="Admission" autocomplete="off"
                        title="This is generated automatically, but you can manually input the admission year" maxlength="4" minlength="4" 
                        pattern="[0-9]+" value="<?php echo $row["admissionYear"]?>">
                        <span style="font-size: 11px; color: #999; display: inline-block; text-align: center">Current Admission Year [Generated by system]</span>
                    </div>                    
                </label>
                <label for="academic_year">
                    <span class="label_image" style="align-self: flex-start;">
                        <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="academic year">
                    </span>
                    <div class="flex flex-column" style="flex:1">
                        <input type="text" disabled name="academic_year" id="academic_year" class="text_input" placeholder="Academic Year*" autocomplete="off" 
                        title="Enter the current academic year" maxlength="11" minlength="9" value="<?php echo $row["academicYear"] ?>">
                        <span style="font-size: 11px; color: #999; display: inline-block; text-align: center">Current Academic Year [Generated by system]</span>
                    </div>
                </label>
                <label for="reopening">
                    <span class="label_image" style="align-self: flex-start;">
                        <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="username_logo">
                    </span>
                    <div class="flex flex-column" style="flex:1">
                        <input type="date" name="reopening" id="reopening" class="text_input" placeholder="Reopening Date [eg. <?php echo date("l, d M, Y")?>]*" autocomplete="off" 
                        title="Enter the date of reopening" value="<?php echo $row["reopeningDate"] ?>">
                        <span style="font-size: 11px; color: #999;">Reopening Date for next term</span>
                    </div>
                </label>
            </div>
            
            <label for="admission">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/megaphone-outline.svg" alt="announce">
                </span>
                <textarea name="admission" id="admission" placeholder="Update the body for your admission letter" 
                class="tinymce" title="Body of admission letter ">
                <?php echo $schoolDetail["admissionPath"] ?>
                </textarea>
            </label>
            <label for="description">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/megaphone-outline.svg" alt="announce">
                </span>
                <textarea name="description" id="description" placeholder="A description about your school..." 
                class="tinymce" title="Description of school to be previewed on schools list in main website">
                <?php echo $schoolDetail["description"] ?>
                </textarea>
            </label>

            <label for="announcement">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/megaphone-outline.svg" alt="announce">
                </span>
                <textarea name="announcement" id="announcement" placeholder="Enter an announcement here. This announcement will be displayed to 
                students in their student portals..." title="Enter an announcement to be displayed when the student wants to 
                log in or check admission"><?php echo $row["announcement"]?></textarea>
            </label>

            <div class="joint flex-wrap">
                <label for="avatar" class="file_label" style="flex:2">
                    <span class="label_title">Update your logo</span>
                    <div class="fore_file_display">
                        <input type="file" name="avatar" id="avatar" accept="image/*">
                        <span class="plus">+</span>
                        <span class="display_file_name">Choose or drag your file here</span>
                    </div>
                </label>
                <label for="display_avatar" class="" style="flex:1">
                    <div id="display_avatar" class="display_image_box">
                        <img src="<?php echo $url."/".$schoolDetail["logoPath"]?>" alt="avatar">
                    </div>
                </label>
            </div>

            <div class="joint">
                <label for="prospectus" class="file_label">
                    <span class="label_title">Please upload your Prospectus (PDF format)</span>
                    <div class="fore_file_display">
                        <input type="file" name="prospectus" id="prospectus" accept=".pdf">
                        <span class="plus">+</span>
                        <span class="display_file_name">Choose or drag your file here</span>
                    </div>
                    <span class="label_title">Current Filename: <a href="<?php 
                        $prospectus = explode("/",$schoolDetail["prospectusPath"]);
                        echo $url."/".$schoolDetail["prospectusPath"];
                    ?>"><?php echo end($prospectus) ?></a></span>
                </label>

                <label for="autoHousePlace" class="checkbox">
                    <input type="checkbox" name="autoHousePlace" id="autoHousePlace" <?php 
                        if($schoolDetail["autoHousePlace"]){
                            echo "checked";
                        } 
                    ?>>
                    <span class="label_title">
                        Automatically Place students into houses<br>
                        This feature is used by the system to place students into houses upon form fill.
                        Not enabling this feature would require you to upload students manually using the default
                        <a href="<?php echo $url?>/admin/admin/files/default files/house_allocation.xlsx">file</a>;
                    </span>
                </label>
            </div>

            <label for="submit" class="btn">
                <button type="submit" name="submit" value="admissiondetails">Save</button>
            </label>

            <div class="message_box no_disp">
                <span class="message">Here is a test message</span>
                <div class="close"><span>&cross;</span></div>
            </div>
        </div>
        <div class="foot">
            <p>
                @<?php echo date("Y")." ".$_SERVER['HTTP_HOST']?>
            </p>
        </div>
    </form>

    <script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce/jquery.tinymce.min.js"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce/tinymce.min.js"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/admin/admin/assets/scripts/admission.js?v=<?php echo time()?>"></script>