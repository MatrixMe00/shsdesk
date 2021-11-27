//variables to use
var admission_button_tab_index = 1;     //This will be used to track the tab index in the admission form

//getting the host url
var url = location.protocol + '//' + location.host + "/" + location.pathname.split("/")[1];

$(document).ready(function(){
    //fill the years select box
    $("select[name=ad_year]").html(function(){
        i = 1990;

        for(i; i <= 2005; i++){
            new_option = "<option value='" + i + "'>" + i + "</option>";

            $(this).append(new_option);
        }
    })

    //select the first tab of the form by default
    $("span.tab_button:nth-child(1)").click();
})

//fill the number of days depending on the selected month
$("select[name=ad_month]").change(function(){
    //set the first key to be select
    html1 = "<option value=''>Select Your Day of Birth</option>";
    $("select[name=ad_day]").html(html1);

    //now determine which month was picked
    days = 31;
    this_month = parseInt($(this).val());

    //calculate a leap year for the sake of february
    lunar_year = parseInt($("select[name=ad_year]").val() % 4);
    
    if(this_month == 4 || this_month == 6 || this_month == 9 || this_month == 11){
        days = 30;
    }else if(this_month == 2 && lunar_year == 0){
        days = 29;
    }else if(this_month == 2 && lunar_year != 0){
        days = 28;
    }

    //generate the days for the month selected
    for(var i = 1; i <= days; i++){
        new_option = "<option value='" + i + "'>" + i + "</option>";

        $("select[name=ad_day]").append(new_option);
    }
})

//canceling results
$("button[name=modal_cancel]").click(function(){
    admission_button_tab_index = 1;
    $(this).parents(".form_modal_box").addClass("no_disp");

    if($(this).parents(".form_modal_box").prop("id") == "payment_form"){
        //enable all fields
        $("form[name=paymentForm] input").prop("disabled", false);

        //keep the amount section disabled
        $("#pay_amount").prop("disabled", true);
    }else if($(this).parents(".form_modal_box").prop("id") == "admission"){
        //enable the index input field
        $("#ad_index").prop("disabled", false);
    }
})

//what to do with the submit admission button
$("button[name=submit_admission]").click(function() {
    //get the total number of levels we have
    total = $(".tabs").children("span.tab_button").length;

    if(admission_button_tab_index < total){
        admission_button_tab_index += 1;
    }

    $(".tabs span.tab_button:nth-child(" + admission_button_tab_index + ")").click();
})

//marking the button enabled when user agrees that data is correct
$("label[for=agree]").click(function(){
    if($("label[for=agree] input[name=agree]").prop("checked") == false){
        $("button[name=submit_admission]").prop("disabled", true);

        //change the type of the admission button
        $("button[name=submit_admission]").prop("type","button");

        //show the other steps
        // $(".tab_button").removeClass("no_disp");
    }else{
        $("button[name=submit_admission]").prop("disabled",false);

        //change the type of the admission button
        $("button[name=submit_admission]").prop("type","submit");

        //change the button name to submit
        $("button[name=submit_admission] span").html("Submit");

        //hide the other steps
        // $(".tab_button").addClass("no_disp");
        $(".tab_botton.active").removeClass("no_disp");
    }
})

//dynamically help the user when typing the phone number
$("input[type=tel]").keyup(function(){
    val = $(this).val();

    //give a default maximum length
    maxlength = 10;

    //check if the user is starting from 0 or +
    if(val[0] == "+" && val.includes(" ")){
        maxlength = 16;
    }else if(val[0] == "+" && !val.includes(" ")){
        maxlength = 13;
    }else if(val[0] == "0" && val.includes(" ")){
        maxlength = 12;
    }else if(val[0] == "0" && !val.includes(" ")){
        maxlength = 10;
    }else{
        $(this).val('');
    }

    //change the maximum length
    $(this).prop("maxlength",maxlength);
})

//remove spaces from phone numbery
$("input[type=tel]").blur(function(){
    i = 0;
    value = $(this).val();

    if(value.includes(" ")){
        //split value
        value = value.split(" ");
        length = 0;
        new_value = "";

        //join separate values
        while(length < value.length){
            new_value += value[length];
            length++;
        }

        //pass new value into value
        value = new_value;
    }

    //convert value into +233
    if(value[0] == "0"){
        new_val = "+233";

        //grab from second value
        i = 1;

        while(i <= 9){
            new_val += value[i];
            i++;
        }

        value = new_val;
    }

    $(this).val(value);
})

