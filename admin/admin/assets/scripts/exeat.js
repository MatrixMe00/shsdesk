$("form[name=exeatForm]").submit(function(e){
    e.preventDefault();

    response = formSubmit($(this), $("form[name=exeatForm] button[name=submit]"));

    if(response == true){
        message = "Exeat provided successfully";
        type = "success";
    }else{
        type = "error";

        if(response == "no-index"){
            message = "Index Number field is empty";
        }else if(response == "no-town"){
            message = "Please provied the exeat town";
        }else if(response == "no-exeat-date"){
            message = "Exeat date has not been provided";
        }else if(response == "no-return-date"){
            message = "Return date has not been provided";
        }else if(response == "date-conflict"){
            message = "Return date cannot be lower than exeat date";
        }else if(response == "no-exeat-type"){
            message = "Please specify the type of exeat";
        }else if(response == "no-reason"){
            message = "Please provide a reason for this exeat";
        }else if(response == "range-error"){
            message = "Reason should range between 3 and 80 characters";
        }else if(response == "not-student"){
            message = "Index number entered cannot be found. Please check and try again";
        }else if(response == "not-registered"){
            message = "Student has not been registered";
        }else{
            message = response;
        }
    }

    messageBoxTimeout("exeatForm",message, type);
})