$("form[name=user_account_form]").submit(function(e){
	e.preventDefault();

	response = formSubmit($(this), $("form[name=user_account_form] button[name=submit]"));

	time = 5;

	if(response == true){
		message = "User data has been updated successfully";
		type = "success";

        setTimeout(function(){
            location.reload();
        }, 3000);
        
	}else if(response == "no-change"){
		message = "No change was detected";
		type = "load";
	}else if(response == "update-error"){
		message = "Update was unsuccessful. Please try again later";
		type="error";
	}else{
		message = response;
		type="error";
	}

	messageBoxTimeout("user_account_form", message, type, time);
})

//display or hide content if user wants to add new role
$("form[name=addAdmin] select[name=role]").change(function(){
	if($(this).val().toLowerCase() == "others"){
		$("form[name=addAdmin] label[for=other_role]").removeClass("no_disp");
	}else{
		if(!$("form[name=addAdmin] label[for=other_role]").hasClass("no_disp")){
			$("form[name=addAdmin] label[for=other_role]").addClass("no_disp");
		}
	}
})

//delete a user
$("#users .item-event.delete").click(function(){
	item_id = $(this).attr("data-user-id");
    fullname = $(this).parents(".user_container").children(".top").children("h4").html();
	fullname = fullname.split(" (");
	fullname = fullname[0];

    //display yes no modal box
    $("#gen_del").removeClass("no_disp");

    //message to display
    // item_header = $(this).parents(".item").children(".top").children(".flex").children(".content_title").children("h4").html();``
    $("#gen_del p#warning_content").html("Do you want to remove <b>" + fullname + "</b> from your database?");

    //fill form with needed details
    $("#gen_del input[name=sid]").val(item_id);
    $("#gen_del input[name=mode]").val("delete");
    $("#gen_del input[name=table]").val("admins_table");
})

$("#users .item-event.edit").click(function(){
	$("#editAccount").removeClass("no_disp");
})

$("#users .item-event.dev").click(function(){
    //retrieve data to deliver results
    dataString = "submit=retrieveDetails&id=" + $(this).attr("data-user-id");
    
    $.ajax({
        url: $("form[name=addAdmin]").attr("action"),
        data: dataString,
        dataType: "json",
        success: function (json){
            json = JSON.parse(JSON.stringify(json));

            if(json["status"] == "success"){
                //fill table with data
                $("form #fullname").val(json["fullname"]);
                $("form #user_id").val(json["user_id"]);
                $("form #email").val(json["email"]);
                $("form #contact").val(json["contact"]);
                $("form #username").val(json["username"]);
            }else{
                alert_box("User Undefined", "danger");
                $("#editAccount").addClass("no_disp");
            }
        },
        error: function (){
            alert_box("Error communicating with server. Try again after a page refresh", "warning", 8);
        }
    })
})

$("form[name=addAdmin]").submit(function(e){
    e.preventDefault();

    result = formSubmit($(this), $("form[name=addAdmin] button[name=submit]"), false);

    if(result == true){
        $("#pMessage").html("Admin added succefully. Preparing dashboard...");

        //refresh in 3seconds
        setTimeout(function(){
            $("#pMessage").html("Welcome " + $("#new_username").val());
        },3000);

        setTimeout(function(){
            location.href = location.href;
        },4000);
    }else{
        html = $("#pMessage").html();
        message = "";
        
        if(result == "insert-error"){
			message = "Problem adding user to your database. Reload page and try again";
		}else{
            message = result;
        }

        $("#pMessage").html(message).addClass("danger");

        setTimeout(function(){
            $("#pMessage").html(html).removeClass("danger");
        },5000);
    }
})

//activate or deactivate a user
$(".item-event.status").click(function(){
    me = $(this);
    text = $(this).html();
    user_id = $(this).attr("data-user-id");

    if(text.toLowerCase() == "activate"){
        stat = 1;
    }else{
        stat = 0;
    }

    $.ajax({
        url: $("form[name=user_account_form]").prop("action"),
        data: "submit=status_modify&stat=" + stat + "&user_id=" + user_id,
        cache: false,
        success: function (){
            if(text.toLowerCase() == "activate"){
                $(me).html("Deactivate");
            }else{
                $(me).html("Activate");
            }
        },
        error: function (){
            alert_box("User status could not be modified. Try again later", "danger", 8);
        }
    })
})