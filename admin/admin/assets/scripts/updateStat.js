$("form").submit(function(e){
    e.preventDefault();

    result = formSubmit($(this), $("form[name=update_new_user] button[name=submit]"), false);

    if(result == true){
        $("#pMessage").html("Update Successful. Preparing dashboard...");

        //refresh in 3seconds
        setTimeout(function(){
            $("#pMessage").html("Welcome " + $("#new_username").val());
        },3000);

        setTimeout(function(){
            location.href = location.href;
        },4000);
    }else{
        html = $("#pMessage").html();
        message = "";

        if(result == "wrong-email-fullname"){
           message = "Email or fullname provided is wrong. Please check and try again";
        }else if(result == "same-username"){
            message = "You cannot use the same username";
        }else if(result == "same-password"){
            message = "You cannot use the same password";
        }else if(result == "update-error"){
            message = "Your data could not be updated. Please try again later or contact the admin";
        }else if(result == "cannot login"){
            message = "Update was unsuccessful. Contact Admin for help";
        }else if(result == "short-password"){
            message = "Password is too short. Please enter at least an 8 character long password";
        }else if(result == "username-exist"){
            message = "Username already exist. Please select a new username";
        }else{
            message = result;
        }

        $("#pMessage").html(message).addClass("danger");

        setTimeout(function(){
            $("#pMessage").html(html).removeClass("danger");
        },5000);
    }
})