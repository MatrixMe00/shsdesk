<?php
    include_once("auth.php");

    //set nav_point session
    $_SESSION["nav_point"] = "sms";
?>

<section>
    <div class="wmax-md sm-auto form">
        <div class="head txt-al-c">
            <h3>School USSD</h3>
        </div>
        <div class="joint flex-align-end">
            <label for="sms_id" class="flex-column w-full" style="flex:1">
                <span class="label_title">School USSD</span>
                <input type="text" name="sms_id" id="sms_id" placeholder="Enter your SMS USSD here [maximum length is 11]..." maxlength="11" value="<?php 
                    $ussd = fetchData1("sms_id, status","school_ussds","school_id=$user_school_id");
                    echo $ussd == "empty" ? "Not Set" : $ussd["sms_id"];
                ?>" readonly>
            </label>
            <label for="" class="btn gap-sm w-full" style="flex:0">
                <button type="submit" name="submit" value="change_sms_id" class="primary w-full change_sms" data-change="0">Change</button>
                <button type="button" name="reset" class="pink w-full reset_sms no_disp">Reset</button>
            </label>
        </div>
    </div>
</section>

<section class="txt-al-c txt-fs p-sm-tp p-med-lr">
    <h3>Notice:</h3>
    <?php if(is_array($ussd) && $ussd["status"] == "approve") : ?>
    <p>Please be informed that this page is for sending SMS to persons registered into this system. When specifying individuals, do well to provide their index numbers if they are students, or the teacher id if they are teachers</p>
    <p>Specific individuals should have their index numbers (if they are students) or teacher ids (if they are teachers) specified with comma and space separations for multiple values. Eg. 0000000001, 0000000002</p>
    <?php elseif(is_array($ussd)): ?>
    <p>
        <?= $ussd["status"] == "pending" ? "Your USSD is on pending and would be reveiwed by the admins soon." : "Your USSD was rejected by the admin. This means that your USSD is not unique and an organization or service provider is already using it. Please provide a new USSD unique for this system"; ?>
    </p>
    <?php else: ?>
    <p>You have not provided a USSD yet. Please provide one to have access to the SMS section of the system.</p>
    <?php endif; ?>
</section>

<?php if(is_array($ussd) && $ussd["status"] == "approve"): ?>
<section>
    <p class="txt-al-c sm-med-b">Select Group of Recipients</p>
    <div class="flex-all-center w-full flex-wrap btn gap-md">
        <button class="plain-r primary group" data-section-id="student">Students</button>
        <button class="plain-r primary group" data-section-id="teacher">Teachers</button>
        <button class="plain-r danger group reset no_disp">Reset Block</button>
    </div>
</section>

<section class="groups no_disp">
    <div id="student" class="group_content no_disp">
        <p class="txt-al-c sm-med-b">Select a class or group</p>
        <div class="flex-all-center w-full flex-wrap btn gap-sm">
            <button class="plain-r teal individual_item" data-id="1">Year 1</button>
            <button class="plain-r teal individual_item" data-id="2">Year 2</button>
            <button class="plain-r teal individual_item" data-id="3">Year 3</button>
            <button class="plain-r teal individual_item" data-id="all">All Students</button>
            <button class="plain-r teal specify">Specify</button>
        </div>
    </div>
    <div id="teacher" class="group_content no_disp">
        <p class="txt-al-c sm-med-b">Select Teacher Group</p>
        <div class="flex-all-center w-full flex-wrap btn gap-sm">
            <button class="plain-r teal individual_item" data-id="all">All Teachers</button>
            <button class="plain-r teal individual_item" data-id="male">Only Males</button>
            <button class="plain-r teal individual_item" data-id="female">Only Females</button>
            <button class="plain-r teal specify">Specify</button>
        </div>
    </div>
    <label for="specific" class="no_disp flex-column sm-auto wmax-sm sm-auto-lr sm-lg-t gap-sm">
        <span class="label_title">Specify Individual person(s)</span>
        <input type="text" name="specific" id="specific" placeholder="Type name of individual here. Separate with comma">
        <div id="individual_match" class="no_disp flex-wrap gap-md" style="max-height: 30vh; overflow: auto"></div>
    </label>
</section>

<section id="sms_message" class="no_disp txt-al-c">
    <label for="">
        <textarea name="text_message" maxlength="160" id="text_message" class="txt-fl" placeholder="SMS Text goes here. You have a maximum of 160 characters..."></textarea>
    </label>
    <span class="item-event info" id="message_count"></span>
    <div class="btn w-full w-fluid-child p-med wmax-sm sm-auto">
        <button class="primary send" name="submit">Send</button>
    </div>
</section>
<?php endif; ?>

<script src="<?= "$url/admin/admin/assets/scripts/messaging.js?v=".time() ?>"></script>
<?php close_connections() ?>