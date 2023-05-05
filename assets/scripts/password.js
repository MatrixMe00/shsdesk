//check for valid email
$("input#email").blur(function() {
    if($(this).val().length <= 5) {
        messageBoxTimeout("passwordForm","Email field is incomplete!", "error", 5);

        if(!$("#password_change").hasClass("no_disp")){
            $("#password_change").addClass("no_disp");
        }
    }else{
        $.ajax({
            url: $("form").attr("action"),
            data: "email=" + $(this).val() + "&submit=user_check",
            type: "POST",
            dataType: "html",
            cache: false,
            timeout: 15000,
            beforeSend: function(){
                messageBoxTimeout("passwordForm",loadDisplay({
                    span1: "gray", 
                    span2: "gray", 
                    span3: "gray", 
                    span4: "gray",
                    size: "vsmall"
                }), "load", 0);
            },
            success: function(data){
                if(data.includes("success")){
                    $("#password_change").removeClass("no_disp");

                    //parse the user id
                    user_id = data.split("+");

                    $("input[name=user_id]").val(user_id[1]);

                    messageBoxTimeout("passwordForm","Email was found", "success", 5);
                }else{
                    messageBoxTimeout("passwordForm","Email is invalid", "error", 7);
                    $("#password_change").addClass("no_disp");
                }
            },
            complete: function(){
                //hide the message box
                $(".message_box").removeClass("load").addClass("no_disp");
            },
            error: function(e, textStatus){
                let message = ""
                if(textStatus == "timeout"){
                    message = "Connection was timed out due to a slow network. Please try again later"
                }else{
                    message = "Error communicating with server"
                }
                messageBoxTimeout("passwordForm", message, "error", 5);
            }
        });
    }

    if($("#password_change").hasClass("no_disp") && ($(this).val().length > 0 && $(this).val().length <= 2)){
        messageBoxTimeout("passwordForm","Error getting account information! Check and try again later", "error", 5);
    }
})

//disable the email field when the password field is focused
$("input#password, input#password2").focus(function(){
    $("input#email").prop("disabled", true);
})

$("input#password2").keyup(function(){
    if($("#password2").val() == $("#password").val()){
        $("button[name=submit]").prop("disabled", false);
    }else{
        $("button[name=submit]").prop("disabled", true);
    }

    //display a message when its value is of same lenght with original and passwords match
    if($(this).val().length == $("input#password").val().length){
        if($(this).val() == $("input#password").val()){
            messageBoxTimeout("passwordForm", "Passwords Match", "success", 5);
        }else{
            messageBoxTimeout("passwordForm", "Passwords Mismatch", "error", 5);
        }
    }
})

$("form").submit(function(e){
    e.preventDefault();

    $.ajax({
        url: $(this).prop("action"),
        data: $(this).serialize() + "&submit=" + $("button[name=submit]").val(),
        type: $(this).prop("method"),
        dataType: "html",
        timeout: 15000,
        beforeSend: function(){
            messageBoxTimeout("passwordForm",loadDisplay({
                    size: "vsmall"
                }), "load", 0);
        },
        success: function(data){
            if(data === "success"){
                messageBoxTimeout("passwordForm", "Password has been changed successfully", "success");

                //disable all inputs
                $("input#password, input#password2, button[name=submit]").prop("disabled", true);

                //return to previous page
                setTimeout(function(){
                    location.href = "admin/";
                }, 1500);
            }else{
                messageBoxTimeout("passwordForm", "Password could not be changed. Try again later", "error");

                //enable inputs and button
                $("input#password, input#password2, button[name=submit]").prop("disabled", false);
            }
        },
        error: (xhr, textStatus) => {
            let message = ""
            if(textStatus == "timeout"){
                message = "Connection was timed out due to a slow network. Please try again later"
            }

            alert_box(message, "danger", 10);
        }
    })
})

$("button[name=cancel]").click(function(){
    //check if user should be sent to admin login page or its a reset it is making
    return_user = false;
    if($("#password_change").hasClass("no_disp")){
        return_user = true;
    }

    $("#password_change").addClass("no_disp");
    $("button[name=submit]").prop("disabled", true);
    $("#email").prop("disabled", false);

    if(return_user){
        location.href = "admin/";
    }
})