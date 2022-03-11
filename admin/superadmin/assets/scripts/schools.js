// $(".menu_bar .name").click(function(){
//     sibling = $(this).parent().siblings(".window_space");
//     $(sibling).toggleClass("no_disp");
// })

//clicking activate and deactivate buttons
$(".item-event.deactivate, .item-event.activate").click(function(){
    $("#gen_del").removeClass("no_disp");

    //retrieve the school id
    s_id = $(this).attr("data-school-id");

    //parse id into secret form
    $("#yes_no_form input[name=sid]").val(s_id);
    $("#yes_no_form input[name=table]").val("schools");
})

//deactivate button click
$(".item-event.deactivate").click(function(){
    $("#gen_del p#warning_content").html("Do you want to deactivate <strong>" + 
    $(this).parents(".user_container").children(".top").children("h3").html() + 
    "</strong> from this system?");

    //parse mode and submit value to secret form
    $("#yes_no_form input[name=mode]").val("deactivate");
})

//activate school button click
$(".item-event.activate").click(function(){
    $("#gen_del p#warning_content").html("Do you want to activate <strong>" + 
    $(this).parents(".user_container").children(".top").children("h3").html() + 
    "</strong> on this system?");

    //parse mode and submit value to secret form
    $("#yes_no_form input[name=mode]").val("activate");
})

//clicking tab button
$(".tabs .tab_btn").click(function(){
    //make this button active
    $(".tab_btn.active").removeClass("active");
    $(this).addClass("active");

    //display desired menu detail
    $("#edit_modal .edit_detail p").addClass("no_disp");
    $("#" + $(this).attr("data-det-id")).removeClass("no_disp");

    //grab school id
    school_id = $("#edit_modal #school_select").html();

    //grab current tab content
    content_box = $(".tabs .tab_btn.active").attr("data-det-id");

    //retrieve data
    $.ajax({
        url: "superadmin/submit.php",
        data: "submit=fetchEdit&school_id=" + school_id + "&content_box=" + content_box,
        beforeSend: function(){
            //show loader if there is no content
            htm = $("#edit_modal .content .content_box." + content_box).html();

            if(htm == ""){
                $("#edit_modal .content .content_box." + content_box).addClass(
                    "flex flex-center-content flex-center-align"
                    ).html(loadDisplay({
                        circular: true
                    })
                );
            }
        },
        success: function (html){
            //display all tabs
            $(".tabs .tab_btn").removeClass("no_disp");

            $("#edit_modal .content .content_box").addClass("no_disp");
            $("#edit_modal .content .content_box." + content_box).removeClass(
                  "flex flex-center-content flex-center-align no_disp"
                ).html(html);
        },
        error: function (){
            $("#edit_modal .content .content_box." + content_box).addClass(
                  "flex flex-center-content flex-center-align"
                ).html("An error occured. Please reload the page and try again, else contact admin for support");
        }
    })
})

$("#edit_modal #wrapper > .foot .close").click(function(){
    $("#edit_modal").addClass("no_disp");

    //reset tab to first one
    $(".tabs .tab_btn.active").removeClass("active");
    $(".tabs .tab_btn:first-child").addClass("active");

    //reset menu detail
    $("#edit_modal .edit_detail p").addClass("no_disp");
    $("#edit_modal .edit_detail p:first-child").removeClass("no_disp");

    //reset content boxes
    $("#edit_modal .content .content_box").addClass("no_disp");
    $("#edit_modal .content .content_box:first-child").removeClass("no_disp");
    $("#edit_modal .content .content_box").html("");
})

//show edit options
$(".item-event.edit").click(function(){
    //display edit modal box
    $("#edit_modal").removeClass("no_disp");

    //pass school id into edit box
    $("#edit_modal #school_select").html($(this).attr("data-school-id"));

    //click the first tab automatically
    $(".tabs .tab_btn:first-child").click();
})

//clear all records of school
$(".item-event.clear").click(function(){
    //display the general delete box
    $("#gen_del").removeClass("no_disp");

    //display a message
    $("#gen_del p#warning_content").html("Do you want to clear every detail of <strong>" + 
    $(this).parents(".user_container").children(".top").children("h3").html() + 
    "</strong> from this system?<br>These details includes all records of your students, houses " +
    "exeat details");

    //retrieve the school id
    s_id = $(this).attr("data-school-id");

    //parse id into secret form
    $("#yes_no_form input[name=sid]").val(s_id);
    $("#yes_no_form input[name=table]").val("schools");
    $("#yes_no_form input[name=mode]").val("clear_school");
})