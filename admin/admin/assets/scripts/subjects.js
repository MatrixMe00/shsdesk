//variable to hold the current active section
current_section = $("#lhs .item.active[data-tab]").attr("data-tab")
ajaxCall = null
var selectedClasses = []
var selectedSubject = {option:"", value:""}

$(document).ready(function(){
    $(".control button[data-section=" + current_section + "]").click()
})

//effect a change in view depending on the button clicked
$(".control button").click(function(){
    $(".control button:not(.plain)").addClass("plain")
    $(this).removeClass("plain")

    $(".section_box").addClass("no_disp")
    $("#lhs .item.active").attr("data-tab", $(this).attr("data-section"))
    $("#" + $(this).attr("data-section")).removeClass("no_disp")

    if($(this).attr("data-refresh") && $(this).attr("data-refresh") == "true"){
        $("#lhs.menu .item.active").click()
    }
})

$("#courseIDs label input").change(function(){
    let active_value = $(this).prop("checked")
    let check_value = $(this).prop("value")
    const form = $(this).parents("form")

    //get content of course ids
    let course_ids = $(form).find("input[name=course_ids]").val()
    
    if(active_value){
        course_ids = course_ids.concat(check_value," ")
    }else{
        course_ids = course_ids.replace(check_value + " ", '')
    }

    //push new response into course ids
    $(form).find("input[name=course_ids]").val(course_ids)
})

$("#classIDs label input").change(function(){
    let active_value = $(this).prop("checked")
    let check_value = $(this).prop("value")
    const form= $(this).parents("form")

    //get content of course ids
    let course_ids = $(form).find("input[name=class_ids]").val()
    
    if(active_value){
        course_ids = course_ids.concat(check_value," ")
    }else{
        course_ids = course_ids.replace(check_value + " ", '')
    }

    //push new response into course ids
    $(form).find("input[name=class_ids]").val(course_ids)
})

$("form").submit(function(e){
    e.preventDefault()

    const formName = $(this).attr("name")

    if(formName === "delete_form"){
        const response = formSubmit($(this), $(this).find("input[name=submit]"), false)
        
        if(response === true || response === "true"){
            $("tr.remove_marker").remove()

            num = parseInt($(".content.orange").find("h2").html()) - 1
            $(".content.orange").find("h2").html(num)

            $("#table_del").addClass("no_disp")
        }else{
            $("tr.remove_marker").removeClass("remove_marker")
        }
    }else{
        const response = jsonFormSubmit($(this), $(this).find("button[name=submit]"))
        response.then((return_data)=>{
            return_data = JSON.parse(JSON.stringify(return_data))
            
            messageBoxTimeout($(this).prop("name"), return_data["message"], return_data["status"] ? "success" : "error")

            if(return_data["sms-message"] && return_data["sms-message"] != "no-message"){
                alert_box(return_data["sms-message"],"danger")
            }

            if(return_data["status"] === true || return_data["status"] === "true"){
                if(!formName.includes("update")){
                    setTimeout(()=> {
                        $(this).find("input:not([type=checkbox], [name=school_id])").val("")
                        $(this).find("input[type=checkbox]").prop("checked", false)
                    }, 3000)
                }
                
                $(".control button").attr("data-refresh","true")
            }

            //refresh page if first course
            if($("form").prop("name") === "addCourseForm" && return_data["isFirst"]){
                location.href = location.href
            }
        })
    }
    
})

$("select[name=class_id]").change(function(){
    var selectedOptions = $(this).find('option:selected'); // Get the selected options
    
    //reset selected classes
    selectedClasses = []

    selectedOptions.each(function() {
        let element = $(this)
        selectedClasses.push({
            "option_name":$(element).text(),
            "option_value": $(element).val()
        }); // Add text of each selected option to the array
    });
})

$("select[name=course_id]").change(function(){
    var selectedOption = $(this).find('option:selected').text()
    var selectedValue = $(this).val()
    
    //reset subject values
    selectedSubject = {option: selectedOption, value: selectedValue}
})

