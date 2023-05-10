//concerning the files that will be chosen
$("input[type=file]").change(function(){
    //get the value of the image name
    image_path = $(this).val();

    //strip the path name to file name only
    image_name = image_path.split("C:\\fakepath\\");

    //store the name of the file into the display div
    if(image_path != ""){
        $(this).siblings(".plus").hide();
        $(this).siblings(".display_file_name").html(image_name);       
    }else{
        $(this).siblings(".plus").css("display","initial");
        $(this).siblings(".display_file_name").html("Choose or drag your file here");
    }
})

//grab original image url
school_image = $("#display_avatar img").attr("src");

//the avatar of te school
$("input[name=avatar]").change(function(){
    if($(this).val() != ''){
        //show the selected image
        $("label[for=display_avatar]").show();  

        //make the file ready for display
        var file = $("input[type=file]").get(0).files[0];

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
        //return to the default image src
        $("#display_avatar img").prop("src", school_image);
    }
})

$("form[name=admissiondetailsForm]").submit(function(e){
    e.preventDefault();
    
    formData = new FormData();

    //preparing file and submit values
    file1 = $("input[name=avatar]").prop("files")[0];
    file1_name = $("input[name=avatar]").attr("name");
    file2 = $("input[name=prospectus]").prop("files")[0];
    file2_name = $("input[name=prospectus]").attr("name");

    submit_value = $("form[name=admissiondetailsForm] button[name=submit]").prop("value");

    //strip form data into array form and attain total data
    form_data = $(this).serializeArray();
    split_lenght = form_data.length;

    //loop and fill form data
    counter = 0;
    while(counter < split_lenght){
        //grab each array data
        new_data = form_data[counter];

        key = new_data["name"];
        value = new_data["value"];

        //append to form data
        formData.append(key, value);

        //move to next data
        counter++;
    }

    //append name and value of file
    formData.append(file1_name, file1);
    formData.append(file2_name, file2);

    //append submit if not found
    if(!$(this).serialize().includes("&submit=")){
        formData.append("submit", submit_value + "_ajax");
    }

    response = null;
    
    $.ajax({
        url: $(this).attr("action"),
        data: formData,
        method: "post",
        dataType: "text",
        cache: false,
        async: false,
        contentType: false,
        processData: false,
        timeout: 8000,
        beforeSend: function(){
            message = loadDisplay({size: "small"});
            type = "load";
            time = 0;
                
            messageBoxTimeout($(this).prop("name"), message, type, time);    
        },
        success: function(text){
            $("form[name=" + $(this).prop("name") + "] .message_box").addClass("no_disp");
            if(text == "success" || text.includes("success")){
                alert_box("Update was successful!");
            }else{
                alert_box(text, "danger", 8);
            }
        },
        error: function(xhr, textMessage){
            if(textMessage == "timeout"){
                message = "Connection was timed out. Please take a moment and try again"
            }else{
                message = "Please check your internet connection and try again"
            }
            
            type = "error";

            messageBoxTimeout($(this).prop("name"), message, type);
        }
    })
})