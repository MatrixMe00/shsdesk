<?php include_once("../includes/session.php")?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php @include_once($rootPath.'/blocks/generalHead.php')?>

<!--Document title-->
<title>Register</title>

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
</style>
</head>
<body>
    <main>
        <form action="form.php" enctype="multipart/form-data" method="post" name="schoolAddForm">
            <div class="head">
                <h2>Add Your School</h2>
            </div>
            <div class="body">
                <div id="message_box" class="no_disp">
                    <span class="message">Here is a test message</span>
                    <div class="close"><span>&cross;</span></div>
                </div>

                <?php
                    $res = $connect->query("SELECT id FROM schools ORDER BY DESC LIMIT 1");

                    if(@$res->num_rows > 0){
                        $row = $res->fetch_array();
                        $form_id = $row["id"];
                        $form_id += 1;
                    }else{
                        $form_id = 1;
                    }
                ?>
                <input type="hidden" name="form_id" value="<?php echo $form_id?>">

                <div class="joint">
                    <label for="school_name">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/user.png" alt="fullname_logo">
                        </span>
                        <input type="text" name="school_name" id="school_name" class="text_input" placeholder="Name of School" pattern="[a-zA-Z\s]{6,}">
                    </label>
                    <label for="abbreviation">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/user.png" alt="abbr">
                        </span>
                        <input type="text" name="abbreviation" id="abbreviation" title="Write the abbreviation of your school's name here" placeholder="Abbreviated name of school">
                    </label>
                    <label for="head_name">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/user.png" alt="head name">
                        </span>
                        <input type="text" name="head_name" id="head_name" class="text_input" placeholder="Name of School Head" pattern="[a-zA-Z\s]{6,}"
                        title='Provide the name of the head of the institution'>
                    </label>
                    <label for="technical_name">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/user.png" alt="fullname_logo">
                        </span>
                        <input type="text" name="technical_name" id="technical_name" class="text_input" placeholder="Name of Technical Support Personnel" pattern="[a-zA-Z\s]{6,}"
                        title="This is the name of the technical support personnel. It is the same that people will call for assitance">
                    </label>
                    <label for="technical_phone">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="call">
                        </span>
                        <input type="tel" name="technical_phone" id="technical_phone" class="text_input" placeholder="Technical Person Phone Contact*"
                        title="Personnel's phone contact. This person is probably the school's administrator">
                    </label>
                    <label for="school_email">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/mail-outline.svg" alt="email_icon">
                        </span>
                        <input type="email" name="school_email" id="school_email" class="text_input" placeholder="School's email address">
                    </label>
                </div>
                
                <label for="description">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/information-outline.svg" alt="icon">
                    </span>
                    <textarea type="text" name="description" id="description" placeholder="Provide a  brief description about the school*"></textarea>
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
                        <select name="residence_status" id="residence_status">
                            <option value="">Please select your residence status*</option>
                            <option value="boarding">Boarding Only</option>
                            <option value="day">Day Only</option>
                            <option value="boarding/day">Boarding and Day</option>
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
                
                <div class="joint">
                    <label for="prospectus" class="file_label">
                        <span class="label_title">Please upload your Prospectus (PDF format)</span>
                        <div class="fore_file_display">
                            <input type="file" name="prospectus" id="prospectus" accept=".pdf">
                            <span class="plus">+</span>
                            <span class="display_file_name">Choose or drag your file here</span>
                        </div>
                    </label>
                    <label for="admission_letter" class="file_label">
                        <span class="label_title">Please upload a sample of your admission letter (PDF format)</span>
                        <div class="fore_file_display">
                            <input type="file" name="admission_letter" id="admission_letter" accept=".pdf">
                            <span class="plus">+</span>
                            <span class="display_file_name">Choose or drag your file here</span>
                        </div>
                    </label>
                </div>

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
                    @2021 shsdesk.com
                </p>
            </div>
        </form>
    </main>

    <?php @include_once($rootPath.'/blocks/footer.php')?>

    <script>        
        $(document).ready(function(){
            //this process will automatically fill the options of the category with numbers
            /*$("select[name=category]").ready(function(){
                //count the number of options
                option_total = $(this).children("option").length;

                for(var i=2; i <= option_total; i++){
                    //fill the options with their respective values
                    $(this).children("option:nth-child(" + i + ")").attr("value",i);
                }
            })*/
        })

        //concerning the files that will be chosen
        $("input[type=file]").change(function(){
            //get the value of the image name
            image_path = $(this).val();

            //strip the path name to file name only
            image_name = image_path.split("C:\\fakepath\\");

            //store the name of the file into the display div
            if(image_path != ""){
                $(this).siblings(".plus").hide();
                $(this).siblings(".display_file_name").html(image_name);       
            }else{
                $(this).siblings(".plus").css("display","initial");
                $(this).siblings(".display_file_name").html("Choose or drag your file here");
            }
        })

        //the avatar of te school
        $("input[name=avatar]").change(function(){
            if($(this).val() != ''){
                //show the selected image
                $("label[for=display_avatar]").show();  

                //make the file ready for display
                var file = $("input[type=file]").get(0).files[0];

                if(file){
                    //create a variable to make a read class instance
                    reader = new FileReader();

                    reader.onload = function(){
                        //pass the result to the image element
                        $("#display_avatar img").attr("src", reader.result);
                    }

                    //make the reading data a demo url
                    reader.readAsDataURL(file);
                }
            }else{
                //hide the selected image
                $("label[for=display_avatar]").hide();

                //empty the image src
                $("#display_avatar img").prop("src", "");
            }
        })

        $("input[name=other_category]").blur(function(){
            $("select[name=category]").val($(this).val());
        })

        //function to show a message
        function shortDisplay(message, className, time = 5){
            //display the message box
            $("#message_box").addClass(className).show();
            $("#message_box .message").html(message);

            //calculate the time
            time *= 1000;

            if(time > 0){
                //hide the message box
                setInterval(function(){
                    $("#message_box").removeClass("success error load").hide();
                    $("#message_box .message").html("");
                }, time);
            }
        }

        $("form").submit(function(event){
            //event.preventDefault();
            formData = $(this).serialize() + "&submit=" + $("button[name=submit]").val();

            $(this).submit();
        })
    </script>
</body>
</html>