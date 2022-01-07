<?php include_once("includes/session.php") ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!--Scripts-->
    <script src="assets/scripts/jquery/uncompressed_jquery.js"></script>
    <script src="assets/scripts/angular/angular.js"></script>
    <script src="assets/scripts/index.js"></script>
    <script src="assets/scripts/functions.js"></script>

    <!--Styles-->
    <link rel="stylesheet" href="assets/styles/general.css">
    <link rel="stylesheet" href="assets/styles/admin/admin_form.css">
    <link rel="stylesheet" href="assets/styles/loader.css?v=<?php echo time()?>">

    <style>
        body{
            background-color: #eee;
            background-image: url("assets/images/backgrounds/leone-venter-pVt9j3iWtPM-unsplash.jpg");
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form{
            width: 80vw;
        }

        /*success message*/
        #success_box{
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            width: 80vw;
            margin-right: 10vw;
            display: none;
        }
        #success_box #message{
            padding: 0.5em;
            width: fit-content;
        }

        #message p{
            margin: 1em auto;
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

    <!--Forgot password Section-->
    <form action="<?php echo $url?>/admin/submit.php" method="post" name="passwordForm">
        <div class="back_btn no_disp">
            <button type="button" class="btn" title="Back">&leftarrow;</button>
        </div>
        <div class="head">
            <h2>Forgot Password</h2>
        </div>
        <div class="body">
            <div id="message_box" class="no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <label for="email">
                <span class="label_image">
                    <img src="assets/images/icons/person-outline.svg" alt="email_logo">
                </span>
                <input type="email" name="email" id="email" class="text_input" placeholder="Your School Email" autocomplete="off">
            </label>
    
            <div class="password_change no_disp" id="password_change">
                <input type="hidden" name="user_id">
                
                <label for="password">
                    <span class="label_image">
                        <img src="assets/images/icons/lock.png" alt="password">
                    </span>
                    <input type="password" name="password" id="password" class="text_input password" placeholder="Your New Password" autocomplete="off">
                </label>
                <label for="password2">
                    <span class="label_image">
                        <img src="assets/images/icons/lock.png" alt="password">
                    </span>
                    <input type="password" name="password2" id="password2" class="text_input password" placeholder="Re-enter New Password" autocomplete="off">
                </label>
                <label for="show_password" class="checkbox">
                    <input type="checkbox" name="show_password" id="show_password">
                    <span class="label_title">Show Password</span>
                </label>
            </div>
            <div class="flex">
                <label for="submit" class="btn_label">
                    <button type="submit" name="submit" value="verify_password" disabled>Submit</button>
                </label>
                <label for="cancel" class="btn">
                    <button type="reset" name="cancel">Cancel</button>
                </label>
            </div>
            
        </div>
        <div class="foot">
            <p>
                @2021 shsdesk.com
            </p>
        </div>
    </form>

    <!--Success message box-->
    <div id="success_box">
        <div id="message">
            <p>
                <span>You password has been updated successfully</span>
            </p>
            <p>Click <a href="<?php echo $url?>/admin/">here</a> to login with your new details</p>
        </div>
    </div>
<script src="assets/scripts/form/general.js"></script>
<script>
    //check for valid email
    $("input#email").blur(function() {
        if($(this).val().length <= 5) {
            messageBoxTimeout("passwordForm","Email field is incomplete!", "error", 5);

            if(!$("#password_change").hasClass("no_disp")){
                $("#password_change").addClass("no_disp");
            }
        }else{
            $.ajax({
                url: $("form").attr("action"),
                data: "email=" + $(this).val() + "&submit=user_check",
                type: "POST",
                dataType: "html",
                cache: false,
                beforeSend: function(){
                    messageBoxTimeout("passwordForm",loadDisplay({
                        span1: "gray", 
                        span2: "gray", 
                        span3: "gray", 
                        span4: "gray",
                        size: "vsmall"
                    }), "load", 0);
                },
                success: function(data){
                    if(data.includes("success")){
                        $("#password_change").removeClass("no_disp");

                        //parse the user id
                        user_id = data.split("+");

                        $("input[name=user_id]").val(user_id[1]);

                        messageBoxTimeout("passwordForm","Email was found", "success", 5);
                    }else{
                        messageBoxTimeout("passwordForm","Email is invalid", "error", 7);
                        $("#password_change").addClass("no_disp");
                    }
                },
                complete: function(){
                    //hide the message box
                    $("#message_box").removeClass("load").addClass("no_disp");
                },
                error: function(){
                    messageBoxTimeout("passwordForm","Error communicating with server", "error", 5);
                }
            });
        }

        if($("#password_change").hasClass("no_disp") && ($(this).val().length > 0 && $(this).val().length <= 2)){
            messageBoxTimeout("passwordForm","Error getting account information! Check and try again later", "error", 5);
        }
    })

    //disable the email field when the password field is focused
    $("input#password, input#password2").focus(function(){
        $("input#email").prop("disabled", true);
    })

    $("input#password2").keyup(function(){
        if($("#password2").val() == $("#password").val()){
            $("button[name=submit]").prop("disabled", false);
        }else{
            $("button[name=submit]").prop("disabled", true);
        }

        //display a message when its value is of same lenght with original and passwords match
        if($(this).val().length == $("input#password").val().length){
            if($(this).val() == $("input#password").val()){
                messageBoxTimeout("passwordForm", "Passwords Match", "success", 5);
            }else{
                messageBoxTimeout("passwordForm", "Passwords Mismatch", "error", 5);
            }
        }
    })

    $("form").submit(function(e){
        e.preventDefault();

        $.ajax({
            url: $(this).prop("action"),
            data: $(this).serialize() + "&submit=" + $("button[name=submit]").val(),
            type: $(this).prop("method"),
            dataType: "html",
            beforeSend: function(){
                $("#message_box").removeClass("no_disp success error").addClass("load");
                $("#message_box .message").html("Please Wait...");
            },
            success: function(data){
                if(data === "success"){
                    messageBoxTimeout("passwordForm", "Password has been changed successfully", "success");

                    //disable all inputs
                    $("input#password, input#password2, button[name=submit]").prop("disabled", true);
                }else{
                    messageBoxTimeout("passwordForm", "Password could not be changed. Try again later", "error");

                    //enable inputs and button
                    $("input#password, input#password2, button[name=submit]").prop("disabled", false);
                }
            }
        })
    })

    $("button[name=cancel]").click(function(){
        $("#password_change").addClass("no_disp");
        $("button[name=submit]").prop("disabled", true);
    })
</script>
</body>
</html>

<?php $connect->close() ?>