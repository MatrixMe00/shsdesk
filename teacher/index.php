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
                    This is the SHSDesk teachers portal. Provide your username and password to enter the portal
                </p>
                <label for="teacher_id">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="username_logo">
                    </span>
                    <input type="text" name="teacher_id" id="teacher_id" class="text_input" placeholder="Your Teacher ID" autocomplete="off"
                    title="Enter your id number" value="">
                </label>
                <label for="password" class="no_disp">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/key-outline.svg" alt="user_password">
                    </span>
                    <input type="password" name="password" id="password" class="text_input" placeholder="Your Password" autocomplete="off"
                    title="Enter your password to continue">
                </label>
                <label for="forget-link" class="color-teal txt-fs link-label bg-plain flex-content-start">
                    <a href="./forgot-account" name="forget-link" data-step="1">Forgot your account?</a>
                </label>
            </div>
            <div class="foot">
                <div class="flex flex-wrap btn_label gap-sm">
                    <button name="submit" type="submit" class="plain-r primary" data-step="1" value="user_login">Proceed</button>
                    <button name="reset" type="reset" class="no_disp plain-r orange">Cancel</button>
                </div>
            </div>
        </form>
    </main>

    <script src="<?php echo $url?>/assets/scripts/general.min.js?v=<?php echo time()?>" async></script>
    <script src="<?= $url ?>/assets/scripts/functions.min.js"></script>
    <script>
        $("button[name=submit]").click(function(){
            id = $("input[name=teacher_id]").val()
            step = $(this).attr("data-step")
            form_name = "teacherForm"

            if(parseInt(step) == 1){
                if($("input[name=teacher_id]").val() === ""){
                    messageBoxTimeout(form_name, "Your teacher ID is required to continue", "error");
                    return;
                }

                $.ajax({
                    url: "./submit.php",
                    data: {
                        submit: "user_login", teacher_id: id, step: 1
                    },
                    type: "GET",
                    timeout: 30000,
                    beforeSend: function(){
                        //disable editing for the id section
                        $("label[for=teacher_id] input").attr("readonly", true)
                        messageBoxTimeout(form_name, "Checking id...", "load", 0)
                    },
                    success: function(response){
                        if(typeof response === "object"){
                            if(response["error"] === true){
                                messageBoxTimeout(form_name, response["message"], "error")
                                $("label[for=teacher_id] input").attr("readonly", false);
                            }else{
                                messageBoxTimeout(form_name, "User was found", "success")
                                //show the password field and the reset button
                                $("label[for=password], button[name=reset]").removeClass("no_disp");

                                //indicate that it should move to the next step
                                $("button[name=submit]").attr("data-step","2");
                                $("a[name=forget-link]").attr("data-step","2").attr("href","./forgot-password");

                                //change html content of forget link
                                $("a[name=forget-link]").html("Forgot your password?");

                                $(this).html("Login");
                            }
                        }else{
                            messageBoxTimeout(form_name,response,"error")
                            $("label[for=teacher_id] input").attr("readonly", false);
                        }
                    },
                    error: function(xhr, textStatus){
                        let message = ""

                        if(textStatus === "timeout"){
                            message = "Connection was timed out. Please check your network connection and try again"
                        }else{
                            message = xhr.responseText
                        }

                        alert_box(message,"error")

                        $("label[for=teacher_id] input").attr("readonly", false);
                    }
                })
            }else if(parseInt(step) == 2){
                const password = $("input#password").val()
                $.ajax({
                    url: "./submit.php",
                    data: {
                        submit: "user_login", teacher_id: id, step: 2, password: password
                    },
                    type: "POST",
                    timeout: 30000,
                    beforeSend: function(){
                        //disable editing for the id section
                        $("label[for=password] input").attr("readonly", true);
                        messageBoxTimeout(form_name, "Checking password...", "load", 0)
                    },
                    success: function(response){
                        if(typeof response === "object"){
                            if(response["error"] === true){
                                messageBoxTimeout(form_name, response["message"], "error")
                                $("label[for=password] input").attr("readonly", false);
                            }else{
                                messageBoxTimeout(form_name, "Login was successful", "success")
                                location.href = "./main"
                            }
                        }else{
                            console.log(response);
                            messageBoxTimeout(form_name,response,"error")
                            $("label[for=password] input").attr("readonly", false);

                        }
                    },
                    error: function(xhr, textStatus){
                        let message = ""

                        if(textStatus === "timeout"){
                            message = "Connection was timed out. Please check your network connection and try again"
                        }else{
                            message = xhr.responseText
                        }

                        alert_box(message,"error")
                        $("label[for=password] input").attr("readonly", false);
                    }
                })
            }
            
        })

        $("button[name=reset]").click(function(){
            //reset submit step
            $("button[name=submit], a[name=forget-link]").attr("data-step", "1").attr("href","./forgot-account");

            //reset submit button text
            $("button[name=submit]").html("Proceed");

            //change html content of forget link
            $("a[name=forget-link]").html("Forgot your account?");

            //enable editing for the id section
            $("label[for=teacher_id] input").attr("readonly", false);

            //hide password and reset button
            $("label[for=password], button[name=reset]").addClass("no_disp");
        })
    </script>
</body>
</html>