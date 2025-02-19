var current_section = $("#lhs .item.active[data-tab]").attr("data-tab");

$(document).ready(function(){
    ajaxCall_ = null;

    //direct displays to the control buttons
    $(".control_btn").click(function(){
        const section = $(this).attr("data-section");

        $(".section_box:not(.no_disp)").addClass("no_disp");
        $("#lhs .item.active").attr("data-tab", section);
        $("#" + $(this).attr("data-section")).removeClass("no_disp");

        $(".control_btn:not(.plain)").addClass("plain");
        $(this).removeClass("plain");

        if($(this).attr("data-refresh") && $(this).attr("data-refresh") === "true"){
            $("#lhs.menu .item.active").click();
        }
    })

    $(".control_btn[data-section=" + current_section + "]").click()

    $("#courseIDs label input").change(function(){
        let active_value = $(this).prop("checked")
        let check_value = $(this).prop("value")
        let parentForm = $(this).parents("form")

        //get content of course ids
        let course_ids = $(parentForm).find("input[name=course_ids]").val()
        
        if(active_value){
            course_ids = course_ids.concat(check_value," ")
        }else{
            course_ids = course_ids.replace(check_value + " ", '')
        }

        //push new response into this course id element
        parentForm.find("input[name=course_ids]").val(course_ids)
    })

    $("form[name=addProgramForm]").submit(function(e){
        e.preventDefault()
        
        const response = jsonFormSubmit($(this), $(this).find("button[name=submit]"))
        response.then((return_data)=>{
            return_data = JSON.parse(JSON.stringify(return_data))
            
            messageBoxTimeout($(this).prop("name"), return_data["message"], return_data["status"] ? "success" : "error")

            if(return_data["status"] === true){
                num = parseInt($(".content.orange").find("h2").html()) + 1
                $(".content.orange").find("h2").html(num)

                $("#viewAll").attr("data-refresh","true")

                setTimeout(()=> {
                    $(this).find("input:not([type=checkbox], [name=school_id])").val("")
                    $(this).find("input[type=checkbox]").prop("checked", false)
                }, 3000)
            }         
        })
    })

    $(".item-event").click(function(){
        item_id = $(this).parents("tr").attr("data-item-id")
        
        if($(this).hasClass("edit")){
            $("#updateProgram").removeClass("no_disp")
            
            ajaxCall_ = $.ajax({
                url: $("#updateProgram form").attr("action"),
                data: {
                    program_id: item_id,
                    submit: "getProgram"
                },
                timeout: 30000,
                beforeSend: function(){
                    $("#updateLoader").toggleClass("no_disp flex")
                    $("#updateProgram #getLoader").html(loadDisplay({
                        circular: true, 
                        circleColor: "light"
                    }));
                },
                success: function(data){
                    $("#updateProgram #getLoader").html("")
                    $("#updateProgram #getLoader").toggleClass("no_disp flex")
                    $("#updateProgram form").removeClass("no_disp")

                    data = JSON.parse(JSON.stringify(data))
                    const results = data["results"]

                    if(data["status"] === true){
                        const course_ids = results["course_ids"].split(" ");

                        //remove empty element at back
                        course_ids[course_ids.length - 1] == "" ? course_ids.pop() : '';
                        
                        $("#updateProgram input[name=program_id]").val(results["program_id"])
                        $("#updateProgram input[name=program_name]").val(results["program_name"])
                        $("#updateProgram select[name=associate_program]").val(results["associate_program"])
                        $("#updateProgram input[type=checkbox]").each((index, element)=>{
                            element_val = $(element).val()
                            if(course_ids.includes(element_val) === true){
                                $(element).prop('checked', true)
                            }
                        })
                        $("#updateProgram input[name=short_form]").val(results["short_form"])
                        $("#updateProgram input[name=course_ids]").val(results["course_ids"])  
                    }else{
                        alert(results)
                    }
                },
                error: function(xhr, textStatus){
                    let message = ""

                    if(textStatus === "timeout"){
                        message = "Connection was timed out due to a slow network. Please check your internet connection and try again"
                    }else if(textStatus === "parsererror"){
                        message = "Status: Data returned cannot be parsed. Please try again later else contact admin for help"
                    }else{
                        message = "Status: " + xhr.responseText
                    }

                    alert_box(message, "warning color-dark", 10)
                }
            })
            
        }else if($(this).hasClass("delete")){
            let program_name = $(this).parents("tr").children("td:nth-child(2)").html()
            $("#table_del").removeClass("no_disp")

            //message to display
            $("#table_del p#warning_content").html("Do you want to remove <b>" + program_name + "</b> from your records?");

            //fill form with needed details
            $("#delete_form input[name=item_id]").val(item_id)
            $("#delete_form input[name=table_name]").val("program")
            $("#delete_form input[name=db]").val("shsdesk2")
            $("#delete_form input[name=column_name]").val("program_id")

            $(this).parents("tr").addClass("remove_marker");
        }else if($(this).hasClass("approve") || $(this).hasClass("reject")){
            const item_status = $(this).hasClass("approve") ? "accepted" : "rejected"
            const table_foot = $(this).parents("table").find("tfoot")
            const this_row = $(this).parents("tr")
            
            $.ajax({
                url: $("#updateProgram form").attr("action"),
                data: {
                    record_token: item_id,record_status: item_status, 
                    submit: "result_status_change"
                },
                type: "POST",
                dataType: "json",
                timeout: 30000,
                cache: false,
                beforeSend: function(){
                    $(table_foot).addClass("sticky top secondary w-full")
                    $(table_foot).find(".res_stat").html("Status: Updating...")
                },
                success: function(response){
                    $(table_foot).removeClass("sticky top secondary w-full")

                    if(typeof response["status"] && response['status'] === true){
                        $(this_row).remove()
                        $(table_foot).find(".res_stat").html("The record was " + response["rec_stat"])
                        
                        //refresh this page
                        $("#lhs .item.active").click()
                    }else{
                        $(table_foot).find(".res_stat").html(response["message"])
                    }
                },
                error: function(xhr, textStatus){
                    let message = ""
                    if(textStatus === "timeout"){
                        message = "Status: Connection was timed out. Please check your network connection and try again"
                    }else if(textStatus === "parsererror"){
                        message = "Status: Data returned cannot be parsed. Please try again later else contact admin for help"
                    }else{
                        message = "Status: " + xhr.responseText
                    }
                    
                    $(table_foot).find(".res_stat").html(message)
                }
            })
        }else if($(this).hasClass("view")){
            $("#view_results").removeClass("no_disp");
            const parent = $(this).parents("tr");
            const p_class = parent.find("td.program_name").html();
            const p_subject = parent.find("td.course_name").html();
            const p_year = parent.find("td.exam_year").html();
            const p_sem = parent.find("td.semester").html();

            $("#view_results table tfoot #year_sem").html("Class Year: " + p_year + " | Class Sem: " + p_sem);
            $("#view_results #topic").html(p_subject + " results for " + p_class);

            $.ajax({
                url: "./admin/submit.php",
                data: {submit: "view_results", token_id: item_id},
                timeout: 30000,
                beforeSend: function(){
                    const tr = "<tr class='empty'><td colspan='6'>Fetching results, please wait...</td></tr>"
                    $("#view_results table tbody").html(tr)
                },
                success: function(response){
                    response = JSON.parse(JSON.stringify(response))
                    $("#view_results table tbody").html("")

                    if(response.error){
                        const tr_error = "<tr class='empty'><td colspan='6'>" + response["message"] + "</td></tr>"
                        $("#view_results table tbody").html(tr_error);
                    }else{
                        const data = response.message;
                        data.forEach((result) => {
                            const tr = "<tr data-belongs-to=\"" + item_id + "\">\n" +
                                            "<td class=\"index_number\">" +  result.indexNumber + "</td>\n" +
                                            "<td class=\"fullname\">" +  result.fullname + "</td>\n" + 
                                            "<td class=\"class_mark\">" +  result.class_mark + "</td>\n" + 
                                            "<td class=\"exam_mark\">" +  result.exam_mark + "</td>\n" + 
                                            "<td class=\"total_mark\">" +  result.mark + "</td>\n" + 
                                            "<td class=\"grade\">" +  result.grade + "</td>\n" + 
                                            "<td class=\"position\">" + result.position + "</td>\n" +
                                            ((result.can_remove) ? "<td><span class=\"item-event remove-score\" data-item-id=\"" + result.id + "\">Remove</span></td>\n" : "") +
                                    "</tr>\n";
                            $("#view_results table tbody").append(tr);
                        });
                        
                        // $("#view_results table")
                    }
                },
                error: function(xhr){
                    let message = xhr.responseText

                    if(xhr.textStatus == "timeout"){
                        message = "Server communication was halted due to slow network. Please check your network and try again"
                    }

                    alert_box(message, "danger", 7)
                }
            })
        }else if($(this).hasClass("remove")){
            let program_name = $(this).parents("tr").children("td:nth-child(2)").html()
            let subject_name = $(this).parents("tr").children("td:nth-child(3)").html()
            $("#table_del").removeClass("no_disp")

            //message to display
            $("#table_del p#warning_content").html("Do you want to remove <b>" + subject_name + "</b> uploaded for <b>" + program_name + "</b> from your records?");

            //fill form with needed details
            $("#delete_form input[name=item_id]").val(item_id)
            $("#delete_form input[name=table_name]").val("recordapproval")
            $("#delete_form input[name=column_name]").val("result_token")
            $("#delete_form input[name=db]").val("shsdesk2")

            $(this).parents("tr").addClass("remove_marker");
        }
    })

    $("body").on("click", ".remove-score", function(){
        const parent = $(this).parents("tr");
        const item_id = $(this).attr("data-item-id");
        const result_token = parent.attr("data-belongs-to");
        const student_name = parent.find("td.fullname").html();

        //message to display
        $("#table_del p#warning_content").html("Do you want to remove <b>" + student_name + "</b> from this record data?");

        //fill form with needed details
        $("#delete_form input[name=item_id]").val(item_id + "-" + result_token);
        $("#delete_form input[name=table_name]").val("results");
        $("#delete_form input[name=db]").val("shsdesk2");
        $("#delete_form input[name=column_name]").val("id");

        parent.addClass("remove_marker");

        // show delete modal
        $("#table_del").removeClass("no_disp");
    })

    $("form[name=delete_form]").submit(function(e){
        e.preventDefault();

        const response = formSubmit($(this), null, false)
        
        if(response === true || response === "true"){
            $("tr.remove_marker").remove();

            alert_box("Data deleted successfully", "success");

            $("#table_del").addClass("no_disp")
        }else{
            $("tr.remove_marker").removeClass("remove_marker")
            console.log(response);
        }
    })

    $("#cancelUpdate").click(function(){
        if(ajaxCall_){
            ajaxCall_.abort()
        }else{
            alert("no ajax")
        }

        $("#updateProgram").addClass("no_disp")
    })

    $("form[name=updateProgramForm]").submit(function(e){
        e.preventDefault()

        const response = jsonFormSubmit($(this), $(this).find("button[name=submit]"))
        response.then((return_data)=>{
            return_data = JSON.parse(JSON.stringify(return_data))
            
            messageBoxTimeout($(this).prop("name"), return_data["message"], return_data["status"] ? "success" : "error")

            if(return_data["status"] === true || return_data["status"] === "true"){                
                $("button.control_btn").attr("data-refresh","true")
            }

            //refresh page if first course
            if($("form").prop("name") === "addCourseForm" && return_data["isFirst"]){
                location.href = location.href
            }
        })
    })

    //search through a table
    $("label.search-label input[name=search]").keyup(function(){
        const label  = $(this).parents("label");
        const table = $("#" + label.attr("data-table"));

        //table contents
        const table_body = table.find("tbody");

        //search text
        const searchText = $(this).val().toLowerCase();
        
        if(searchText !== ""){        
            $(table_body).children("tr").each(function(){
                const rowData = $(this).text().toLowerCase()
                if(rowData.indexOf(searchText) === -1){
                    $(this).addClass("no_disp")
                }else{
                    $(this).removeClass("no_disp")
                }
            })
            
            //count number of visible results
            const tr = table_body.children("tr:visible");
            
            if(tr.length === 0)
                alert_box("No results were returned", "secondary");
        }else{
            table_body.children("tr.no_disp").removeClass("no_disp");
        }
    })

    // filter reviewed table by academic years
    $("#academic_year_select").change(function(){
        const table = $("#" + $(this).attr("data-table-id"));
        const academic_year_rows = table.find("td.academic_year");
        const value = $(this).val().toLowerCase();

        if(value !== ""){
            academic_year_rows.each(function (index, element) {
                const parent = $(element).parents("tr");
                if($(element).text().toLowerCase() == value){
                    parent.addClass("no_disp");
                }else{
                    parent.removeClass("no_disp");
                }
            });
        }else{
            academic_year_rows.each(function (index, element) {
                const parent = $(element).parents("tr");
                parent.removeClass("no_disp");
            });
        }
    })

    function enable_disabled_buttons(section){
        const selected = section.find("tr.secondary-i").length;
        section.find(".multi_button").prop("disabled", selected < 2);
        section.find(".de_select").toggleClass("no_disp", selected < 2);

        // display the number of selected row in the
        section.find("span.selected_rows").html(selected);
    }

    $("tbody td:not(.options):not(.result-edit)").click(function(){
        const row = $(this).parent();
        const section = $(this).parents("section").first();

        row.toggleClass("secondary-i");

        enable_disabled_buttons(section);
    })

    // deselect all selected columns
    $(".de_select").click(function(){
        const section = $(this).parents("section").first();
        section.find("tr.secondary-i").removeClass("secondary-i");
        enable_disabled_buttons(section);
    })

    // multi select doing
    $(".multi_button").click(async function(){
        const button = $(this);

        // disable sibling elements
        button.siblings("button").prop("disabled", true);

        const event = button.attr("data-event");   // event on the button
        const section = button.parents("section").first(); // section it belongs to
        const selected_rows = section.find("tbody tr.secondary-i");   // the selected rows
        const confirmed = confirm("Are your sure you want to " + event + " these " + selected_rows.length + " rows?");
        const original_text = button.html();

        if(confirmed){
            const data = {};
            let id_name = "";

            if(event == "approve" || event == "reject"){
                data.submit = "result_status_change";
                data.record_status = event == "approve" ? "accepted" : "rejected";
                id_name = "record_token";
            }else if(event == "delete"){
                data.submit = "delete_item";
                data.table_name = button.attr("data-table");
                data.column_name = button.attr("data-del-col");
                data.db = "shsdesk2";
                id_name = "item_id";
            }

            // get the total rows
            const total_rows = selected_rows.length;

            // for each item[row], perform an operation
            for(let i = 0; i < total_rows; i++){
                const element = $(selected_rows[i]);
                const id = element.attr("data-item-id");
                data[id_name] = id;

                const response = await ajaxCall({
                    url: "./admin/submit.php",
                    formData: data,
                    returnType: event == "delete" ? "text" : "json",
                    method: event == "delete" ? "GET" : "POST",
                    beforeSend: function(){
                        button.html("Processing [" + (i + 1) + "]").attr("disabled", true);
                    }
                });

                if(response === "success"){
                    // response for delete
                    element.remove();
                }else if(response.status === true){
                    if(event == "reject"){
                        element.addClass("red-i").removeClass("secondary-i");
                    }else{
                        element.removeClass("red-i secondary-i");
                    }
                }else{
                    alert_box(event == "delete" ? response : response.message);
                }
            }

            alert_box("Changes were successful", "success");
            await delay(2000);  // make some delay before returning text
            button.html(original_text);
            enable_disabled_buttons(section);
            
            if(event == "approve" || event == "reject"){
                $("#lhs .item.active").click();
            }
        }
    })

    $(".result-edit").click(function(){
        const parent = $(this).parent();
        const form = $("form[name=updateResultForm]");

        // fill in details
        form.find("input[name=teacher_name]").val(parent.find(".teacher_name").text());
        form.find("select[name=semester]").val(parent.find(".semester").text());
        form.find("select[name=academic_year]").val(parent.find(".academic_year").text());
        form.find("input[name=submit_date]").val(parent.find(".result_date").text());
        form.find("input[name=form_level]").val(parent.find(".exam_year").text());
        form.find("input[name=subject]").val(parent.find(".course_name").text());
        form.find("input[name=program_name]").val(parent.find(".program_name").text());
        form.find("input[name=result_id]").val(parent.attr("data-item-id"));

        $("#updateResult").removeClass("no_disp");

    })

    $("form[name=updateResultForm]").submit(function(e){
        e.preventDefault();
        const form = $(this);
        const response = jsonFormSubmit(form, form.find("button[name=submit]"));
        
        response.then((resp) => {
            resp = JSON.parse(JSON.stringify(resp));
            let time = 0;
            let type = "error";

            if(resp.status){
                time = 5; type = "success"; 
                const row = $("tr[data-item-id=" + form.find("input[name=result_id]").val() + "]");
                row.find(".academic_year").text(form.find("select[name=academic_year]").val());
                row.find(".semester").text(form.find("select[name=semester]").val());
            }

            messageBoxTimeout(form.attr("name"), resp.message, type, time);
        })
    })
})