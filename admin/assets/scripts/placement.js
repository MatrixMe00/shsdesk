$("form[name=importForm] input[type=file]").change(function(){
    //get the value of the image name
    image_path = $(this).val();

    //strip the path name to file name only
    image_name = image_path.split("C:\\fakepath\\");

    //store the name of the file into the display div
    if(image_path != ""){
        $("#plus").hide();
        $("#display_file_name").html(image_name);
    }else{
        $("#plus").css("display","initial");
        $("#display_file_name").html("Choose or drag your file here");
    }
})

//open the registered students contents section
$(".title_bar #close").click(function(){
    $(this).toggleClass("clicked");
    $("#display #content").slideToggle();

    title = $(this).attr("title");
    if(title == "Hide Details"){
        title = "Show Details";
    }else{
        title = "Hide Details";
    }

    $(this).prop("title", title);
})


///this function will be used to parse a common timeout function for the message box
function messageBoxTimeout(message, message_type, time=5){
    //change the time to miliseconds
    time = time * 1000;

    $("#message_box").removeClass("error success load").addClass(message_type).show();
    $("#message_box .message").html(message)

    //prevent the timeout function if the time is set to 0
    if(time > 0){
        setTimeout(function(){
            $("#message_box").removeClass("error success load");
            $("#message_box").slideUp();
            $("#message_box .message").html('');
        }, time);
    }
}


//uploading the excel file
$("form[name=importForm").on('submit',(function(e){
    e.preventDefault();

    if($("input#import").val() == ""){
        messageBoxTimeout("No file has been chosen", "error");
    }else{
        //grab the file uploaded
        file = $("input#import").prop("files")[0];
        submit = $("form[name=importForm] button[name=submit]").val();
        alert(submit);

        //create a new form data
        form_data = new FormData();

        //append the data into the form
        form_data.append("import", file);
        form_data.append("submit", submit);

        //parse data into ajax
        $.ajax({
            url: $(this).prop("action"),
            contentType: false,
            data: form_data,
            type: "POST",
            dataType: "text",
            cache: false,

            beforeSend: function(){
                messageBoxTimeout("Uploading...", "load", 0);
            },
            success: function(html){
                message = "";
                message_type = "";
                time = 5;

                if(html == "no-file"){
                    message = "No file was found! Please upload an excel file to continue";
                    message_type = "error";
                }else{
                    message = html;
                    message_type = "load";
                    time = 0;
                }

                //display according to reply received
                messageBoxTimeout(message, message_type, time);
            },
            error: function(){
                messageBoxTimeout("Error communicating with the server! Please try again later.", "error", 0);
            }
        });
    }
}))
/*$("form[name=importForm]").submit(function(e){
    e.preventDefault();

    if($("input#import").val() == ""){
        messageBoxTimeout("No file has been chosen", "error");
    }else{
        //grab the file uploaded
        file = $("input#import").prop("files")[0];
        submit = $("form[name=importForm] button[name=submit]").val();
        alert(submit);

        //create a new form data
        form_data = new FormData();

        //append the data into the form
        form_data.append("import", file);
        form_data.append("submit", submit);

        //parse data into ajax
        $.ajax({
            url: $(this).prop("action"),
            contentType: false,
            data: new form_data,
            type: "POST",
            dataType: "text",
            cache: false,

            beforeSend: function(){
                messageBoxTimeout("Uploading...", "load", 0);
            },
            success: function(html){
                message = "";
                message_type = "";
                time = 5;

                if(html == "no-file"){
                    message = "No file was found! Please upload an excel file to continue";
                    message_type = "error";
                }else{
                    message = html;
                    message_type = "load";
                    time = 0;
                }

                //display according to reply received
                messageBoxTimeout(message, message_type, time);
            },
            error: function(){
                messageBoxTimeout("Error communicating with the server! Please try again later.", "error", 0);
            }
        });
    }
})*/

//when the import form is closed
$("form[name=importForm] button[name=close]").click(function(){
    //hide the form
    fadeOutElement($("#modal_2"));

    //display that the file input has nothing
    $("#plus").css("display","initial");
    $("#display_file_name").html("Choose or drag your file here");

    //hide the message box
    $("#message_box").hide();
})