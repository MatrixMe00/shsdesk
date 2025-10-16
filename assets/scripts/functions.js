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
 * Used to show a field's error
 * @param {*} $el The element
 * @param {*} message The message to be shown
 */
function showFieldError($el, message) {
    // Remove existing error
    $el.next(".field-error").remove();

    // Add only if there's a message
    if (message) {
        $el.after('<small class="field-error" style="color:red;display:block;margin-top:3px;">' + message + '</small>');
    }
}

/**
 * This function here would be used to alert the user on required forms field in the admission form
 * 
 * @param {any} element The current selected element
 * @return {void} The function returns nothing
 */
function formRequiredCheck(element) {
    let $el = $(element);

    // Check if element is required
    let required = $el.prop("required") || $el.hasClass("required");

    // Pick input element type (if needed)
    let type = $el.attr("type");

    // Grab content
    let content = $el.val() || "";

    // Min/Max length
    let maxlength = parseInt($el.attr("maxlength"), 10) || 0;
    let minlength = parseInt($el.attr("minlength"), 10) || 0;

    let isValid = true;

    if (required) {
        if (content.length < minlength) {
            isValid = false;
        } else if (maxlength > 0 && content.length > maxlength) {
            isValid = false;
        } else if (content.length === 0) {
            isValid = false;
        }
    }

    if (required) {
        $el.css("border", isValid ? "1px solid green" : "1px solid red");
    } else {
        $el.css("border", ""); // neutral for non-required
    }
}

/**
 * Validate a single field and show inline error
 * @param {jQuery} $el The input element wrapped in jQuery
 * @returns {boolean} true = valid, false = invalid
 */
function validateField($el) {
    // ✅ Skip disabled fields
    if ($el.prop("disabled")) {
        $el.css("border", "");          // reset border
        showFieldError($el, "");        // clear any error message
        return true;                    // treat as valid
    }

    let required = $el.prop("required") || $el.hasClass("required");
    let value = ($el.val() || "").trim();
    let minlength = parseInt($el.attr("minlength"), 10) || 0;
    let maxlength = parseInt($el.attr("maxlength"), 10) || Infinity;
    let pattern = $el.attr("pattern"); // regex pattern if defined

    let message = "";

    // ✅ Required check
    if (required && value.length === 0) {
        message = "This field is required.";
    } else if (value.length > 0) {
        // ✅ Only validate non-required fields if they have a value
        if (value.length < minlength) {
            message = "Minimum length is " + minlength + " characters.";
        } else if (value.length > maxlength) {
            message = "Maximum length is " + maxlength + " characters.";
        } else if (pattern) {
            try {
                let regex = new RegExp("^" + pattern + "$");
                if (!regex.test(value)) {
                    message = "Invalid format.";
                }
            } catch (e) {
                console.warn("Invalid regex pattern for", $el.attr("name"), pattern);
            }
        }
    }

    // ✅ Update border
    if (message) {
        $el.css("border", "1px solid red");
    } else if (value.length > 0 || required) {
        // only show green if filled or required
        $el.css("border", "1px solid green");
    } else {
        // reset border for optional empty
        $el.css("border", "");
    }

    // ✅ Show or clear error
    showFieldError($el, message);

    return message === ""; // valid if no error
}

/**
 * Validate all fields in the current tab
 * Includes per-field and step-level rules
 * @returns {boolean} true = tab valid, false = tab invalid
 */
function validateCurrentTab() {
    let $activeTab = $(".form_views > div:not(.no_disp)");
    let allValid = true;

    // Validate each field individually
    $activeTab.find("input, select, textarea").each(function() {
        let valid = validateField($(this));
        if (!valid) allValid = false;
    });

    // Step-level special rules
    if ($activeTab.attr("id") === "view2") {
        let fatherOk = $("#ad_father_name").val().trim().length >= 6 && $("#ad_father_occupation").val().trim() !== "";
        let motherOk = $("#ad_mother_name").val().trim().length >= 6 && $("#ad_mother_occupation").val().trim() !== "";
        let guardianOk = $("#ad_guardian_name").val().trim().length >= 5;

        if (!(fatherOk || motherOk || guardianOk)) {
            allValid = false;
        }
    }

    return allValid;
}

