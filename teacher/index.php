<?php include_once("../includes/session.php"); $gen1 = true?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($rootPath."/blocks/generalHead.php")?>
    
    <title>Teachers Portal</title>

    <meta name="description" content="This is the portal for teachers on the SHSDesk system. Use this portal to upload documents
    and results. Provide your unique number and proceed to the session of the system you will want delivered to you. SHSDesk
    is ready to provide all that you need.">

    <style>
        body{
            background-color: #eee;
            background-image: url("<?php echo $url?>/assets/images/backgrounds/HAN4890-web-sq_fb2957a9-958f-47c8-aeb1-8bf211bfbbfb.jpg");
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form{
            width: 80vw;
        }

        @media screen and (orientation: landscape) and (min-width:770px){
            body{
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                justify-content: flex-end;
            }

            form{
                margin-right: 45px;
                width: 45vw;
                background-color: rgba(255,255,255,0.85);
            }
        }
    </style>
</head>
<body>
    <main>
        <form action="<?php echo $url?>/submit.php" method="post" name="teacherForm" class="img-label fluid">
            <div class="head">
                <h2>Teacher's Portal</h2>
            </div>
            <div class="body">
                <div class="message_box">
                    <span class="message"></span>
                    <div class="close"><span>&cross;</span></div>
                </div>
                <p class="txt-fs txt-al-c" style="color: #444;">
                    This is the SHSDesk teachers portal. You can access your admission details using the admission button
                    or use the Enter button to proceed into your portal
                </p>
                <label for="teacherID">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="username_logo">
                    </span>
                    <input type="text" name="teacherID" id="teacherID" class="text_input" placeholder="Your Teacher ID" autocomplete="off"
                    title="Enter your id number" value="<?php echo md5("Password@1")?>">
                </label>
                <label for="password" class="no_disp">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/key-outline.svg" alt="user_password">
                    </span>
                    <input type="password" name="password" id="password" class="text_input" placeholder="Your Password" autocomplete="off"
                    title="Enter your password to continue">
                </label>
                <label for="forget-link" class="color-teal txt-fs link-label bg-plain flex-content-start">
                    <a href="#" name="forget-link" data-step="1">Forgot your account?</a>
                </label>
            </div>
            <div class="foot">
                <div class="flex btn m-sm-lr">
                    <button name="submit" type="submit" class="plain-r primary" data-step="1">Proceed</button>
                    <button name="reset" type="reset" class="no_disp plain-r orange">Cancel</button>
                </div>
            </div>
        </form>
    </main>

    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
    <script>
        $("button[name=submit]").click(function(){
            id = $("input[name=teacherID]").val();
            step = $(this).attr("data-step");
            form_name = "teacherForm";

            if(parseInt(step) == 1){
                if($("input[name=teacherID]").val() === ""){
                    messageBoxTimeout(form_name, "Your teacher ID is required to continue", "error");
                    return;
                }
                //disable editing for the id section
                $("label[for=teacherID] input").attr("disabled", true);

                //show the password field and the reset button
                $("label[for=password], button[name=reset]").removeClass("no_disp");

                //indicate that it should move to the next step
                $(this).attr("data-step","2");
                $("a[name=forget-link]").attr("data-step","2");

                //change html content of forget link
                $("a[name=forget-link]").html("Forgot your password?");

                $(this).html("Login");
            }else if(parseInt(step) == 2){
                alert_box("Submission will be done at this step", "light");
            }
            
        })

        $("button[name=reset]").click(function(){
            //reset submit step
            $("button[name=submit], a[name=forget-link]").attr("data-step", "1");

            //reset submit button text
            $("button[name=submit]").html("Proceed");

            //change html content of forget link
            $("a[name=forget-link]").html("Forgot your account?");

            //enable editing for the id section
            $("label[for=teacherID] input").attr("disabled", false);

            //hide password and reset button
            $("label[for=password], button[name=reset]").addClass("no_disp");
        })
    </script>
</body>
</html>