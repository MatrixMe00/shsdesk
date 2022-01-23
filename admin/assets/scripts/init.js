$("form").submit(function(e){
    e.preventDefault();
    message = "";
    message_type = "";
    time = 0;

    //check if there are user errors
    if($("input#username").val() == ""){
        message = "Please enter your username";
        message_type = "error";
        time = 5;

        //focus into this input element
        $("input#username").focus();

        messageBoxTimeout("loginForm",message, message_type, time);

        return false;
    }else if($("input#password").val() == ""){
        message = "Please enter your password";
        message_type = "error";
        time = 5;

        //focus into this input element
        $("input#password").focus();

        messageBoxTimeout("loginForm",message, message_type, time);

        return false;
    }else{
        //data of form
        dataString = $(this).serialize() + "&submit=" + $("button[name=submit]").val() + "_ajax";

        reload = "";
        //perform the validation and ajax request
        $.ajax({
            url: $(this).attr("action"),
            data: dataString,
            cache: false,
            dataType: "html",
            type: "POST",
            async: false,
            beforeSend: function(){
                message = loadDisplay();
                message_type = "load";

                messageBoxTimeout("loginForm",message, message_type, time);
            },
            success: function(html){
                if(html == "login_success"){
                    message_type = "success";
                    message = "Login Successful";
                    time = 0;
                    location.href = location.href;
                }else if(html === "password_error"){
                    message_type = "error";
                    message = "Wrong Password was entered";
                    time = 5;
                }else if(html === "username_error"){
                    message_type = "error";
                    message = "Username entered is invalid or could not be found";
                    time = 10;
                }else if(html == "not-active"){
                    message = "Your account has been disabled. Contact admin for more info";
                    time = 0;
                    message_type = "error";
                }else{
                    message_type = "error";
                    message = html;
                }
                messageBoxTimeout("loginForm",message, message_type, time);                      
            },
            error: function(){
                message = "Cannot connect to server. Please try again";
                message_type = "error";
            }
        })
    }
})