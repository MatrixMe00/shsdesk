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

    //push new response into course ids
    $("input[name=course_ids]").val(course_ids)
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
            timeout: 8000,
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
                    $("#updateProgram input[name=program_id]").val(results["program_id"])
                    $("#updateProgram input[name=program_name]").val(results["program_name"])
                    $("#updateProgram input[type=checkbox]").each((index, element)=>{
                        element_val = $(element).val() + " "
                        if(results["course_ids"].includes(element_val)){
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
            timeout: 8000,
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
    }
})

$("form[name=delete_form]").submit(function(e){
    e.preventDefault()

    const response = formSubmit($(this), $(this).find("input[name=submit]"), false)
    
    if(response === true || response === "true"){
        $("tr.remove_marker").remove()

        num = parseInt($(".content.orange").find("h2").html()) - 1
        $(".content.orange").find("h2").html(num)

        $("#table_del").addClass("no_disp")
    }else{
        $("tr.remove_marker").removeClass("remove_marker")
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