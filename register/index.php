<?php
    //depending on where the page is being called from
    $this_url = $_SERVER["REQUEST_URI"];

    if(strpos($this_url, "shsdesk")){
        include_once($_SERVER["DOCUMENT_ROOT"]."/shsdesk/includes/session.php");
    }else{
        include_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/blocks/generalHead.php')?>
    <link rel="stylesheet" href="<?php echo $url?>/admin/assets/styles/tinymce.min.css?v=<?php echo time()?>">

    <!--Document title-->
    <title>SHSDesk - Register Your School</title>
    <meta name="description" content="Subscribe to our service for free. Register with SHSDesk to put you in the plan to be able to manage student admission and to easily receive record details">
    <meta name="keywords" content="subscribe, register, shs, desk, shsdesk, free, service, student, admission, application, school, category">
    <style>
        @media screen and (min-width: 768px){
            form{
                width: 80vw;
                position: relative;
                left: 10vw;
                right: 10vw;
                margin: 10px 0;
            }
        }

        #demo_doc{
            padding-left: 1.5em; color: blue
        }

        #demo_doc:hover{
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main>
        <form action="form.php" enctype="multipart/form-data" method="post" name="schoolAddForm">
            <div class="head">
                <h2>Add Your School</h2>
            </div>
            <div class="body">
                <div class="message_box no_disp">
                    <span class="message"></span>
                    <div class="close"><span>&cross;</span></div>
                </div>

                <?php
                    $res = $connect->query("SELECT MAX(id) AS id FROM schools");
                    $form_id = $res->fetch_assoc()["id"] + 1;
                ?>
                <input type="hidden" name="form_id" value="<?php echo $form_id?>">

                <div class="joint">
                    <label for="school_name">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/user.png" alt="fullname_logo">
                        </span>
                        <input type="text" name="school_name" id="school_name" class="text_input" placeholder="Name of School" pattern="[a-zA-Z\s]{6,}[\.\-\']{0,}"
                        autocomplete="off" title="Please provide the name of your school. It can include dot, hyphen or apostrophe. Numbers would be rejected" required>
                        
                    </label>
                    <label for="abbreviation">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/user.png" alt="abbr">
                        </span>
                        <input type="text" name="abbreviation" class="text_input" id="abbreviation" 
                        autocomplete="off" title="Write the abbreviation of your school's name here" placeholder="Abbreviated name of school">
                    </label>
                    <label for="head_name">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/user.png" alt="head name">
                        </span>
                        <input type="text" name="head_name" id="head_name" class="text_input" placeholder="Name of School Head" pattern="[a-zA-Z\s]{6,}"
                        autocomplete="off" title='Provide the name of the head of your institution' required>
                    </label>
                    <label for="technical_name">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/user.png" alt="fullname_logo">
                        </span>
                        <input type="text" name="technical_name" id="technical_name" class="text_input" placeholder="Name of Technical Support Personnel" pattern="[a-zA-Z\s]{6,}"
                        autocomplete="off" title="This is the name of the technical support personnel. It is the same that people will call for assitance" required>
                    </label>
                    <label for="technical_phone">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="call">
                        </span>
                        <input type="text" name="technical_phone" id="technical_phone" class="text_input tel" placeholder="Technical Person Phone Contact*"
                        autocomplete="off" title="Personnel's phone contact. This person is probably the school's [IT] administrator" required>
                    </label>
                    <label for="school_email">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/mail-outline.svg" alt="email_icon">
                        </span>
                        <input type="email" name="school_email" id="school_email" class="text_input" placeholder="School's email address"
                        autocomplete="off" title="Please provide your school email" required>
                    </label>
                    <label for="postal_address">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/Sign Post.png" alt="postal">
                        </span>
                        <input type="text" name="postal_address" id="postal_address" class="text_input" placeholder="Postal Address*" required
                        autocomplete="off" title="Please provide your postal address. It will be useful in details of the admission form">
                    </label>
                </div>
                
                <label for="description">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/information-outline.svg" alt="icon">
                    </span>
                    <textarea type="text" name="description" id="description" placeholder="Provide a  brief description about the school [800 characters max]*"
                    class="admin_tinymce" maxlength="800" autocomplete="off" title="Provide a brief description about your school"></textarea>
                </label>

                <div class="joint flex-wrap">
                    <label for="category">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/server-outline.svg" alt="icon">
                        </span>
                        <select name="category" id="category">
                            <option value="">Select A Category</option>
                            <?php
                                $result = $connect->query("SELECT * FROM school_category");

                                if($result->num_rows > 0){
                                    while($row = $result->fetch_assoc()){
                                        echo "
                                        <option value=\"".$row['id']."\">".$row['title']."</option>
                                        ";
                                    }
                                }
                            ?>
                        </select>
                    </label>

                    <label for="avatar" class="file_label">
                        <span class="label_title">Please upload your logo</span>
                        <div class="fore_file_display">
                            <input type="file" name="avatar" id="avatar" accept="image/*">
                            <span class="plus">+</span>
                            <span class="display_file_name">Choose or drag your file here</span>
                        </div>
                    </label>
                    <label for="display_avatar" class="no_disp">
                        <div id="display_avatar" class="display_image_box">
                            <img src="" alt="avatar">
                        </div>
                    </label>
                </div>
                
                <div class="joint">
                    <label for="residence_status">
                        <select name="residence_status" id="residence_status" required>
                            <option value="">Please select your residence status*</option>
                            <option value="Boarding">Boarding Only</option>
                            <option value="Day">Day Only</option>
                            <option value="Boarding/Day">Boarding and Day</option>
                        </select>
                    </label>
                    <label for="sector">
                        <select name="sector" id="sector">
                            <option value="">Please select your sector*</option>
                            <option value="private">Private</option>
                            <option value="government">Government / Public</option>
                        </select>
                    </label>
                </div>
                
                <label for="prospectus" class="file_label">
                    <span class="label_title">Please upload your Prospectus (PDF format)</span>
                    <div class="fore_file_display">
                        <input type="file" name="prospectus" id="prospectus" accept=".pdf">
                        <span class="plus">+</span>
                        <span class="display_file_name">Choose or drag your file here</span>
                    </div>
                </label>
                <p id="demo_doc"><a href="<?php echo $url?>/admin/admin/assets/files/default files/Admission_Form__Demo.pdf">Download Demo Admission Letter [PDF]</a></p>
                
                <label for="admission_letter_head" class="flex flex-column gap-sm">
                    <span class="label_title txt-bold">Admission Letter Heading [Optional]</span>
                    <input type="text" name="admission_letter_head" id="admission_letter_head" class="border sp-med" placeholder="Default: Offer of Admission" title="Provide your custom heading to be displayed on your admission letter">
                </label>
                <label for="admission_letter" class="textarea">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/megaphone-outline.svg" alt="admission" srcset="">
                    </span>
                    <textarea name="admission_letter" id="admission_letter" class="admin_tinymce" placeholder="Please provide a body for your admission letter. Refer to the demo document to know what is expected of you"></textarea>
                </label>

                <label for="autoHousePlace" class="checkbox">
                    <input type="checkbox" name="autoHousePlace" id="autoHousePlace">
                    <span class="label_title">Automatically Place students</span>
                </label>

                <div class="flex flex-center-align flex-wrap">
                    <label for="submit" class="btn btn_label">
                        <button type="submit" name="submit" value="register_school" class="img_btn">
                            <img src="<?php echo $url?>/assets/images/icons/add-circle-outline.svg" alt="lock">
                            <span>Add My School</span>
                        </button>
                    </label>
                    <label for="modal_cancel" class="btn">
                        <button type="button" class="modal_cancel" name="modal_cancel" value="cancel">Cancel</button>
                    </label>
                </div>                
            </div>
            <div class="foot">
                <p>
                    @<?= date("Y") ?> shsdesk.com
                </p>
            </div>
        </form>
    </main>

    <?php @include_once($rootPath.'/blocks/footer.php')?>
    
    <!--Document Scripts-->
    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/form/register.min.js?v=<?= time() ?>"></script>

    <!--TinyMCE scripts-->
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce/jquery.tinymce.min.js"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce/tinymce.min.js"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce.js?v=<?php echo time()?>"></script>
</body>
</html>