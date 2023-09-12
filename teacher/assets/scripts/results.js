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

function blurOnEnter(element){
    $(element).keydown(function(event){
        if(event.key === "Enter"){
            event.preventDefault()
            $(this).blur()
        }
    })
}

$(".reset_table").click(function(){
    $("#result_slip").find(".class_score, .exam_score").html("0")
    $("#result_slip").find(".class_score").blur()
    $("#table_status").html("")
})

$("#search").keyup(function(){
    const searchText = $(this).val().toLowerCase()
    $("table#result_slip tbody tr").each(function(){
        const rowData = $(this).text().toLowerCase()
        if(rowData.indexOf(searchText) === -1){
            $(this).hide()
        }else{
            $(this).show()
        }
    })
})

$("form").submit(function(e){
    e.preventDefault()
    const cid = $("select[name=class]").val()
    const yid = $("select[name=year]").val()
    const columns = $("#result_slip thead td").length

    $.ajax({
        url: $(this).attr("action"),
        type: "GET",
        data: {
            submit: $(this).find("button[name=submit]").val(), class: cid, year: yid
        },
        beforeSend: function(){
            $("#result_slip tbody").html(insertEmptyTableLabel("Fetching class data, please wait a bit...", columns))
            $("#result_slip tfoot").addClass("no_disp")
            $("#subject_form").addClass("no_disp")
        },
        success: function(response){
            response = JSON.parse(JSON.stringify(response))
            $("select[name=year]").attr("data-selected-year", yid)
            if(response["status"] == false){
                $("#result_slip tbody").html(insertEmptyTableLabel(response["message"], columns))
                $("#result_slip tfoot").addClass("no_disp")
                $("#sub_term_form, #search_form").addClass("no_disp")
            }else{
                $("#head_class_name").html($("select[name=class] option:selected").text())
                $("#sub_term_form, #result_slip tfoot").removeClass("no_disp")

                //fill table
                const tableData = response["message"]
                $("#table_section").addClass("no_disp")
                $("#result_slip tbody").html("")
                for(var i=0; i < tableData.length; i++){
                    let tr = "<tr class=\"p-lg\">"
                        tr += "<td>" + tableData[i]["indexNumber"] + "</td>"
                        tr += "<td>" + tableData[i]["Lastname"] + " " + tableData[i]["Othernames"] + "</td>"
                        tr += "<td contenteditable=\"true\" class=\"white class_score\" data-max=\"30\" onblur=\"examScoreBlur($(this))\" onkeydown=\"blurOnEnter($(this))\">0</td>"
                        tr += "<td contenteditable=\"true\" class=\"white exam_score\" data-max=\"70\" onblur=\"examScoreBlur($(this))\" onkeydown=\"blurOnEnter($(this))\">0</td>"
                        tr += "<td class=\"total_score\">0</td>"
                        tr += "<td class=\"grade\">" + giveGrade(0,$("input#result_type").val()) + "</td>"
                    tr += "</tr>"

                    $("#result_slip tbody").append(tr)
                }

                //reset form fields
                $("form select").prop("selectedIndex", 0)
            }
        },
        error: function(xhr){
            let message = ""
            if(xhr.statusText == "timeout"){
                message = "Connection was timed out due to a slow network. Please check and try again"
            }else if(xhr.statusText == "parsererror"){
                message = "Response recieve is incorrect. Please try again after a short while"
            }else{
                message = xhr.responseText
            }

            alert_box(message, "danger", 8)
        }
    })
})

$("select[name=class]").change(function(){
    const index = $(this).val()
    if(index === ""){
        $("select[name=subject]").prop("selectedIndex",0);
        $("select[name=subject] option").each(function(){
            $(this).show()
        })
    }else{
        $("select[name=subject] option").each(function(){
            if($(this).val() != ""){
                if($(this).attr("data-pid") == index){
                    $(this).show()
                }else{
                    $(this).hide()
                }
            }            
        })

        $(this).attr("data-selected-class", index)
    }
})

$("select[name=subject], select[name=semester]").change(function(){
    if($("select[name=subject]").val() != "" && $("select[name=semester]").val() != ""){
        $("#search_form, #table_section").removeClass("no_disp")
    }else{
        $("#subject_form, #table_section").addClass("no_disp")
    }
})

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
                    exam_year: e_year, semester: sem, isFinal: isLast, program_id: p_id
                },
                type: "POST",
    
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

$("#save_result").click(function(){
    let isHaveToken = false; let token = ""
    const c_id = $("select[name=subject]").val()
    const new_save = true;

    if(c_id == ""){
        alert_box("Course has not been selected yet. Please select one to continue", "danger", 8)
        return
    }

    //get a token from the server
    $.ajax({
        url: "./submit.php",
        data: {submit: "getToken"},
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
                    submit: "save_result", student_index: stud_index, mark: score,
                    exam_mark: e_mark, class_mark: c_mark, course_id: c_id, result_token: token,
                    exam_year: e_year, semester: sem, isFinal: isLast, program_id: p_id, saved: new_save
                },
                type: "POST",
    
                async: false,
                beforeSend: function(){
                    $("#table_status").html("Saving " + stud_index + " | Success: " + success + " of " + total + " | " + "Fail: " + fail + " of " + total)
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

        $("#table_status").html("Save completed! | Success: " + success + " of " + total + " | " + "Fail: " + fail + " of " + total)

        if(fail > 0){
            $("#table_status").append("<br>Failed Saves: " + failIndex.join(", "))
        }
    }
})