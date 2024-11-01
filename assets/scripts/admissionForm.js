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
    const parent = $(this).parents(".form_modal_box"); 
    
    // hide current parent
    parent.addClass("no_disp");

    if(parent.prop("id") == "payment_form"){
        //enable all fields
        $("form[name=paymentForm] input").prop("disabled", false);
        $("form[name=paymentForm]")[0].reset();

        //reset the index number field
        $("button[name=student_cancel_operation]").click();

        //keep the amount section disabled
        $("#pay_amount").prop("readonly", true);

        //get and display intial payment value
        const payValue = $("form[name=paymentForm] input#pay_amount").attr("data-init")
        $("#pay_amount").val("GHC " + payValue)
    }else if(parent.prop("id") == "admission"){
        //enable the index input field
        $("#ad_index").prop("readonly", false);

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

        $("#admission input[required], #admission input.required").blur();
        $("#admission select[required], #admission select.required").change();

        //get intial payment value
        const payValue = $("form[name=paymentForm] input#pay_amount").attr("data-init")
        $("form[name=paymentForm] input#pay_amount").val("GHC " + payValue)
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

        //disable print button
        $("button[name=print_summary]").prop("disabled", true);
    }else if(check == true){
        //change the type of the admission button
        $("button[name=submit_admission]").prop("type","submit");

        //enable print button
        $("button[name=print_summary]").prop("disabled", false);
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
$("label.ad_interest input").change(function(){
    let active_value = $(this).prop("checked")
    let check_value = $(this).attr("data-value")
    const interest_item = $("input#interest")

    //get content of course ids
    let interests = interest_item.val().split(", ")

    if(interest_item.val() === ""){
        interests = [];
    }

    if(active_value){
        interests.push(check_value)
    }else{
        interests = $.grep(interests, function(value){
            return value !== check_value;
        })
    }

    //push new response into interests
    interest_item.val(interests.join(", "))
    $("#interest_value").html(interests.join(", "))
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

$("button[name=activate_student_index_number]").click(function(){
    $("#activate_index_number").removeClass("no_disp").addClass("flex-all-center fixed");
    $("#check_index_number").val($("#student_index_number").val());
})

const reset_activation_fields = () => {
    const index_field = $("#check_index_number");
    const submit_btn = $("#checkIndexButton");
    const datalist = $("datalist#student_names");

    datalist.empty();
    index_field.prop("readonly", true);
    submit_btn.prop("disabled", true);
    $("#check_status_span").addClass("not-visible").html("")
}

$("#check_school_id").change(async function(){
    const value = $(this).val();
    const option = $(this).find("option:selected");
    const check_program = $("select#check_programme");

    check_program.html("<option value\"\">Select a Programme</option>")
    reset_activation_fields();

    if(value != ""){
        if(option.attr("data-programs") == ""){
            let programs = await ajaxCall({
                url: "./submit.php",
                returnType: "json",
                method: "POST",
                beforeSend: function(){
                    $("#check_status_span").removeClass("not-visible").html("Fetching programs...");
                },
                formData: {submit: "get_cssps_programs", school_id: value}
            });
            
            programs = programs.data;            

            if(typeof programs === "object"){
                $("#check_status_span").addClass("not-visible");
                option.attr("data-programs", JSON.stringify(programs));
            }else{
                alert_box(programs, "error");
                $("#check_status_span").html(programs);

                setTimeout(function(){
                    $("#check_status_span").addClass("not-visible").html("")
                }, 10000);
                return;
            }
        }

        var programs_ = JSON.parse(option.attr("data-programs"));

        $.each(programs_, function (index, value) { 
            check_program.append("<option value=\"" + value + "\">" + value + "</option>\n");
        });
    }
})

$("#check_programme").change(async function(){
    const value = $(this).val();
    const school_id = $("#check_school_id").val();
    const datalist = $("datalist#student_names");
    const index_field = $("#students_index");
    const index_field_hidden = $("#students_index_hidden");
    const index_field_hashed = $("#students_index_hashed");

    datalist.empty();
    index_field.val("").prop("readonly", false); index_field_hidden.val(""); index_field_hashed.val("");

    if(value != ""){
        let students = await ajaxCall({
            url: "./submit.php",
            returnType: "json",
            method: "POST",
            beforeSend: function(){
                $("#check_status_span").removeClass("not-visible").html("Fetching students...");
            },
            formData: {submit: "get_cssps_students", school_id: school_id, programme: value}
        });

        students = students.data;            

        if(typeof students === "object"){
            index_field.attr("placeholder", "Search your name here");
            $("#check_status_span").addClass("not-visible");

            $.each(students, function(index, student){
                datalist.append("<option value=\"" + student.fullname + "\" data-hidden-index=\"" + student.hidden_index + "\" data-temp-index=\"" + student.indexNumber + "\" >" + student.fullname + "</option>")
            })
        }else{
            alert_box(students, "error");
            $("#check_status_span").html(students);
            index_field.val("").prop("readonly", true).attr("placeholder", "No assigned students"); index_field_hidden.val("");

            setTimeout(function(){
                $("#check_status_span").addClass("not-visible").html("")
            }, 10000);
            return;
        }
    }
})

$("#students_index").on("input", function(){
    const datalist = $("datalist#student_names");
    const value = $(this).val();
    const index_field_hashed = $("#students_index_hashed");
    const temp_index_number = $("#students_index_hidden");
    const index_field = $("#check_index_number");
    const submit_btn = $("#checkIndexButton");

    if(datalist.html() != "" && datalist.find("option[value='" + value + "']").length > 0){
        const hidden_index = datalist.find("option[value='" + value + "']").attr("data-hidden-index");
        const temp_index = datalist.find("option[value='" + value + "']").attr("data-temp-index");
        index_field_hashed.val(hidden_index);
        temp_index_number.val(temp_index);

        index_field.prop("readonly", false);
        submit_btn.prop("disabled", false);
    }else{
        index_field.prop("readonly", true);
        submit_btn.prop("disabled", true);
    }
})

$("form[name=indexNumberCheckerForm]").submit(async function(e){
    e.preventDefault();
    const me = $(this);
    const response = formSubmit(me, me.find("#checkIndexButton"), true);
    
    if(response === true){
        messageBoxTimeout("indexNumberCheckerForm", "Your index number has been activated", "success");
        setTimeout(function(){
            me.find("button[name=cancel]").click();
        }, 5000);
    }else{
        messageBoxTimeout("indexNumberCheckerForm", response, "error");
    }
})

$("form[name=indexNumberCheckerForm] button[name=modal_cancel]").click(function(){
    reset_activation_fields();
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
        timeout: 30000,
        beforeSend: function(){
            $("#view1 .para_message").html("Checking index number, please wait...");
            alert_box("Retrieving CSSPS data...", "secondary", 2);
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

                //mark provided fields as entered in
                $("form[name=admissionForm] #ad_lname, form[name=admissionForm] #ad_oname, " + 
                "form[name=admissionForm] #ad_jhs").blur();
                $("form[name=admissionForm] #ad_gender").change();

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
                $("#ad_index").prop("readonly", true);

                alert_box("CSSPS data has been filled", "success", 1.5)

                //display admission form
                $('#admission').removeClass('no_disp');

                // fetch class details
                ajaxCall({
                    url: $("form[name=admissionForm]").attr("action"),
                    formData: {submit: "get_programs", course_name: data["programme"], school_id: school_id},
                    method: "GET"
                }).then(resp => {
                    if(resp == "none"){
                        $("#class_fieldset").addClass("no_disp");
                        $("#program_display_val").text("N/A");
                        $("#program_select").html("");
                    }else{
                        $("#program_select").html("");

                        if(is_json_string(resp)){
                            resp = JSON.parse(resp);

                            if(resp.length > 1)
                                $("#program_select").html("<option value=\"\">Select preferred Class</option>")                        

                            for(i = 0; i < resp.length; i++){
                                const opt = resp[i];
                                const option = "<option value=\"" + opt.id + "\" data-courses=\"" + opt.courses + "\">" + opt.name + "</option>";
                                $("#program_select").append(option);
                            }
                        }
                    }
                })
            }else if(data["status"] == "wrong-school-select"){
                //display an error message
                $("#view1 .para_message").html("Incorrect school chosen. Select your right school to continue and enter your transaction ID to continue");

                //disable these controls for the time being so that user can take a look at the error
                $("form[name=admissionForm] button[name=continue]").prop('disabled', true);
                $('#ad_enrol').prop('readonly', true);
                $('button[name=modal_cancel]').prop('disabled', true);

                setTimeout(function(){
                    $("#view1 .para_message").html("Parts with * means they are required fields");

                    $("form[name=admissionForm] button[name=continue]").prop('disabled', false);
                    $('#ad_enrol').prop('readonly', false);
                    $('button[name=modal_cancel]').prop('disabled', false);

                    $("form button[name=modal_cancel]").click();
                },5000);
            }else if(data["status"] == "wrong-index"){
                //display an error message
                $("#view1 .para_message").html("Incorrect or invalid index number provided. Please check and try again");

                //disable these controls for the time being so that user can take a look at the error
                $("form[name=admissionForm] button[name=continue]").prop('disabled', true);
                $('#ad_enrol').prop('readonly', true);
                $('button[name=modal_cancel]').prop('disabled', true);

                setTimeout(function(){
                    $("form[name=admissionForm] button[name=continue]").prop('disabled', false);
                    $('#ad_enrol').prop('readonly', false);
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
                $('#ad_enrol').prop('readonly', true);
                $('button[name=modal_cancel]').prop('disabled', true);

                setTimeout(function(){
                    $("form[name=admissionForm] button[name=continue]").prop('disabled', false);
                    $('#ad_enrol').prop('readonly', false);
                    $('button[name=modal_cancel]').prop('disabled', false);
                },3000);

                setTimeout(function(){
                    $("#view1 .para_message").html("Parts with * means they are required fields");
                },7000);
            }else{
                alert_box("Your data could not be retrieved. Please try again with your transaction ID", "warning", 7)
            }
        },
        error: function(r, textStatus){
            let message = JSON.stringify(r)

            if(textStatus == "timeout"){
                message = "Connection was timed out due to a slow network. Please try again later"
            }

            $("#view1 .para_message").html(message);

            setTimeout(function(){
                $("#view1 .para_message").html("Parts with * means they are required fields");
            },5000);
        }
    })
})

// show the courses where the need be
$("#program_select").change(function(){
    $("#course_displays").html("");
    $("#program_display_val").text("Not Set");

    if($(this).val() != ""){
        const option = $(this).find("option:selected");
        $("#course_displays").html(option.attr("data-courses"));
        $("#program_display_val").text(option.text());
    }
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
    $("#ad_index").prop("readonly", false);
})

//submit the admission form
$("form[name=admissionForm]").submit(function(e){
    e.preventDefault();

    fileUpload($(this), $(this).find("button[name=submit_admission]"), true)
    .then(response => {
        time = 5;

        if(response === true){
            message = "Enrolment was successful. Please wait as your letters are prepared";
            type = "success";

            messageBoxTimeout("admissionForm",message, type, 3);

            //click on cancel and open blank tab in 3 seconds
            setTimeout(function(){
                $("#handle_pdf")[0].click();
                $("button[name=modal_cancel]").click();
            },3000);
        }else{
            type = "error";

            if(response == "no-transaction-id"){
                message = "No transaction id was provided";
                i = 1;
            }else if(response == "no-index-number"){
                message = "No index number was provided";
                i = 1;
            }else if(response == "profile-wrong-ext"){
                message = "Profile picture must be of type jpg, jpeg or png";
                i=1;
            }else if(response == "profile-pic-required"){
                message = "Please provide your profile picture";
                i=1;
            }else if(response == "no-enrolment-code"){
                message = "No enrolment code was provided";
                i = 1;
                $("#ad_enrol_code").focus();
            }else if(response == "enrolment-code-short"){
                message = "Your enrolment code should be 10 characters long";
                i = 1;
                $("#ad_enrol_code").focus();
            }else if(response == "enrolment-code-exist"){
                message = "The enrolment code <b>'" + $("#ad_enrol_code").val() + "'</b> already exists. Please check your placement form and provide a valid one";
                i = 1;
                $("#ad_enrol_code").focus();
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
            }else if(response == "p-p-invalid"){
                message = "Primary phone number is not a invalid phone number";
                i = 2;
            }else if(response == "s-p-short"){
                message = "Secondary phone number is shorter than normal";
                i = 2;
            }else if(response == "s-p-long"){
                message = "Secondary phone number is longer than normal";
                i = 2;
            }else if(response == "s-p-invalid"){
                message = "Secondary phone number is not a invalid phone number";
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
            }else if(response == "witness-phone-invalid"){
                message = "Witness' phone number is not a valid phone number";
                i = 2;
            }else{
                message = response;
                time = 0;
            }
            
            //click respective head
            $(".tabs span.tab_button:nth-child(" + i + ")").click();

            messageBoxTimeout("admissionForm", message, type, time);
        }
    }).then(error => {
        let message = ""

            if(error == "timeout"){
                message = "Connection was timed out due to a slow network. Please try again later"
            }else{
                message = error.toString();
            }
            type = "error";

            messageBoxTimeout(form_element.prop("name"), message, type);
    })
})

//display print button on admission form
$(".tab_button").click(function(){
    id = $(this).attr("id");

    if(id == "sumView"){
        $("label[for=print_summary]").removeClass("no_disp");
    }else{
        $("label[for=print_summary]").addClass("no_disp");
    }
})

//the avatar of the school
$("input#profile_pic").change(function(){
    if($(this).val() != ''){
        $("#res_ad_profile_picture").text("Provided");
    }else{
        $("#res_ad_profile_picture").text("Not Set");
    }
})

//print summary on button click
$("label[for=print_summary] button").click(function(){
    let html = $("#" + $("#sumView").attr("data-views")).html();
    html = html.replace(/></g, ">Not Defined<");
    
    const index = $("input#ad_index").val();
    const printStyle = "<style>\n" + 
    "fieldset{display: block;margin-bottom: 1cm;border: 1px solid black;}\n" + 
    ".joint{margin: 5px; display: flex; flex-wrap: wrap; gap: 5mm}\n" +
    ".joint .label{flex: 1 1 auto; min-width: 40mm; border: 1px solid lightgrey;padding: 2mm 3mm;min-height: 1.25em;}\n" + 
    ".label .value{color: #222;font-variant: small-caps;}\n" +
    ".ng-hide{display: none;}\n" +
    ".cur_time{margin-top: 5mm; text-align: center;}\n" +
    ".checkbox{margin-top: 3mm; padding: 2mm 2mm 1mm}\n" +  
    "</style>\n";

    // set document title
    const title = "Admission Summary | " + index;

    // add current date and time as print time
    html += "\n<p class='cur_time'>Document Generated at " + printTime() + "</p>";

    // open a print section
    printSection(html, printStyle, title);
})

function printSection(content, style, title="Print View"){
    const printWindow = window.open('', title);

    printWindow.document.write('<html><head><title>' + title +'</title></head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');

    // Optional: Add additional styles or scripts for the print window
    printWindow.document.head.innerHTML += style;

    printWindow.document.close(); // Important: Close the document stream before printing

    printWindow.print();
}

function printTime(){
    var currentDate = new Date();

            var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            var month = months[currentDate.getMonth()];

            var day = currentDate.getDate();
            var year = currentDate.getFullYear();

            var hours = currentDate.getHours();
            var minutes = currentDate.getMinutes();
            var seconds = currentDate.getSeconds();

            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // Handle midnight (0:00)

            var formattedDate = month + ' ' + day + ', ' + year;
            var formattedTime = hours + ':' + (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds + ' ' + ampm;

            var dateNow = formattedDate + ' at ' + formattedTime;

            return dateNow;
}