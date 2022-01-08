//variables to use
var admission_button_tab_index = 1;     //This will be used to track the tab index in the admission form

//getting the host url
var url = location.protocol + '//' + location.host + "/" + location.pathname.split("/")[1];

//function to check the form and make submit button ready
function checkForm(i){
    //return value
    return_value = true;

    //cssps details
    shs_placed = $("#shs_placed").val();
    ad_enrol_code = $("#ad_enrol_code").val();
    ad_index = $("#ad_index").val();
    ad_aggregate = $("#ad_aggregate").val();
    ad_course = $("#ad_course").val();

    //personal details of candidate
    ad_fname = $("#ad_fname").val();
    ad_lname = $("#ad_lname").val();
    ad_oname = $("#ad_oname").val();
    ad_gender = $("#ad_gender").val();
    ad_jhs = $("#ad_jhs").val();
    ad_jhs_town = $("#ad_jhs_town").val();
    ad_jhs_district = $("#ad_jhs_district").val();
    ad_birthdate = $("#ad_birthdate").val();
    ad_year = $("#ad_year").val();
    ad_month = $("#ad_month").val();
    ad_day = $("#ad_day").val();
    ad_birth_place = $("#ad_birth_place").val();

    //parents particulars
    ad_father_name = $("#ad_father_name").val();
    ad_father_occupation = $("#ad_father_occupation").val();
    ad_mother_name = $("#ad_mother_name").val();
    ad_mother_occupation = $("#ad_mother_occupation").val();
    ad_guardian_name = $("#ad_guardian_name").val();
    ad_resident = $("#ad_resident").val();
    ad_postal_address = $("#ad_postal_address").val();
    ad_phone = $("#ad_phone").val();
    ad_other_phone = $("#ad_other_phone").val();

    //interests
    interest = $("#interest").val();

    //others
    ad_awards = $("#ad_awards").val();
    ad_position = $("#ad_position").val();

    //witness
    ad_witness = $("#ad_witness").val();
    ad_witness_phone = $("#ad_witness_phone").val();

    if(parseInt(i) == 1){
        if(ad_enrol_code != "" && ad_index != "" && ad_aggregate != "" && ad_course != "" &&
        ad_fname != "" && ad_lname != "" && ad_gender != "" && ad_jhs != "" && ad_jhs_town != "" &&
        ad_jhs_district != "" && ad_year != "" && ad_month != "" && ad_day != "" && ad_birth_place != ""){
            $(".tab_button.active").removeClass("incomplete");
            return_value = false;
        }else{
            $(".tab_button.active").addClass("incomplete");
            return_value = true;
        }
    }else if(parseInt(i) == 2){
        if(((ad_father_name != "" && ad_father_occupation != "") || (ad_mother_name != "" && ad_mother_occupation != "") || ad_guardian_name != "") && 
        ad_resident != "" && ad_phone != "" && ad_witness != "" && ad_witness_phone != ""){
            $(".tab_button.active").removeClass("incomplete");
            return_value = false;
        }else{
            $(".tab_button.active").addClass("incomplete");
            return_value = true;
        }
    }

    if(return_value == false){
        next = parseInt(i) + 1;
        element = $(".tab_button:nth-child(" + next + ")");
        if($(element).hasClass("no_disp")){
            $(element).removeClass("no_disp");
        }
    }

    return return_value;
}

//function to be used to check if the next or submit button should be
//shown in the admission form
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
    fadeOutElement($(".form_modal_box"));
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
    if($("input[name=agree]").prop("checked") == false){
        $("button[name=submit_admission]").prop("disabled", true);

        //change the type of the admission button
        $("button[name=submit_admission]").prop("type","button");

        //show the other steps
        $(".tab_button").remove("no_disp");
    }else{
        $("button[name=submit_admission]").prop("disabled",false);

        //change the type of the admission button
        $("button[name=submit_admission]").prop("type","submit");

        //change the button name to submit
        $("button[name=submit_admission] span").html("Submit");

        //hide the other steps
        fadeOutElement($(".tab_button"));
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
        fadeOutElement($("label[for=health_specify]"));
    }
})

//show the payment form when the payment / enrol button is clicked
$("#payment_button, .enrol_button").click(function(){
    $("#payment_form").toggleClass("no_disp").addClass("flex");
})

//enabling the payment button when needed info is available
$("#payment_form form input").keyup(function(){
    if($("#fullname").val() != "" && $("#email").val() != "" && $("#password").val() != ""  && $("#phone").val() != ""){
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
        url: url + "submit.php",
        data: dataString,
    })
})