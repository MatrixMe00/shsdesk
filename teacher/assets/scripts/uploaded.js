saved_token = null
$(document).ready(function(){
    const current_tab = $("#lhs .tab.active").attr("data-current-tab")

    $(".section_btn").click(function(){
        const section_id = $(this).attr("data-section-id");
        $("#lhs .tab.active").attr("data-current-tab",section_id)

        $(this).siblings("button.light").addClass("secondary").removeClass("light")
        $(this).removeClass("secondary").addClass("light")

        $(".btn_section:not(.no_disp)").addClass("no_disp")
        $("#"+section_id).removeClass("no_disp");
    })
    
    $("input[name=search]").keyup(function(){
        const parent = $(this).parents(".btn_section")
        const val = $(this).val()
        const cards = parent.find(".card")
        
        if(val !== ""){
            var displays = 0
            cards.filter(function(){
                const switch_ = $(this).text().toLowerCase().indexOf(val) == -1
                displays = !switch_ ? displays + 1 : displays
                $(this).toggleClass("no_disp", switch_);
            })

            if(displays == 0){
                parent.find(".no-result").removeClass("no_disp")
            }else{
                parent.find(".no-result").addClass("no_disp")
            }
        }else{
            cards.removeClass("no_disp")
            parent.find(".no-result").addClass("no_disp")
        }
    })

    //hide all containers
    $(".btn_section").addClass("no_disp")
    $(".section_btn[data-section-id=" + current_tab + "]").click()
})

function viewData(element){
    const tr = $(element).parents("tr")
    const index = $(tr).children("td:nth-child(1)").html()
    const name = $(tr).children("td:nth-child(2)").html()
    const gender = $(tr).children("td:nth-child(3)").html()
    const mark = $(tr).children("td:nth-child(4)").html()
    const grade = $(tr).children("td:nth-child(5)").html()

    //fill single student  
    $("#class_single .name").html(name)
    $("#class_single .index").html(index)
    $("#class_single .gender").html(gender)
    $("#class_single .mark").html(mark)
    $("#class_single .grade").html(grade)

    $('section#class_single').removeClass('no_disp')
}