//form tab menu
$(".tabs span.tab_button").click(function(){
    //shade the selected tab button
    $(".tabs span.tab_button").removeClass("active");
    $(this).addClass("active");

    //hide all views
    $(".form_views > div").addClass("no_disp");

    //display its associated form view
    $("#" + $(this).attr("data-views")).removeClass("no_disp");

    admissionFormButtonChange();
})

//automatically check for enabling the button when changes are made from select and input fields
$("#admission select").change(function(){
    admissionFormButtonChange();
})

$("#admission input").blur(function(){
    admissionFormButtonChange();
})

$(".checkbox").click(function(){
    admissionFormButtonChange();
})

//retrieve health specification information
$("select[name=ad_health]").change(function () {
    if($(this).val() == "yes"){
        $("label[for=health_specify]").removeClass("no_disp");
    }else{
        $("label[for=health_specify]").addClass("no_disp");
    }
})

//show the payment form when the payment / enrol button is clicked
$("#payment_button, .enrol_button").click(function(){
    $("#payment_form").removeClass("no_disp").addClass("flex");

    //when it is client side, by default, the index number section should display first
    //for verification before continuation
    $("form[name=admissionForm] fieldset").addClass("no_disp");
    $("form[name=admissionForm] #enrol_field").removeClass("no_disp");

    //on client side, do not display the other tabs when the user has not given the index number
    // $(".tabs span.tab_button").addClass("no_disp");
    $(".tabs span.tab_button.active").removeClass("no_disp");
})

//enabling the payment button when needed info is available
$("#payment_form form input").keyup(function(){
    if(($("#pay_fullname").val() != "" && $("#pay_phone").val() != "" && $("#pay_email").val() != "") || $("#pay_reference").val() != ""){
        $("#payment_form form button[name=submit]").prop("disabled",false);
        $("#payment_form form label.btn_label img").prop("src",url + "/assets/images/icons/lock-open-outline.svg");
    }else{
        $("#payment_form form button[name=submit]").prop("disabled",true);
        $("#payment_form form label.btn_label img").prop("src",url + "/assets/images/icons/lock.png");
    }
})

//interests
$("label[for=ad_interest]").click(function(){
    val = $(this).children("input[type=checkbox]").val();
    val_int = $("input[name=interest]").val();

    if($(this).children("input[type=checkbox]").prop("checked") == false){
        $(this).children("input[type=checkbox]").prop("checked",true);
        if(val_int == ""){
            val_int = val;
        }else{
            val_int += ", " + val;
        }
    }else{
        $(this).children("input[type=checkbox]").prop("checked",false);
        if(val_int.includes(",")){
            //if value is not at the end
            temp_val = val + ", ";

            //if value is at the end
            temp_val2 = ", " + val;

            if(val_int.replace(temp_val2)){
                val_int = val_int.replace(temp_val2,"");
            }else{
                val_int = val_int.replace(temp_val1,"");
            }            
        }else{
            val_int = "";
        }
    }

    $("input[name=interest]").val(val_int);
    $("#interest_value").html(val_int);
})

//check if the user entered a valid enrolment code
$("#ad_index").keyup(function(){
    index_val = $(this).val();

    if(index_val.length >= 4){
        $("button[name=continue]").prop("disabled", false);
    }else{
        $("button[name=continue]").prop("disabled", true);
    }
})

