$(document).ready(function(){
    //get all input labels and add their titles when they are focused
    $("label input").each(function(){
        //grab the html content
        message_span = "<span class='title_message'></span>";
        parent = $(this).parent();

        //add relative class to parent
        $(parent).addClass("relative");

        //check and see if this has a title addressed to it
        if($(this).attr("title") != null){
            $(parent).append(message_span);
            $(this).siblings(".title_message").html($(this).attr("title"));
        }
    })

    //add .textarea to all texarea labels
    $("textarea").parents("label").addClass("textarea");
})

$(".text_input").on({
    focus: function(){
        $(this).parent().css("border-bottom-color", "lightseagreen");
        $(this).parent().css("transition", "border-bottom-color 0.5s");
    },
    blur: function(){
        $(this).parent().css("border-bottom-color", "transparent");
        $(this).parent().css("transition", "border-bottom-color 0.5s");
    }
})

$("form").submit(function(e){
    e.preventDefault();
})

$("body").on("click", ".message_box .close", function(){
    const parent = $(this).parent().first();
    $(this).siblings("span.message").html('');
    parent.slideUp(200);
})

$("label input").click(function(){
    $(".message_box").slideUp(200);
})

//show passwords
$("input[name=show_password]").click(function(){
    if($(this).prop("checked") == true){
        $("input.password").prop("type", "text");
    }else{
        $("input.password").prop("type", "password");
    }
})

//timeout messages for messagebox
function messageBoxTimeout(form_name, message, message_type, time=5){
    //make form name css style
    form_name = "form[name=" + form_name + "] ";

    //change the time to miliseconds
    time = time * 1000;

    $(form_name + ".message_box").removeClass("error success load no_disp").addClass(message_type).show();
    $(form_name + ".message_box .message").html(message)

    //prevent the timeout function if the time is set to 0
    if(time > 0){
        setTimeout(function(){
            $(form_name + ".message_box").removeClass("error success load").addClass("no_disp");
            // $(form_name + ".message_box").slideUp();
            $(form_name + ".message_box .message").html('');
        }, time);
    }
}

//generate reports
$("button.request_btn").click(function(){
    // var url = location.protocol + '//' + location.host + "/" + location.pathname.split("/")[1];
    var url = location.protocol + '//' + location.host + "/" + location.pathname.split("/")[0];
    
    url += "/create_excel.php" + "?submit=" + $(this).val();
    
    location.href = url ;
})

//concerning the files that will be chosen
$("input[type=file]").change(function(){
    //get the value of the image name
    file_path = $(this).val();

    //strip the path name to file name only
    file_name = file_path.split("C:\\fakepath\\");

    //store the name of the file into the display div
    if(file_path != ""){
        $(this).siblings(".plus").hide();
        $(this).siblings(".display_file_name").html(file_name);       
    }else{
        $(this).siblings(".plus").css("display","initial");
        $(this).siblings(".display_file_name").html("Choose or drag your file here");
    }
})

// used for images or documents
$("input.file_input").change(function(){
    const label = $(this).parents("label").first();
    const show_file = parseInt($(this).attr("data-show-file"));

    if($(this).val() != ''){
        //show the selected image
        label.removeClass("no_disp");  

        //make the file ready for display
        var file = $(this).get(0).files[0];

        if(file){
            //create a variable to make a read class instance
            reader = new FileReader();

            reader.onload = function(){
                //pass the result to the image element
                $("#display_avatar img").attr("src", reader.result);
            }

            //make the reading data a demo url
            reader.readAsDataURL(file);
        }
    }else{
        //hide the selected image
        label.addClass("no_disp");

        //empty the image src
        $("#display_avatar img").prop("src", "");
    }
})