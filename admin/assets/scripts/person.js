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
		message = "An unexpected error occured. Please try again later";
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
    $("#gen_del input[name=indexNumber]").val(item_id);
})

$("#users .item-event.edit").click(function(){
	$("#editAccount").removeClass("no_disp");
})