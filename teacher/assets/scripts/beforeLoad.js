//the loader container
const loader = "<div id=\"page_loading\" class=\"fixed center txt-fl\" style=\"z-index: 900; background-color: white\">" + 
                "<span>Page is loading <span id=\"circle-animate\" class=\"black sp-sm round\" style=\"width: 10px; height: 10px; display: inline-block\"></span></span>" + 
                "</div>"

//push loader into body
$("html").append(loader)
$("html").css("overflow", "hidden")

//opacity for the circle
opacity = 0;

//let animation display at least once
run_once = true;

//blinking animation using interval
blink = setInterval(function(){
    opacity = opacity == 0 ? 1 : 0
    $("#circle-animate").css("opacity", opacity)
    $("#circle-animate").toggleClass("sm-lg-l")
}, 500)

timer = setInterval(function(){
    if($("body") && $("body").html() != "" && !run_once){
        clearInterval(timer)
        clearInterval(blink)
        $("#page_loading").remove();
        $("html").css("overflow", "auto")
    }

    //declare that it has finished running once
    run_once = false
}, 2000)