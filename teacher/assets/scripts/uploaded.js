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
                    $("#sub_term_form, #result_slip tfoot").removeClass("no_disp")

                    if(table_id == "save_data_table"){
                        saved_token = token;
                        tbody.html("");
                        const tableData = response["message"];

                        for(var i=0; i < tableData.length; i++){
                            let tr = "<tr class=\"p-lg\">"
                                tr += "<td>" + tableData[i]["indexNumber"] + "</td>"
                                tr += "<td>" + tableData[i]["Lastname"] + " " + tableData[i]["Othernames"] + "</td>"
                                tr += "<td contenteditable=\"true\" class=\"white class_score\" data-max=\"30\" onblur=\"examScoreBlur($(this))\" onkeydown=\"blurOnEnter($(this))\">" + tableData[i]["class_mark"] + "</td>"
                                tr += "<td contenteditable=\"true\" class=\"white exam_score\" data-max=\"70\" onblur=\"examScoreBlur($(this))\" onkeydown=\"blurOnEnter($(this))\">"+ tableData[i]["exam_mark"] +"</td>"
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

$("#submit_result").click(function(){
    let isHaveToken = false; let token = ""
    const c_id = $("select[name=subject]").val()

    if(c_id == ""){
        alert_box("Course has not been selected yet. Please select one to continue", "danger", 8)
        return
    }

    //get a token from the server
    $.ajax({
        url: "./submit.php",
        data: {submit: "getToken"},
        timeout: 30000,
        async: false,
        beforeSend: function(){
            $("#table_status").html("Generating results token...")
        },
        success: function(response){
            response = JSON.parse(JSON.stringify(response))

            if(response["error"] == true){
                $("#table_status").html("Token Generation failed. Please try again later")
            }else{
                $("#table_status").html("Token acquired.")
                token = response["data"]
                isHaveToken = true
            }
        }
    })

    if(isHaveToken){
        let success = 0; let fail = 0; let failIndex = []
        const total = $("#result_slip tbody tr").length;
        
        $("#result_slip tbody tr").each(function(){
            const stud_index = $(this).children("td:first-child").html()
            const score = $(this).children(".total_score").html()
            const c_mark = $(this).children(".class_score").html()
            const e_mark = $(this).children(".exam_score").html()
            const e_year = $("select[name=year]").attr("data-selected-year")
            const sem = $("select[name=semester").val()
            const isLast = (success+fail+1) == total ? true : false
            const c_id = $("select[name=subject]").val()
            const p_id = $("select[name=class]").attr("data-selected-class")

            $.ajax({
                url: "./submit.php",
                data: {
                    submit: "submit_result", student_index: stud_index, mark: score,
                    exam_mark: e_mark, class_mark: c_mark, course_id: c_id, result_token: token,
                    exam_year: e_year, semester: sem, isFinal: isLast, program_id: p_id, prev_token: saved_token,
                },
                type: "POST",
                timeout: 30000,
                async: false,
                beforeSend: function(){
                    $("#table_status").html("Submitting " + stud_index + " | Success: " + success + " of " + total + " | " + "Fail: " + fail + " of " + total)
                },
                success: function(data){
                    if(data == "true"){
                        success += 1
                    }else{
                        if(data !== "false"){
                            alert_box(data, "danger", 8)
                        }
                        fail += 1
                        failIndex.push(stud_index)
                    }
                },
                error: function(xhr){
                    let message = ""
                    if(xhr.statusText == "timeout"){
                        message = "Connection was timed out due to poor network connection. Please try again later";
                    }else{
                        message = xhr.responseText
                    }

                    alert_box(message, "danger", 8)
                }
            })
        })

        $("#table_status").html("Submission completed! | Success: " + success + " of " + total + " | " + "Fail: " + fail + " of " + total)

        if(fail > 0){
            $("#table_status").append("<br>Failed Submission: " + failIndex.join(", "))
        }
    }
})