//function to change the page from main view to single view and vice versa
function pageChange({index = 0, program_name="", token="", type="", table_id=""}){
    $("#page_title, #classes, #single_class, #cards-section").toggleClass("no_disp")
    $("#cards-section").toggleClass("flex")
    $("#content_wrapper").toggleClass("no_disp")
    $("span#single_class_name").html("")
    $("#table_status").html("")
    // $("#class_list_table tbody").html(insertEmptyTableLabel("Make a search on your class year to proceed", columns))

    $("#single_class table:not(.no_disp)").addClass("no_disp")
    $("#single_class table#" + table_id).removeClass("no_disp")

    if(index > 0){
        $("span#single_class_name").html(program_name + " | " + formatItemId(index, "PID"))
        const cols = $("#" + table_id + " thead td").length
        const tbody = $("#" + table_id).find("tbody");
        const tfoot = tbody.siblings("tfoot");
        const result_type = $("input#result_type").val();

        $.ajax({
            url: "./submit.php",
            data: {
                submit: "pull_results", token_id: token, response_type: type
            },
            timeout: 30000,
            method: "POST",
            beforeSend: function(){
                tbody.html(insertEmptyTableLabel("Fetching results, please wait...",cols))
            },
            success: function(response){
                if(response["error"] == true){
                    tbody.html(insertEmptyTableLabel(response["message"], cols))
                    tfoot.addClass("no_disp")
                    $("#sub_term_form, #search_form").addClass("no_disp")
                }else if(response["error"] == false){
                    $("#sub_term_form, #save_data_table tfoot").removeClass("no_disp")

                    if(table_id == "save_data_table"){
                        saved_token = token;
                        tbody.html("");
                        const tableData = response["message"];

                        for(var i=0; i < tableData.length; i++){
                            let tr = "<tr class=\"p-lg\">"
                                tr += "<td>" + tableData[i]["indexNumber"] + "</td>"
                                tr += "<td>" + tableData[i]["Lastname"] + " " + tableData[i]["Othernames"] + "</td>"
                                tr += "<td contenteditable=\"true\" class=\"white class_score\" data-max=\"30\" data-initial=\"" + tableData[i]["class_mark"] + "\" onblur=\"examScoreBlur($(this))\" onkeydown=\"blurOnEnter($(this))\">" + tableData[i]["class_mark"] + "</td>"
                                tr += "<td contenteditable=\"true\" class=\"white exam_score\" data-max=\"70\" data-initial=\"" + tableData[i]["exam_mark"] + "\" onblur=\"examScoreBlur($(this))\" onkeydown=\"blurOnEnter($(this))\">"+ tableData[i]["exam_mark"] +"</td>"
                                tr += "<td class=\"total_score\">" + tableData[i]["mark"] + "</td>"
                                tr += "<td class=\"grade\">" + giveGrade(tableData[i]["mark"],$("input#result_type").val()) + "</td>"
                            tr += "</tr>"

                            tbody.append(tr)
                        }
                    }else{
                        fillTable({
                            table_id: "class_list_table", result_data: response["message"], first_countable: false,
                            has_mark: true, mark_index: 4, mark_first: true, mark_result_type: result_type
                        })
    
                        //add a view td
                        $("#class_list_table tbody tr").append("<td><span class=\"item-event view\" onclick=\"viewData($(this))\">View</span></td>")
                    }
                }else{
                    tbody.html(insertEmptyTableLabel(response, cols))
                }
            },
            error: function(xhr){
                let message = xhr.responseText

                if(xhr.statusText == "timeout"){
                    message = "Connection was timed out due to slow network response";
                }

                alert_box(message, "danger")
            }
        })
    }else{
        $("button[name=reset], label[for=search_table]").addClass("no_disp")
    }
}

$("#submit_result").click(async function(){
    let isHaveToken = false; let token = ""
    let c_id = 0, p_id = 0, e_year = 0, sem = 0;

    //search for other details about the school
    await getPCES(saved_token).then((response) => {
        response = JSON.parse(JSON.stringify(response))
        c_id = parseInt(response["course_id"]); p_id = parseInt(response["program_id"])
        e_year = parseInt(response["exam_year"]); sem = parseInt(response["semester"])
    }).catch((err)=>{
        alert_box(err, "danger", 7)
    })

    if(c_id <= 0){
        alert_box("Course has not been selected yet. Please select one to continue", "danger", 8)
        return
    }

    //get a token from the server
    token = await getAToken("table_status").then((response)=>{
        isHaveToken = true; return response;
    }).catch((err)=>{
        alert_box(err,"danger"); isHaveToken = false;
        return "";
    })

    if(isHaveToken){
        let success = 0; let fail = 0; let failIndex = []
        const total = $("#save_data_table tbody tr").length;
        
        for(let i = 0; i < total; i++){
            const element = $("#save_data_table tbody tr").eq(i);
            const stud_index = element.children("td:first-child").html();
            const score = parseFloat(element.children(".total_score").html()).toFixed(1);
            const c_mark = parseFloat(element.children(".class_score").html()).toFixed(1);
            const e_mark = parseFloat(element.children(".exam_score").html()).toFixed(1);
            const isLast = (success+fail+1) == total ? true : false
            
            try{
                const response = await $.ajax({
                    url: "./submit.php",
                    data: {
                        submit: "submit_result", student_index: stud_index, mark: score,
                        exam_mark: e_mark, class_mark: c_mark, course_id: c_id, result_token: token,
                        exam_year: e_year, semester: sem, isFinal: isLast, program_id: p_id, prev_token: saved_token,
                    },
                    type: "POST",
                    timeout: 5000
                });

                if(response == "true"){
                    success += 1
                }else{
                    fail += 1
                    failIndex.push(stud_index)
                }

                if(isLast){
                    alert_box("Process completed", "success", 3);
                }
            }catch(error){
                let message = "";
                if(error.statusText == "timeout"){
                    message = "Connection was timed out due to poor network connection. Please try again later";
                }else{
                    message = error.responseText;
                }

                alert_box(message, "danger", 8);
            }
            $("#table_status").html("Saving " + stud_index + " | Success: " + success + " of " + total + " | " + "Fail: " + fail + " of " + total);
        }

        $("#table_status").html("Submission completed! | Success: " + success + " of " + total + " | " + "Fail: " + fail + " of " + total)

        if(fail > 0){
            $("#table_status").append("<br>Failed Submission: " + failIndex.join(", "));
            deleteTokenResults(token);
            alert_box(fail + " results could not be submitted", "error", 8);
        }
    }
})

