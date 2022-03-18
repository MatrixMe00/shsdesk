/**
 * This function will be used to determine load events for message boxes
 * 
 * @param {string} size This parameter will hold the size of the buttons
 * Acceptable sizes are v-small, small, med and large
 * @param {string} display This parameter will hold the type of shape the buttons should take
 * Acceptable values are semi-round and rounded. Default is semi-round
 * @param {string} animation This param is for taking animation classes
 * Acceptable values are anim-swing, anim-fade, anim-fade-swing. Default is anim-swing
 * @param {boolean} full This is for verifying if the div should be for full screen or not
 * @param {boolean} circular This is for displaying a circular loader
 * @param {string} circleColor This is for providing a color for a circular loader
 * @param {string} span1 This is for taking the color for the first span
 * @param {string} span2 This is for taking the color for the second span
 * @param {string} span3 This is for taking the color for the third span
 * @param {string} span4 This is for taking the color for the fourth span
 * 
 * @returns {string} Returns a string of the created div element
 */
 function loadDisplay(element = {
     size: "",
     display: "",
     animation: "",
     full: false,
     circular: false,
     span1: "",
     span2: "",
     span3: "",
     span4: "",
     circleColor: "",
 }){
    //initialize values with default
    if(element["size"] == null || element["size"] == ""){
        wide = $(window).width();

        if(wide < 480){
            element["size"] = "vsmall";
        }else if(wide < 720){
            element["size"] = "small";
        }else{
            element["size"] = "med";
        }
    }

    if(element["animation"] == null || element["animation"] ==""){
        element["animation"] = "anim-swing";
    }

    if(element["display"] == null || element["display"] ==""){
        element["display"] = "semi-round";
    }

    if(element["span1"] == null || element["span1"] ==""){
        element["span1"] = "red";
    }

    if(element["span2"] == null || element["span2"] ==""){
        element["span2"] = "yellow";
    }

    if(element["span3"] == null || element["span3"] ==""){
        element["span3"] = "green";
    }

    if(element["span4"] == null || element["span4"] ==""){
        element["span4"] = "teal";
    }

    if(element["full"]){
        fullClass = "full";
    }else{
        fullClass = "";
    }

    if(element["circleColor"] == null || element["circleColor"] ==""){
        element["circleColor"] = "dark";
    }

    if(element["circular"]){
        load = 
        "<div class=\"loader " + fullClass + " circle-loader flex anim-fade\" style=\"--c:8\">\n" +
            "<div class=\"span-container flex\">\n" + 
                "<span class=\"stroke " + element["display"] + " " + element["circleColor"] + "\" style=\"--i:0\"></span>\n" +
                "<span class=\"stroke " + element["display"] + " " + element["circleColor"] + "\" style=\"--i:1\"></span>\n" +
                "<span class=\"stroke " + element["display"] + " " + element["circleColor"] + "\" style=\"--i:2\"></span>\n" +
                "<span class=\"stroke " + element["display"] + " " + element["circleColor"] + "\" style=\"--i:3\"></span>\n" +
                "<span class=\"stroke " + element["display"] + " " + element["circleColor"] + "\" style=\"--i:4\"></span>\n" +
                "<span class=\"stroke " + element["display"] + " " + element["circleColor"] + "\" style=\"--i:5\"></span>\n" +
                "<span class=\"stroke " + element["display"] + " " + element["circleColor"] + "\" style=\"--i:6\"></span>\n" +
                "<span class=\"stroke " + element["display"] + " " + element["circleColor"] + "\" style=\"--i:7\"></span>\n" +
            "</div>\n" +
        "</div>";
    }else{
        load = "<div class=\"loader flex " + element["animation"] + " " + fullClass + "\" style=\"--c:4\">\n" +
            "<div class=\"span-container flex\">\n" +
                "<span class=\"" + element["size"] + " " + element["display"] + " " + element["span1"] + "\" style=\"--i:0\"></span>\n" + 
                "<span class=\"" + element["size"] + " " + element["display"] + " " + element["span2"] + "\" style=\"--i:1\"></span>\n" + 
                "<span class=\"" + element["size"] + " " + element["display"] + " " + element["span3"] + "\" style=\"--i:2\"></span>\n" + 
                "<span class=\"" + element["size"] + " " + element["display"] + " " + element["span4"] + "\" style=\"--i:3\"></span>\n" +
            "</div>\n" +
        "</div>\n";
    }
    
        return load;
}

