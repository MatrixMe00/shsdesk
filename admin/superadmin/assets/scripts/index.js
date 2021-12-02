$(".item").click(function(){
    //remove and add the active class
    $(".item").removeClass("active");
    $(this).addClass("active");

    //get the parent element
    parent = $(this).parent();

    //remove and add active class to the head element
    $(".menu .head").removeClass("active");
    $(parent).children(".head").addClass("active");

    //change the contents on the rhs
    $("#rhs .head #title").html($(this).children(".menu_name").children("span").text());

    //display the contents of the said property
    if($(this).attr("data-url")){
        $.ajax({
        url: $(this).attr("data-url"),
        type: "GET",
        cache: false,
        dataType: "html",

        beforeSend: function(){
            $("#rhs .body").html("Please wait...");
        },

        success: function(html){
            $("#rhs .body").html(html);
        },

        error: function(){
            $("#rhs .body").html("Requested page could not be loaded. Please try again later. If this continues after three spaced tries, contact the admin for help.");
        }
    })
    }else{
        $("#rhs .body").html("No data is available");
    }
})

$("#ham").click(function(){
    $("#lhs").toggleClass("menu_click");
    $(this).toggleClass("clicked");
})

$("#user_control").click(function(){
    $("#logout").slideToggle();
})

//yes no form submission
$("#yes_no_form").submit(function(){
    submit_val = $("#yes_no_form input[name=submit]").val();

    if(!submit_val.includes("_ajax"))
        submit_val += "_ajax";

    $("#yes_no_form input[name=submit]").val(submit_val);

    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        type: "get",
        dataType: "text",

        success: function(text){
            if(text == "update-success"){
                $("#lhs .menu .item.active").click();
            }else if(text == "update-error"){
                alert("Unable to cause change. Please try again later");
            }else{
                alert(text);
            }
        },

        error: function(){
            alert("Please communication could not be established");
        },

        complete: function(){
            $("#modal_yes_no button[name=no_button]").click();
        }
    })
})