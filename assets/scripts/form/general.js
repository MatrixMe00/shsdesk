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

$("#message_box .close").click(function(){
    $("#message_box span.message").html('');
    $("#message_box").slideUp(200);
})

$("label input").click(function(){
    $("#message_box").slideUp(200);
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

    $(form_name + "#message_box").removeClass("error success load").addClass(message_type).show();
    $(form_name + "#message_box .message").html(message)

    //prevent the timeout function if the time is set to 0
    if(time > 0){
        setTimeout(function(){
            $(form_name + "#message_box").removeClass("error success load");
            $(form_name + "#message_box").slideUp();
            $(form_name + "#message_box .message").html('');
        }, time);
    }
}