/**
 * This function here would be used to alert the user on required forms field in the admission form
 * 
 * @param {any} element The current selected element
 * @return {void} The function returns nothing
 */
function formRequiredCheck(element) {
    //check if element has required field
    required = $(element).prop("required");

    if(!required){
        //check if it has the required class
        if($(element).hasClass("required")){
            required = true;
        }
    }

    //pick input element type
    type = element.attr("type");

    //grab content
    content = $(element).val();

    //check if element has max length
    maxlength = $(element).prop("maxlength");

    if(maxlength <= 0){
        maxlength = 0;
    }

    //if element is required, paint as red else as green
    if(required && maxlength > 0 && content.length < maxlength){
        $(element).css("border", "1px solid red");
    }else if(required && content.length < 1){
        $(element).css("border", "1px solid red");
    }else{
        $(element).css("border", "1px solid green");
    }
}

/**
 * This function will be used to regulate fade outs in the project
 * 
 * @param {number} time This takes the length of time for the effect. Its defaulted at 0.7s
 * @param {any} element This picks the element to add or retrieve the fade animation to
 * 
 * @return {void} The function returns nothing
 */
function fadeOutElement(element, time = 0.7){
    //set time to miliseconds
    time *= 1000;

    //add the fadeout class to the element
    $(element).addClass("fadeOut");

    //set an interval to make animation
    timeout = setTimeout(function(){
        //remove and add neccessary classes
        $(element).removeClass("fadeOut").addClass("no_disp");
    }, time);
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

//variables to be used to check if agree checkbox should be enabled or not in admission form
var accept1 = false;
var accept2 = false;

/**
 * Function to reset accept values
 * 
 * @return {void}
 */
function resetAccepts(){
    accept1 = false;
    accept2 = false;
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

    //cssps details
    shs_placed = $("#shs_placed").val();
    ad_enrol_code = $("#ad_enrol_code").val();
    ad_index = $("#ad_index").val();
    ad_aggregate = $("#ad_aggregate").val();
    ad_course = $("#ad_course").val();

    //personal details of candidate
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
        ad_oname != "" && ad_lname != "" && ad_gender != "" && ad_year != "" && ad_month != "" 
        && ad_day != "" && ad_birth_place != ""){
            if(ad_jhs != "" && ad_jhs_district != "" && ad_jhs_town != ""){
                $(".tab_button.active").removeClass("incomplete");
                return_value = false;

                //prepare to be agreed
                accept1 = true;
            }else{
                return_value = true;

                //disagree with document
                accept1 = false;
            }
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
        $("label[for=agree] input[name=agree]").prop("disabled", false);
        return_value = false;

        if(parseInt(i) == 3){
            val = $("label[for=agree] input[name=agree]").prop("checked");

            if(val){
                return_value = false;
            }else{
                return_value = true;
            }
        }
    }else{
        $("label[for=agree] input[name=agree]").prop("disabled", true);
    }

    return return_value;
}

/**
 * This function will be used to parse any file type into the database
 * 
 * @param {string} file_element This takes the element name of the file
 * @param {string} form_element This takes a specified form element
 * @param {string} submit_element This takes the name of the submit button
 * @param {boolean} messageBox This tests if there is a message box
 * 
 * @return {boolean|string} Returns a boolean value or an error message
 */

function fileUpload(file_element, form_element, submit_element, messageBox = true){
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
    if(!$(form_element).serialize().includes("&submit=")){
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
        beforeSend: function(){
            if(messageBox){
                message = loadDisplay({size: "small"});
                type = "load";
                time = 0;

                messageBoxTimeout(form_element.prop("name"), message, type, time);
            }            
        },
        success: function(text){
            $("form[name=" + $(form_element).prop("name") + "] .message_box").addClass("no_disp");
            if(text == "success" || text.includes("success")){
                response = true;
            }else{
                response = text;
            }
        },
        error: function(){
            message = "Please check your internet connection and try again";
            type = "error";

            messageBoxTimeout(form_element.prop("name"), message, type);
        }
    })

    return response;
}

