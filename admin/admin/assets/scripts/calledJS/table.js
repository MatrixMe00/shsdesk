$("table tbody tr .edit").click(function(){
    //take id number
    id_number = $(this).parents("tr").attr("data-index");
    registered = $(this).parents("tr").attr("data-register");

    dataString = "index_number=" + id_number + "&registered=" + registered + "&submit=fetchStudentDetails";

    //display form modal
    $("#updateStudent").removeClass("no_disp");

    //hide form
    $("#updateStudent form").addClass("no_disp");

    //display loader
    $("#updateStudent #getLoader").parent().removeClass("no_disp").addClass("flex");
    $("#updateStudent #getLoader").removeClass("no_disp").html(loadDisplay({
        circular: true, circleColor: "light"
    }));

    //grab action point from form
    action = $("form[name=adminUpdateStudent]").attr("action");

    //fetch results
    $.ajax({
        url: action,
        type: "post",
        data: dataString,
        dataType: "json",
        cache: true,
        success: function(response){
            response = JSON.parse(JSON.stringify(response));

            if(response["status"] == "success"){
                //hide loader
                $("#updateStudent #getLoader").parent().addClass("no_disp").removeClass("flex");
                $("#updateStudent #getLoader").html("");

                //load details
                $("form[name=adminUpdateStudent] input[name=student_index]").val(response["indexNumber"]);
                $("form[name=adminUpdateStudent] input[name=lname]").val(response["Lastname"]);
                $("form[name=adminUpdateStudent] input[name=oname]").val(response["Othernames"]);
                $("form[name=adminUpdateStudent] select[name=gender]").val(response["Gender"]);

                if(registered == "true"){
                    $("form[name=adminUpdateStudent] select[name=house]").prop("disabled", false).val(response["houseID"]);
                    $("form[name=adminUpdateStudent] label[for=house]").removeClass("no_disp");
                }else{
                    $("form[name=adminUpdateStudent] select[name=house]").prop("disabled", true);
                    $("form[name=adminUpdateStudent] label[for=house]").addClass("no_disp");
                }

                $("form[name=adminUpdateStudent] input[name=student_course]").val(response["programme"]);
                $("form[name=adminUpdateStudent] input[name=aggregate]").val(response["aggregate"]);
                $("form[name=adminUpdateStudent] input[name=jhs]").val(response["jhsAttended"]);
                $("form[name=adminUpdateStudent] input[name=dob]").val(response["dob"]);
                $("form[name=adminUpdateStudent] input[name=track_id]").val(response["trackID"]);
                $("form[name=adminUpdateStudent] select[name=boarding_status]").val(response["boardingStatus"]);

                //display form
                $("#updateStudent form").removeClass("no_disp");
            }else{
                alert_box("Data was not found", "danger");
                $("form[name=adminUpdateStudent] button[name=cancel]").click();
            }
        },
        error: function(r){
            alert_box("Invalid request to server. Please try again later", "danger", 8);
            $("form[name=adminUpdateStudent] button[name=cancel]").click();
        }
    })
})

$("table tbody tr .delete").click(function(){
    item_id = $(this).parents("tr").attr("data-index");
    fullname = $(this).parents("tr").children("td:nth-child(2)").html();

    //display yes no modal box
    $("#table_del").removeClass("no_disp");

    //message to display
    $("#table_del p#warning_content").html("Do you want to remove <b>" + fullname + "</b> from your database?");

    //fill form with needed details
    $("#table_del input[name=indexNumber]").val(item_id);
})

//delete all records button
$("button#del_all").click(function(){
    item_id = "all";
    fullname = "all records".toUpperCase();

    //display yes no modal box
    $("#table_del").removeClass("no_disp");

    //message to display
    $("#table_del p#warning_content").html("Are you sure you want to remove <b>" + fullname + "</b> from your database?");

    //fill form with needed details
    $("#table_del input[name=indexNumber]").val(item_id);
})

$("#table_del form").submit(function(e){
    e.preventDefault();
    response = formSubmit($(this), $("form[name=table_yes_no_form] input[name=submit]", false));
    if(response == true){
        if($("#table_del input[name=indexNumber]").val() == "all"){
            (".tabs .tab_btn.active").click();
        }

        //remove row from table
        registered = $("tr[data-index=" + $("#table_del input[name=indexNumber]").val() + "]").attr("data-register");
        $("tr[data-index=" + $("#table_del input[name=indexNumber]").val() + "]").remove();

        cssps_head = $(".content.cssps .head h2");
        comp_head = $(".content.reg_comp .head h2");
        not_comp_head = $(".content.reg_uncomp .head h2");
        
        $(cssps_head).html(parseInt($(cssps_head).html())-1);

        if(registered == "true"){
            $(comp_head).html(parseInt($(comp_head).html())-1);
        }else{
            $(not_comp_head).html(parseInt($(not_comp_head).html())-1);
        }
    }else{
        alert_box(response,"warning",8);
    }

    //reset form and delete
    $("#table_del form")[0].reset();
    $("#table_del").addClass("no_disp");
})

//close form
$("form[name=adminUpdateStudent] button[name=cancel]").click(function(){
    $("#updateStudent").addClass("no_disp");

    //clear loader content
    $("#updateStudent #getLoader").parent().addClass("no_disp").removeClass("flex");
    $("#updateStudent #getLoader").html("");
})

$("#updateStudent .item-event").click(function(){
    $("form[name=adminUpdateStudent] button[name=cancel]").click();
})

