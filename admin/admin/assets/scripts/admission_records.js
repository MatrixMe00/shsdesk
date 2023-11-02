$("select#school_result").change(function(){
    const val = $(this).val() === "" ? "empty" : $(this).val()
    $(".section_container.grade_table").addClass("no_disp")

    if(val != "both")
        $(".section_container#" + val).removeClass("no_disp")
    else
        $(".section_container.both").removeClass("no_disp")
})

$(document).ready(function(){
    $("select#school_result").change();
    $(".btn_connect").addClass("no_disp");

    $(".btn-item").click(function(){
        //get the associate elements
        const elements = $(this).attr("data-section");

        //change the marker to this one
        $(this).siblings("button:not(.plain-r)").addClass("plain-r");
        $(this).removeClass("plain-r");

        $(".btn_connect").addClass("no_disp");
        $("#" + elements).removeClass("no_disp");
    })

    $("#force_update").click(function(){
        const text1 = "Force an Update";
        const text2 = "Maintain Current";

        if($(this).text().toLowerCase() === text1.toLocaleLowerCase()){
            $(this).addClass("border b-red plain").html(text2);
        }else{
            $(this).removeClass("border b-red plain").html(text1);
        }
        $(".update_section").toggleClass("no_disp");
    })
})

$("form[name=recordsForm]").submit(function(){
    records = $(this).serialize() + "&submit=" + $(this).find("button[name=submit]").val()
    $.ajax({
        url: "admin/submit.php",
        data: records,
        timeout: 30000,
        beforeSend: function(){
            messageBoxTimeout("recordsForm","Updating...", "load",0)
        },
        success: function(response){
            if(response == true){
                messageBoxTimeout("recordsForm","Update complete", "success")
            }else{
                messageBoxTimeout("recordsForm",response,"error",8)
            }
        },
        error: function(xhr, textStatus){
            if(textStatus == "timeout"){
                messageBoxTimeout("recordsForm","Connection was timed out. Please check your network and try again", "error", 0)
            }
        }
    })
})

$("form[name=records_date]").submit(function() {
    const response = formSubmit($(this), $(this).find("button[name=submit]"), true);
    
    if(response === true || response == "true"){
        alert_box("Record submission date updated", "success");
        $("#lhs .active").click()
    }else{
        alert_box(response, "danger");
    }
})