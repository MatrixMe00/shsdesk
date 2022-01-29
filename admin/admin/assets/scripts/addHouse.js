$("form[name=addHouseForm]").submit(function(e){
    e.preventDefault();

    response = formSubmit($(this), $("form[name=addHouseForm] button[name=submit]"));
    time = 5;

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
        }else if(response == "male-room-total-empty"){
            message = "Total rooms for male field is empty";
        }else if(response == "male-room-zero"){
            message = "Total number of rooms for males cannot be less than 1";
        }else if(response == "male-head-total-empty"){
            message = "Heads per room field for males is empty";
        }else if(response == "male-head-zero"){
            message = "Heads per room for males cannot be less than 1";
        }else if(response == "female-room-total-empty"){
            message = "Total rooms for female field is empty";
        }else if(response == "female-room-zero"){
            message = "Total number of rooms for females cannot be less than 1";
        }else if(response == "female-head-total-empty"){
            message = "Heads per room field for females is empty";
        }else if(response == "female-head-zero"){
            message = "Heads per room for females cannot be less than 1";
        }else{
            message = response;
            time = 0;
        }
    }

    messageBoxTimeout("addHouseForm",message, type, time);
})

$("form[name=updateHouseForm]").submit(function(e){
    e.preventDefault();

    response = formSubmit($(this), $("form[name=updateHouseForm] button[name=submit]"));
    time = 5;

    if(response == true){
        message = "House details has been updated";
        type = "success";

        //close box and refresh page
        setTimeout(function(){
            $("form[name=updateHouseForm] button[name=cancel]").click();
            location.reload();
        },3000)
        
    }else{
        type = "error";

        if(response == "no-house-name"){
            message = "House name field is empty";
        }else if(response == "no-gender"){
            message = "Please select a gender type";
        }else if(response == "male-room-total-empty"){
            message = "Total rooms for male field is empty";
        }else if(response == "male-room-zero"){
            message = "Total number of rooms for males cannot be less than 1";
        }else if(response == "male-head-total-empty"){
            message = "Heads per room field for males is empty";
        }else if(response == "male-head-zero"){
            message = "Heads per room for males cannot be less than 1";
        }else if(response == "female-room-total-empty"){
            message = "Total rooms for female field is empty";
        }else if(response == "female-room-zero"){
            message = "Total number of rooms for females cannot be less than 1";
        }else if(response == "female-head-total-empty"){
            message = "Heads per room field for females is empty";
        }else if(response == "female-head-zero"){
            message = "Heads per room for females cannot be less than 1";
        }else{
            message = response;
            time = 0;
        }
    }

    messageBoxTimeout("updateHouseForm",message, type, time);
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

                //fill house figures for males
                if(json["gender"].toLowerCase() == "male" || json["gender"].toLowerCase() == "both"){
                    $("form[name=updateHouseForm] #male_house").show();

                    $("form[name=updateHouseForm] input[name=male_house_room_total]").val(json["maleTotalRooms"]);
                    $("form[name=updateHouseForm] input[name=male_head_per_room]").val(json["maleHeadPerRoom"]);
                }

                //fill house figures for females
                if(json["gender"].toLowerCase() == "female" || json["gender"].toLowerCase() == "both"){
                    $("form[name=updateHouseForm] #female_house").show();

                    $("form[name=updateHouseForm] input[name=female_house_room_total]").val(json["femaleTotalRooms"]);
                    $("form[name=updateHouseForm] input[name=female_head_per_room]").val(json["femaleHeadPerRoom"]);
                }
                
                //work on title
                $("form[name=updateHouseForm] span#houseName").html(json["title"]);

                //provide id for selected house
                $("form[name=updateHouseForm] input[name=house_id]").val(json["id"]);

                //display form
                $("#modal_1 .my_loader").fadeOut();
                $("form[name=updateHouseForm]").removeClass("no_disp");
            }
        }
    })
    // alert("Model disabled by development procedures. \nAlert developer for more info");
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

$("button[name=cancel]").click(function(){
    formName = $(this).parents("form").attr("name");

   //hide displays of the number sections
    $("form[name=" + formName + "] #female_house").hide();
    $("form[name=" + formName + "] #male_house").hide(); 
})
    


//display gender model to display for number input
$("input[name=gender]").change(function(){
    myVal = $(this).val();
    myVal = myVal.toLowerCase();
    formName = $(this).parents("form").attr("name");

    //display required number receiver
    if(myVal == "male"){
        $("form[name=" + formName + "] #male_house").show();
        $("form[name=" + formName + "] #female_house").hide();
    }else if(myVal == "female"){
        $("form[name=" + formName + "] #female_house").show();
        $("form[name=" + formName + "] #male_house").hide();
    }else{
        $("form[name=" + formName + "] #female_house").show();
        $("form[name=" + formName + "] #male_house").show();
    }
})