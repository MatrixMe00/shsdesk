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
        }

        $("#pMessage").html(message);

        setTimeout(function(){
            $("#pMessage").html(html);
        },5000);
    }
})