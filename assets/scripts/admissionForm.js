//variables to use
var admission_button_tab_index = 1;     //This will be used to track the tab index in the admission form

//getting the host url
var url = location.protocol + '//' + location.host + "/" + location.pathname.split("/")[1];

$(document).ready(function(){
    //select the first tab of the form by default
    $("span.tab_button:nth-child(1)").click();

    //default the admission form
    $("form[name=admissionForm] button[name=modal_cancel]").click();
})

//function to be used to check if the next or submit button should be
//shown in the admission form
/**
 * This function is used to check if the next or submit button should be shown in the admission form
 * @returns {void} The function returns nothing
 */
 function admissionFormButtonChange()
 {
     //get the total number of levels we have
     total = $(".tabs").children("span.tab_button").length;
     var i = 1;
     
     for(i; i <= total; i++){
         //search for the currently selected tab
         if($(".tabs span.tab_button:nth-child(" + i + ")").hasClass("active") && i < total){
             $("button[name=submit_admission] span").html("Next");
             $("button[name=submit_admission]").prop("disabled", checkForm(i));
 
             admission_button_tab_index = i;
 
             break;
         }else if($(".tabs span.tab_button:nth-child(" + i + ")").hasClass("active") && i == total){
             $("button[name=submit_admission] span").html("Save");
             $("button[name=submit_admission]").prop("disabled", checkForm(i));
 
             break;
         }
     }
 
     return;
 }

//fill the number of days depending on the selected month
$("select[name=ad_month]").change(function(){
    //set the first key to be select
    html1 = "<option value=''>Select Your Day of Birth</option>";
    $("select[name=ad_day]").html(html1);

    if($("select[name=ad_year]").val() != "") {
        //now determine which month was picked
        days = 31;
        this_month = parseInt($(this).val());

        //calculate a leap year for the sake of february
        lunar_year = parseInt($("select[name=ad_year]").val() % 4);

        if(lunar_year == 0){
            lunar_year = parseInt($("select[name=ad_year]").val() % 400);
        }
        
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
    }
})

$("select[name=ad_year]").change(function(){
    if($("select[name=ad_month]").val() != ""){
        $("select[name=ad_month]").change();
    }else{
        //set the first key to be select
        html1 = "<option value=''>Select Your Day of Birth</option>";
        $("select[name=ad_day]").html(html1);
    }
})

