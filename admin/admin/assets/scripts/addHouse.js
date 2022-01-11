$("form[name=addHouseForm]").submit(function(e){
    e.preventDefault();

    response = formSubmit($(this), $("form[name=addHouseForm] button[name=submit]"));

    if(response == true){
        message = "House has been added";
        type = "success";

        //close box and refresh page
        setTimeout(function(){
            $("form[name=addHouseForm] button[name=cancel]").click();
            $('#lhs .menu .item.active').click();
        },6000)
        
    }else{
        type = "error";

        if(response == "no-house-name"){
            message = "House name field is empty";
        }else if(response == "no-gender"){
            message = "Please select a gender type";
        }else if(response == "room-total-empty"){
            message = "Total rooms field is empty";
        }else if(response == "room-zero"){
            message = "Total number of rooms cannot be less than 1";
        }else if(response == "head-total-empty"){
            message = "Heads per room field is empty";
        }else if(response == "head-zero"){
            message = "Heads per room cannot be less than 1";
        }
    }

    messageBoxTimeout("addHouseForm",message, type);
})