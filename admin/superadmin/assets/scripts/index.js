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