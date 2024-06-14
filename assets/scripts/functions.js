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

    //check if element has max length or min length
    maxlength = $(element).attr("maxlength");
    minlength = $(element).attr("minlength");

    if(maxlength == null){maxlength = 0}
    if(minlength == null){minlength = 1}

    //if element is required, paint as red else as green
    if((required && minlength > 1 && content.length < minlength) || 
        (required && maxlength > 0 && content.length < maxlength)
    ){
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
    an_error = true;

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
        if(ad_enrol_code != "" && ad_enrol_code.length == 6 && ad_index != "" && ad_aggregate != "" && ad_course != "" &&
        ad_oname != "" && ad_lname != "" && ad_gender != "" && ad_year != "" && ad_month != "" 
        && ad_day != "" && ad_birth_place != ""){
            if(ad_jhs != "" && ad_jhs_district != "" && ad_jhs_town != ""){
                $(".tab_button.active").removeClass("incomplete");
                an_error = false;

                //prepare to be agreed
                accept1 = true;
            }else{
                an_error = true;

                //disagree with document
                accept1 = false;
            }
        }else{
            $(".tab_button.active").addClass("incomplete");
            an_error = true;
        }
    }else if(parseInt(i) == 2){
        if((((ad_father_name != "" && ad_father_occupation != "") && ad_father_name.length >= 6) || 
          ((ad_mother_name != "" && ad_mother_occupation != "") && ad_mother_name.length >= 6) || 
          (ad_guardian_name != "") && ad_guardian_name.length >= 5) && ad_resident != "" && 
          ad_phone != "" && ad_witness != "" && ad_witness_phone != ""){
            $(".tab_button.active").removeClass("incomplete");
            an_error = false;

            //prepare to be agreed
            accept2 = true;
        }else{
            $(".tab_button.active").addClass("incomplete");
            an_error = true;
        }
    }

    //check if tab is completely filled with required data
    if(an_error == false){
        next = parseInt(i) + 1;
        element = $(".tab_button:nth-child(" + next + ")");
        if($(element).hasClass("no_disp")){
            $(element).removeClass("no_disp");
        }
    }

    //enable or disable agree button
    if(accept1 && accept2){
        $("label[for=agree] input[name=agree]").prop("disabled", false);
        an_error = false;

        if(parseInt(i) == 3){
            val = $("label[for=agree] input[name=agree]").prop("checked");

            if(val){
                an_error = false;
            }else{
                an_error = true;
            }
        }
    }else{
        $("label[for=agree] input[name=agree]").prop("disabled", true);
    }
    
    //update interest
    $("#res_ad_interest").html($("input#interest").val());

    return an_error;
}

/**
 * This function converts serialized form into formdata element
 * @param {any} form This is the serialized form data to be coverted
 * @return {FormData} returns a FormData value
 */
function toFormData(form){
    const split_lenght = form.length
    let formData = new FormData()

    //loop and fill form data
    let counter = 0;
    while(counter < split_lenght){
        //grab each array data
        new_data = form_data[counter]

        key = new_data["name"]
        value = new_data["value"]

        //append to form data
        formData.append(key, value)

        //move to next data
        counter++
    }

    return formData
}

/**
 * Converts formdata format to json object
 * @param {FormData} form_data The form data to be converted
 * @return {JSON}
 */