$(".item-event").click(function(){
    const item_id = $(this).attr("data-item-id")
    const table = $(this).parents("section").attr("data-table")
    const table_col = $(this).parents("section").attr("data-table-col")
    const this_eventBtn = $(this)
    let is_teacher = false

    if($(this).hasClass("edit")){
        let formName = ""

        if(table == "courses"){
            formName = "updateCourseForm"
        }else if(table == "teachers"){
            formName = "updateTeacherForm"
            is_teacher = true
        }
        
        $("#updateItem form").addClass("no_disp")
        $("#updateItem").removeClass("no_disp")

        ajaxCall = $.ajax({
            url: $("#updateItem form[name=" + formName + "]").attr("action"),
            data: {
                item_id: item_id,
                item_table: table,
                item_table_col: table_col,
                submit: "getItem",
                isTeacher: is_teacher
            },
            timeout: 30000,
            beforeSend: function(){
                $("#updateLoader").addClass("flex").removeClass("no_disp")
                $("#updateItem #getLoader").html(loadDisplay({
                    circular: true, 
                    circleColor: "light"
                }));
            },
            success: function(data){
                $("#updateItem #getLoader").html("")
                $("#updateLoader").addClass("no_disp").removeClass("flex")

                data = JSON.parse(JSON.stringify(data))
                
                const results = data["results"][0]
                if(data["status"] === true){
                    if(table === "courses"){
                        $("#updateItem span#courseID").html($(this_eventBtn).parents("tr").children("td:first-child").html())
                        $("#updateItem form[name=" + formName + "] input[name=course_name]").val(results["course_name"])
                        $("#updateItem form[name=" + formName + "] input[name=course_alias]").val(results["short_form"])
                        $("#updateItem form[name=" + formName + "] input[name=course_id]").val(results["course_id"])
                        $("#updateItem form[name=" + formName + "] input[name=course_credit]").val(results["credit_hours"] == null ? 0 : results["credit_hours"])
                    }else{
                        $("#updateItem form[name=" + formName + "] span#teacherID").html($(this_eventBtn).parents("tr").children("td:first-child").html())
                        $("#updateItem form[name=" + formName + "] input[name=teacher_lname]").val(results["lname"])
                        $("#updateItem form[name=" + formName + "] input[name=teacher_oname]").val(results["oname"])
                        $("#updateItem form[name=" + formName + "] select[name=teacher_gender]").val(results["gender"]).change()
                        $("#updateItem form[name=" + formName + "] input[name=teacher_phone]").val(results["phone_number"])
                        $("#updateItem form[name=" + formName + "] input[name=course_ids]").val(results["course_id"])
                        $("#updateItem form[name=" + formName + "] input[name=teacher_email]").val(results["email"])
                        $("#updateItem form[name=" + formName + "] input[name=teacher_id]").val(results["teacher_id"])

                        //parse data into table
                        let course_ids = results["course_id"].split(" ")
                        let course_names = results["course_names"].split(",")
                        const tbody = $("#updateItem form table tbody")

                        course_ids.pop()
                        course_names.pop()

                        if(course_ids.length !== course_names.length){
                            alert_box("Invalid identities presented for classes and subjects", "danger")
                        }else{
                            //clean arrays
                            $(course_ids).each(function(index, value) {
                                var cleanedValue = value.replace(/^\[|\]$/g, "");
                                course_ids[index] = cleanedValue;
                            });
                            $(course_names).each(function(index, value) {
                                var cleanedValue = value.replace(/^\[|\]$/g, "");
                                course_names[index] = cleanedValue;
                            });
                            
                            for(var i = 0; i < course_ids.length; i++){
                                let ids = course_ids[i].split("|")
                                let names = course_names[i].split("|")

                                let tr = "<tr>"
                                    tr += "<td>" + formatItemId(ids[0],"CID") + "</td>"
                                    tr += "<td>" + names[0] + "</td>"
                                    tr += "<td>" + formatItemId(ids[1], "SID") + "</td>"
                                    tr += "<td>" + names[1] + "</td>"
                                    tr += "<td><span class='item-event' onclick='removeRow($(this))'>Remove</span></td>"
                                tr += "</tr>"

                                $(tbody).append(tr)
                            }
                        }

                        $("#updateItem table tbody");
                    }

                    $("#updateItem form[name="+ formName + "]").removeClass("no_disp")
                }else{
                    alert(results)
                }

            },
            error: function(e){
                let message = ""
                if(e.responseText === "timeout"){
                    message = "Connection was timed out. Please check your network and try again";
                }else{
                    message = e.responseText
                }

                alert_box(message,"light",10)
            }
        })
        
    }else if($(this).hasClass("delete")){
        let item_name = $(this).parents("tr").children("td:nth-child(2)").html()
        $("#table_del").removeClass("no_disp")

        //message to display
        $("#table_del p#warning_content").html("Do you want to remove <b>" + item_name + "</b> from your records?");

        //fill form with needed details
        $("#delete_form input[name=item_id]").val(item_id)
        $("#delete_form input[name=table_name]").val(table)
        $("#delete_form input[name=column_name]").val(table_col)

        $(this).parents("tr").addClass("remove_marker");
    }else if($(this).hasClass("remove_td")){
        $(this).parents("tr").remove()
    }
})

