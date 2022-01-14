$("table tbody tr").click(function(){
    //take id number
    id_number = $(this).attr("data-index");
    registered = $(this).attr("data-register");

    dataString = "index_number=" + id_number + "&registered=" + registered + "&submit=fetchStudentDetails";

    //display form modal
    $("#updateStudent").removeClass("no_disp");

    //hide form
    $("#updateStudent form").addClass("no_disp");

    //display loader
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
                $("#updateStudent #getLoader").addClass("no_disp").html("");

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
                alert("data not found");
                $("form[name=adminUpdateStudent] button[name=cancel]").click();
            }
        },
        error: function(r){
            alert("Invalid request to server. Please try again later");
            $("form[name=adminUpdateStudent] button[name=cancel]").click();
        }
    })
})

//close form
$("form[name=adminUpdateStudent] button[name=cancel]").click(function(){
    $("#updateStudent").addClass("no_disp");

    //clear loader content
    $("#updateStudent #getLoader").addClass("no_disp").html("");
})

$("#updateStudent .item-event").click(function(){
    $("form[name=adminUpdateStudent] button[name=cancel]").click();
})