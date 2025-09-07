<?php 
    include_once("../includes/session.php");
    
    //determine the page to show
    if(isset($_SESSION['user_login_id']) && $_SESSION['user_login_id'] > 0 && $staff_menu === true) :
        //determine which page to show per user role
        require("main.php");
    else:
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHSDesk - Admin Login Portal</title>

    <meta name="description" content="Manage your student records and details here. Log in into your portal to manage students, houses and admission details.">
    <meta name="keyboard" content="manage, student, record, detail, portal, house, admission, detail, admin, shs, desk, shsdesk, login">

    <!--Scripts-->
    <script src="<?php echo $url?>/assets/scripts/jquery/uncompressed_jquery.js" async></script>
    <script src="<?php echo $url?>/assets/scripts/functions.min.js?v=<?php echo time()?>" async></script>

    <!--Styles-->
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/admin/admin_form.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/general.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/loader.min.css?v=<?php echo time()?>">

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
            <div class="message_box no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <label for="username">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="username_logo">
                </span>
                <input type="text" name="username" id="username" class="text_input" placeholder="Your Username or School Email" autocomplete="off" value="<?= isset($_SESSION["user_login_id"]) ? $user_username : "" ?>">
            </label>
            <label for="password">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/lock.png" alt="username_logo">
                </span>
                <input type="password" name="password" id="password" class="text_input" placeholder="Your Password" autocomplete="off">
            </label>
            <label for="submit" class="btn_label">
                <button type="submit" name="submit" value="login" class="sp-lg">Login</button>
            </label>
        </div>
        <div class="foot">
            <p>
                @<?php echo date("Y")." ".$_SERVER["SERVER_NAME"] ?>
            </p>
        </div>
    </form>
    
    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
    <script src="<?php echo $url?>/assets/scripts/index.min.js?v=<?php echo time()?>" async></script>
    <script src="<?php echo $url?>/admin/assets/scripts/init.min.js?v=<?php echo time()?>" async></script>
</body>
</html>
<?php 
    endif;
    close_connections();
?>