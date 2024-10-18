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

    fileUpload($(this), $(this).find("button[name=submit]"), true).then((response) => {
        if(response == true){
            alert_box("Update was successful!");
        }else{
            alert_box(response, "danger", 8);
        }
    });
})