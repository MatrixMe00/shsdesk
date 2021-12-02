<?php include_once("../includes/session.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!--Scripts-->
    <script src="<?php echo $url?>/assets/scripts/jquery/uncompressed_jquery.js"></script>
    <script src="<?php echo $url?>/assets/scripts/functions.js"></script>

    <!--Styles-->
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/admin/admin_form.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/general.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/loader.css?v=<?php echo time()?>">

    <style>
        body{
            background-color: #eee;
            background-image: url("<?php echo $url?>/assets/images/backgrounds/leone-venter-pVt9j3iWtPM-unsplash.jpg");
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form{
            width: 80vw;
        }

        @media screen and (orientation: landscape) and (min-width:748px){
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
    <div id="cover"></div>
    <form action="submit.php" method="post" name="loginForm">
        <div class="head">
            <h2>Login</h2>
        </div>
        <div class="body">
            <div id="message_box" class="no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <label for="username">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="username_logo">
                </span>
                <input type="text" name="username" id="username" class="text_input" placeholder="Your Username or School Email" autocomplete="off">
            </label>
            <label for="password">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/lock.png" alt="username_logo">
                </span>
                <input type="password" name="password" id="password" class="text_input" placeholder="Your Password" autocomplete="off">
            </label>
            <label for="pass_forg">
                <p onclick="window.location.href = '<?php echo $url?>/password.php'">Forgot Your Password?</p>
            </label>
            <label for="submit" class="btn_label">
                <button type="submit" name="submit" value="login">Login</button>
            </label>
        </div>
        <div class="foot">
            <p>
                @2021 shsdesk.com
            </p>
        </div>
    </form>

    <script src="<?php echo $url?>/assets/scripts/form/general.js"></script>
    <script src="<?php echo $url?>/assets/scripts/index.js?v=<?php echo time()?>"></script>
    <script>
        function messageTimeout(message, message_type, time = 0){
            //transform time into miliseconds
            time *= 1000;

            //display message and its type
            $("#message_box").fadeIn().addClass(message_type);
            $("#message_box .message").html(message);

            if(time){
                //automatically hide the message box
                setTimeout(function(){
                    //remove all classes and messages
                    $("#message_box").fadeOut().removeClass("load success error");
                    $("#message_box .message").html("");
                }, time)
            }
        }

        $("form").submit(function(e){
            e.preventDefault();
            message = "";
            message_type = "";
            time = 0;

            //check if there are user errors
            if($("input#username").val() == ""){
                message = "Please enter your username";
                message_type = "error";
                time = 5;

                //focus into this input element
                $("input#username").focus();

                messageBoxTimeout("loginForm",message, message_type, time);

                return false;
            }else if($("input#password").val() == ""){
                message = "Please enter your password";
                message_type = "error";
                time = 5;

                //focus into this input element
                $("input#password").focus();

                messageBoxTimeout("loginForm",message, message_type, time);

                return false;
            }else{
                //data of form
                dataString = $(this).serialize() + "&submit=" + $("button[name=submit]").val();

                reload = "";
                //perform the validation and ajax request
                $.ajax({
                    url: $(this).attr("action"),
                    data: dataString,
                    cache: false,
                    dataType: "html",
                    type: "POST",
                    beforeSend: function(){
                        message = loadDisplay();
                        message_type = "load";

                        messageBoxTimeout("loginForm",message, message_type, time);
                    },
                    success: function(html){
                        if(html.includes("login_success")){
                            message_type = "success";
                            message = "Login Successful";
                            time = 0;
                        }else if(html === "password_error"){
                            message_type = "error";
                            message = "Wrong Password was entered";
                            time = 5;
                        }else if(html === "username_error"){
                            message_type = "error";
                            message = "Username entered is invalid or could not be found";
                            time = 10;
                        }else{
                            message_type = "error";
                            message = "Could not receive response from server, please try again later";
                        }
                        messageBoxTimeout("loginForm",message, message_type, time);

                        //if login was successful, reload this page via the user role
                        if(html === "login_success"){
                            //reload to normal admin page
                            location.reload("admin/");
                        }else if(html === "admin_login_success"){
                            //reload into super admin page
                            location.reload("superadmin/");
                        }else{
                            return false;
                        }
                    },
                    error: function(){
                        message = "Cannot connect to server. Please try again";
                        message_type = "error";
                    }
                })
            }
        })
    </script>
</body>
</html>