const loader = "<div id=\"page_loading\" class=\"absolute center txt-fl white\" style=\"z-index: 900\">" + 
                "<span>Page is loading <span id=\"circle-animate\" class=\"black sp-sm round\" style=\"width: 10px; height: 10px; display: inline-block\"></span></span>" + 
                "</div>"

//push loader into body
$("html").append(loader)
$("body").css("overflow", "hidden")
opacity = 0;

blink = setInterval(function(){
    opacity = opacity == 0 ? 1 : 0
    $("#circle-animate").css("opacity", opacity)
    $("#circle-animate").toggleClass("sm-lg-l")
}, 500)

timer = setInterval(function(){
    if($("body") && $("body").html() != ""){
        clearInterval(timer)
        clearInterval(blink)
        $("#page_loading").remove();
        $("body").css("overflow", "auto")
    }
}, 2000)