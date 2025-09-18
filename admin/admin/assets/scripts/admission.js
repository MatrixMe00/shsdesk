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
    tinymce.triggerSave();
    e.preventDefault();

    fileUpload($(this), $(this).find("button[name=submit]"), true).then((response) => {
        if(response == true){
            alert_box("Update was successful!");
        }else{
            alert_box(response, "danger", 8);
        }
    });
})

$('#multi_prospectus').on('change', function () {
    if ($(this).is(':checked')) {
        $('#multi_prospectus_container').removeClass('no_disp');
        $('#single_prospectus').addClass('no_disp');
    } else {
        $('#multi_prospectus_container').addClass('no_disp');
        $('#single_prospectus').removeClass('no_disp');
    }
});

$('#letter_prefix').on('change', function () {
    $("#prefix-container").toggleClass('no_disp', !$(this).is(':checked'));
});

function prefix_preview() {
    // Get values
    let prefix = $('#prefix_text').val().trim();
    let yearOpt = $('#prefix_year').val();
    let yearVal = $('#prefix_year option:selected').data('current-year') || '';
    let separator = $('#prefix_separator').val() || '';

    // Use fallback if prefix is empty
    if (!prefix) {
        prefix = 'ADM';
    }

    // Build admission number example
    let parts = [];
    if (prefix) parts.push(prefix);
    if (yearOpt) parts.push(yearVal);

    // Always append a sample number
    parts.push('0001');

    let preview = parts.join(separator);

    // Update preview span
    $('#prefix_preview').text(preview);
}

$(document).ready(function() {
    prefix_preview();
});

$('#prefix_text, #prefix_year, #prefix_separator').on('input change', function() {
    prefix_preview();
});

$(".remove-template").on("click", function() {
    const confirm = window.confirm("Are you sure you want to remove this template instead of replacing?");

    if(confirm){
        $("input[name=old_admission_template]").val("");
        $(this).addClass("info").html("Save form to finalize changes");
    }
    
});