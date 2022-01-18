$("form[name=addHouseForm]").submit(function(e){
    e.preventDefault();

    response = formSubmit($(this), $("form[name=addHouseForm] button[name=submit]"));

    if(response == true){
        message = "House has been added";
        type = "success";

        //close box and refresh page
        setTimeout(function(){
            $("form[name=addHouseForm] button[name=cancel]").click();
            location.reload();
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

$("form[name=updateHouseForm]").submit(function(e){
    e.preventDefault();

    response = formSubmit($(this), $("form[name=updateHouseForm] button[name=submit]"));

    if(response == true){
        message = "House has been updated";
        type = "success";

        //close box and refresh page
        setTimeout(function(){
            $("form[name=updateHouseForm] button[name=cancel]").click();
            location.reload();
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

    messageBoxTimeout("updateHouseForm",message, type);
})

//fetch house Details
$("table tbody tr .edit").click(function(){
    //grab id
    id = $(this).parents("tr").attr("data-item-id");
    $.ajax({
        url: $("#modal form").attr("action"),
        data: "submit=fetchHouseDetails&id=" + id,
        method: "get",
        dataType: "json",
        beforeSend: function(){
            $("#modal_1 #getLoader").html(loadDisplay({
                circular: true, 
                circleColor: "light"
            }));
            $("#modal_1").removeClass("no_disp");
        },
        success: function(json){
            json = JSON.parse(JSON.stringify(json));

            if(json["status"] == "success"){
                //fill form with Details
                $("form[name=updateHouseForm] input[name=house_name]").val(json["title"]);
                
                //select gender
                $("form[name=updateHouseForm] input[value=" + json["gender"] + "]").attr("checked", true);

                $("form[name=updateHouseForm] input[name=house_room_total]").val(json["totalRooms"]);
                $("form[name=updateHouseForm] input[name=head_per_room]").val(json["headPerRoom"]);
                $("form[name=updateHouseForm] span#houseName").html(json["title"]);

                //display form
                $("#modal_1 .my_loader").fadeOut();
                $("form[name=updateHouseForm]").removeClass("no_disp");
            }
        }
    })
})

$("#modal_1 .item-event.cancel").click(function(){
    $("#modal_1 #getLoader").html("");
    $("#modal_1").addClass("no_disp");
})

//delete a record
$("table tbody tr .delete").click(function(){
    item_id = $(this).parents("tr").attr("data-item-id");
    fullname = $(this).parents("tr").children("td:nth-child(2)").html();

    //display yes no modal box
    $("#gen_del").removeClass("no_disp");

    //message to display
    // item_header = $(this).parents(".item").children(".top").children(".flex").children(".content_title").children("h4").html();``
    $("#gen_del p#warning_content").html("Do you want to remove <b>" + fullname + "</b> from your database?");

    //fill form with needed details
    $("#gen_del input[name=sid]").val(item_id);
    $("#gen_del input[name=mode]").val("delete");
    $("#gen_del input[name=table]").val("houses");
})