$("#save_result").click(async function(){
    let isHaveToken = false; let token = saved_token
    let c_id = 0, p_id = 0, e_year = 0, sem = 0;
    const new_save = false;

    //search for other details about the school
    await getPCES(saved_token).then((response) => {
        response = JSON.parse(JSON.stringify(response))
        c_id = parseInt(response["course_id"]); p_id = parseInt(response["program_id"])
        e_year = parseInt(response["exam_year"]); sem = parseInt(response["semester"])
    }).catch((err)=>{
        alert_box(err, "danger", 7)
    })

    if(c_id <= 0){
        alert_box("Course has not been selected yet. Please select one to continue", "danger", 8)
        return
    }

    //get a token from the server
    if(token !== ""){
        isHaveToken = true;
    }

    if(isHaveToken){
        let success = 0; let fail = 0; let failIndex = []
        const total = $("#save_data_table tbody tr").length;
        
        for(let i = 0; i < total; i++){
            const element = $("#save_data_table tbody tr").eq(i);
            const stud_index = element.children("td:first-child").html();
            const score = parseFloat(element.children(".total_score").html()).toFixed(1);
            const c_mark = parseFloat(element.children(".class_score").html()).toFixed(1);
            const e_mark = parseFloat(element.children(".exam_score").html()).toFixed(1);
            const isLast = (success+fail+1) == total ? true : false
            
            try{
                const response = await $.ajax({
                    url: "./submit.php",
                    data: {
                        submit: "save_result", student_index: stud_index, mark: score,
                        exam_mark: e_mark, class_mark: c_mark, course_id: c_id, result_token: token,
                        exam_year: e_year, semester: sem, isFinal: isLast, program_id: p_id, prev_token: saved_token,
                        saved: new_save
                    },
                    type: "POST",
                    timeout: 5000
                });

                if(response == "true"){
                    success += 1
                }else{
                    fail += 1
                    failIndex.push(stud_index)
                }

                if(isLast){
                    alert_box("Process completed", "success", 3);
                }
            }catch(error){
                let message = "";
                if(error.statusText == "timeout"){
                    message = "Connection was timed out due to poor network connection. Please try again later";
                }else{
                    message = error.responseText;
                }

                alert_box(message, "danger", 8);
            }
            $("#table_status").html("Saving " + stud_index + " | Success: " + success + " of " + total + " | " + "Fail: " + fail + " of " + total);
        }

        $("#table_status").html("Save completed! | Success: " + success + " of " + total + " | " + "Fail: " + fail + " of " + total)

        if(fail > 0){
            $("#table_status").append("<br>Failed Saves: " + failIndex.join(", "));
            deleteTokenResults(token);
            alert_box(fail + " results could not be saved", "error", 8);
        }
    }
})

