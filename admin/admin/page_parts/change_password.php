<?php include_once("auth.php");

    //set nav_point session
    $_SESSION["nav_point"] = "password";
?>
<form action="<?php echo $url?>/admin/submit.php" method="post" name="changePasswordForm">
    <div class="body">
        <div class="message_box no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <label for="prev_password">
            <span class="label_image">
                <img src="<?php echo $url?>/assets/images/icons/lock.png" alt="password_icon">
            </span>
            <input type="password" name="prev_password" id="prev_password" class="text_input password" placeholder="Your Current Password" autocomplete="off">
        </label>
        <label for="new_password">
            <span class="label_image">
                <img src="<?php echo $url?>/assets/images/icons/lock.png" alt="password_icon">
            </span>
            <input type="password" name="new_password" id="new_password" class="text_input password" placeholder="Your New Password" autocomplete="off">
        </label>
        <label for="new_password2">
            <span class="label_image">
                <img src="<?php echo $url?>/assets/images/icons/lock.png" alt="password_icon">
            </span>
            <input type="password" name="new_password2" id="new_password2" class="text_input password" placeholder="Re-enter New Password" autocomplete="off">
        </label>
        <label for="show_password" class="checkbox">
            <input type="checkbox" name="show_password" id="show_password">
            <span class="label_title">Show Password</span>
        </label>
        <label for="submit" class="btn_label">
            <button type="submit" name="submit" value="change_password" class="sp-lg">Change Password</button>
        </label>
    </div>
    <div class="foot">
        <p>
            @<?= date("Y") ?> shsdesk.com
        </p>
    </div>
</form>

<script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/assets/scripts/password.min.js?v=<?php echo time()?>" async></script>
<?php close_connections() ?>