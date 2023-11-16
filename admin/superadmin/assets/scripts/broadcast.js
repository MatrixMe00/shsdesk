// message type button
$("#view_btns button").click(function(){
    const section = $("#" + $(this).attr("data-section"));
    // hide currently showing section and display this button's section
    $(".section-wrapper:not(.no_disp)").addClass("no_disp");
    section.removeClass("no_disp");

    // modify visuals so user can see currently selected tab
    $(this).siblings("button").addClass("plain-r");
    $(this).removeClass("plain-r");
})

//show other options in email section
$("#other_options").change(function(){
    $("#options").toggleClass("no_disp");
})

// option buttons should add a placeholder to the message
$("#options button").click(function(){
    const placeholder = $(this).attr("data-placeholder");
    const email_message = $("#email-message");
    const edit_area = tinymce.get("email-message");
    let message = "";

    if(placeholder !== ""){
        message = "{" + placeholder + "}";
    }

    //pass the new message into the email message and focus on it
    // email_message.append(message);
    edit_area.setContent(edit_area.getContent() + message);
    edit_area.focus();
})

//start of sms edit section
$("input[name=specific]").keyup(function(){
    if($(this).val() === ""){
        $("#sms_message").addClass("no_disp")
    }else{
        $("#sms_message").removeClass("no_disp")
    }
})

//count the number of characters for the sms
$("#text_message").keyup(function(e){
    $("#message_count").html($(this).val().length + " of " + $(this).attr("maxlength"))
})

// count the number of recipients for the email
$("input#email-recipients").keyup(function(){
    const value = $(this).val().replace(" ", "");
    let recipients = value.split(",");
    let total = 0;

    if(value != ""){
        total = recipients.length;

        if(recipients[total-1] == ""){
            total -= 1;
        }
    }

    $("#email_count").html("Number of Recipients: " + total);
})

// determine the sender name
$("input[name=sendas]").change(function(){
    $("input[name=sendas_val]").val($(this).val());
})

$("button.send").click(function(){
    const section = $(this).parents(".section-wrapper");

    const message = section.find(".text-message").val();
    const recipients = section.find(".recipients").val();
    const submit = $(this).val();

    const initial_message = $(this).html();
    const mode = $(this).attr("data-mode");
    let data_string = {};

    if(mode == "sms"){
        data_string = {submit: submit, message: message, recipients: recipients};
    }else{
        const sendas = $("input[name=sendas_val]").val();
        const extra_options = Boolean($("input#other_options").prop("checked"));
        const email_subject = $("input#email-subject").val();

        data_string = {
            submit: submit, message: message, recipients: recipients,
            sendas: sendas, extra: extra_options, subject: email_subject
        }
    }

    $.ajax({
        url: "./superadmin/submit.php",
        data: data_string,
        method: "POST",
        timeout: 30000,
        beforeSend: function(){
            $("button.send").html("Sending...")
        },
        success: function(response){
            $("button.send").html(initial_message);
            color = "danger";
            time = 6;

            if(response == "sms sent"){
                color = "success";
                time = 2.5;

                $("#text_message, input[name=specific]").val("")
                $("#sms_message").addClass("no_disp")
            }else if(response == "email sent"){
                color = "success";
                time = 2.5;

                // $("#mail input[type=text], #mail input[type=hidden], #mail textarea").val("");
                // $("#mail input[type=radio], #mail input[type=checkbox]").prop("checked", false);
                // $("#options").addClass("no_disp");

                response = "Email have been sent successfully";
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