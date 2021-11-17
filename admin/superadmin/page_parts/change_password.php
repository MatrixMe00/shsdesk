<form action="" method="post">
    <div class="body">
        <div id="message_box" class="no_disp">
            <span class="message">Here is a test message</span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <label for="prev_password">
            <span class="label_image">
                <img src="../../assets/images/icons/lock.png" alt="password_icon">
            </span>
            <input type="password" name="prev_password" id="prev_password" class="text_input password" placeholder="Your Old Password" autocomplete="off">
        </label>
        <label for="new_password">
            <span class="label_image">
                <img src="../../assets/images/icons/lock.png" alt="password_icon">
            </span>
            <input type="password" name="new_password" id="new_password" class="text_input password" placeholder="Your New Password" autocomplete="off">
        </label>
        <label for="new_password2">
            <span class="label_image">
                <img src="../../assets/images/icons/lock.png" alt="password_icon">
            </span>
            <input type="password" name="new_password2" id="new_password2" class="text_input password" placeholder="Re-enter New Password" autocomplete="off">
        </label>
        <label for="show_password" class="checkbox">
            <input type="checkbox" name="show_password" id="show_password">
            <span class="label_title">Show Password</span>
        </label>
        <label for="submit" class="btn_label">
            <button type="submit" name="submit" value="change_password">Change Password</button>
        </label>
    </div>
    <div class="foot">
        <p>
            @2021 shsdesk.com
        </p>
    </div>
</form>
<script src="../../assets/scripts/form/general.js"></script>