function FormDataToJSON(form_data){
    // Create an empty object to store the form data
    const jsonObject = {};

    // Iterate over the FormData entries and store them in the jsonObject
    for (var pair of form_data.entries()) {
        jsonObject[pair[0]] = pair[1];
    }

    return jsonObject;
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

async function fileUpload(form_element, submit_element, messageBox = true){
    if(!checkFormElement(form_element, submit_element)){
        return false;
    }

    submit_element = submit_element === null ? null : submit_element[0];
    const formData = FormDataToJSON(new FormData(form_element[0], submit_element));
 
    response = null;
    
    $.ajax({
        url: $(form_element).attr("action"),
        data: formData,
        method: $(form_element).attr("method") ? $(form_element).attr("method") : "POST",
        dataType: "text",
        cache: false,
        async: false,
        contentType: false,
        processData: false,
        timeout: 30000,
        beforeSend: function(){
            if(messageBox){
                message = loadDisplay({size: "small"});
                type = "load";
                time = 0;
 
                messageBoxTimeout(form_element.prop("name"), message, type, time);
            }            
        },
        success: function(text){
            if(messageBox){
                 $("form[name=" + $(form_element).prop("name") + "] .message_box").addClass("no_disp");
            }
            if(text == "success"){
                response = true;
            }else{
                response = text;
            }
        },
        error: function(er, textStatus){
            message = JSON.parse(JSON.stringify(er));
            if(textStatus === "timeout"){
                 message = "Connection was timed out due to a slow network. Please try again later"
            }
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
 
 async function jsonFileUpload(form_element, submit_element, messageBox = true){
    if(!checkFormElement(form_element, submit_element)){
        return false;
    }

    submit_element = submit_element === null ? null : submit_element[0];
    const formData = FormDataToJSON(new FormData(form_element[0], submit_element));
 
    response = null;
    
    $.ajax({
        url: $(form_element).attr("action"),
        data: formData,
        method: $(form_element).attr("method") ? $(form_element).attr("method") : "POST",
        dataType: "json",
        cache: false,
        async: false,
        contentType: false,
        processData: false,
        timeout: 30000,
        beforeSend: function(){
            if(messageBox){
                message = loadDisplay({size: "small"});
                type = "load";
                time = 0;
 
                messageBoxTimeout(form_element.prop("name"), message, type, time);
            }            
        },
        success: function(text){
            if(messageBox){
                 $("form[name=" + $(form_element).prop("name") + "] .message_box").addClass("no_disp");
            }
            text = JSON.parse(JSON.stringify(text));
 
            if(text["status"] == "success" || text["status"].includes("success")){
                response = true;
            }else{
                response = text;
            }
        },
        error: function(xhr, textStatus){
            message = "Please check your internet connection and try again";
            
            if(textStatus == "timeout"){
                 messgae = "Connection was timed out due to a slow network. Please try again later"
            }
            type = "error";
 
            if(messageBox){
             messageBoxTimeout(form_element.prop("name"), message, type)
            }
        }
    })
 
    return response;
 }

 /**
  * This checks if a form element has necessary devices
  * @param {jQuery} form_element This take the form element object
  * @param {jQuery} submit_element This takes the submit element
  * @return {boolean}
  */
 function checkFormElement(form_element, submit_element){
    if(!(form_element instanceof jQuery)){
        alert_box("Form is not a jquery object", "danger");
        return false;
    }
 
    if(submit_element !== null && !(submit_element instanceof jQuery)){
        alert_box("Button element is not a jquery object", "danger");
        return false;
    }

    if(!$(form_element).attr("name")){
        alert_box("Your form has no attribute name", "danger")
        return false
   }

   return true;
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
    if(!checkFormElement(form_element, submit_element)){
        return false;
    }

    submit_element = submit_element === null ? null : submit_element[0];
    const formData = FormDataToJSON(new FormData(form_element[0], submit_element));
 
    response = null;
    
    $.ajax({
        url: form_element.attr("action"),
        data: formData,
        method: form_element.attr("method") ? form_element.attr("method") : "POST",
        dataType: "text",
        cache: false,
        async: false,
        timeout: 30000,
        beforeSend: function(){
            if(messageBox){
                message = loadDisplay({size: "small"});
                type = "load";
                time = 0;
 
                messageBoxTimeout(form_element.prop("name"), message, type, time);
            }
        },
        success: function(text){
            if(messageBox){
                 $("form[name=" + form_element.prop("name") + "] .message_box").addClass("no_disp");
            }
            if(text == "success" || text.includes("success")){
                response = true;
            }else{
                response = text;
            }
        },
        error: function(xhr, textStatus){
            message = "Please check your internet connection and try again";
            type = "error";
 
            if(textStatus == "timeout"){
                message = "Connection was timed out due to a slow network. Please try again later"
            }
 
            if(messageBox){
                messageBoxTimeout(form_element.prop("name"), message, type);
            }else{
                return message;
            }
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
 * @return {Promise<boolean|array>} returns a boolean value or an array
 */
 async function jsonFormSubmit(form_element, submit_element, messageBox = true){
    if(!checkFormElement(form_element, submit_element)){
        return false;
    }

    submit_element = submit_element === null ? null : submit_element[0];
    const formData = FormDataToJSON(new FormData(form_element[0], submit_element));
 
    response = null;
    
    await $.ajax({
        url: form_element.attr("action"),
        data: formData,
        method: form_element.attr("method") ? form_element.attr("method") : "POST",
        dataType: "json",
        cache: false,
        timeout: 30000,
        beforeSend: function(){
            if(messageBox){
                message = loadDisplay({size: "small"});
                type = "load";
                time = 0;
 
                messageBoxTimeout(form_element.prop("name"), message, type, time);
            }
        },
        success: function(text){
            if(messageBox){
                 $("form[name=" + form_element.prop("name") + "] .message_box").addClass("no_disp");
            }
            text = JSON.parse(JSON.stringify(text));
            
            if(typeof text["status"] === "boolean"){
                response = text
            }else if(typeof text["status"] === "string"){
                if(text["status"] == "success" || text["status"].includes("success"))
                    response = true
                else
                    response = text
            }
        },
        error: function(e, textStatus){
            // message = "Please check your internet connection and try again";
            message = JSON.stringify(e)
            type = "error";
 
            if(textStatus === "timeout"){
                 message = "Connection was timed out due to a slow network. Please try again later"
            }
 
            if(messageBox){
                messageBoxTimeout(form_element.prop("name"), message, type, 0)
            }else{
                alert_box(message, "danger")
            }
        }
    })
 
    return response;
 }

/**
 * This is a custom override function to show a custom alert display onscreen
 * This automatically adds the alert modal if it was not found on a page
 * 
 * @param {string} message This is the message to be displayed
 * @param {string} color This is the background color of your message. It is set to primary by default
 * @param {integer} time This receives the time for the message to be displayed in seconds
 * 
 * @return this returns a message at the right top of a screen for an amount of time then disappears
 */
 function alert_box(message, color = "primary", time = 5){
    //search for the alert modal
    if($("body").find("#alert_modal").length < 1){
        alert_modal = "<div id=\"alert_modal\" class=\"fixed flex flex-column flex-align-end\"></div>";
        $("body").append(alert_modal);
    }
    my_number = $("#alert_modal").children(".alert_box").length;
    box = "<div class=\"alert_box " + color + "\" id=\"alert_box" + (my_number + 1) + "\">" + 
          " <div class=\"message\">" + 
          "     <span>" + message + "</span>" + 
          " </div>" + 
          "</div>";
    $("#alert_modal").append(box);

    removeAlert("alert_box" + (my_number + 1), time);
}

/**
 * This function works with the alert_box to temporarily show an alert message
 * 
 * @param {string} id This receives the id of the element to be removed in string format
 * @param {int} time Receives the time to wait before element is removed 
 */
function removeAlert(id, time){
    if(time > 0){
        setTimeout(function(){
            $("#" + id).remove();
        }, time*1000);
    }    
}

/**
 * This function is used to handle drag of an html element
 * @param {any} element This is the element to be worked on
 */
function dragElement(element){
    var dragging = false
    var x, y, dx, dy
    var $element = $(element)

    //take initial width and height
    const width = $element.width()

    $element.css({
        width: width
    })

    $element.mousedown(function(e) {
        dragging = true
        x = e.pageX
        y = e.pageY
        dx = x - $element.offset().left
        dy = y - $element.offset().top
    })

    $(document).mousemove(function(e) {
        if (dragging) {
            $element.css({
                left: e.pageX - dx,
                top: e.pageY - dy
            })
        }
    })

    $(document).mouseup(function(e) {
        dragging = false
    })
}

/**
 * This function is used to handle drag of an html element with touch
 * @param {any} element This is the element to be worked on
 */
function touchDragElement(element) {
    var dragging = false;
    var x, y, dx, dy;
    var $element = $(element);
  
    //take initial width and height
    const width = $element.width();
  
    $element.css({
      width: width
    });
  
    $element.on('touchstart', function(e) {
      dragging = true;
      x = e.originalEvent.touches[0].pageX;
      y = e.originalEvent.touches[0].pageY;
      dx = x - $element.offset().left;
      dy = y - $element.offset().top;
    });
  
    $(document).on('touchmove', function(e) {
      if (dragging) {
        $element.css({
          left: e.originalEvent.touches[0].pageX - dx,
          top: e.originalEvent.touches[0].pageY - dy
        });
      }
    });
  
    $(document).on('touchend', function(e) {
      dragging = false;
    });
  }

/**
 * The function is used to format the id of a program into the form PID XXXX
 * @param {string}|int $subject_id This is the id to be converted
 * @param {string} $prefix The prefix is used to provide the pre-text of the identifier
 * @param {boolean} $reverse This tells if it should convert the item id to integer
 * @return {string|int} Returns the formatted program id (string or int)
 */
function formatItemId(subject_id, prefix, reverse = false) {
    if (!reverse) {
        subject_id = subject_id.toString().padStart(4, "0");
        subject_id = `${prefix} ${subject_id}`;
    } else {
        subject_id = subject_id.toUpperCase();
        subject_id = subject_id.replace(prefix, "").replace(" ", "");
        subject_id = parseInt(subject_id);
    }
  
    return subject_id;
}

/**
 * This changes a json object to url string data
 * @param {object} json_object The json object
 * @return {string}
 */
function jsonToURL(json_object){
    json_object = JSON.stringify(json_object);

    let dataString = json_object.replace(/[{}"]/g, "");
    dataString = dataString.replace(/[:,]/g, function (match) {
                return match === ':' ? '=' : '&';
                });
    dataString = dataString.replace(/\//g, "_");

    return dataString;
}
  