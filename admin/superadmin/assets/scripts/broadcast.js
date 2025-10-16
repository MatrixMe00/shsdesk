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

$("button.send").click(async function() {
    const section = $(this).parents(".section-wrapper");

    const message = section.find(".text-message").val();
    const recipients = section.find(".recipients").val();
    const submit = $(this).val();
    const initial_message = $(this).html();
    const mode = $(this).attr("data-mode");

    // Create FormData for flexible data + file support
    const formData = new FormData();

    formData.append("submit", submit);
    formData.append("message", message);
    formData.append("recipients", recipients);

    if (mode === "sms") {
        formData.append("mode", "sms");
    } else {
        const sendas = $("input[name=sendas_val]").val();
        const extra_options = $("input#other_options").prop("checked") ? 1 : 0;
        const email_subject = $("input#email-subject").val();

        formData.append("sendas", sendas);
        formData.append("extra", extra_options);
        formData.append("subject", email_subject);
        formData.append("mode", "email");

        // 🔹 Add attachments if any
        const attachments = section.find("input[type=file]")[0]?.files;
        if (attachments && attachments.length > 0) {
            for (let i = 0; i < attachments.length; i++) {
                formData.append("attachments[]", attachments[i]);
            }
        }
    }

    // 🔸 Call your ajaxCall() utility
    const response = await ajaxCall({
        url: "./superadmin/submit.php",
        formData: formData,
        method: "POST",
        returnType: "text",
        sendRaw: true, // Important: needed for FormData
        timeout: 30000,
        beforeSend: function() {
            $("button.send").html("Sending...");
        }
    });

    // 🔹 Handle response
    $("button.send").html(initial_message);
    let color = "danger";
    let time = 6;
    let message_ = response;

    if (response === "sms sent") {
        color = "success";
        time = 2.5;
        $("#text_message, input[name=specific]").val("");
        $("#sms_message").addClass("no_disp");
    } else if (response === "email sent") {
        color = "success";
        time = 2.5;
        message_ = "Email has been sent successfully";
        // You could clear email form inputs here if needed
    }

    alert_box(message_, color, time);
});