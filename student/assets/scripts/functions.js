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
* This function will be used to parse any file type into the database
* 
* @param {string} file_element This takes the element name of the file
* @param {string} form_element This takes a specified form element
* @param {string} submit_element This takes the name of the submit button
* @param {boolean} messageBox This tests if there is a message box
* 
* @return {boolean|string} Returns a boolean value or an error message
*/

async function fileUpload(file_element, form_element, submit_element, messageBox = true){
   formData = new FormData();

   //preparing file and submit values
   file = $(file_element).prop("files")[0];
   file_name = $(file_element).attr("name");
   submit_value = $(submit_element).prop("value");

   //strip form data into array form and attain total data
   form_data = $(form_element).serializeArray();
   formData = toFormData(form_data)

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
       method: $(form_element).attr("method") ? $(form_element).attr("method") : "POST",
       dataType: "text",
       cache: false,
       async: false,
       contentType: false,
       processData: false,
       timeout: 8000,
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

async function jsonFileUpload(file_element, form_element, submit_element, messageBox = true){
    formData = new FormData();
 
    //preparing file and submit values
    file = $(file_element).prop("files")[0];
    file_name = $(file_element).attr("name");
    submit_value = $(submit_element).prop("value");
 
    //strip form data into array form and attain total data
    form_data = $(form_element).serializeArray();
    formData = toFormData(form_data)
 
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
        method: $(form_element).attr("method") ? $(form_element).attr("method") : "POST",
        dataType: "json",
        cache: false,
        async: false,
        contentType: false,
        processData: false,
        timeout: 8000,
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
       method: $(form_element).attr("method") ? $(form_element).attr("method") : "POST",
       dataType: "text",
       cache: false,
       async: false,
       timeout: 8000,
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
       error: function(xhr, textStatus){
           message = "Please check your internet connection and try again";
           type = "error";

           if(textStatus == "timeout"){
            message = "Connection was timed out due to a slow network. Please try again later"
           }

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
* @return {Promise<boolean|array>} returns a boolean value or an array
*/
async function jsonFormSubmit(form_element, submit_element, messageBox = true){
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
 
    let response = null;
    
    await $.ajax({
        url: $(form_element).attr("action"),
        data: formData,
        method: $(form_element).attr("method") ? $(form_element).attr("method") : "POST",
        dataType: "json",
        cache: false,
        timeout: 8000,
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
 * @param {TableOptions} tableOptions The table's options
 */
function fillTable({table_id, result_data, first_countable=true, has_mark, mark_index, mark_result_type="wassce"}){
    const tbody = $("#" + table_id).find("tbody")
    $(tbody).html("")
    const isArray = typeof result_data[0] === "object" ? true : false
    let reduceMarkIndex = false

    if(isArray !== null && isArray){
        const keys = Object.keys(result_data[0])

        for(i = 0; i < result_data.length; i++){
            tr = "<tr>"

            if(first_countable){
                tr += "<td>" + (i+1) + "</td>"

                if(mark_index && !reduceMarkIndex) {--mark_index; reduceMarkIndex = true}
            }

            for(j = 0; j < keys.length; j++){
                if(has_mark && (mark_index - 1) == j){
                    tr += "<td>" + giveGrade(result_data[i][keys[j]], mark_result_type) + "</td>"
                }
                tr += "<td>" + result_data[i][keys[j]] + "</td>"
            }

            tr += "</tr>"

            $(tbody).append(tr)
        }
    }else{
        alert_box("Table cannot be populated because there are no keys to generate for a table", "warning color-black", 10)
    }
}