/**
 * Update guardian/father/mother rules dynamically
 */
function updateGuardianRules() {
    const fatherName = $("#ad_father_name").val().trim();
    const fatherOcc = $("#ad_father_occupation").val().trim();
    const motherName = $("#ad_mother_name").val().trim();
    const motherOcc = $("#ad_mother_occupation").val().trim();
    const guardianName = $("#ad_guardian_name").val().trim();

    const fatherOk = fatherName.length >= 6 && fatherOcc.length > 0;
    const motherOk = motherName.length >= 6 && motherOcc.length > 0;
    const guardianOk = guardianName.length >= 6;

    // If father OR mother is valid → lock guardian
    if (fatherOk || motherOk) {
        $("#ad_guardian_name").prop("disabled", true).val(""); // clear guardian
    } else {
        $("#ad_guardian_name").prop("disabled", false);
    }

    // If guardian is valid → lock father + mother
    if (guardianOk) {
        $("#ad_father_name, #ad_father_occupation, #ad_mother_name, #ad_mother_occupation")
            .prop("disabled", true)
            .val(""); // clear fields
    } else {
        $("#ad_father_name, #ad_father_occupation, #ad_mother_name, #ad_mother_occupation")
            .prop("disabled", false);
    }

    // If ANY of them is valid → set all boxes to green
    if (fatherOk || motherOk || guardianOk) {
        $("#ad_father_name, #ad_father_occupation, #ad_mother_name, #ad_mother_occupation, #ad_guardian_name")
            .css("border", "2px solid green");
    } else {
        $("#ad_father_name, #ad_father_occupation, #ad_mother_name, #ad_mother_occupation, #ad_guardian_name")
            .css("border", ""); // reset border
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
 * Validate admission form step by step
 * @param {number} i The current tab index (1-based)
 * @returns {boolean} true = has error, false = valid
 */
function checkForm(i) {
    let an_error = true;
    let step = parseInt(i, 10);

    // Run field validation for the current tab
    let tabValid = validateCurrentTab();

    // Track agreements for step 1 & 2
    if (step === 1) {
        accept1 = tabValid;
        $(".tab_button.active").toggleClass("incomplete", tabValid === false);
    } else if (step === 2) {
        accept2 = tabValid;
        $(".tab_button.active").toggleClass("incomplete", tabValid === false);
    }

    // If valid → allow next tab to be visible
    if (tabValid) {
        let next = step + 1;
        let $nextTab = $(".tab_button:nth-child(" + next + ")");
        if ($nextTab.hasClass("no_disp")) {
            $nextTab.removeClass("no_disp");
        }
    }

    // Enable or disable agree checkbox (step 3 logic)
    if (accept1 && accept2) {
        let $agreeInput = $("label[for=agree] input[name=agree]");
        $agreeInput.prop("disabled", false);

        if (step === 3) {
            let checked = $agreeInput.prop("checked");
            tabValid = checked; // must check agreement
        }
    } else {
        $("label[for=agree] input[name=agree]").prop("disabled", true);
    }

    // Update interests in summary (still needed)
    $("#res_ad_interest").html($("input#interest").val());

    // Final decision
    an_error = !tabValid;
    return an_error;
}

/**
* This function converts an object into a formadata object
* @param {any} object This is the object to be processed
* @return {FormData} returns a FormData value
*/
function toFormData(object){
if(object instanceof FormData){
        return object;
}

return JSONtoFormData(object);
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
 * Converts json to formdata
 * @param {object} json The json object
 * @return {FormData}
 */
function JSONtoFormData(json){
    const formData = new FormData();

    for (const key in json) {
        if (json.hasOwnProperty(key)) {
            formData.append(key, json[key]);
        }
    }

    return formData;
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

    const formData = new FormData(form_element[0], submit_element[0]);
    response = null;
    
    await $.ajax({
        url: $(form_element).attr("action"),
        data: formData,
        method: $(form_element).attr("method") ? $(form_element).attr("method") : "POST",
        dataType: "text",
        cache: false,
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
 * This is used to check if a string can be parsed in json
 * @param {string} value The string value to be checked
 * @return {bool}
 */
function is_json_string(value){
    try {
        JSON.parse(value);
        return true;
    } catch (error) {
        return false;
    }
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

    // submit_element = submit_element === null || form_element.find("input[name=submit]") ? null : submit_element[0];
    const formData = new FormData(form_element[0], submit_element[0]);
    formData.append("response_type", "json");

    response = null;
    
    await $.ajax({
        url: $(form_element).attr("action"),
        data: formData,
        method: $(form_element).attr("method") ? $(form_element).attr("method") : "POST",
        dataType: "json",
        cache: false,
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
        success: function(response_){
            if(messageBox){
                $("form[name=" + $(form_element).prop("name") + "] .message_box").addClass("no_disp");
            }

            response = response_;
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
 * This is used for formless transactions
 * @typedef {Object} AJAXOptions
 * @property {string} url The url of the form
 * @property {FormData} formData The form data to be sent
 * @property {string} returnType The return type the request
 * @property {string} method The method of the request
 * @property {bool} sendRaw Set this to true if the call contains a file
 * @property {Function} beforeSend A method to be run when beforeSend is called
 * @property {int} timeout The wait time until timeout
 * @param {AJAXOptions} ajaxOptions
 * @return
 */
async function ajaxCall({url, formData = {}, returnType = "text", method = "GET", sendRaw = false, beforeSend = null, timeout = 0}){
    let response_ = false;
    try {
        if(formData instanceof FormData){
            formData.append("response_type", returnType);
        }else{
            formData.response_type = returnType;
        }

        await $.ajax({
            type: method,
            url: url,
            data: formData,
            dataType: returnType,
            timeout: timeout,
            contentType: sendRaw ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
            processData: !sendRaw,
            beforeSend: function(){
                if(beforeSend != null)
                    beforeSend();
            },
            success: function (response) {
                response_ = response;
            },
            error: function (xhr, status, error) {
                console.error(`Error: ${error}`, xhr);
                alert_box(status != "" ? status : error, "danger");
            }
        });
    } catch (error) {
        alert_box(error.toString(), "danger");
        console.log(error);
    }

    return response_;
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

    submit_element = submit_element === null || form_element.find("input[name=submit]").length > 0 ? null : submit_element[0];
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

    submit_element = submit_element === null || form_element.find("input[name=submit]").length > 0 ? null : submit_element[0];
    const formData = FormDataToJSON(new FormData(form_element[0], submit_element));
    formData.response_type = "json";

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

/**
 * Convert a number to position
 * @param {*} number The number to be formated
 * @returns {string}
 */
function positionFormat(number) {
    number = parseInt(number, 10);
    let suffix = "";

    switch (number % 10) {
        case 1: suffix = (number > 20 || number < 10) ? "st" : "th"; break;
        case 2: suffix = (number > 20 || number < 10) ? "nd" : "th"; break;
        case 3: suffix = (number > 20 || number < 10) ? "rd" : "th"; break;
        default: suffix = "th";
    }

    return number + suffix;
}

/**
 * Returns a promise
 * @param {*} ms Time in miliseconds
 * @returns {Promise}
 */
function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Used in the admin section to redirect to a menu item
 * @param {string} menu_name The menu unique name
 * @param {string} menu_parent The root parent of the menu
 * @param {string} menu_class The class name for the menu item
 */
function menu_route(menu_name, menu_parent = "lhs", menu_class = "item"){
    const menu = $("#" + menu_parent).find("." + menu_class + "[name=" + menu_name + "]");
    if(menu_name && menu.length > 0){
        menu.click();
    }
}
  