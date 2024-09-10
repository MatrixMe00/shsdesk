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
  * @param {AJAXOptions} ajaxOptions
  * @return
  */
 async function ajaxCall({url, formData, returnType = "text", method = "GET", sendRaw = false, beforeSend = null}){
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
            contentType: sendRaw ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
            processData: !sendRaw,
            beforeSend: function(){
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

chartColors = [
    "#007bff", "#6610f2", "#6f42c1", "#e83e8c",
    "#dc3545", "#fd7e14", "#ffc107", "#28a745",
    "#20c997", "#17a2b8", "#6c757d", "#343a40",
    "#EF5777", "#575FCF", "#48D0FA", "#35E7E4",
    "#0CE881", "#F53B57", "#3D40C6", "#10BCF9",
    "#00D8D6", "#04C56B", "#FFC048", "#FFDD59",
    "#FF5E58", "#48545F", "#FFA8D0", "#FFD32A",
    "#FF3F34", "#818E98", "#182027"
]

/**
 * This function is used to select colors for charts
 * @param {string} chartType receive the type of chart 
 * @param {number} dataCount the number of data
 * @returns {arral|string}
 */
function selectChartColors(chartType, dataCount = 0){
    var colors = []
    const singleColorCharts = ["line","scatter"]

    if(singleColorCharts.includes(chartType.toLowerCase()) || dataCount === 1){
        let chartIndex = parseInt((Math.random() * 1000) % chartColors.length)
        colors.push(chartColors[chartIndex])
    }else if(dataCount > 1){
        //generate randomized chart colors
        let selectedColors = []
        let counter = 0
        
        while(counter < dataCount){
            let chartIndex = parseInt((Math.random() * 1000) % chartColors.length)

            if(selectedColors.includes(chartIndex)){
                continue;
            }else{
                //push color into selected colors array
                selectedColors.push(chartIndex)
                colors.push(chartColors[chartIndex])

                ++counter
            }
        }
    }

    return colors
}

/**
 * This function is used to give a student a result grade
 * @param {number} mark This is the mark to be graded
 * @param {string} exam_type This is the type of exams to be graded with
 * @return {string} returns the grade
 */
function giveGrade(mark, exam_type="wassce"){
    let grade = ""

    switch(exam_type){
        case "wassce":
            if(mark >= 80){grade = "A1"}
            else if(mark >= 70){grade = "B2"}
            else if(mark >= 65){grade = "B3"}
            else if(mark >= 60){grade = "C4"}
            else if(mark >= 55){grade = "C5"}
            else if(mark >= 50){grade = "C6"}
            else if(mark >= 45){grade = "D7"}
            else if(mark >= 40){grade = "E8"}
            else {grade = "F9"}
            
            break
        case "ctvet":
            if(mark >= 80){grade = "D"}
            else if(mark >= 60){grade = "C"}
            else if(mark >= 40){grade = "P"}
            else {grade = "F"}
            
            break
    }

    return grade
}

/**
 * This function checks if a string is in json format or not
 * @param {Object} jsonData This is the data to be checked
 * @returns 
 */
function isJSON(jsonData){
    let isJson = false
    try {
        const jsonObj = JSON.parse(JSON.stringify(jsonData));
        isJson = true
    } catch (e) {
        isJson = false
    }

    return isJson
}
  

/**
 * This function is used to fill a table with data
 * @typedef {Object} TableOptions
 * @property {string} table_id This is the id of the table or element which will receive the filling
 * @property {any[]} result_data This is the array of data to be checked
 * @property {boolean} first_countable This asks if the first section is a js countable column or not
 * @property {boolean} has_mark This checks if the table has a mark
 * @property {number} mark_index This is the initial index of the mark so it is added automatically [not in array format]
 * @property {string} mark_result_type This is the type of grade that should be used for the marks
 * @property {Array} options This is an extra options to be added to specific indexes. Has the attribute and the value
 * @property {string[]} reject This is the columns not to be displayed. Usually can be found in the options
 * @param {TableOptions} tableOptions The table's options
 */
function fillTable({
        table_id, result_data, first_countable=true, has_mark,
        mark_index, mark_first=false, mark_result_type="wassce",
        options = [], reject = null
    }){
    const tbody = $("#" + table_id).find("tbody")
    $(tbody).html("")
    const isArray = typeof result_data[0] === "object" ? true : false
    let reduceMarkIndex = false

    if(isArray !== null && isArray){
        const keys = removeKeys(Object.keys(result_data[0]), reject);
        const indexes = getIndexes(options, mark_index);

        for(i = 0; i < result_data.length; i++){
            let tr = "<tr>"

            if(first_countable){
                tr += "<td>" + (i+1) + "</td>"

                if(mark_index && !reduceMarkIndex) {--mark_index; reduceMarkIndex = true}
            }

            for(j = 0; j < keys.length; j++){
                if((has_mark && (mark_index - 1) == j) && mark_first === true){
                    tr += "<td>" + giveGrade(result_data[i][keys[j]], mark_result_type) + "</td>\n"
                }
                
                if(indexes && indexes.some(item => item.indexKey === j)){
                    const index = indexes.find(item => item.indexKey === j);
                    const actual = options[index.position];
                    let text = result_data[i][keys[j]];

                    tr += "<td";
                    tr += (actual.class || actual.class != "" ? " class=\"" + actual.class + "\"" : "");

                    if(actual.attributes){
                        for(k = 0; k < actual.attributes.length; k++){
                            const attr_text = actual.attributes[k].value;
                            tr += " " + actual.attributes[k].name + " = \"" + result_data[i][attr_text] + "\""
                        }
                    }

                    if(actual.format_function && typeof window[actual.format_function] == "function"){
                        text = window[actual.format_function](text);
                    }

                    tr += ">" + text + "</td>\n";
                    
                }else{
                    tr += "<td>" + result_data[i][keys[j]] + "</td>\n"
                }

                if((has_mark && (mark_index - 1) == j) && mark_first === false){
                    tr += "<td>" + giveGrade(result_data[i][keys[j]], mark_result_type) + "</td>"
                }
                
            }

            tr += "</tr>"

            $(tbody).append(tr)
        }
    }else{
        alert_box("Table cannot be populated because there are no keys to generate for a table", "warning color-black", 10)
    }
}

/**
 * This is used to remove keys from an array of keys
 * @prop {object} keys
 * @prop {object} reject
 * @return {object}
 */
function removeKeys(keys, reject){
    if(reject !== null || (Array.isArray(reject) && reject.length > 0))
        return keys.filter(key => !reject.includes(key));
    else
        return keys;
}

/**
 * This gets the indexes of the table options
 * @param {Array} options The options array or object to be used
 * @return {Array|null}
 */
function getIndexes(options, mark_index){
    let result = null;

    if(Array.isArray(options) && options.length > 0){
        result = [];

        options.forEach((item, i) => {
            if ('index' in item) {
                if(item.index == "mark_index"){
                    item.index = mark_index;
                }

                item.index = item.index - 1;
                result.push({ indexKey: item.index, position: i });
            }
        });
    }

    return result;
}

/**
 * This creates a table data with some options
 * @param {object} data The option data 
 * @return {string}
 */
function createOptionsTD(data){

}

/**
 * The function is used to format the id of a program into the form PID XXXX
 * @param string|int $subject_id This is the id to be converted
 * @param string $prefix The prefix is used to provide the pre-text of the identifier
 * @param bool $reverse This tells if it should convert the item id to integer
 * @return string|int Returns the formatted program id (string or int)
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
 * This function is used to insert a single table row with the empty tr
 * @param {string} message This is the message to be displayed to the user in the row
 * @param {number} colspan This receives the span of the column of the table
 * @param {boolean} auto_hide This hides the field when created
 */
function insertEmptyTableLabel(message, colspan, auto_hide = false){
    return "<tr class='empty" + (auto_hide ? ' no_disp' : '') + "'><td colspan='" + colspan + "'>" + message + "</td></tr>"
}

/**
 * This is used to delete a set of results with the specified token
 * @param {string} token Token of the result set
 * @param {string} table The table from which token should delete
 */
async function deleteTokenResults(token, table="results"){
    const response = await $.ajax({
        url: "./submit.php",
        data: {submit: "delete_token", token: token, table: table},
        timeout: 30000,
        success: function(response){
            if(response == "success"){
                alert_box("Result ready for re-entry", "secondary");
            }else{
                alert_box(response, "danger");
                console.log(response);
            }
        },
        error: function(xhr){
            console.log(xhr);
            alert_box("Unknown error occured", "danger");
        }
    })
}

/**
 * Creates a result header for the current result slip
 * @param {string} token The token to be used
 * @param {number} course_id The subject id
 * @param {number} program_id The class
 * @param {number} semester The current semester
 * @param {number} exam_year The class year
 */
async function create_result_head(token, course_id, program_id, semester, exam_year){
    let response_  = false;
    let academic_year = $("label[for=academic_year] #academic_year").length > 0 ? $("#academic_year").val() : 0;
    await $.ajax({
        url:"./submit.php",
        data: {
            result_token: token, course_id: course_id, program_id: program_id,
            semester: semester, exam_year: exam_year, submit: "submit_result_head",
            academic_year: academic_year
        },
        method: "POST",
        success:function(response){
            if(response == "success"){
                response_ = true;
                console.log("result slip head created");
            }else{
                alert_box("Result slip head failed to create on init", "danger");
                console.log(response);
            }
        },
        error: function(xhr){
            console.log(xhr);
            alert_box("Unknown error! Refer to logs", "danger");
        }
    })

    return response_;
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
 * Get the next available editable content
 * @param {*} $current The current element
 * @param {string} editable_elements The editabke element group
 * @returns 
 */
function getNextEditableElement($current, editable_elements = ".class_score, .exam_score") {
    let $allEditableElements = $(editable_elements);
    let currentIndex = $allEditableElements.index($current);
    let nextIndex = (currentIndex + 1) % $allEditableElements.length;
    return $allEditableElements.eq(nextIndex);
}

/**
 * This gets all the total scores and then provides positions for them
 * @param {jQuery} table_element The table element
 */
function assignPositions(table_element){
    // Extract scores and their indices
    const total_scores = table_element.find("td.total_score").map(function() {
        return {
            score: parseFloat($(this).text()),
            index: $(this).closest('tr').index()
        };
    }).get();

    // Sort scores in descending order
    total_scores.sort((a, b) => b.score - a.score);

    // successfully provides the position value
    let current_position = 0;
    let last_grade = -1;

    // Assign positions based on sorted scores
    total_scores.forEach((item, position) => {
        if(item.score != last_grade){
            last_grade = item.score;
            current_position = position + 1;
        }
        table_element.find("tr").eq(item.index + 1).find("td.position").text(positionFormat(current_position)).attr("data-position-value",current_position);
    });
}

/**
 * Returns a promise
 * @param {*} ms Time in miliseconds
 * @returns {Promise}
 */
function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}