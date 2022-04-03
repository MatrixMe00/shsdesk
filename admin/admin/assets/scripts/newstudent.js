var data_entry = false;

$("form[name=adminAddStudent] button[name=cancel]").click(function(){
    //check what to do using html message
    if($(this).html() == "Cancel"){
        $(this).parents('#modal').addClass('no_disp');
    }else{
        $(this).html("Cancel");
    }

    //refresh page if there was some data sent to database
    if(data_entry){
        // location.reload();
        $("#lhs .item.active").click();
    }
})

$("form[name=adminAddStudent] input").keyup(function(){
    form_inputs = $("form[name=adminAddStudent] input").length;
    
    var $empty = $("form[name=adminAddStudent] input").filter(function(){
        return $.trim(this.value) === "";
    })

    if($empty.length == form_inputs){
        $("form[name=adminAddStudent] button[name=cancel]").html("Cancel");
    }else{
        $("form[name=adminAddStudent] button[name=cancel]").html("Reset");
    }
})

$("form[name=adminAddStudent]").submit(function(event){
    event.preventDefault();

    //prepare defaults for message box
    form_name="adminAddStudent";
    time = 5;

    //send data
    response = formSubmit($(this), $("form[name=adminAddStudent] button[name=submit]"));
    
    if(response == true){
        message = "Data for " + $("form[name=adminAddStudent] #lname").val() + " was added successfully";
        message_type = "success";
        time = 3;

        //disable buttons
        $("form[name=adminAddStudent] .foot button").prop("disable", true);

        //flag that data entry is true
        data_entry = true;

        //enable buttons and reset the form
        setTimeout(function(){
            $("form[name=adminAddStudent] .foot button").prop("disable", false);
            $("form[name=adminAddStudent] button[name=cancel]").click();
        }, 3000);
        
    }else{
        message_type = "error";

        if(response == "index-number-empty"){
            message = "No index number was provided";
        }else if(response == "lastname-empty"){
            message = "No lastname was provided";
        }else if(response == "no-other-name"){
            message = "Othername field is empty";
        }else if(response == "gender-not-set"){
            message = "Please select a gender";
        }else if(response == "boardin-status-not-set"){
            message = "Please select a boarding status";
        }else if(response == "no-student-program-set"){
            message = "You have not specified student's program";
        }else if(response == "no-aggregate-set"){
            message = "No aggregate score has been provided";
        }else if(response == "aggregate-wrong"){
            message = "Aggregate score is invalid";
        }else if(response == "no-jhs-set"){
            message = "You have not provided student's JHS school";
        }else if(response == "no-dob"){
            message = "Please provide student's date of birth";
        }else if(response == "no-track-id"){
            message = "Please provide student's track id";
        }else if(response == "data-exist"){
            message = "Index number already exists in database. Please enter another one";
        }else{
            message = "An unknown error has occured. Please try again later";
        }
    }

    messageBoxTimeout(form_name, message, message_type, time);
})

$("form[name=adminUpdateStudent]").submit(function(event){
    event.preventDefault();

    //prepare defaults for message box
    form_name="adminUpdateStudent";
    time = 5;

    //send data
    response = formSubmit($(this), $("form[name=adminUpdateStudent] button[name=submit]"));
    
    if(response == true){
        message = "Data for " + $("form[name=adminUpdateStudent] #lname").val() + " was updated successfully";
        message_type = "success";
        time = 3;

        //disable buttons
        $("form[name=adminUpdateStudent] .foot button").prop("disable", true);

        //flag that data entry is true
        data_entry = true;

        //enable buttons and reset the form
        setTimeout(function(){
            $("form[name=adminUpdateStudent] .foot button").prop("disable", false);
            // location.reload();
            // $("#lhs .item.active").click();
        }, 3000);
        
    }else{
        message_type = "error";

        if(response == "index-number-empty"){
            message = "No index number was provided";
        }else if(response == "lastname-empty"){
            message = "No lastname was provided";
        }else if(response == "no-other-name"){
            message = "Othername field is empty";
        }else if(response == "gender-not-set"){
            message = "Please select a gender";
        }else if(response == "boardin-status-not-set"){
            message = "Please select a boarding status";
        }else if(response == "no-student-program-set"){
            message = "You have not specified student's program";
        }else if(response == "no-aggregate-set"){
            message = "No aggregate score has been provided";
        }else if(response == "aggregate-wrong"){
            message = "Aggregate score is invalid";
        }else if(response == "no-jhs-set"){
            message = "You have not provided student's JHS school";
        }else if(response == "no-dob"){
            message = "Please provide student's date of birth";
        }else if(response == "no-track-id"){
            message = "Please provide student's track id";
        }else if(response == "data-exist"){
            message = "Index number already exists in database. Please enter another one";
        }else{
            message = response;
        }
    }

    messageBoxTimeout(form_name, message, message_type, 0);
})