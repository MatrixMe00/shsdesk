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


//uploading the excel file
$("form[name=importForm").submit(function(e){
    e.preventDefault();

    if($("input#import").val() == ""){
        messageBoxTimeout("importForm","No file has been chosen", "error");
    }else{
        //submit the form
        response = fileUpload($("form[name=importForm] input#import"), $(this), $("form[name=importForm] button[name=submit]"));

        if(response == true){
            message = "Your file has been received";
            type = "success";
        }else{
            type = "error";

            if(response == "no-file"){
                message = "No file has been chosen";
            }else if(response.includes("Upload failed")){
                message = "File could not be uploaded";
            }else if(response == "extension-error"){
                message = "Incorrect file type sent, please send correct file format"
            }else{
                message = response;
            }
        }

        messageBoxTimeout("importForm", message, type);
    }
})

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

//search button workout
$(".display .btn button[name=search_submit]").click(function(){
    search_value = $(this).parent().siblings("label[for=search]").children("input").val();

    dataString = "search_value=" + search_value + "&submit=" + $(this).val();

    table_foot = $(this).parents("#content").children(".body").children("table").children("tfoot");
    table_body = $(this).parents("#content").children(".body").children("table").children("tbody");

    //store initial data of the body
    init_data = $(table_body).html();

    td = $(table_foot).children("tr").children("td");

    if(search_value == ""){
        $(td).html("Search value is empty");
        $(table_foot).removeClass("no_disp");

        setTimeout(function(){
            $(table_foot).addClass("no_disp");
        },5000);
    }else{
        $(table_body).addClass("no_disp");

        $.ajax({
            url: $(this).parents("#content").children(".form.search").attr("data-action"),
            data: dataString,
            type: "get",
            dataType: "html",
            async: false,
            beforeSend: function(){
                //show a loading panel in foot
                $(td).html("Fetching Results...");
                $(table_foot).removeClass("no_disp");
            },
            success: function(html){
                if(html == "no-result"){
                    $(td).html("No results were found. Please make a valid search");
                    $(table_foot).removeClass("no_disp");
                }else{
                    if(html.includes("total=")){
                        val = html.split("total=");
                        
                        html = val[0];
                        total = val[1];
                        
                        //display new data into
                        $(table_body).html(html);
                        $(table_body).removeClass("no_disp");
                        
                        //display total table foot
                        $(table_foot).html(total + " results returned");
                        $(table_foot).removeClass("no_disp");
                    }else{
                        $(td).html("An error occured. Please try again later.");
                        $(table_foot).removeClass("no_disp");
                    }                    
                }
            },
            error: function(){
                $(td).html("An interruption has occured");
                $(table_foot, table_body).removeClass("no_disp");
            }
        })
    }
})