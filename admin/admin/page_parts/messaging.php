<?php
if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
    $user_school_id = $_REQUEST["school_id"];
    $user_details = getUserDetails($_REQUEST["user_id"]);
    
    include_once("../../includes/session.php");
}else{
    include_once("../../../includes/session.php");

    //set nav_point session
    $_SESSION["nav_point"] = "sms";
}
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
        <input type="text" name="specific" id="specific" placeholder="Separate multiple individual ids or index numbers with comma and a space. eg. tid0001, tid0002">
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

<script>
    $(document).ready(function(){
        $("#text_message").keyup()
        var specifc = false
        var group = ""
        var individuals = null
        const ussd = $("input#sms_id").val()

        $("button.change_sms").click(function(){
            const change = parseInt($(this).attr("data-change"))

            if(change){
                if($("input#sms_id").val() === ""){
                    alert_box("You cannot set an empty sms id", "danger")
                }else{
                    $.ajax({
                        url: "./admin/submit.php",
                        data: {submit: "add_update_sms", sms_id: $("input#sms_id").val()},
                        type: "POST",
                        timeout: 10000,
                        beforeSend: function(){
                            $("button.change_sms").html("Updating...").prop("disabled", true)
                        },
                        success: function(response){
                            if(response == "change-complete"){
                                alert_box("Your USSD was successfully modified", "success")
                                $("#lhs .item.active").click()
                            }else{
                                alert_box(response, "danger", 7)
                            }
                            $("button.change_sms").html("Update").prop("disabled", false)
                        },
                        error: function(xhr){
                            let message = ""

                            if(xhr.statusText == "timeout"){
                                message = "Error communciating with server due to slow network Please check your connection and try again"
                            }else{
                                message = xhr.responseText
                            }

                            alert_box(message, "danger", 6)
                            $("button.change_sms").prop("disabled",false).attr("data-change","0").html("Update")

                        }
                    })
                }
            }else{
                $("button.reset_sms").removeClass("no_disp")
                $("input#sms_id").prop("readonly",false).focus().select()
            }
            
        })

        $("input#sms_id").keyup(function(){
            if($(this).val() === ussd){
                $("button.change_sms").html("Change").attr("data-change","0")
            }else{
                $("button.change_sms").html("Update").attr("data-change","1")
                $("button.reset_sms").removeClass("no_disp")
            }
        })

        $("button.reset_sms").click(function(){
            $(this).addClass("no_disp")
            $("button.change_sms").html("Change").prop("disabled", false)
            $("input#sms_id").val(ussd).prop("readonly", true)
            $("button.change_sms").attr("data-change","0")
        })

        $("button.group:not(.reset)").click(function(){
            $("button.group, button.individual_item").addClass("plain-r")
            $(this).removeClass("plain-r")
            $("section.groups .group_content, section#sms_message").addClass("no_disp")
            $("section.groups, button.group.reset, #" + $(this).attr("data-section-id")).removeClass("no_disp")
            $("input#specific").val("")

            group = $(this).attr("data-section-id")
        })

        $("button.individual_item:not(.specify)").click(function(){
            $(this).siblings(".individual_item, .specify").addClass("plain-r")
            $(this).removeClass("plain-r")
            $("label[for=specific]").addClass("no_disp")

            individuals = $(this).attr("data-id")

            $("#sms_message").removeClass("no_disp")

            specific = false
        })

        $("button.specify").click(function(){
            specific = true
            $(this).siblings(".individual_item").addClass("plain-r")
            $(this).removeClass("plain-r")
            $("label[for=specific]").removeClass("no_disp")

            if($("input[name=specific]").val() === ""){
                $("#sms_message").addClass("no_disp")
            }else{
                $("#sms_message").removeClass("no_disp")
            }
        })

        $("input[name=specific]").blur(function(){
            if($(this).val() === ""){
                $("#sms_message").addClass("no_disp")
            }else{
                $("#sms_message").removeClass("no_disp")
                individuals = $(this).val()
            }
        })

        $("button.group.reset").click(function(){
            $(this).addClass("no_disp")
            $("button.group, button.individual_item").addClass("plain-r")
            $("section.groups, #sms_message").addClass("no_disp")

            specifc = false
            group = ""
            individuals = null
        })

        $("button.send").click(function(){
            const message = $("#text_message").val()

            $.ajax({
                url: "./admin/submit.php",
                data: {
                    submit: "send_sms", group: group, individuals: individuals, message: message
                },
                method: "GET",
                timeout: 8000,
                beforeSend: function(){
                    $("button.send").html("Sending...")
                },
                success: function(response){
                    $("button.send").html("Send")
                    alert_box(response)
                },
                error: function(response){
                    var message = ""
                    if(response.statusText == "timeout"){
                        message = "Connection was timed out due to a slow network. Please try again later"
                    }else{
                        message = JSON.stringify(response)
                    }

                    alert_box(message, "danger",6)
                    $("button.send").html("Send")
                }
            })
        })

        $("#text_message").keyup(function(e){
            $("#message_count").html($(this).val().length + " of " + $(this).attr("maxlength"))
        })
    })
</script>