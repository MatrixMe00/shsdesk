<?php 
    include_once("../includes/session.php");
    $view_url = str_replace("/","",$_SERVER["REQUEST_URI"]);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $view_url == "forgot-password" ? "Forgot my Password" : "Forgot my Account" ?></title>
    
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/general.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/head_foot.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/admin_form.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/loader.min.css?v=<?php echo time()?>">

    <script src="<?php echo $url?>/assets/scripts/jquery/compressed_jquery.js?v=<?php echo time()?>"></script>
</head>
<body class="sm-auto flex-all-center light" style="min-height: 100vh">
    <form action="./submit.php" name="reset_password_form" class="white sp-lg-lr sp-xlg-tp w-full wmax-sm sm-auto" method="post">
        <div class="txt-al-c sm-xlg-b m-lg-b">
            <h3 class="txt-fl2">Password Reset</h3>
            <p>Provide your email and new password.</p>
        </div>
        <div class="message_box no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="body">
            <label for="email" class="flex flex-column gap-sm">
                <span class="label_title">Email</span>
                <input type="email" name="email" id="email" placeholder="Your Email">
            </label>
            <label for="password" class="flex flex-column gap-sm">
                <span class="label_title">New Password</span>
                <input type="password" name="password" id="password" placeholder="New Password">
            </label>
            <label for="password_c" class="flex flex-column gap-sm">
                <span class="label_title">Confirm Password</span>
                <input type="password" name="password_c" id="password_c" placeholder="Confirm Password">
            </label>
            <label for="show_password" class="checkbox">
                <input type="checkbox" id="show_password">
                <span class="label_title">Show Password</span>
            </label>
        </div>
        <div class="btn wmax-sm w-full w-fluid-child p-lg">
            <button class="primary" type="submit" name="submit" value="change_password">Reset</button>
        </div>
    </form>

    <script src="<?= "$url/assets/scripts/functions.min.js" ?>"></script>
    <script src="<?= "$url/assets/scripts/general.min.js" ?>"></script>
    <script>
        $("#show_password").change(function(){
            if($(this).prop("checked") == true){
                $("input[name=password], input[name=password_c]").prop("type","text");
            }else{
                $("input[name=password], input[name=password_c]").prop("type","password");
            }
        })

        $("form").submit(async function(e){
            e.preventDefault();
            const response = await formSubmit($(this), $(this).find("button[name=submit]"));

            if(response == true){
                messageBoxTimeout("reset_password_form", "Password reset was successful", "success")
                setTimeout(function(){
                    location.href = "./";
                },2000)
            }else{
                messageBoxTimeout("reset_password_form", response, "error");
            }
        })
    </script>
</body>
</html>