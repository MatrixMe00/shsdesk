<?php include_once("../../../includes/session.php");?>
    <form action="" method="post" class="" name="user_account_form">
        <div class="head">
            <h2>My Account</h2>
        </div>
        <div class="body">
            <div class="joint">
                <label for="fullname">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/id card.png" alt="password_icon">
                    </span>
                    <input type="text" name="fullname" id="fullname" class="text_input" placeholder="Full Name" autocomplete="off"
                    title="Enter your full name">
                </label>
                <label for="email">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/mail-outline.svg" alt="password_icon">
                    </span>
                    <input type="email" name="email" id="email" class="text_input" placeholder="Email Address" autocomplete="off"
                    title="Provide your email address">
                </label>
                <label for="contact">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="password_icon">
                    </span>
                    <input type="tel" name="contact" id="contact" class="text_input" placeholder="Provide your contact" autocomplete="off">
                </label>
                <label for="username">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="password_icon">
                    </span>
                    <input type="text" name="username" id="username" class="text_input" placeholder="Enter your username" minlength="8" autocomplete="off">
                </label>
            </div>
            <div class="flex">
                <label for="submit" class="btn">
                    <button type="submit" name="submit" value="user_detail_update">Update</button>
                </label>
            </div>
        </div>
    </form>