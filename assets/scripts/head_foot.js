//getting the height of the window
var document_height = $(window).height();
var percentile = document_height * 0.001;

$(document).scroll(function() {
    var scrolling_position = $(window).scrollTop();
    //change the scroll position to percentage
    scrolling_position = (scrolling_position / document_height) * 100;

    //get the parent element
    const parent = $("body").attr("id");

    if(scrolling_position > percentile){
        $("nav.no-sticky").css("background-color", "#222");
        $("nav.no-sticky").removeClass("absolute").addClass("sticky");
    }else{
        $("nav.no-sticky").removeClass("sticky").addClass("absolute");
        if(parent == "index_main"){
            $("nav.no-sticky").css("background-color", "transparent");
        }
    }
})

//display the nav buttons when the ham button is clicked
$("#ham_button").click(function(){
    $("#buttons").toggleClass("flex");
    $(this).toggleClass("click");
})

//go to the home page when the logo is clicked
$("#logo").click(function(){
    location.href = $("nav #buttons a.button:first-child").attr("href");
})

//disable inspection of code
$(document).bind("contextmenu", function(e){
    //e.preventDefault();
})

$(document).keydown(function(e){
    if(e.which === 123){
        return false;
    }
})