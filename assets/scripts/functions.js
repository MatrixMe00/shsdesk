/**
 * This function will be used to determine load events for message boxes
 * @param {string} size This parameter will hold the size of the buttons
 * @param {string} display This parameter will hold the type of shape the buttons should take
 * @param {string} animation This param is for taking animation classes
 * @param {boolean} full This is for verifying if the div should be for full screen or not
 * @param {string} span1 This is for taking the color for the first span
 * @param {string} span2 This is for taking the color for the second span
 * @param {string} span3 This is for taking the color for the third span
 * @param {string} span4 This is for taking the color for the fourth span
 * @returns {string} Returns a string of the created div element
 */
 function loadDisplay(element = {
     size: null,
     display: null,
     animation: null,
     full: false,
     span1: null,
     span2: null,
     span3: null,
     span4: null
 }){
    //initialize values with default
    if(element["size"] == null){
        wide = $(window).width();

        if(wide < 480){
            element["size"] = "vsmall";
        }else if(wide < 720){
            element["size"] = "small";
        }else{
            element["size"] = "med";
        }
    }

    if(element["animation"] == null){
        element["animation"] = "anim-swing";
    }

    if(element["display"] == null){
        element["display"] = "semi-round";
    }

    if(element["span1"] == null){
        element["span1"] = "red";
    }

    if(element["span2"] == null){
        element["span2"] = "yellow";
    }

    if(element["span3"] == null){
        element["span3"] = "green";
    }

    if(element["span4"] == null){
        element["span4"] = "teal";
    }

    if(element["full"]){
        fullClass = "full";
    }else{
        fullClass = "";
    }

    load = "<div class=\"loader flex " + element["animation"] + " " + fullClass + "\">\n" +
            "<div class=\"span-container flex\">\n" +
                "<span class=\"" + element["size"] + " " + element["display"] + " " + element["span1"] + "\"></span>\n" + 
                "<span class=\"" + element["size"] + " " + element["display"] + " " + element["span2"] + "\"></span>\n" + 
                "<span class=\"" + element["size"] + " " + element["display"] + " " + element["span3"] + "\"></span>\n" + 
                "<span class=\"" + element["size"] + " " + element["display"] + " " + element["span4"] + "\"></span>\n" +
            "</div>\n" +
        "</div>\n";
    
        return load;
}

function sometin(data = {url: "key", password: ""}){
    alert(data["password"]);
}

/**
 * This function will be used to play a slideshow on the index page
 * @param {number} i This paramenter receives the current slide number to display
 */
 function slideshow(i){
    //show the image
    $(".img_container img").hide();
    $(".img_container img:nth-child(" + i + ")").show();

    //show the content
    $(".description .detail").hide();
    $(".description .detail:nth-child(" + i + ")").fadeIn();
}

//function to check the form and make submit button ready
/**
 * This value is used for tracking changes in the admission form control
 * @param {number} i This paramneter receives the index of the current tab
 * @returns {boolean} The function returns a true or false value which is used to
 * tell if the submit button should be enabled or not
 */
function checkForm(i){
    //return value
    return_value = true;

    //variables to be used to check if agree checkbox should be enabled or not
    accept1 = false;
    accept2 = false;

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

            //prepare to be agreed
            accept1 = true;
        }else{
            $(".tab_button.active").addClass("incomplete");
            return_value = true;
        }
    }else if(parseInt(i) == 2){
        if(((ad_father_name != "" && ad_father_occupation != "") || (ad_mother_name != "" && ad_mother_occupation != "") || ad_guardian_name != "") && 
        ad_resident != "" && ad_phone != "" && ad_witness != "" && ad_witness_phone != ""){
            $(".tab_button.active").removeClass("incomplete");
            return_value = false;

            //prepare to be agreed
            accept2 = true;
        }else{
            $(".tab_button.active").addClass("incomplete");
            return_value = true;
        }
    }

    //check if tab is completely filled with required data
    if(return_value == false){
        next = parseInt(i) + 1;
        element = $(".tab_button:nth-child(" + next + ")");
        if($(element).hasClass("no_disp")){
            $(element).removeClass("no_disp");
        }
    }

    //enable or disable agree button
    if(accept1 && accept2){
        $("label[for=agree] input[name=agree]").prop("disabled", true);
    }else{
        $("label[for=agree] input[name=agree]").prop("disabled", false);
    }

    return return_value;
}

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

/**
 * This function will be used to parse any file type into the database
 * 
 * @param {string} file_element This takes the element name of the file
 * @param {string} form_element This takes a specified form element
 * @param {string} submit_element This takes the name of the submit button
 * 
 * @return {boolean|string} Returns a boolean value or an error message
 */

function fileUpload(file_element, form_element, submit_element){
    formData = new FormData();

    //preparing file and submit values
    file = $(file_element).prop("files")[0];
    file_name = $(file_element).attr("name");
    submit_value = $(submit_element).prop("value");

    //strip form data into array form and attain total data
    form_data = $(form_element).serializeArray();
    split_lenght = form_data.length;

    //loop and fill form data
    counter = 0;
    while(counter < split_lenght){
        //grab each array data
        new_data = form_data[counter];

        key = new_data["name"];
        value = new_data["value"];

        //append to form data
        formData.append(key, value);

        //move to next data
        counter++;
    }

    //append name and value of file
    formData.append(file_name, file);

    //append submit if not found
    if(!$(form_element).serialize().includes("submit=")){
        formData.append("submit", submit_value + "_ajax");
    }

    response = null;
    
    $.ajax({
        url: $(form_element).attr("action"),
        data: formData,
        method: "post",
        dataType: "text",
        cache: false,
        async: false,
        contentType: false,
        processData: false,

        success: function(text){
            if(text == "success" || text.includes("success")){
                response = true;
            }else{
                response = text;
            }
        }
    })

    return response;
}