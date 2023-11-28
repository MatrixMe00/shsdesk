var timer = null;

//create a slideshow automatically
function slideshowTimer(){
    //clear the last slideshow timer
    clearInterval(timer);

    timer = setInterval(function(){
        $(".next").click();
    }, 10000);
}

// Function to navigate to the previous slide
$(".prev").click(function () {
    navigateSlider(-1);
});

// Function to navigate to the next slide
$(".next").click(function () {
    navigateSlider(1);
});

// Function to handle slider navigation
function navigateSlider(direction) {
    const activeIndex = $(".slider_counter span.active").index() + 1;
    const totalSlides = $(".slider_counter span").length;
    const newIndex = (activeIndex + direction + totalSlides) % totalSlides || totalSlides;

    $(".slider_counter span").removeClass("active").eq(newIndex - 1).addClass("active");
    slideshow(newIndex);
}

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

//hide the labels when the reset button is clicked
$("button[name=student_cancel_operation]").click(function(){
    //hide or show labels
    $(".hide_label").addClass("no_disp");
    $(".case, label[for=student_check]").removeClass("no_disp");

    //clear fields
    $("input#student_index_number").val("");
    $("select[name=school_select]").prop("selectedIndex", 0);
    $("select[name=school_select]").children("option.selected").
        text("").val("");

    //disable and reset buttons
    $("button#student_check").prop("disabled", false).html("Check");
})

//alert users when they have to fill a required form
$("#admission input").blur(function() {
    formRequiredCheck($(this));
})

$("#admission select").change(function() {
    formRequiredCheck($(this));
})

//retrieve the school of the student via index number
$("button#student_check").click(function(){
    const this_element = $(this);
    const index = $("input#student_index_number").val();
    $("input#student_check").prop('data-index',index);
    if(index === ""){
        alert_box("Index Number is required", "danger", 7);
        $("input#student_index_number").focus();
        return false;
    }else if(index.length < 5){
        alert_box("Your index number is too short", "danger", 7);
        $("input#student_index_number").focus();
        return false;
    }
    // dataString = "indexNumber=" + index + "&submit=studentSchool";
    $.ajax({
        url: "submit.php",
        data: {
            indexNumber: index, submit: "studentSchool"
        },
        type: "GET",
        dataType: "json",
        timeout: 30000,
        beforeSend: function(){
            this_element.prop("disabled", true);
            this_element.html("Checking...");
        },
        success: function(response_data){
            this_element.prop("disabled", false).html("Check");
            
            const data = response_data;
            
            if(data["status"] == "success"){                
                //save student's selected school
                $("#school_select option.selected").attr("value", data["schoolID"]).html(data["schoolName"]);

                //hide this button
                this_element.parent().addClass("no_disp");

                //preview the payment button and save index number in admission text field
                $("#school_select").val(data["schoolID"]).change();
                $("span#res_ad_index").html(index);

                //show success message
                alert_box(data["successMessage"], "success", 7);
            }else{
                // show error message
                alert_box(data["status"], "danger", 10);
            }
        },
        error: function(status, textStatus){
            let message = ""
            if(textStatus == "timeout"){
                message = "Connection was timed out due to a slow network. Please try again later"
            }else{
                message = status.responseText;
                console.log(status);
            }
            alert_box(message, "danger", 10);

            this_element.prop("disabled", false).html("Check");
        }
    })
})

//get contact of admin
$("select#getContact").change(function(){
    if($(this).val() != ""){
        $.ajax({
            url: "submit.php",
            data: "submit=getContact&schoolID=" + $(this).val(),
            dataType: "text",
            timeout: 30000,
            beforeSend: function(){
                $("span#contResult").html("Fetching contact...");
            },
            success: function(data){
                if(data != "")
                    $("span#contResult").html(data);
                else
                    $("span#contResult").html("no results were returned");
            },
            error: function(data, textStatus){
                let message = ""
                if(textStatus == "timeout"){
                    message = "Connection was timed out due to a slow network. Please try again later"
                }else{
                    message = JSON.stringify(data)
                }
                $("span#contResult").html(message);
            }
        })
    }else{
        $("span#contResult").html("");
    }
})

$(document).ready(function(){
    //mark all required fields as red
    $("#admission input[required], #admission input.required, " + 
    "#admission select[required], #admission select.required").each(function(){
        $(this).css("border","1px solid red");
    })

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

    //start the slideshow
    slideshowTimer();
})