/**
 * This function will be used to parse any file type into the database
 * 
 * @param {string} file_element This takes the element name of the file
 * @param {string} form_element This takes a specified form element
 * @param {string} submit_element This takes the name of the submit button
 * @param {boolean} messageBox This tests if there is a message box
 * 
 * @return {boolean|array} Returns a boolean value or an array
 */

 function jsonFileUpload(file_element, form_element, submit_element, messageBox = true){
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
    if(!$(form_element).serialize().includes("&submit=")){
        formData.append("submit", submit_value + "_ajax");
    }

    response = null;
    
    $.ajax({
        url: $(form_element).attr("action"),
        data: formData,
        method: "post",
        dataType: "json",
        cache: false,
        async: false,
        contentType: false,
        processData: false,
        beforeSend: function(){
            if(messageBox){
                message = loadDisplay({size: "small"});
                type = "load";
                time = 0;

                messageBoxTimeout(form_element.prop("name"), message, type, time);
            }            
        },
        success: function(text){
            $("form[name=" + $(form_element).prop("name") + "] .message_box").addClass("no_disp");
            text = JSON.parse(JSON.stringify(text));

            if(text["status"] == "success" || text["status"].includes("success")){
                response = true;
            }else{
                response = text;
            }
        },
        error: function(){
            message = "Please check your internet connection and try again";
            type = "error";

            messageBoxTimeout(form_element.prop("name"), message, type);
        }
    })

    return response;
}

/**
 * This function will be used to send textual information from forms
 * 
 * @param {any} form_element This takes the form element object
 * @param {any} submit_element This takes the submit element of the form
 * @param {boolean} messageBox This tests if there is a message box
 * 
 * @return {boolean|string} returns a boolean value or a string
 */
function formSubmit(form_element, submit_element, messageBox = true){
    // formData = new FormData();

    //submit value
    submit = $(submit_element).val();

    //strip form data into array form and attain total data
    form_data = $(form_element).serializeArray();
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
    if(!$(form_element).serialize().includes("&submit=")){
        formData += "&submit=" + submit + "_ajax";
    }

    response = null;
    
    $.ajax({
        url: $(form_element).attr("action"),
        data: formData,
        method: "post",
        dataType: "text",
        cache: false,
        async: false,
        beforeSend: function(){
            if(messageBox){
                message = loadDisplay({size: "small"});
                type = "load";
                time = 0;

                messageBoxTimeout(form_element.prop("name"), message, type, time);
            }
        },
        success: function(text){
            $("form[name=" + $(form_element).prop("name") + "] .message_box").addClass("no_disp");
            if(text == "success" || text.includes("success")){
                response = true;
            }else{
                response = text;
            }
        },
        error: function(){
            message = "Please check your internet connection and try again";
            type = "error";

            messageBoxTimeout(form_element.prop("name"), message, type);
        }
    })

    return response;
}

/**
 * This function will be used to send textual information from forms
 * 
 * @param {any} form_element This takes the form element object
 * @param {any} submit_element This takes the submit element of the form
 * @param {boolean} messageBox This tests if there is a message box
 * 
 * @return {boolean|array} returns a boolean value or an array
 */
 function jsonFormSubmit(form_element, submit_element, messageBox = true){
    // formData = new FormData();

    //submit value
    submit = $(submit_element).val();

    //strip form data into array form and attain total data
    form_data = $(form_element).serializeArray();
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
    if(!$(form_element).serialize().includes("&submit=")){
        formData += "&submit=" + submit + "_ajax";
    }

    response = null;
    
    $.ajax({
        url: $(form_element).attr("action"),
        data: formData,
        method: "post",
        dataType: "json",
        cache: false,
        async: false,
        beforeSend: function(){
            if(messageBox){
                message = loadDisplay({size: "small"});
                type = "load";
                time = 0;

                messageBoxTimeout(form_element.prop("name"), message, type, time);
            }
        },
        success: function(text){
            $("form[name=" + $(form_element).prop("name") + "] .message_box").addClass("no_disp");
            text = JSON.parse(JSON.stringify(text));

            if(text["status"] == "success" || text["status"].includes("success")){
                response = true;
            }else{
                response = text;
            }
        },
        error: function(){
            message = "Please check your internet connection and try again";
            type = "error";

            messageBoxTimeout(form_element.prop("name"), message, type);
        }
    })

    return response;
}