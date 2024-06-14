current_section = $("#lhs .item.active[data-tab]").attr("data-tab")
ajaxCall = null

$(document).ready(function(){
    $(".control_btn[data-section=" + current_section + "]").click()
})

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

//direct displays to the control buttons
$(".control_btn").click(function(){
    const section = $(this).attr("data-section")

    $(".section_box:not(.no_disp)").addClass("no_disp")
    $("#lhs .item.active").attr("data-tab", section)
    $("#" + $(this).attr("data-section")).removeClass("no_disp")

    $(".control_btn:not(.plain)").addClass("plain")
    $(this).removeClass("plain")

    if($(this).attr("data-refresh") && $(this).attr("data-refresh") === "true"){
        $("#lhs.menu .item.active").click()
    }
})

$(".item-event").click(function(){
    item_id = $(this).attr("data-item-id")
    
    if($(this).hasClass("edit")){
        $("#updateProgram").removeClass("no_disp")
        
        ajaxCall = $.ajax({
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
        $("#view_results").removeClass("no_disp")
        const parent = $(this).parents("tr")
        const p_class = $(parent).find("td:nth-child(2)").html()
        const p_subject = $(parent).find("td:nth-child(3)").html()
        const p_year = $(this).attr("data-p-year")
        const p_sem = $(this).attr("data-p-sem")

        $("#view_results table tfoot #year_sem").html("Class Year: " + p_year + " | Class Sem: " + p_sem)
        $("#view_results #topic").html(p_subject + " results for " + p_class)

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

                if(response["error"] === true){
                    const tr_error = "<tr class='empty'><td colspan='6'>" + response["message"] + "</td></tr>"
                    $("#view_results table tbody").html(tr_error);
                }else{
                    for(var i=0; i < response["message"].length; i++){
                        const data = response["message"][i];
                        const tr = "<tr>" +
                                        "<td>" +  data["indexNumber"] + "</td>" +
                                        "<td>" +  data["fullname"] + "</td>" + 
                                        "<td>" +  data["class_mark"] + "</td>" + 
                                        "<td>" +  data["exam_mark"] + "</td>" + 
                                        "<td>" +  data["mark"] + "</td>" + 
                                        "<td>" +  data["grade"] + "</td>" + 
                                   "</tr>";
                        $("#view_results table tbody").append(tr);
                    }
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

$("form[name=delete_form]").submit(function(e){
    e.preventDefault();

    const response = formSubmit($(this), null, false)
    
    if(response === true || response === "true"){
        $("tr.remove_marker").remove()

        num = parseInt($(".content.orange").find("h2").html()) - 1
        $(".content.orange").find("h2").html(num)

        $("#table_del").addClass("no_disp")
    }else{
        $("tr.remove_marker").removeClass("remove_marker")
        console.log(response);
    }
})

$("#cancelUpdate").click(function(){
    if(ajaxCall){
        ajaxCall.abort()
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
                $(this).hide()
            }else{
                $(this).show()
            }
        })
        
        //count number of visible results
        const tr = table_body.children("tr:visible");
        
        if(tr.length === 0)
            alert_box("No results were returned", "secondary");
    }else{
        table_body.children("tr").show();
    }
})