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
        }else if(response == "no-enrol-code"){
            message = "Please provide an enrolment code for this registered student";
        }else if(response == "enrol-code-short"){
            message = "Enrolment code should be a minimum of 6 characters";
        }else if(response == "boarding-status-not-set"){
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
        }else if(response == "no-house-id"){
            message = "Please select a house for the student";
        }else if(response == "no-track-id"){
            message = "Please provide student's track id";
        }else if(response == "no-program-id"){
            message = "Please select the class of the student";
        }else if(response == "no-student-year"){
            message = "Please select the year of the student";
        }else if(response == "no-index"){
            message = "Index number could not be verified in the request";
        }else if(response == "no-school-id"){
            message = "Your school could not be validated in the request. Please refresh the page and try again";
        }else if(response == "student-index-short"){
            message = "Your index number has less than 6 characters";
        }else if(response == "student-index-long"){
            message = "Your index number has more than 13 characters";
        }else{
            // message = "An unknown error has occured. Please try again later";
            message = response;
        }
    }

    messageBoxTimeout(form_name, message, message_type, time);
})

$("form[name=adminUpdateStudent]").submit(function(event){
    event.preventDefault();

    //prepare defaults for message box
    form_name="adminUpdateStudent";
    time = 5;

    // Enable all disabled elements before submission
    var disabledElements = $("form[name=adminUpdateStudent] :disabled");
    disabledElements.prop("disabled", false);

    // Perform the submission
    response = formSubmit($(this), $("form[name=adminUpdateStudent] button[name=submit]"));

    // Disable the elements again after submission
    disabledElements.prop("disabled", true);
    
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

$(".table_section .head button").click(function(){
    $(".table_section .head button.primary").removeClass("primary").addClass("light");
    $(this).toggleClass("light primary");
})

$(".navs span").click(function(){
    prev = false, next = false, last = 0;

    parent = $(this).parents(".year").attr("id");

    if($(this).hasClass("prev")){
        prev = true;
    }else{
        next = true;
    }
    
    rows = parseInt($("#" + parent + " table tbody tr").length);
    breakPoint = parseInt($(this).attr("data-break-point")), current = 0;

    if(rows < breakPoint){
        breakPoint = rows;
    }
    
    $("#" + parent + " table tbody tr").addClass("no_disp");
    
    if(prev){           //the previous button
        if(parseInt($("#" + parent + " table tfoot tr td .current").html()) > 0){
            if($("#" + parent + " table tfoot tr td .current").html() != ""){
                current = breakPoint * parseInt($("#" + parent + " table tfoot tr td .current").html());
                last = current - breakPoint;
            }else{
                last = breakPoint;
            }

            for(var i = (last-breakPoint) + 1; i <= last; i++){
                $("#" + parent + " table tbody tr:nth-child(" + i + ")").removeClass("no_disp");
            }

            $("#" + parent + " table tfoot tr td .current").html(parseInt(last/breakPoint));
            if(rows % breakPoint == 0){
                $("#" + parent + " table tfoot tr td .last").html(parseInt(rows/breakPoint));
            }else{
                $("#" + parent + " table tfoot tr td .last").html(parseInt(rows/breakPoint) + 1);
            }
        }    
    }else if(next){     //the next button
        if(parseInt($("#" + parent + " table tfoot tr td .current").html()) < parseInt($("#" + parent + " table tfoot tr td .last").html())){
            if($("#" + parent + " table tfoot tr td .current").html() != ""){
                current = breakPoint * parseInt($("#" + parent + " table tfoot tr td .current").html());
                last = current + breakPoint;
            }else{
                last = breakPoint;
            }

            for(var i = current+1; i < last+1; i++){
                $("#" + parent + " table tbody tr:nth-child(" + i + ")").removeClass("no_disp");
            }

            $("#" + parent + " table tfoot tr td .current").html(parseInt(last/breakPoint));
            if(rows % breakPoint == 0){
                $("#" + parent + " table tfoot tr td .last").html(parseInt(rows/breakPoint));
            }else{
                $("#" + parent + " table tfoot tr td .last").html(parseInt(rows/breakPoint) + 1);
            }
        }            
    }

    if(parseInt(last/breakPoint) - 1 == 0){
        $("#" + parent + " .navs span.prev").addClass("no_disp");
    }else{
        $("#" + parent + " .navs span.prev").removeClass("no_disp");
    }

    if(parseInt(last/breakPoint) == parseInt($("#" + parent + " table tfoot tr td .last").html())){
        $("#" + parent + " .navs span.next").addClass("no_disp");
    }else{
        $("#" + parent + " .navs span.next").removeClass("no_disp");
    }
})

$(".table_section .head .btn button").click(function(){
    //retrieve year number
    year = $(this).attr("data-year");

    //pass parent id to search
    $("label[for=search_mul_table] input[name=search]").attr("data-parent-value", $(this).attr("data-year"))

    //display all navigations
    $("#year" + year + " .navs span").removeClass("no_disp");

    //hide all year sections and display selected year
    $(".table_section .body .year").addClass("no_disp");
    $(".table_section .body #year" + year).removeClass("no_disp");

    //retrieve number of rows and breakpoints
    rows = parseInt($("#year" + year + " table tbody tr").length);
    breakPoint = parseInt($(this).attr("data-break-point")), current = 0;

    if(rows < breakPoint){
        breakPoint = rows;
    }

    //hide all rows of current display
    $("#year" + year + " table tbody tr").addClass("no_disp");

    //display necessary spots
    for(var i = 1; i <= breakPoint; i++){
        $("#year" + year + " table tbody tr:nth-child(" + i + ")").removeClass("no_disp");
    }

    //update page numbers
    $("#year" + year + " table tfoot tr td .current").html("1");
    if(parseInt(rows/breakPoint) > 1 && rows%breakPoint == 0){
        $("#year" + year + " table tfoot tr td .last").html(parseInt(rows/breakPoint));
    }else if(parseInt(rows/breakPoint) > 1 || rows%breakPoint > 0){
        $("#year" + year + " table tfoot tr td .last").html(parseInt(rows/breakPoint) + 1);
    }else{
        $("#year" + year + " table tfoot tr td .last").html("1");
        $("#year" + year + " .navs span.next").addClass("no_disp");
    }

    $("#year" + year + " .navs span.prev").addClass("no_disp");
})