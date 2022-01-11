$("form").submit(function(e){
    e.preventDefault();

    dataString = $(this).serialize() + "&submit=" + $("button[name=submit]").val();

    $.ajax({
        url: $(this).attr("action"),
        data: dataString,
        dataType: "html",
        cache: true,
        type: "POST",

        beforeSend: function(){
            message = "Please wait...";
            message_type = "load";
            time = 0;

            messageBoxTimeout("contactForm",message, message_type, time);
        },

        success: function(html){
            message = "";
            message_type = "";
            time = 5;

            if(html == "fname_long"){
                message = "Your fullname is too long";
                message_type = "error";
            }else if(html == "fname_short"){
                message = "Your fullname is too short or empty";
                message_type = "error";
            }else if(html == "email_long"){
                message = "Your email is too long";
                message_type = "error";
            }else if(html == "email_short"){
                message = "Your email is too short or empty";
                message_type = "error";
            }else if(html == "eformat"){
                message = "You have provided a wrong email format";
                message_type = "error";
            }else if(html == "message_long"){
                message = "Your message is too long. Limit it to 500 characters";
                message_type = "error";
            }else if(html == "message_short"){
                message = "Your message is too short or empty. It should not be less than 10 characters.";
                message_type = "error";
            }else if(html == "true"){
                message = "Message was sent successfully";
                message_type = "success";
            }else{
                message = html;
                message_type = "error";
                time = 10;
            }

            messageBoxTimeout("contactForm",message, message_type, time);
        },

        error: function(){
            message = "Communication could not be established! Please check your connection and try again later";
            message_type = "error";
            time = 5;

            messageBoxTimeout("contactForm",message, message_type, time);
        }
    })
})