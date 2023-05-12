$(document).ready(function(){
    //get all input labels and add their titles when they are focused
    $("label input").val(function(){
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

$(".message_box .close").click(function(){
    $(".message_box span.message").html('');
    $(".message_box").slideUp(200);
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
    var url = location.protocol + '//' + location.host + "/" + location.pathname.split("/")[1];
    //var url = location.protocol + '//' + location.host + "/" + location.pathname.split("/")[0];
    
    url += "/create_excel.php" + "?submit=" + $(this).val();
    
    location.href = url ;
})