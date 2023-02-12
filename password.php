<?php include_once("includes/session.php") ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robot" content="nofollow">
    <title>SHSDesk - Forgot Your Password</title>

    <!--Scripts-->
    <script src="assets/scripts/jquery/uncompressed_jquery.js?v=<?php echo time()?>" async></script>
    <script src="assets/scripts/index.min.js?v=<?php echo time()?>" async></script>
    <script src="assets/scripts/functions.min.js?v=<?php echo time()?>" async></script>

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
            <div class="message_box no_disp">
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
<script src="assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/assets/scripts/password.min.js?v=<?php echo time()?>" async></script>
</body>
</html>

<?php $connect->close() ?>