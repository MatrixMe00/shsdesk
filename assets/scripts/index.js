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