$(".menu_bar .name").click(function(){
    sibling = $(this).parent().siblings(".window_space");
    $(sibling).toggleClass("no_disp");
})

//clicking activate and deactivate buttons
$(".deactivate button, .activate button").click(function(){
    $("#modal_yes_no").removeClass("no_disp");

    //retrieve the school id
    s_id = $(this).attr("data-school-id");

    //parse id into secret form
    $("#yes_no_form input[name=sid]").val(s_id);
    $("#yes_no_form input[name=table]").val("schools");
})

//deactivate button click
$(".deactivate button").click(function(){
    $("#modal_yes_no p#warning_content").html("Do you want to deactivate <strong>" + 
    $(this).parents(".school_container").children(".menu_bar").children(".name").children("h3").html() + 
    "</strong> from this system?");

    //parse mode and submit value to secret form
    $("#yes_no_form input[name=mode]").val("deactivate");
})

//activate school button click
$(".activate button").click(function(){
    $("#modal_yes_no p#warning_content").html("Do you want to activate <strong>" + 
    $(this).parents(".school_container").children(".menu_bar").children(".name").children("h3").html() + 
    "</strong> on this system?");

    //parse mode and submit value to secret form
    $("#yes_no_form input[name=mode]").val("activate");
})