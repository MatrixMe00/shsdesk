$("body").on("blur",".class_score, .exam_score", function(){
    const element = $(this);
    const tr = element.parents("tr");
    const class_score = tr.find(".class_score")
    const exam_mark = tr.find(".exam_score")
    const total_mark = tr.find(".total_score")
    const grade = tr.find(".grade")
    const result_type = $("input#result_type").val()
    
    if(element.html() === ""){
        element.html("0")
    }else if(parseInt(element.html()) < 0){
        alert_box("Lower than 0 value rejected", "red")
        element.html("0")
    }else if(parseInt(element.html()) > parseInt(element.attr("data-max"))){
        alert_box("Value greater than " + element.attr("data-max") + " rejected", "red")
        element.html("0")
    }

    const total_score = parseFloat(class_score.html()) + parseFloat(exam_mark.html())
    //grab total mark
    total_mark.html(total_score)
    grade.html(giveGrade(total_score, result_type))

    // assign positions
    assignPositions(element.parents("table"));
})

$("body").on("keydown", ".class_score, .exam_score", function(event){
    if(event.key === "Enter"){
        event.preventDefault()
        let $next = getNextEditableElement($(this));
        if ($next.length) {
            $next.focus();
        } else {
            $(this).blur();
        }
    }
})

$("body").on("focus", ".class_score, .exam_score", function(){
    var element = this;
    setTimeout(function() {
        var range = document.createRange();
        range.selectNodeContents(element);
        
        var selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);
    }, 1);
})

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
        async: true,
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
                    let tr = "<tr class=\"p-lg\">\n"
                        tr += " <td>" + tableData[i]["indexNumber"] + "</td>\n"
                        tr += " <td>" + tableData[i]["Lastname"] + " " + tableData[i]["Othernames"] + "</td>\n"
                        tr += " <td contenteditable=\"true\" class=\"white class_score\" data-max=\"30\">0</td>\n"
                        tr += " <td contenteditable=\"true\" class=\"white exam_score\" data-max=\"70\">0</td>\n"
                        tr += " <td class=\"total_score\">0</td>\n"
                        tr += " <td class=\"grade\">" + giveGrade(0,$("input#result_type").val()) + "</td>\n"
                        tr += " <td class=\"position\"></td>"
                        tr += "</tr>\n"

                    $("#result_slip tbody").append(tr)
                }

                //reset form fields
                $("form select").prop("selectedIndex", 0);

                // fill the positions
                assignPositions($("#result_slip"))
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

$("#submit_result").click(async function(){
    $(this).prop("disabled", true);
    let isHaveToken = false; let token = ""
    const c_id = $("select[name=subject]").val()

    if(c_id == ""){
        alert_box("Course has not been selected yet. Please select one to continue", "danger", 8)
        return
    }

    //get a token from the server
    await $.ajax({
        url: "./submit.php",
        data: {submit: "getToken"},
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
        const total = $("#result_slip tbody tr:not(.empty)").length;

        const sem = $("select[name=semester]").val();
        const c_id = $("select[name=subject]").val();
        const p_id = $("select[name=class]").attr("data-selected-class");
        const e_year = $("select[name=year]").attr("data-selected-year");
        const academic_year = $("#academic_year").val();

        // create the result head first
        await create_result_head(
            token, c_id, p_id, sem, e_year
        );
        
        for(let i = 0; i < total; i++){
            const element = $("#result_slip tbody tr").eq(i);
            const stud_index = element.children("td:first-child").html();
            const score = parseFloat(element.children(".total_score").html()).toFixed(1);
            const c_mark = parseFloat(element.children(".class_score").html()).toFixed(1);
            const e_mark = parseFloat(element.children(".exam_score").html()).toFixed(1);
            const isLast = (success+fail+1) == total ? true : false;
            const position = parseInt(element.children(".position").attr("data-position-value"));
            
            try{
                const response = await $.ajax({
                    url: "./submit.php",
                    data: {
                        submit: "submit_result", student_index: stud_index, mark: score,
                        exam_mark: e_mark, class_mark: c_mark, course_id: c_id, result_token: token,
                        exam_year: e_year, semester: sem, isFinal: isLast, program_id: p_id,
                        position: position, academic_year: academic_year
                    },
                    type: "POST",
                    timeout: 5000
                });

                if(response == "true"){
                    success += 1
                }else{
                    if(response !== "false"){
                        alert_box(response, "danger", 8)
                    }
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
        }
    }

    $(this).prop("disabled", false);
})

$("#save_result").click(async function(){
    $(this).prop("disabled", true);
    let isHaveToken = false; let token = ""
    const c_id = $("select[name=subject]").val()
    const new_save = true;

    if(c_id == ""){
        alert_box("Course has not been selected yet. Please select one to continue", "danger", 8)
        return
    }

    //get a token from the server
    await $.ajax({
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
        const total = $("#result_slip tbody tr:not(.empty)").length;

        const sem = $("select[name=semester]").val();
        const c_id = $("select[name=subject]").val();
        const p_id = $("select[name=class]").attr("data-selected-class");
        const e_year = $("select[name=year]").attr("data-selected-year");
        
        for(let i = 0; i < total; i++){
            const element = $("#result_slip tbody tr").eq(i);
            const stud_index = element.children("td:first-child").html();
            const score = parseFloat(element.children(".total_score").html()).toFixed(1);
            const c_mark = parseFloat(element.children(".class_score").html()).toFixed(1);
            const e_mark = parseFloat(element.children(".exam_score").html()).toFixed(1);
            const isLast = (success+fail+1) == total ? true : false;
            
            try{
                const response = await $.ajax({
                    url: "./submit.php",
                    data: {
                        submit: "save_result", student_index: stud_index, mark: score,
                        exam_mark: e_mark, class_mark: c_mark, course_id: c_id, result_token: token,
                        exam_year: e_year, semester: sem, isFinal: isLast, program_id: p_id, saved: new_save
                    },
                    type: "POST",
                    timeout: 5000
                });

                if(response == "true"){
                    success += 1
                }else{
                    if(response !== "false"){
                        alert_box(response, "danger", 8)
                    }
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
        }
    }

    $(this).prop("disabled", false);
})