$("form[name=exeatForm]").submit(function(e){
    e.preventDefault();

    response = formSubmit($(this), $("form[name=exeatForm] button[name=submit]"));

    if(response == true){
        message = "Exeat provided successfully";
        type = "success";

        location.reload();
    }else{
        type = "error";

        if(response == "no-index"){
            message = "Index Number field is empty";
        }else if(response == "no-town"){
            message = "Please provied the exeat town";
        }else if(response == "no-exeat-date"){
            message = "Exeat date has not been provided";
        }else if(response == "no-return-date"){
            message = "Return date has not been provided";
        }else if(response == "date-conflict"){
            message = "Return date cannot be lower than exeat date";
        }else if(response == "no-exeat-type"){
            message = "Please specify the type of exeat";
        }else if(response == "no-reason"){
            message = "Please provide a reason for this exeat";
        }else if(response == "range-error"){
            message = "Reason should range between 3 and 80 characters";
        }else if(response == "not-student"){
            message = "Index number entered cannot be found. Please check and try again";
        }else if(response == "not-registered"){
            message = "Student has not been registered";
        }else{
            message = response;
        }
    }

    messageBoxTimeout("exeatForm",message, type, 0);
})

//sign return for student
$(".item-event.sign-return").click(function(){
    id = $(this).attr("data-item-id");
    parent = $(this).parents("tr");
    me = $(this);

    $.ajax({
        url: $("form[name=exeatForm]").attr("action"),
        dataType: "json",
        type: "POST",
        data: "submit=markReturn&id=" + id,
        timeout: 15000,
        success: function(text){
            text = JSON.parse(JSON.stringify(text));

            if(text["status"] == "success"){
                //present current date
                ret_date = $(parent).children("td.ret_date").html(text["date"]);

                //parse return status
                $(parent).children("td.ret").html("Returned");

                //remove item from table
                $(me).remove();
            }else{
                alert_box("Update was unsuccessful", "danger");
            }
        },
        error: function(xhr, textMessage){
            if(textMessage == "timeout"){
                alert_boc("Connection was timed out. Please try again later.", "danger", 8)
            }else{
            alert_box("An error occured. Please try again later!", "danger", 8)
            }
        }
    })
})

//show modal box
$("tbody tr").click(function(){
    id = $(this).attr("data-item-id");

    $.ajax({
        url: $("form[name=exeatForm]").attr("action"),
        data: "submit=getExeat&id=" + id,
        dataType: "json",
        timeout: 15000,
        success: function (data){
            data = JSON.parse(JSON.stringify(data));

            if(data["status"] == "success"){
                //student detail section
                $(".form .body #indexNumber").val(data["indexNumber"]);
                $(".form .body #fullname").val(data["fullname"]);
                $(".form .body #house").val(data["house"]);

                //exeat details
                $(".form .body #exeat_town").val(data["exeat_town"]);
                $(".form .body #exeat_date").val(data["exeat_date"]);
                $(".form .body #exp_date").val(data["exp_date"]);

                if(data["returnStatus"]){
                    $(".form .body #ret_date").show().val(data["ret_date"]);
                }

                //other details
                $(".form .body #exeat_reason").html(data["exeat_reason"]);
                $(".form .body #issueBy").val(data["issueBy"]);

                if(data["returnStatus"]){
                    $(".form .body #returnStatus").val("Student returned to school");
                }else{
                    $(".form .body #returnStatus").val("Student not returned to school");
                }

                //reveal form
                $("#modal").removeClass("no_disp");
            }else{
                alert_box(data["status"], "danger", 10);
            }
        },
        error: function (xhr, textMessage){
            if(textMessage === "timeout"){
                alert_box("Connection was timed out. Please try again later", "danger", 10);
            }else{
                alert_box("An error was encountered. Please try again later", "danger", 10);
            }            
        }
    })
})

//hide modal box
$("#modal button[name=cancel]").click(function(){
    $("#modal .form input").val("");
    $("#modal .form .body #exeat_reason").html("");
    $("#modal").addClass("no_disp");
})