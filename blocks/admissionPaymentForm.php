<form action="<?php echo $url?>/submit.php" method="post" class="fixed" name="paymentForm">
                    <div class="head">
                        <h2>Payment Method</h2>
                    </div>
                    <div class="body">
                        <div id="message_box" class="success no_disp">
                            <span class="message">Here is a test message</span>
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
                                    <input type="email" name="pay_email" id="pay_email" class="text_input" placeholder="Your Email"
                                    title="Provide an email so that secured information about your transaction can be passed there" autocomplete="off" required>
                                </label>
                            </div>
                            
                            <div class="joint">
                                <label for="pay_phone">
                                    <span class="label_image">
                                        <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="username_logo">
                                    </span>
                                    <input type="tel" name="pay_phone" id="pay_phone" class="text_input" placeholder="Your Phone Number" 
                                    title="Please provide your mobile money number" autocomplete="off" maxlength="10">
                                </label>
                                <label for="pay_amount">
                                    <span class="label_image">
                                        <img src="<?php echo $url?>/assets/images/icons/cash-outline.svg" alt="money">
                                    </span>
                                    <input type="text" name="pay_amount" id="pay_amount" 
                                    title="This is the amount you will be charged from your mobile money wallet" value="GHC 30" disabled>
                                </label>
                            </div>
                            <!--<label for="form_type">
                                <span class="label_image">
                                    <img src="<?php echo $url?>/assets/images/icons/receipt-outline.svg" alt="username_logo">
                                </span>
                                <select name="form_type" id="form_type">
                                    <option value="NULL">Select A Form</option>
                                </select>
                            </label>-->
                        </section>
                        <br>
                        <section id="trans">
                            <p>Already Paid? Then enter your transaction id</p>
                            <label for="pay_reference">
                                <span class="label_image">
                                    <img src="<?php echo $url?>/assets/images/icons/information-outline.svg" alt="">
                                </span>
                                <input type="text" name="pay_reference" id="pay_reference" class="text_input" placeholder="Enter your Reference ID" autocomplete="off">
                            </label>
                        </section>
                        
                    </div>
                    <div class="flex">
                        <label for="submit" class="btn">
                            <button type="button" name="submit" value="login" class="dbl_btn img_btn" id="paymentFormButton" onclick="$('form[name=admissionForm]').submit()" disabled>
                                <img src="<?php echo $url?>/assets/images/icons/lock.png" alt="lock">
                                <span>Continue</span>
                            </button>
                        </label>
                        <label for="modal_cancel" class="btn dbl_btn_label">
                            <button type="reset" name="modal_cancel" value="cancel">Cancel</button>
                        </label>
                    </div>
                <div class="foot">
                        <p>
                            @2021 shsdesk.com
                        </p>
                    </div>
                </form>