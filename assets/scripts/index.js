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
        $("label[for=payment_button]").removeClass("no_disp");
        $("#results_case").addClass("no_disp");

        //hide other case
        $(".case").addClass("no_disp");
        $(this).parents(".case").removeClass("no_disp");
    }else{
        $("button[name=student_cancel_operation]").click();
    }
})

//select change for end of semester results
$("select[name=school_select2").change(function(){
    if($(this).val() != "NULL"){
        $("label[for=year_level]").removeClass("no_disp");
        $("#school_admission_case").addClass("no_disp");

        //hide other case
        $(".case").addClass("no_disp");
        $(this).parents(".case").removeClass("no_disp");
    }else{
        $("button[name=student_cancel_operation]").click();
    }
})

//choose a year level
$("select[name=year_level]").change(function(){
    if($(this).val() != "NULL"){
        $("label[for=student_name]").removeClass("no_disp");
    }else{
        $("label[for=student_name]").addClass("no_disp");
    }
});

//show the results of the student
$("button[name=search]").click(function(){
    //take student's name
    $("input[name=student_name]").val('');
    $("#results").removeClass("no_disp");

    //an ajax call will happen here
})

$(".hide_label #student_name").keyup(function(){
    if($(this).val().length >= 4)
        $("button#res_search").parent().removeClass("no_disp");
});

//hide the labels when the reset button is clicked
$("button[name=student_cancel_operation]").click(function(){
    $(".hide_label").addClass("no_disp");
    $(".hide_label #student_name").val("");
    $(".case").removeClass("no_disp");
    $(".case select").val("NULL");
})

/*$("input").blur(function(){
    $(".tabs span.tab_button.active").click();
})
$("select").change(function(){
    $(".tabs span.tab_button.active").click();
})*/