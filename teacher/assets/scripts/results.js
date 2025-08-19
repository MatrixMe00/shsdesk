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

    // check back all students
    $(".include-student:not(:checked)").prop("checked", true).change();
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
                        tr += " <td class=\"td-student cursor-p\"><input type=\"checkbox\" id=\"student" + i + "\" class=\"include-student\" checked tabindex=\"-1\" style=\"min-width: unset !important\" /></td>"
                        tr += " <td class=\"index_number\">" + tableData[i]["indexNumber"] + "</td>\n"
                        tr += " <td class=\"lastname\">" + tableData[i]["Lastname"] + " " + tableData[i]["Othernames"] + "</td>\n"
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

$("body").on("click", ".td-student", function(e) {
    // If the actual click was *on* the checkbox, ignore it
    if ($(e.target).is('input[type="checkbox"]')) {
        return;
    }

    const $row = $(this).closest("tr");
    const $checkbox = $row.find('input[type="checkbox"]');

    // Toggle the checkbox state programmatically
    const isChecked = !$checkbox.prop("checked");
    $checkbox.prop("checked", isChecked).change();
});

// Only react to changes (whether programmatic or not)
$("body").on("change", ".td-student input[type='checkbox']", function () {
    const $row = $(this).closest("tr");
    const isChecked = $(this).prop("checked");
    const $tbody = $row.closest("tbody");

    // Apply your row logic
    $row.find(".class_score, .exam_score")
        .attr("contenteditable", isChecked)
        .toggleClass("white", isChecked);

    if (isChecked) {
        // Find the last checked row (excluding current one)
        const $lastChecked = $tbody.find(".td-student input[type='checkbox']:checked")
                                   .not(this)
                                   .closest("tr")
                                   .last();

        if ($lastChecked.length) {
            $row.detach().insertAfter($lastChecked);
        } else {
            $row.detach().prependTo($tbody);
        }

        // Scroll smoothly to new row position
        $row[0].scrollIntoView({
            behavior: "smooth",
            block: "nearest"
        });

    } else {
        // Move unchecked rows to bottom
        $row.detach().appendTo($tbody);
    }
});


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
    let isHaveToken = false; let token = "";
    const c_id = $("select[name=subject]").val();
    const checked_students = $("#result_slip tbody tr:not(.empty) input[type=checkbox]:checked");

    if(checked_students.length == 0){
        alert_box("No student has been selected. Please select at least one to continue", "danger", 8);
        $(this).prop("disabled", false);
        return;
    }

    if(c_id == ""){
        alert_box("Course has not been selected yet. Please select one to continue", "danger", 8);
        $(this).prop("disabled", false);
        return;
    }

    // 1. Get token
    await $.ajax({
        url: "./submit.php",
        data: { submit: "getToken" },
        beforeSend: function(){
            $("#table_status").html("Generating results token...");
        },
        success: function(response){
            response = JSON.parse(JSON.stringify(response));
            if(response["error"] === true){
                $("#table_status").html("Token Generation failed. Please try again later");
            }else{
                $("#table_status").html("Token acquired.");
                token = response["data"];
                isHaveToken = true;
            }
        }
    });

    if(isHaveToken){
        const sem = $("select[name=semester]").val();
        const p_id = $("select[name=class]").attr("data-selected-class");
        const e_year = $("select[name=year]").attr("data-selected-year");
        const academic_year = $("#academic_year").val();

        // 2. Build student data array
        let students = [];
        checked_students.each(function(){
            const $row = $(this).closest("tr");
            const stud_index = $row.children(".index_number").html();
            const score = parseFloat($row.children(".total_score").html()).toFixed(1);
            const c_mark = parseFloat($row.children(".class_score").html()).toFixed(1);
            const e_mark = parseFloat($row.children(".exam_score").html()).toFixed(1);
            const position = parseInt($row.children(".position").attr("data-position-value"));

            students.push({
                student_index: stud_index,
                mark: score,
                class_mark: c_mark,
                exam_mark: e_mark,
                position: position
            });
        });

        // 3. Create the result head first
        await create_result_head(token, c_id, p_id, sem, e_year);

        // 4. Send all students in ONE call with simulated progress
        let index = 0;
        const total = students.length;

        // simulate per-student progress messages
        const progressInterval = setInterval(() => {
            if(index < total){
                $("#table_status").html(`Processing ${index+1}/${total}...`);
                index++;
            }
        }, 150);

        try {
            const response = await $.ajax({
                url: "./submit.php",
                type: "POST",
                timeout: 15000,
                data: {
                    submit: "submit_results",
                    students: JSON.stringify(students),
                    course_id: c_id,
                    result_token: token,
                    exam_year: e_year,
                    semester: sem,
                    program_id: p_id,
                    academic_year: academic_year
                }
            });

            clearInterval(progressInterval);
            $("#table_status").html(`Processing ${total}/${total}...`);

            let parsed;
            try {
                parsed = JSON.parse(response);
            } catch(e) {
                parsed = { success: false, message: response };
            }

            if(parsed.success){
                const failIndex = parsed.failed || [];
                const fail = failIndex.length;
                const success = total - fail;

                $("#table_status").html(
                    `Submission completed! | Success: ${success} of ${total} | Fail: ${fail} of ${total}`
                );

                if(fail > 0){
                    $("#table_status").append("<br>Failed Submission: " + failIndex.join(", "));
                    deleteTokenResults(token); // rollback if failures exist
                } else {
                    alert_box("Results have been submitted for review", "success", 3);
                }
            } else {
                alert_box(parsed.message || "Submission failed", "danger", 8);
                deleteTokenResults(token);
            }

        } catch(error) {
            clearInterval(progressInterval);
            let message = "";
            if(error.statusText === "timeout"){
                message = "Connection timed out. Please try again later.";
            }else{
                message = error.responseText;
            }
            alert_box(message, "danger", 8);
        }
    }

    $(this).prop("disabled", false);
});

$("#save_result").click(async function(){
    $(this).prop("disabled", true);
    let isHaveToken = false; let token = ""
    const c_id = $("select[name=subject]").val();
    const checked_students = $("#result_slip tbody tr:not(.empty) input[type=checkbox]:checked");
    const new_save = true;

    if(checked_students.length == 0){
        alert_box("No student has been selected. Please select at least one to continue", "danger", 8);
        $(this).prop("disabled", false);
        return;
    }
    
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
        const total = checked_students.length;

        const sem = $("select[name=semester]").val();
        const c_id = $("select[name=subject]").val();
        const p_id = $("select[name=class]").attr("data-selected-class");
        const e_year = $("select[name=year]").attr("data-selected-year");
        
        for(let i = 0; i < total; i++){
            const element = checked_students.eq(i).closest("tr");
            const stud_index = element.children(".index_number").html();
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