$(".add_detail").click(function(){
    const form = $(this).parents("form")
    const class_select = $(form).find("select[name=class_id]")
    const subject_select = $(form).find("select[name=course_id]")
    const table = $(form).find("table")

    if(selectedClasses.length < 1){
        const message = "Please select at least one class"
        messageBoxTimeout($(form).attr("name"), message, "error")
    }else if(selectedSubject["value"] === ""){
        let message = "Please select the subject taught by the teacher in " 
        message += selectedClasses.length > 1 ? "these classes" : "this class"
        messageBoxTimeout($(form).attr("name"), message, "error")
    }else{
        for(i=0; i < selectedClasses.length; i++){
            const dat = "[" + selectedClasses[i]["option_value"] + "|" + selectedSubject["value"] + "] "
            //get content of course ids
            let course_ids = $(form).find("input[name=course_ids]").val()

            //reset course_ids
            if(course_ids === "wrong array data"){
                $(form).find("input[name=course_ids]").val()
                course_ids = ""
            }

            if(course_ids.includes(dat)){
                alert_box("Teacher has been assigned this detail already, " + selectedSubject["option"] + " at " + selectedClasses[i]["option_name"])
            }else{
                let tr = "<tr>"
                    tr += "<td>" + formatItemId(selectedClasses[i]["option_value"], "CID") + "</td>"
                    tr += "<td>" + selectedClasses[i]["option_name"] +"</td>"
                    tr += "<td>" + formatItemId(selectedSubject["value"], "SID") + "</td>"
                    tr += "<td>" + selectedSubject["option"] + "</td>"
                    tr += "<td>" + "<span class='item-event' onclick='removeRow($(this))'>Remove</span>" + "</td>"
                tr += "</tr>"

                $(table).find("tbody").append(tr)
                
                //push new response into course ids
                $(form).find("input[name=course_ids]").val(course_ids + dat)
            }
        }

        //reset fields
        $(class_select).prop("selectedIndex", -1)
        $(subject_select).prop("selectedIndex", 0)
        selectedClasses = []
        selectedSubject = {option:"", value:""}
    }
})

function removeRow(element){
    const tr = $(element).parents("tr")
    const form = $(element).parents("form")
    const cid = formatItemId($(tr).find("td:first-child").text(), "CID", true)
    const sid = formatItemId($(tr).find("td:nth-child(3)").text(), "SID", true)

    let course_ids = $(form).find("input[name=course_ids]").val()
    const dat = "[" + cid + "|" + sid + "] "

    //push new response into course ids
    $(form).find("input[name=course_ids]").val(course_ids.replace(dat, ''))     

    $(tr).remove()
}

$("#cancelUpdate").click(function(){
    if(ajaxCall){
        ajaxCall.abort()
    }
    
    $("#updateItem").addClass("no_disp")
})

$("#updateItem button[name=cancel]").click(function(){
    $("#updateItem table tbody").html("")
    $("#updateItem").addClass("no_disp")
})