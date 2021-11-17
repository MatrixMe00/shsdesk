//getting the height of the window
var document_height = $(window).height();
var percentile = document_height * 0.01;

var url = location.protocol + '//' + location.host + "/" + location.pathname.split("/")[1];

//get the parent element
var parent = $("nav").parent().attr("id");

$(document).scroll(function() {
    scrolling_position = $(window).scrollTop();

    //change the scroll position to percentage
    scrolling_position = (scrolling_position / document_height) * 100;

    if(scrolling_position > percentile){
        $("nav").css("background-color", "#222");
        $("nav").css("position", "fixed");
    }else{
        if(parent == "index_main"){
            $("nav").css("position", "absolute");
            $("nav").css("background-color", "transparent");
        }else{
            $("nav").css("position", "relative");
            $("main").css("margin-top", "unset");
        }
        
    }
})

$(document).ready(function(){
    //get a default minimum height for the main content
    nav_height = $("nav").height();
    foot_height = $("footer").height();
    doc_height = $(document).height();

    main_height = doc_height - (nav_height + foot_height) - 50;

    $("main").css("min-height", main_height);
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