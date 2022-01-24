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
        //hide the selected image
        $("label[for=display_avatar]").hide();

        //empty the image src
        $("#display_avatar img").prop("src", "");
    }
})

$("input[name=other_category]").blur(function(){
    $("select[name=category]").val($(this).val());
})

//unbind the prevent default
$("form").unbind("submit");

//dynamically help the user when typing the phone number
$("input.tel").keyup(function(){
    val = $(this).val();

    //give a default maximum length
    maxlength = 10;

    //check if the user is starting from 0 or +
    if(val[0] == "+" && val.includes(" ")){
        maxlength = 16;
    }else if(val[0] == "+" && !val.includes(" ")){
        maxlength = 13;
    }else if(val[0] == "0" && val.includes(" ")){
        maxlength = 12;
    }else if(val[0] == "0" && !val.includes(" ")){
        maxlength = 10;
    }else{
        $(this).val('');
    }

    //change the maximum length
    $(this).prop("maxlength",maxlength);
})

//remove spaces from phone number
$("input.tel").blur(function(){
    i = 0;
    value = $(this).val();

    if(value.includes(" ")){
        //split value
        value = value.split(" ");
        length = 0;
        new_value = "";

        //join separate values
        while(length < value.length){
            new_value += value[length];
            length++;
        }

        //pass new value into value
        value = new_value;
    }

    //convert value into +233
    if(value[0] == "0"){
        new_val = "+233";

        //grab from second value
        i = 1;

        while(i <= 9){
            new_val += value[i];
            i++;
        }

        value = new_val;
    }

    $(this).val(value);
})