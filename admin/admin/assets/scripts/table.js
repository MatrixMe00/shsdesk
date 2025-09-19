$("table tbody tr .edit").click(function(){
    //initiate variables to be used to check which table to use
    cssps = false, student = false; db=""

    //determine which kind of table is being used
    if($(this).hasClass("cssps")){
        cssps = true;
    }else if($(this).hasClass("studs")){
        student = true;
    }

    if($(this).hasClass("db2")){
        db = "shsdesk2";
    }

    //take id number
    id_number = $(this).parents("tr").attr("data-index");

    dataString = "";
    if(cssps){
        registered = $(this).parents("tr").attr("data-register");
        dataString = "index_number=" + id_number + "&registered=" + registered + "&submit=fetchStudentDetails";
    }else if(student){
        dataString = "index_number=" + id_number + "&submit=fetchStudentsDetail";
    }

    dataString += "&db=" + db

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
        timeout: 30000,
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
                $("form[name=adminUpdateStudent] select[name=house]").val(response["houseID"]);
                
                if(cssps){
                    if(registered == "true"){
                        $("form[name=adminUpdateStudent] select[name=house]").prop("disabled", false);
                        $("form[name=adminUpdateStudent] label[for=house], form[name=adminUpdateStudent] label[for=enrolCode]")
                        .removeClass("no_disp");
                        $("form[name=adminUpdateStudent] input[name=enrolCode]").val(response["enrolCode"])
                    }else{
                        $("form[name=adminUpdateStudent] select[name=house]").prop("disabled", true);
                        $("form[name=adminUpdateStudent] label[for=house], form[name=adminUpdateStudent] label[for=enrolCode]")
                        .addClass("no_disp");
                    }

                    $("form[name=adminUpdateStudent] input[name=aggregate]").val(response["aggregate"]);
                    $("form[name=adminUpdateStudent] input[name=jhs]").val(response["jhsAttended"]);
                    $("form[name=adminUpdateStudent] input[name=dob]").val(response["dob"]);
                    $("form[name=adminUpdateStudent] input[name=track_id]").val(response["trackID"]);
                    $("form[name=adminUpdateStudent] input[name=guardian_contact]").val(response["guardian_contact"]);
                }else if(student){
                    $("form[name=adminUpdateStudent] select, form[name=adminUpdateStudent] input").css("color", "black");
                    $("form[name=adminUpdateStudent] label[for=house]").removeClass("no_disp");
                    
                    $("form[name=adminUpdateStudent] label[for=aggregate]").hide();
                    $("form[name=adminUpdateStudent] label[for=jhs]").hide();
                    $("form[name=adminUpdateStudent] label[for=dob]").hide();
                    $("form[name=adminUpdateStudent] label[for=track_id]").hide();
                    $("form[name=adminUpdateStudent] select[name=program_id]").val(response["program_id"]);
                    $("form[name=adminUpdateStudent] select[name=year_level]").val(response["studentYear"]);
                    $("form[name=adminUpdateStudent] input[name=guardianContact]").val(response["guardianContact"]);

                    $("form[name=adminUpdateStudent] button[name=submit]");
                }

                $("form[name=adminUpdateStudent] input[name=student_course]").val(response["programme"]);
                $("form[name=adminUpdateStudent] select[name=boarding_status]").val(response["boardingStatus"]);

                //display form
                $("#updateStudent form").removeClass("no_disp");
            }else if(response["status"] == "no-result" && student){
                alert_box("Data for requested student could not be found", "danger", 7);
                $("form[name=adminUpdateStudent] button[name=cancel]").click();
            }else{
                alert_box("Requested data could not be found", "danger");
                $("form[name=adminUpdateStudent] button[name=cancel]").click();
            }
        },
        error: function(r, textMessage){
            if(textMessage === "timeout"){
                alert_box("Connection was timed out or was slow. Please try again.", "danger", 10);
            }else if(r.responseText == "no-submission"){
                alert_box("No submission was detected. Please try again later", "danger", 7);
            }else{
                alert_box("Invalid request to server or an error occurred on the server. Response sent to console. Ask admin for help", "danger", 12);
                console.log(r.responseText);
            }
            $("form[name=adminUpdateStudent] button[name=cancel]").click();
        }
    })
})

$("table tbody tr .delete").click(function(){
    //initiate variables to be used to check which table to use
    cssps = false, student = false; db = ""

    //determine which kind of table is being used
    if($(this).hasClass("cssps")){
        cssps = true;
    }else if($(this).hasClass("studs")){
        student = true;
    }

    if($(this).hasClass("db2")){
        db = "shsdesk2";
    }

    item_id = $(this).parents("tr").attr("data-index");

    if(cssps){
        fullname = $(this).parents("tr").children("td.fullname").html();
    }else if(student){
        fullname = $(this).parents("tr").children("td.lname").html() + " " + $(this).parents("tr").children("td.oname").html();
    }

    //display yes no modal box
    $("#table_del").removeClass("no_disp");

    //message to display
    $("#table_del p#warning_content").html("Do you want to remove <b>" + fullname + "</b> from your records?");

    //fill form with needed details
    $("#table_del input[name=indexNumber]").val(item_id);
    $("#table_del input[name=db]").val(db)
})

//delete all records button
$("button#del_all").click(function(){
    item_id = "all";
    fullname = "all records".toUpperCase(), message = "";

    //display yes no modal box
    $("#table_del").removeClass("no_disp");

    //message to display
    if($(this).hasClass("studs")){
        message = "This will clear all <b>THIRD YEARS</b> from the system, and in turn promote all <b>STUDENTS</b> in the system currently to the next class<br>" +  
                    "Are you sure you want to proceed?";
        //update model that we are deleting from next database
        $("#table_del input[name=db]").val("shsdesk2");
    }else{
        message = "Are you sure you want to remove <b>" + fullname + "</b> from your database?";
        $("#table_del input[name=db]").val("");
    }
    $("#table_del p#warning_content").html(message);

    //fill form with needed details
    $("#table_del input[name=indexNumber]").val(item_id);
})

$("#table_del form").submit(function(e){
    e.preventDefault();

    if($("form[name=table_yes_no_form] input[name=addFirstYears]").val() == "true"){
        $("form[name=table_yes_no_form] input[name=submit]").val("addFirstYears");
    }else{
        $("form[name=table_yes_no_form] input[name=submit]").val("table_yes_no_submit");
    }

    response = formSubmit($(this), $("form[name=table_yes_no_form] input[name=submit]", false));
    if(response == true){
        if($("#table_del input[name=indexNumber]").val() == "all"){
            $("#lhs .menu .item.active").click();
        }

        if($("form[name=table_yes_no_form] input[name=addFirstYears]").val() == "true"){
            $("form[name=table_yes_no_form] input[name=submit]").val("table_yes_no_submit");
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
        alert_box(response,"danger", 10);
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