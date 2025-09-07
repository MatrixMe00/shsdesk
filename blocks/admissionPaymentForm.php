<form action="<?php echo $url?>/submit.php" method="post" class="fixed" name="paymentForm">
    <div class="head">
        <h2>Make Payment to <span id="school_choice"></span></h2>
        
    </div>
    <div class="body">
        <div class="message_box success no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <section id="new_payment">
            <p>Make your payment</p>
            <div class="joint">
                <label for="pay_fullname">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/user.png" alt="fullname_logo">
                    </span>
                    <input type="text" name="pay_fullname" id="pay_fullname" class="text_input" placeholder="Your Fullname" 
                    title="Please provide your full name" autocomplete="off" pattern="[a-zA-Z\s]{6,}">
                </label>
                <label for="pay_email">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/mail-outline.svg" alt="email">
                    </span>
                    <input type="email" name="pay_email" id="pay_email" class="text_input" placeholder="Your Email [required]"
                    title="Provide an email so that secured information about your transaction can be passed there" autocomplete="off"
                    pattern="^\+(?:[0-9] ?){6,14}[0-9]$">
                </label>
            </div>
            
            <div class="joint">
                <label for="pay_phone">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="username_logo">
                    </span>
                    <input type="tel" name="pay_phone" id="pay_phone" class="text_input" placeholder="Your Phone Number" 
                    title="Please provide your mobile money number" autocomplete="off" maxlength="10" minlength="10">
                </label>
                <div class="label flex-column">
                <label for="pay_amount">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/cash-outline.svg" alt="money">
                    </span>
                    <input type="text" name="pay_amount" id="pay_amount" 
                    title="This is the amount you will be charged from your mobile money wallet" value="GHC <?= $system_usage_price ?>" data-init="<?= $system_usage_price ?>" readonly>
                </label>
                    <span class="item-event info" style="color:red">NB: You will be charged an extra GHC <?= number_format($system_up_gross, 2) ?> as bank charge</span>
                </label>
            </div>
        </section>
        <br>
        <section id="trans">
            <p>Already Paid? Then enter your transaction id</p>
            <label for="pay_reference">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/information-outline.svg" alt="">
                </span>
                <input type="text" name="pay_reference" id="pay_reference" class="text_input" placeholder="Enter your Transaction Reference [Sent via SMS by SHSDesk - TXXXXXXXXXXXXXXX]" autocomplete="off">
            </label>
        </section>
    </div>
    <div class="flex flex-eq flex-wrap gap-sm">
        <label for="submit" class="btn_label smt-unset sp-unset">
            <button type="button" name="submit" value="login" class="img_btn w-fluid primary sp-md" id="paymentFormButton" disabled>
                <img src="<?php echo $url?>/assets/images/icons/lock.png" alt="lock">
                <span>Continue</span>
            </button>
        </label>
        <label for="modal_cancel" class="btn w-full sp-unset">
            <button type="reset" name="modal_cancel" value="cancel" class="sp-lg w-fluid secondary">Cancel</button>
        </label>
    </div>
    <div class="foot">
        <p>
            <?php echo "@".date("Y")." ".$_SERVER["SERVER_NAME"] ?>
        </p>
    </div>
</form>