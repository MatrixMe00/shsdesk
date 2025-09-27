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
            <p>Please fill in your details to make payment.</p>
            <p class="txt-al-center">
                <span class="item-event info" style="color:red">
                    Right after paying, please wait for your admission form to appear.
                </span>
            </p>
            <div class="joint">
                <label for="pay_fullname">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/user.png" alt="fullname_logo">
                    </span>
                    <input type="text" name="pay_fullname" id="pay_fullname" class="text_input" placeholder="Enter your full name" 
                    title="Type your full name (at least 6 letters)" autocomplete="off" pattern="[a-zA-Z\s]{6,}">
                </label>
                <label for="pay_email">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/mail-outline.svg" alt="email">
                    </span>
                    <input type="email" name="pay_email" id="pay_email" class="text_input" placeholder="Enter your email (required)"
                    title="Your payment receipt and details will be sent to this email" autocomplete="off">
                </label>
            </div>
            
            <div class="joint">
                <label for="pay_phone">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="phone_logo">
                    </span>
                    <input type="tel" name="pay_phone" id="pay_phone" class="text_input" placeholder="Enter your phone number (10 digits)" 
                    title="Provide the mobile money number you will use for payment" autocomplete="off" maxlength="10" minlength="10">
                </label>
                <div class="label flex-column">
                    <label for="pay_amount">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/cash-outline.svg" alt="money">
                        </span>
                        <input type="text" name="pay_amount" id="pay_amount" 
                        title="This is the amount to be deducted from your mobile money wallet" value="GHC <?= $system_usage_price ?>" data-init="<?= $system_usage_price ?>" readonly>
                    </label>
                    <span class="item-event info" style="color:red">
                        Note: An extra GHC <?= number_format($system_up_gross, 2) ?> will be added as bank charges.  
                        <br><strong>Total you will pay: GHC <?= number_format($system_usage_price + $system_up_gross, 2) ?></strong>
                    </span>
                </div>
            </div>
        </section>
        <br>

        <!-- break -->
        <div style="display:flex; align-items:center; text-align:center; margin: 20px 0;">
        <hr style="flex:1; border:0; border-top:1px solid #ccc;">
        <span style="padding: 0 10px; font-weight:bold;">OR</span>
        <hr style="flex:1; border:0; border-top:1px solid #ccc;">
        </div>

        <section id="trans">
            <p>If you already paid, enter your transaction reference number below.</p>
            <span class="item-event info">
                Use the official transaction reference (TXXXXXXXXXXXXXXX) sent by SMS or on the receipt in your email.
                Do not use your mobile money transaction ID.
            </span>
            <label for="pay_reference">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/information-outline.svg" alt="reference">
                </span>
                <input type="text" name="pay_reference" id="pay_reference" class="text_input" placeholder="Example: T123456789012345"
                title="Enter the transaction reference sent to you by SMS or Email" autocomplete="off">
            </label>
        </section>
    </div>
    <div class="flex flex-eq flex-wrap gap-sm">
        <label for="submit" class="btn_label smt-unset sp-unset">
            <button type="button" name="submit" value="login" class="img_btn w-fluid primary sp-md" id="paymentFormButton" disabled>
                <img src="<?php echo $url?>/assets/images/icons/lock.png" alt="lock">
                <span>Pay Now</span>
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
