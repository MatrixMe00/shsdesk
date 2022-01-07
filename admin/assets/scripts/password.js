$("form[name=changePasswordForm]").submit(function(){
        response = formSubmit($(this), $("form[name=changePasswordForm] button[name=submit]"));

        time = 5;
        if(response == true){
            message = "Password changed successfully";
            type="success";
        }else{
            type = "error";

            if(response == "no-current-password"){
                message = "Current password field is empty";
            }else if(response == "no-new-password"){
                message = "New password field is empty";
            }else if(response == "no-new-password2"){
                message = "Re-enter password field is empty";
            }else if(response == "not-different"){
                message = "You cannot use the same password";
            }else if(response == "new-not-same"){
                message = "New passwords do not match"
            }else if(response == "password-mismatch"){
                message = "Current password entered is invalid";
            }else{
                message = response;
                time = 0;
            }
        }

        messageBoxTimeout("changePasswordForm", message, type, time);
    })