//canceling results
$("button[name=modal_cancel]").click(function(){
    admission_button_tab_index = 1;
    $(this).parents(".form_modal_box").addClass("no_disp");

    if($(this).parents(".form_modal_box").prop("id") == "payment_form"){
        //enable all fields
        $("form[name=paymentForm] input").prop("disabled", false);
        $("form[name=paymentForm]")[0].reset();

        //keep the amount section disabled
        $("#pay_amount").prop("disabled", true);
    }else if($(this).parents(".form_modal_box").prop("id") == "admission"){
        //enable the index input field
        $("#ad_index").prop("disabled", false);

        //display the continue
        $("form[name=admissionForm] label[for=continue]").removeClass("no_disp");

        //hide the submit button
        $("form[name=admissionForm] label[for=submit_admission]").addClass("no_disp");

        //submit button should turn to button and should be disabled
        $("button[name=submit_admission]").prop("disabled", true).prop("type","button");

        //click first tab
        $(".tabs span.tab_button").addClass("no_disp").removeClass("incomplete");
        $(".tabs span.tab_button:first-child").removeClass("no_disp").click();

        //reset accepts
        resetAccepts();

        //reset form
        $("form[name=admissionForm]")[0].reset();

        $("#interest").val('');
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

    /*if($(this).prop("type") == "submit"){
        $("form[name=admissionForm]").submit();
    }*/
})

//marking the button enabled when user agrees that data is correct
$("label[for=agree]").click(function(){
    check = $("label[for=agree] input[name=agree]").prop("checked");

    if(check == false){
        //change the type of the admission button
        $("button[name=submit_admission]").prop("type","button");
    }else if(check == true){
        //change the type of the admission button
        $("button[name=submit_admission]").prop("type","submit");
    }
    $("button[name=submit_admission]").prop("disabled", !check);
})

//dynamically help the user when typing the phone number
$("input.tel").keyup(function(){
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

//remove spaces from phone number
$("input.tel").blur(function(){
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

$("#view2 .checkbox").click(function(){
    admissionFormButtonChange();
})

//retrieve health specification information
$("select[name=ad_health]").change(function () {
    if($(this).val() == "yes"){
        $("label[for=health_specify]").removeClass("no_disp");
    }else{
        fadeOutElement($("label[for=health_specify]"));
    }
})

//show the payment form when the payment / enrol button is clicked
$("#payment_button, .enrol_button").click(function(){
    $("#payment_form").removeClass("no_disp").addClass("flex");

    //when it is client side, by default, the index number section should display first
    //for verification before continuation
    // $("form[name=admissionForm] fieldset").addClass("no_disp");
    $("form[name=admissionForm] #enrol_field").removeClass("no_disp");

    //pass selected school name to form
    $("form[name=paymentForm] #school_choice").html($("#school_admission_case #school_select option:selected").html());

    //on client side, do not display the other tabs when the user has not given the index number
    // $(".tabs span.tab_button").addClass("no_disp");
    $(".tabs span.tab_button.active").removeClass("no_disp");
})

//enabling the payment button when needed info is available
$("#payment_form form input").keyup(function(){
    if(($("#pay_fullname").val() != "" && $("#pay_phone").val() != "") || $("#pay_reference").val() != ""){
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

    dataString = "submit=getStudentIndex&index_number=" + index_number + "&school_name=" + 
    $("#school_admission_case #school_select option:selected").html() + "&school_id=" + school_id;

    //parse through ajax and check input
    $.ajax({
        url: $("form[name=admissionForm]").attr("action"),
        type: "GET",
        data: dataString,
        dataType: "json",
        cache: true,
        async: false,
        beforeSend: function(){
            $("#view1 .para_message").html("Checking index number, please wait...");
        },
        success: function(json){
            $("#view1 .para_message").html("Parts with * means they are required fields");
            data = JSON.parse(JSON.stringify(json));

            if(data["status"] == "success"){
                //successful results should fill the form with needed data
                $("form[name=admissionForm] #ad_aggregate").val(data["aggregate"]);
                $("form[name=admissionForm] #ad_course").val(data["programme"]);
                $("form[name=admissionForm] #ad_lname").val(data["Lastname"]);
                $("form[name=admissionForm] #ad_oname").val(data["Othernames"]);
                $("form[name=admissionForm] #ad_gender").val(data["Gender"]);
                $("form[name=admissionForm] #ad_jhs").val(data["jhsAttended"]);

                //fill results with values
                $("#res_ad_aggregate").html(data["aggregate"]);
                $("#res_ad_course").html(data["programme"]);
                $("#res_ad_lname").html(data["Lastname"]);
                $("#res_ad_oname").html(data["Othernames"]);
                $("#res_ad_gender").html(data["Gender"]);

                //candidate full name entry into swear box
                $("#fullCandidateName").html(data["Lastname"] + " " + data["Othernames"]);
                

                //remove this button and show the submit button
                $("form[name=admissionForm] label[for=continue]").addClass("no_disp");
                $("label[for=submit_admission]").removeClass("no_disp");

                //display the fields
                $("form[name=admissionForm] fieldset").removeClass("no_disp");

                //show all the elements in the enrol field
                $("form[name=admissionForm] #enrol_field label").removeClass("no_disp");

                //when the user has entered the index number
                //provide the school's name
                $("#shs_placed").val($("#school_admission_case #school_select option:selected").html());

                //update school chosen
                $("#res_shs_placed").html($("#shs_placed").val());
                
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
            }else if(data["status"] == "already-registered"){
                //display an error message
                $("#view1 .para_message").html("You are identified as having enrolled already. Please go to shsdesk.com/student to access your documents");

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
                },7000);
            }
        },
        error: function(r){
            $("#view1 .para_message").html(JSON.stringify(r));

            setTimeout(function(){
                $("#view1 .para_message").html("Parts with * means they are required fields");
            },5000);
        }
    })
})

//when the admission form cancel button is clicked, reset that form
$("form[name=admissionForm] button[name=modal_cancel]").click(function(){
    //display continue button and hide the submit button
    $("button[name=continue]").removeClass("no_disp");
    $("label[for=submit_admission]").addClass("no_disp");

    //hide the fields
    $("form[name=admissionForm] fieldset").addClass("no_disp");

    //display only enrol fieldset
    $("form[name=admissionForm] fieldset#enrol_field").removeClass("no_disp");

    //hide all the elements in the enrol field
    $("form[name=admissionForm] #enrol_field label").addClass("no_disp");

    //show only index number field
    $("form[name=admissionForm] #enrol_field label[for=ad_index]").removeClass("no_disp");

    //enable the index input field
    $("#ad_index").prop("disabled", false);
})

//submit the admission form
$("form[name=admissionForm]").submit(function(e){
    e.preventDefault();
    
    //data for disabled fields
    dataString = "shs_placed=" + $("#shs_placed").val() + "&ad_index=" + $("#ad_index").val() + "&ad_aggregate=" + $("#ad_aggregate").val() + "&ad_course=" + $("#ad_course").val() + 
    "&ad_jhs=" + $("#ad_jhs").val() + "&ad_transaction_id=" + $("#ad_transaction_id").val();

    //strip form data into array form and attain total data
    form_data = $(this).serializeArray();
    split_lenght = form_data.length;

    //variable to hold all user data
    formData = "";

    //loop and fill form data
    counter = 0;
    while(counter < split_lenght){
        //grab each array data
        new_data = form_data[counter];

        key = new_data["name"];
        value = new_data["value"];

        //append to form data
        if(formData != ""){
            formData += "&" + key + "=" + value;
        }else{
            formData = key + "=" + value;
        }

        //move to next data
        counter++;
    }

    //append submit if not found
    if(!$(this).serialize().includes("&submit=")){
        formData += "&submit=" + $("form[name=admissionForm] button[name=submit_admission]").val() + "_ajax";
    }

    //append disabled form fields
    formData += "&" + dataString;

    //parse data into database
    $.ajax({
        url: $(this).attr("action"),
        data: formData,
        method: "post",
        dataType: "text",
        cache: false,
        async: false,
        beforeSend: function(){
            message = loadDisplay({size: "small"});
            type = "load";
            time = 0;

            messageBoxTimeout("admissionForm", message, type, time);
        },
        success: function(text){
            time = 5;

            if(text == "success" || text.includes("success")){
                message = "Enrolment was successful. Please wait as your letters are prepared";
                type = "success";

                messageBoxTimeout("admissionForm",message, type, 3);

                //click on cancel and open blank tab in 3 seconds
                setTimeout(function(){
                    $("#handle_pdf")[0].click();
                    $("button[name=modal_cancel]").click();
                },3000);
            }else{
                response = text;
                type = "error";

                if(response == "no-transaction-id"){
                    message = "No transaction id was provided";
                    i = 1;
                }else if(response == "no-index-number"){
                    message = "No index number was provided";
                    i = 1;
                }else if(response == "no-enrolment-code"){
                    message = "No enrolment code was provided";
                    i = 1;
                }else if(response == "wrong-school"){
                    message = "Error with school name. Please report to admin";
                    i = 1;
                }else if(response == "no-aggeregate-score"){
                    message = "Please enter your aggregate score";
                    i = 1;
                }else if(response == "no-course-set"){
                    message = "Please provide the name of your selected program";
                    i = 1;
                }else if(response == "no-lname-set"){
                    message = "Please provide your last name";
                    i = 1;
                }else if(response == "no-oname-set"){
                    message = "Please provide your other name(s)";
                    i = 1;
                }else if(response == "no-gender-set"){
                    message = "Please select your gender";
                    i = 1;
                }else if(response == "no-jhs-name-set"){
                    message = "Please provide the name of your JHS";
                    i = 1;
                }else if(response == "no-jhs-town-set"){
                    message = "Please provide the town in which JHS is found";
                    i = 1;
                }else if(response == "no-jhs-district-set"){
                    message = "Please provide the district of your JHS";
                    i = 1;
                }else if(response == "no-year-set"){
                    message = "Please select your year of birth";
                    i = 1;
                }else if(response == "no-month-set"){
                    message = "Please select your month of birth";
                    i = 1;
                }else if(response == "no-day-set"){
                    message = "Please select your day of birth";
                    i = 1;
                }else if(response == "no-birth-place-set"){
                    message = "Please enter your place of birth";
                    i = 1;
                }else if(response == "no-father-name"){
                    message = "Please provide your father's name";
                    i = 2;
                }else if(response == "no-f-occupation-set"){
                    message = "Please provide your father's occupation";
                    i = 2;
                }else if(response == "no-mother-name"){
                    message = "Please provide your mother's name";
                    i = 2;
                }else if(response == "no-m-occupation-set"){
                    message = "Please provide your mother's occupation";
                    i = 2;
                }else if(response == "no-elder-name"){
                    message = "Please provide your father, mother or a guardian";
                    i = 2;
                }else if(response == "no-residence-set"){
                    message = "Please provide your residential address";
                    i = 2;
                }else if(response == "no-p-p-set"){
                    message = "Please select your primary phone number";
                    i = 2;
                }else if(response == "p-p-short"){
                    message = "Primary phone number is shorter than normal";
                    i = 2;
                }else if(response == "p-p-long"){
                    message = "Primary phone number is longer than normal";
                    i = 2;
                }else if(response == "s-p-short"){
                    message = "Secondary phone number is shorter than normal";
                    i = 2;
                }else if(response == "s-p-long"){
                    message = "Secondary phone number is longer than normal";
                    i = 2;
                }else if(response == "no-interest-set"){
                    message = "Please select at least one interest";
                    i = 2;
                }else if(response == "no-witness-set"){
                    message = "Please provide a witness' name";
                    i = 2;
                }else if(response == "no-witness-phone"){
                    message = "Please provide your witness' phone number";
                    i = 2;
                }else if(response == "witness-phone-long"){
                    message = "Witness' phone number is longer than normal";
                    i = 2;
                }else if(response == "witness-phone-short"){
                    message = "Witness' phone number is shorter than normal";
                    i = 2;
                }else{
                    message = response;
                    time = 0;
                }
                
                //click respective head
                $(".tabs span.tab_button:nth-child(" + i + ")").click();

                messageBoxTimeout("admissionForm", message, type, time);
            }
        },
        error: function(){
            message = "Please check your internet connection and try again";
            type = "error";

            messageBoxTimeout(form_element.prop("name"), message, type);
        }
    })
})