$("button[name=continue]").click(function(){
    //take index number
    index_number = $("#ad_index").val();
    school_id = $("#student #school_select").val();

    dataString = "submit=getStudentIndex&index_number=" + index_number + "&school_name=" + $("#school_admission_case #school_select option:selected").html();

    //parse through ajax and check input
    $.ajax({
        url: $("form[name=admissionForm]").attr("action"),
        type: "GET",
        data: dataString,
        dataType: "json",
        cache: true,
        async: false,
        success: function(json){
            data = JSON.parse(JSON.stringify(json));

            if(data["status"] == "success"){
                //successful results should fill the form with needed data

                //display form information
                $("form[name=admissionForm] button[name=modal_cancel]").addClass("no_disp");
                $("label[for=submit_admission]").removeClass("no_disp");

                //display the fields
                $("form[name=admissionForm] fieldset").removeClass("no_disp");

                //show all the elements in the enrol field
                $("form[name=admissionForm] #enrol_field label").removeClass("no_disp");

                //when the user has entered the index number
                //provide the school's name
                $("#shs_placed").val($("#school_admission_case #school_select option:selected").html());

                //disable the index input field
                $("#ad_index").prop("disabled", true);
            }else if(data["status"] == "wrong-school-select"){
                //display an error message
                $("#view1 .para_message").html("Incorrect school chosen. Select your right school to continue and enter your transaction ID to continue");

                //disable these controls for the time being so that user can take a look at the error
                $("form[name=admissionForm] button[name=continue]").prop('disabled', true);
                $('#ad_enrol').prop('disabled', true);
                $('button[name=modal_cancel]').prop('disabled', true);

                setTimeout(function(){
                    $("#view1 .para_message").html("Parts with * means they are required fields");

                    $("form[name=admissionForm] button[name=continue]").prop('disabled', false);
                    $('#ad_enrol').prop('disabled', false);
                    $('button[name=modal_cancel]').prop('disabled', false);

                    $("form button[name=modal_cancel]").click();
                },5000);
            }else if(data["status"] == "wrong-index"){
                //display an error message
                $("#view1 .para_message").html("Incorrect or invalid index number provided. Please check and try again");

                //disable these controls for the time being so that user can take a look at the error
                $("form[name=admissionForm] button[name=continue]").prop('disabled', true);
                $('#ad_enrol').prop('disabled', true);
                $('button[name=modal_cancel]').prop('disabled', true);

                setTimeout(function(){
                    $("form[name=admissionForm] button[name=continue]").prop('disabled', false);
                    $('#ad_enrol').prop('disabled', false);
                    $('button[name=modal_cancel]').prop('disabled', false);
                },3000);

                setTimeout(function(){
                    $("#view1 .para_message").html("Parts with * means they are required fields");
                },5000);
            }
        }
    })
    /*if($("#ad_index").val() == "01234"){
        //remove this button and show the submit button
        $(this).addClass("no_disp");
        $("label[for=submit_admission]").removeClass("no_disp");

        //display the fields
        $("form[name=admissionForm] fieldset").removeClass("no_disp");

        //show all the elements in the enrol field
        $("form[name=admissionForm] #enrol_field label").removeClass("no_disp");

        //when the user has entered the index number
        //provide the school's name
        $("#shs_placed").val($("#school_admission_case #school_select option:selected").html());

        //disable the index input field
        $("#ad_index").prop("disabled", true);

        //use ajax call to get the details of the student
        $.ajax({
            
        })
    }else{
        //disable these controls for the time being so that user can take a look at the error
        $(this).prop('disabled', true);
        $('#ad_enrol').prop('disabled', true);
        $('button[name=modal_cancel]').prop('disabled', true);

        init = $("#view1 .para_message").html();
        error_message = "Index number provided is invalid";
        $("#view1 .para_message").html(error_message);

        setTimeout(function(){
            $("#view1 .para_message").html(init);

            //enable the freezed controls
            $('#ad_enrol').prop('disabled', false);
            $('button[name=modal_cancel]').prop('disabled', false);
        },5000);
    }*/
})

//when the admission form cancel button is clicked, reset that form
$("form[name=admissionForm] button[name=modal_cancel]").click(function(){
    //display continue button and hide the submit button
    $("button[name=continue]").removeClass("no_disp");
    $("label[for=submit_admission]").addClass("no_disp");

    //hide the fields
    $("form[name=admissionForm] fieldset").addClass("no_disp");

    //hide all the elements in the enrol field
    $("form[name=admissionForm] #enrol_field label").addClass("no_disp");

    //enable the index input field
    $("#ad_index").prop("disabled", false);

    //show view1
    $("#view1 fieldset:first-child").removeClass("no_disp");

    //hide all labels in view 1
    $("#view1 fieldset:first-child label").addClass("no_disp");

    //reveal only index number portion
    $("#view1 fieldset:first-child label[for=ad_index]").removeClass("no_disp");
})

//submit the admission form
$("form[name=admissionForm]").submit(function(e){
    e.preventDefault();

    //change the date format to the standard format
    date = $("#ad_year").val() + "-" + $("#ad_month").val() + "-" + $("#ad_day").val();
    $("#ad_birtdate").val(date);

    //getting the data being parsed
    dataString = $(this).serialize() + "&submit=" + $("button[name=submit_admission]").val();

    //ajax call
    $.ajax({
        url: url + "/submit.php",
        data: dataString,
    })
})