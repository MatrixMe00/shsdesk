$(document).ready(function(){
    //set the number of slider tabs
    $(".slider_counter").html(function(){
        number_of_details = $(".img_container img").length;
        $(this).html("");

        for (var i = 1; i <= number_of_details; i++){
            $(this).append("<span data-child='" + i + "'></span>");
        }

        //mark the first piece
        $(this).children("span:first-child").addClass("active");
    })

    $(".slider_counter span").click(function(){
        $(".slider_counter span").removeClass("active");

        //get the slide number
        child_number = $(this).attr("data-child");

        //display the slide
        slideshow(child_number);

        //mark this span as active
        $(this).addClass("active");
    })
})

//function for load event
/**
 * This function will be used to determine load events for message boxes
 * @param {string} size This parameter will hold the size of the buttons
 * @param {string} display This parameter will hold the type of shape the buttons should take
 * @param {string} animation This param is for taking animation classes
 * @param {boolean} full This is for verifying if the div should be for full screen or not
 * @param {string} span1 This is for taking the color for the first span
 * @param {string} span2 This is for taking the color for the second span
 * @param {string} span3 This is for taking the color for the third span
 * @param {string} span4 This is for taking the color for the fourth span
 * @returns {string} Returns a string of the created div element
 */
function loadDisplay(size = "", display = "", animation = "", full = false, span1 = "", span2 = "", span3 = "", span4 = ""){
    //initialize values with default
    if(size == ""){
        wide = $(window).width();

        if(wide < 480){
            size = "vsmall";
        }else if(wide < 720){
            size = "small";
        }else{
            size = "med";
        }
    }

    if(animation == ""){
        animation = "anim-swing";
    }

    if(display == ""){
        display = "semi-round";
    }

    if(span1 == ""){
        span1 = "red";
    }

    if(span2 == ""){
        span2 = "yellow";
    }

    if(span3 == ""){
        span3 = "green";
    }

    if(span4 == ""){
        span4 = "teal";
    }

    if(full){
        fullClass = "full";
    }else{
        fullClass = "";
    }

    load = "<div class=\"loader flex " + animation + " " + fullClass + "\">\n" +
            "<div class=\"span-container flex\">\n" +
                "<span class=\"" + size + " " + display + " " + span1 + "\"></span>\n" + 
                "<span class=\"" + size + " " + display + " " + span2 + "\"></span>\n" + 
                "<span class=\"" + size + " " + display + " " + span3 + "\"></span>\n" + 
                "<span class=\"" + size + " " + display + " " + span4 + "\"></span>\n" +
            "</div>\n" +
        "</div>\n";
    
        return load;
}

//function for the slideshow
function slideshow(i){
    //show the image
    $(".img_container img").hide();
    $(".img_container img:nth-child(" + i + ")").show();

    //show the content
    $(".description .detail").hide();
    $(".description .detail:nth-child(" + i + ")").fadeIn();
}

$(".prev").click(function(){
    //get the slider button which is active
    number = $(".slider_counter span").length;
    i = 1;

    for(i; i <= number; i++){
        if($(".slider_counter span:nth-child(" + i + ")").hasClass("active")){
            $(".slider_counter span:nth-child(" + i + ")").removeClass("active");

            if(i - 1 <= 0){
                i = number;
            }else{
                i -= 1;
            }

            $(".slider_counter span:nth-child(" + i + ")").addClass("active");

            slideshow(i);

            break;
        }
    }
})

$(".next").click(function(){
    //get the slider button which is active
    number = $(".slider_counter span").length;
    i = 1;

    for(i; i <= number; i++){
        if($(".slider_counter span:nth-child(" + i + ")").hasClass("active")){
            $(".slider_counter span:nth-child(" + i + ")").removeClass("active");

            if(i + 1 > number){
                i = 1;
            }else{
                i += 1;
            }

            $(".slider_counter span:nth-child(" + i + ")").addClass("active");

            slideshow(i);
            
            break;
        }
    }
})

//create a slideshow automatically
setInterval(function(){
    $(".next").click();
}, 10000);

//selecting school for admission
$("select[name=school_select]").change(function(){
    if($(this).val() != "NULL"){
        $("label[for=payment_button]").show();
        $("#results_case").hide();
    }else{
        $("label[for=payment_button]").hide();

        //bring back the previous tabs
        $(".case").css("display", "initial");
    }
})

//select change for end of semester results
$("select[name=school_select2").change(function(){
    if($(this).val() != "NULL"){
        $("label[for=year_level]").show();
        $("#school_admission_case").hide();
    }else{
        $("label[for=year_level]").hide();

        //bring back the previous tabs
        $(".case").css("display", "initial");
    }
})

$("select[name=year_level]").change(function(){
    if($(this).val() != ""){
        $("label[for=student_name]").show();
    }else{
        $("label[for=student_name]").hide();
    }
});

//show the results of the student
$("button[name=search]").click(function(){
    $("input[name=student_name]").val('');
    $("#results").show();
})

//hide the labels when the reset button is clicked
$("button[name=student_cancel_operation]").click(function(){
    $(".hide_label").hide();
    $(".case").css("display", "initial");
    $(".case select").val("NULL");
})

/*$("input").blur(function(){
    $(".tabs span.tab_button.active").click();
})
$("select").change(function(){
    $(".tabs span.tab_button.active").click();
})*/