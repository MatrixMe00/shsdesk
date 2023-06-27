//this function is used to add the required index to the specify input field
function addToList(element){
    var specify_text = $("input#specific").val()
    const text = $(element).html()
    const indexNumber = text.split(" - ")[1]

    if(specify_text.indexOf(",") !== -1){
        var lastText = specify_text.split(", ")
        lastText = lastText[lastText.length-1]

        specify_text = specify_text.replace(lastText,indexNumber)
    }else{
        specify_text = indexNumber
    }

    $("input#specific").val(specify_text)
    $("#individual_match").removeClass("flex-all-center").addClass("no_disp").html("")
}

$(document).ready(function(){
    $("#text_message").keyup()
    var specifc = false
    var group = ""
    var individuals = null
    const ussd = $("input#sms_id").val()

    $("button.change_sms").click(function(){
        const change = parseInt($(this).attr("data-change"))

        if(change){
            if($("input#sms_id").val() === ""){
                alert_box("You cannot set an empty sms id", "danger")
            }else{
                $.ajax({
                    url: "./admin/submit.php",
                    data: {submit: "add_update_sms", sms_id: $("input#sms_id").val()},
                    type: "POST",
                    timeout: 30000,
                    beforeSend: function(){
                        $("button.change_sms").html("Updating...").prop("disabled", true)
                    },
                    success: function(response){
                        if(response == "change-complete"){
                            alert_box("Your USSD was successfully modified", "success")
                            $("#lhs .item.active").click()
                        }else{
                            alert_box(response, "danger", 7)
                        }
                        $("button.change_sms").html("Update").prop("disabled", false)
                    },
                    error: function(xhr){
                        let message = ""

                        if(xhr.statusText == "timeout"){
                            message = "Error communciating with server due to slow network Please check your connection and try again"
                        }else{
                            message = xhr.responseText
                        }

                        alert_box(message, "danger", 6)
                        $("button.change_sms").prop("disabled",false).attr("data-change","0").html("Update")

                    }
                })
            }
        }else{
            $("button.reset_sms").removeClass("no_disp")
            $("input#sms_id").prop("readonly",false).focus().select()
        }
        
    })

    $("input#sms_id").keyup(function(){
        if($(this).val() === ussd){
            $("button.change_sms").html("Change").attr("data-change","0")
        }else{
            $("button.change_sms").html("Update").attr("data-change","1")
            $("button.reset_sms").removeClass("no_disp")
        }
    })

    $("button.reset_sms").click(function(){
        $(this).addClass("no_disp")
        $("button.change_sms").html("Change").prop("disabled", false)
        $("input#sms_id").val(ussd).prop("readonly", true)
        $("button.change_sms").attr("data-change","0")
    })

    $("button.group:not(.reset)").click(function(){
        $("button.group, button.individual_item").addClass("plain-r")
        $(this).removeClass("plain-r")
        $("section.groups .group_content, section#sms_message").addClass("no_disp")
        $("section.groups, button.group.reset, #" + $(this).attr("data-section-id")).removeClass("no_disp")
        $("input#specific").val("")

        group = $(this).attr("data-section-id")
    })

    $("button.individual_item:not(.specify)").click(function(){
        $(this).siblings(".individual_item, .specify").addClass("plain-r")
        $(this).removeClass("plain-r")
        $("label[for=specific]").addClass("no_disp")

        individuals = $(this).attr("data-id")

        $("#sms_message").removeClass("no_disp")

        specific = false
    })

    $("button.specify").click(function(){
        specific = true
        $(this).siblings(".individual_item").addClass("plain-r")
        $(this).removeClass("plain-r")
        $("label[for=specific]").removeClass("no_disp")

        if($("input[name=specific]").val() === ""){
            $("#sms_message").addClass("no_disp")
        }else{
            $("#sms_message").removeClass("no_disp")
        }
    })

    $("input[name=specific]").blur(function(){
        if($(this).val() === ""){
            $("#sms_message").addClass("no_disp")
        }else{
            $("#sms_message").removeClass("no_disp")
            individuals = $(this).val()
        }
    })

    $("button.group.reset").click(function(){
        $(this).addClass("no_disp")
        $("button.group, button.individual_item").addClass("plain-r")
        $("section.groups, #sms_message").addClass("no_disp")

        specifc = false
        group = ""
        individuals = null
    })

    $("button.send").click(function(){
        const message = $("#text_message").val()

        $.ajax({
            url: "./admin/submit.php",
            data: {
                submit: "send_sms", group: group, individuals: individuals, message: message
            },
            method: "GET",
            timeout: 30000,
            beforeSend: function(){
                $("button.send").html("Sending...")
            },
            success: function(response){
                $("button.send").html("Send")
                alert_box(response)
            },
            error: function(response){
                var message = ""
                if(response.statusText == "timeout"){
                    message = "Connection was timed out due to a slow network. Please try again later"
                }else{
                    message = JSON.stringify(response)
                }

                alert_box(message, "danger",6)
                $("button.send").html("Send")
            }
        })
    })

    $("#text_message").keyup(function(e){
        $("#message_count").html($(this).val().length + " of " + $(this).attr("maxlength"))
    })

    $("input#specific").keyup(function(){
        let myword = $(this).val()

        if(myword !== ""){
            //split into comma separated
            myword = myword.split(", ")
            const keyword = myword[myword.length - 1]
            
            $.ajax({
                url: "./admin/submit.php",
                data: {submit: "search_name", keyword: keyword, type: group},
                dataType: "json",
                method: "GET",
                timeout: 30000,
                beforeSend: function(){
                    const span = "<p class='txt-al-c'>Searching...</p>"
                    $("#individual_match").removeClass("no_disp").addClass("flex-all-center").html(span)
                },
                success: function(response){
                    response = JSON.parse(JSON.stringify(response))

                    if(typeof response["message"] === "object"){
                        $("#individual_match").html("")
                        for(var i = 0; i < response["message"].length; i++){
                            const person = response["message"][i]
                            const hide_span = $("input#specific").val().indexOf(person["indexNumber"]) > -1 ? " no_disp" : ""
                            
                            const span = "<p class='w-fit h-light sp-med" + hide_span + "' onclick='addToList($(this))'>" + 
                                            person["Lastname"] + " " + person["Othernames"] + " - " + person["indexNumber"]
                                        "</p>";
                            $("#individual_match").append(span)
                        }
                    }else{
                        var span = "";
                        if(response["message"] == "no-result"){
                            span = "<p class='txt-al-c'>No results were found</p>"
                        }else{
                            span = ""
                            $("#individual_match").addClass("no_disp").removeClass("flex")
                        }

                        $("#individual_match").html(span)
                    }
                },
                error: function(xhr){
                    let message = ""

                    if(xhr.statusText == "timeout"){
                        message = "Connection was timed out due to slow network detected. Please check your internet and try again"
                    }else if(xhr.statusText == "parseerror"){
                        message = "Data receieved from the server is considered invalid type. Please try again"
                    }else{
                        message = xhr.responseText
                    }

                    alert_box(message, "danger", 8)
                }
            })
        }else{
            $("#individual_match").removeClass("flex-all-center").addClass("no_disp").html("")
        }
    })
})