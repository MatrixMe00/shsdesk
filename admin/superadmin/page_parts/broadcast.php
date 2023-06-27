<?php include_once("session.php") ?>
<section class="txt-al-c">
    <p>This is a module for sending broadcast messages. This module has the ability to send texts to persons outside of the system</p>
</section>

<section class="groups">
    <label for="specific" class= "flex-column sm-auto wmax-sm sm-auto-lr sm-lg-t gap-sm">
        <span class="label_title">Specify Individual person(s)</span>
        <input type="text" name="specific" id="specific" placeholder="Provide the contact numbers here. Separate with comma. Eg. 0123456789,0321654987">
    </label>
</section>

<section id="sms_message" class="txt-al-c no_disp">
    <label for="">
        <textarea name="text_message" maxlength="160" id="text_message" class="txt-fl" placeholder="SMS Text goes here. You have a maximum of 160 characters..."></textarea>
    </label>
    <span class="item-event info" id="message_count"></span>
    <div class="btn w-full w-fluid-child p-med wmax-sm sm-auto">
        <button class="primary send" name="submit">Send</button>
    </div>
</section>

<script>
    $("input[name=specific]").keyup(function(){
        if($(this).val() === ""){
            $("#sms_message").addClass("no_disp")
        }else{
            $("#sms_message").removeClass("no_disp")
        }
    })

    $("#text_message").keyup(function(e){
        $("#message_count").html($(this).val().length + " of " + $(this).attr("maxlength"))
    })

    $("button.send").click(function(){
        const message = $("#text_message").val()
        const recipients = $("input#specific").val()

        $.ajax({
            url: "./superadmin/submit.php",
            data: {
                submit: "send_sms_admin", message: message, recipients: recipients
            },
            method: "POST",
            timeout: 30000,
            beforeSend: function(){
                $("button.send").html("Sending...")
            },
            success: function(response){
                $("button.send").html("Send")
                color = "danger";
                time = 6;

                if(response == "sms sent"){
                    color = "success";
                    time = 2.5;

                    $("#text_message, input[name=specific]").val("")
                    $("#sms_message").addClass("no_disp")
                }

                alert_box(response, color, time)
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
</script>