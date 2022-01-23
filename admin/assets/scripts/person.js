$("form[name=user_account_form]").submit(function(e){
	e.preventDefault();

	response = formSubmit($(this), $("form[name=user_account_form] button[name=submit]"));

	time = 5;

	if(response == true){
		message = "User data has been updated successfully";
		type = "success";
	}else if(response == "no-change"){
		message = "No change was detected";
		type = "load";
	}else if(response == "update-error"){
		message = "Update was unsuccessful. Please try again later";
		type="error";
	}else{
		message = response;
		type="error";
		time=0;
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

        if(result == "wrong-email-fullname"){
           message = "Email or fullname provided is wrong. Please check and try again";
        }else if(result == "same-username"){
            message = "You cannot use the same username";
        }else if(result == "same-password"){
            message = "You cannot use the same password";
        }else if(result == "update-error"){
            message = "Your data could not be updated. Please try again later or contact the admin";
        }else if(result == "cannot login"){
            message = "Update was unsuccessful. Contact Admin for help";
        }else if(result == "insert-error"){
			message = "Problem adding user to your database. Reload page and try again";
		}else{
            message = result;
        }

        $("#pMessage").html(message);

        setTimeout(function(){
            $("#pMessage").html(html);
        },5000);
    }
})