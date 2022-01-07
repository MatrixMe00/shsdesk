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