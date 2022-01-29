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
    $("#rhs .head #title #head").html($(this).children(".menu_name").children("span").text());

    //display the contents of the said property
    if($(this).attr("data-url")){
        //attain the height of rhs
        rhs_height = $("#rhs").height();

        if($(window).width() > 650 || $(window).width() < $(window).height()){
            rhs_height *= 0.8;
        }else{
            rhs_height *= 0.6;
        }

        rhs_height = Math.floor(rhs_height);

        //using ajax, get contents of document
        $.ajax({
            url: $(this).attr("data-url"),
            type: "GET",
            cache: false,
            dataType: "html",

            beforeSend: function(){
                element = "<div class=\"relative\" style=\"height: " + rhs_height + "px\">" + 
                            loadDisplay({
                                circular: true,
                                full: true
                            }) + 
                            "</div>";
                $("#rhs .body").html(element);
            },

            success: function(html){
                $("#rhs .body").html(html);
            },

            error: function(){
                errorElement = "<div class=\"item empty\"  style=\"height: " + rhs_height + "px\">\n" + 
                                "<p>This page is currently unavailable to you. Try again later</p>\n" + 
                                "</div>";
                $("#rhs .body").html(errorElement);
            }
        })
    }else{
        $("#rhs .body").html("No data is available");
    }
})

$("#ham").click(function(){
    $(this).toggleClass("clicked");
    $("body").toggleClass("ham_click");
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
            $("#gen_del button[name=no_button]").click();
        }
    })
})

$("#logout").click(function(){
    var url = location.protocol + '//' + location.host + "/" + location.pathname.split("/")[1];
    //var url = location.protocol + '//' + location.host + "/" + location.pathname.split("/")[0];

    //tell user he is logging out
    $("#rhs .body").html("Logging out, please wait...");

    location.href = url + "/admin/logout.php";
})

//generate reports
$("button.request_btn").click(function(){
    
})