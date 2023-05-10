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
        timeout: 8000,
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
        error: function (xhr, textStatus){
            let message = ""

            if(textStatus == "timeout"){
                message = "Connection was timed out due to a slow network. Please try again later"
            }else{
                message = "An error occured. Please reload the page and try again, else contact admin for support"
            }
            $("#edit_modal .content .content_box." + content_box).addClass(
                  "flex flex-center-content flex-center-align"
                ).html(message);
        }
    })
})