function getAToken(response_id){
    return new  Promise((resolve, reject) => {
        $.ajax({
            url: "./submit.php",
            data: {submit: "getToken"},
            timeout: 30000,
            async: false,
            beforeSend: function(){
                if(response_id !== ""){
                    $("#" + response_id).html("Generating results token...")
                }
            },
            success: function(response){
                response = JSON.parse(JSON.stringify(response))

                if(response["error"] == true){
                    if(response_id !== ""){
                        $("#" + response_id).html("Token Generation failed. Please try again later")
                    }else{
                        reject("Token Generation failed. Please try again later")
                    }
                }else{
                    if(response_id !== ""){
                        $("#" + response_id).html("Token acquired.","success",3)
                    }
                    resolve(response["data"])
                }
            },
            error: function(xhr){
                let message = xhr.responseText

                if(xhr.statusText == "timeout"){
                    message = "Connection was timed out because of a slow network detected";
                }

                reject(message);
            }
        })
    })
}

function getPCES(token_id){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "./submit.php",
            data: {submit: "get_pces", token: token_id},
            method: "get",
            timeout: 30000,
            success: function(response){
                response = JSON.parse(JSON.stringify(response))

                if(response["error"] == true){
                    reject(response["message"])
                }else{
                    resolve(response["message"]);
                }
            },
            error: function(xhr){
                let message = xhr.responsetext

                if(xhr.statusText == "timeout"){
                    message = "Connection timed out due to slow network detected. Please check your internet connection and try again";
                }

                reject(message);
            }
        })
    })
}

$(".reset_table").click(function(){
    $("#save_data_table").find(".class_score, .exam_score").html(function(){
        return $(this).attr("data-initial")
    })
    $("#save_data_table").find(".class_score").blur()
    $("#table_status").html("")
})

$("button.pass_to_save").click(function(){
    const alert_message = "Clicking 'Yes' would prepare these records to be editable. Do you wish to proceed?"
    const token = $(this).attr("data-token")
    const mode = "transfer";

    showConfirmBox({
        token: token, mode: mode, message: alert_message
    })
})

$("button.del_save").click(function(){
    const alert_message = "Are you sure you want to delete this record?"
    const token = $(this).attr("data-token")
    const mode = "delete";

    showConfirmBox({
        token: token, mode: mode, message: alert_message
    })
})

function showConfirmBox({token, mode, message}){
    const confirm_box = $("section#confirm_box");

    //fill the confirm box form
    confirm_box.find("input[name=token]").val(token)
    confirm_box.find("input[name=mode]").val(mode)

    //show the display with message
    confirm_box.find(".message p").html(message)
    confirm_box.removeClass("no_disp")
}

$("form[name=confirm_box]").submit(function(e){
    e.preventDefault();
    const mode = $(this).find("input[name=mode]").val()
    
    const response = formSubmit($(this), $(this).find("button[name=submit]"), false);

    if(response == true){
        let message = "";
        if(mode == "delete"){
            message = "Record was deleted successfully";
        }else if(mode == "transfer"){
            message = "Your records have been saved for editing.";
        }

        alert_box(message, "success", 3)
        $("#lhs .active").click()
    }else{
        alert_box(response, "danger")
    }
})

function blurOnEnter(element){
    $(element).keydown(function(event){
        if(event.key === "Enter"){
            event.preventDefault()
            $(this).blur()
        }
    })
}

function examScoreBlur(element){
    const tr = $(element).parents("tr")
    const class_score = $(tr).find(".class_score")
    const exam_mark = $(tr).find(".exam_score")
    const total_mark = $(tr).find(".total_score")
    const grade = $(tr).find(".grade")
    const result_type = $("input#result_type").val()
    
    if($(element).html() === ""){
        $(element).html("0")
    }else if(parseInt($(element).html()) < 0){
        alert_box("Lower than 0 value rejected", "red")
        $(element).html("0")
    }else if(parseInt($(element).html()) > parseInt($(element).attr("data-max"))){
        alert_box("Value greater than " + $(element).attr("data-max") + " rejected", "red")
        $(element).html("0")
    }

    const total_score = parseFloat($(class_score).html()) + parseFloat($(exam_mark).html())
    //grab total mark
    $(total_mark).html(total_score)
    $(grade).html(giveGrade(total_score, result_type))
}