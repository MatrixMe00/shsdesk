<?php include_once("session.php"); $_SESSION["nav_point"] = "broadcast"; ?>
<section class="txt-al-c">
    <p>This is a module for sending broadcast messages. This module has the ability to send texts and emails to persons in and outside of the system</p>
</section>

<!-- buttons to select if sms or mail -->
<section class="sp-lg-tp" id="view_btns">
    <h3 class="txt-al-c sm-lg-b">Select Messaging Type</h3>
    <div class="btn w-full p-med gap-sm flex-all-center flex-wrap">
        <button data-section="sms" class="plain-r primary control_btn">SMS</button>
        <button data-section="mail" class="plain-r teal control_btn">E-Mail</button>
        <button data-section="broadcast" class="plain-r teal control_btn">Broadcast (SMS)</button>
    </div>
</section>

<div class="section-wrapper no_disp" id="sms">
    <section class="groups">
        <label for="specific" class= "flex-column sm-auto wmax-sm sm-auto-lr sm-lg-t gap-sm">
            <span class="label_title">Specify Individual person(s)</span>
            <input type="text" name="specific" id="specific"
                class="recipients" placeholder="Provide the contact numbers here. Separate with comma. Eg. 0123456789,0321654987">
        </label>
    </section>

    <section id="sms_message" class="txt-al-c no_disp">
        <label for="">
            <textarea name="text_message" maxlength="160" id="text_message" class="txt-fl text-message" placeholder="SMS Text goes here. You have a maximum of 160 characters..."></textarea>
        </label>
        <span class="item-event info" id="message_count"></span>
        <div class="btn w-full w-fluid-child p-med wmax-sm sm-auto">
            <button class="primary send" name="submit" value="send_sms_admin" data-mode="sms">Send SMS</button>
        </div>
    </section>
</div>

<section class="form no_disp not-form section-wrapper" id="mail">
    <div class="lt-shade wmax-md sp-lg">
        <label for="email-recipients" class= "flex-column sm-auto wmax-sm sm-auto-lr sm-lg-t gap-sm">
            <span class="label_title">Specify Individual person(s)</span>
            <input type="text" name="email-recipients" id="email-recipients" class="recipients" placeholder="Provide the emails here. Separate with comma. Eg. email1@example.com, email2@example.com">
        </label>
        <label for="email-subject" class= "flex-column sm-auto wmax-sm sm-auto-lr sm-lg-t gap-sm">
            <span class="label_title">Subject of the email</span>
            <input type="text" name="email-subject" id="email-subject" class="recipients" placeholder="Provide the emails here. Separate with comma. Eg. email1@example.com, email2@example.com">
        </label>

        <div class="white sm-xlg-b sp-lg-lr sp-med-tp">
            <p class="sm-xlg-lr">Send message as: </p>
            <div class="joint">
                <label for="sendas_shsdesk" class="radio">
                    <input type="radio" name="sendas" id="sendas_shsdesk" value="SHSDesk">
                    <span class="label_title">SHSDesk</span>
                </label>
                <label for="sendas_successinn" class="radio">
                    <input type="radio" name="sendas" id="sendas_successinn" value="S&S Innovative Hub">
                    <span class="label_title">S&S Inn Hub</span>
                </label>
                <label for="sendas_successinn" class="radio">
                    <input type="radio" name="sendas" id="sendas_customer" value="Customer Care">
                    <span class="label_title">Customer Care</span>
                </label>
                <input type="hidden" name="sendas_val" value="" readonly>
            </div>
        </div>

        <!-- other notable options -->
        <label for="other_options" class="checkbox gap-sm wmax-md">
            <input type="checkbox" name="other_options" id="other_options">
            <span class="label_title">Extra Options [use this if the emails above are in the system]</span>
        </label>

        <!-- options -->
        <div id="options" class="no_disp white sp-lg txt-al-c">
            <p class="txt-fs">
                <span>Select a button to add a placeholder to your message.</span><br>
                <span>This placeholder would add specific details to an email address</span>
            </p>
            <div class="btn flex-all-center sp-lg-t w-full gap-sm flex-wrap">
                <button type="button" class="plain-r secondary border b-secondary" data-placeholder="email" title="Adds recipient's email">Email</button>
                <button type="button" class="plain-r secondary border b-secondary" data-placeholder="name" title="Adds recipient's fullname">Name</button>
                <button type="button" class="plain-r secondary border b-secondary" data-placeholder="username" title="Adds recipient's username">Username</button>
                <button type="button" class="plain-r secondary border b-secondary" data-placeholder="phone" title="Adds recipient's phone number">Phone</button>
                <button type="button" class="plain-r secondary border b-secondary" data-placeholder="school" title="Adds recipient's school name">School</button>
            </div>
        </div>

        <label for="email-message">
            <textarea name="email-message" maxlength="160" id="email-message" class="txt-fn sadmin_tinymce text-message" 
                placeholder="Your email template or message goes here..."></textarea>
        </label>
        <label for="attachments" class="file_label">
            <span class="label_title">Attachments (You can upload multiple files)</span>
            <div class="fore_file_display">
                <input type="file" name="attachments[]" id="attachments" accept=".pdf,.doc,.docx,.jpg,.png" multiple>
                <span class="plus">+</span>
                <span class="display_file_name">Choose or drag your files here</span>
            </div>
        </label>
        <span class="item-event info" id="email_count">Number of Recipients: 0</span>
        <div class="btn w-full w-fluid-child p-med wmax-sm sm-auto">
            <button class="primary send" name="submit" value="send_mail_admin" data-mode="mail">Send Mail</button>
        </div>
    </div>
</section>

<div class="section-wrapper no_disp" id="broadcast">
    <section>
        <p class="txt-al-c sp-med">The message below will be sent to <strong><?= fetchData("COUNT(id) AS total", "cssps_guardians", "last_messaged IS NULL AND is_valid = TRUE")["total"] ?></strong> contacts</p>
    </section>
    <section id="sms_message" class="txt-al-c">
        <label for="">
            <textarea name="broadcast_message" id="broadcast_message" class="txt-fl text-message sadmin_tinymce" placeholder="SMS Text goes here. You have a maximum of 160 characters...">
                <?= fetchData("value", "system_variables", "name = 'sms_broadcast'")["value"] ?? "" ?>
            </textarea>
        </label>
        <span class="item-event info" id="message_count"></span>
        <div class="btn w-full w-fluid-child p-med wmax-sm sm-auto">
            <button class="primary send" name="submit" value="send_sms_broadcast" data-mode="sms">Save Template</button>
        </div>
    </section>
</div>

<script src="<?= "$url/admin/superadmin/assets/scripts/broadcast.js?v=".time() ?>"></script>

<!--TinyMCE scripts-->
<script src="<?php echo $url?>/admin/assets/scripts/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php echo $url?>/admin/assets/scripts/tinymce/tinymce.min.js"></script>
<script src="<?php echo $url?>/admin/assets/scripts/tinymce.min.js"></script>
<?php close_connections() ?>