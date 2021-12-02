//edit the carousel button
$("input[name=item_button]").change(function(){
    check = $(this).prop("checked");

    if(check == true){
        $("label[for=button_text], label[for=button_url]").removeClass("no_disp");
    }else{
        $("label[for=button_text], label[for=button_url], label[for=button_text_input]").addClass("no_disp");

        //clear value of selected button
        $("input[name=real_button_text], input[name=button_text]").val("");

        //remove selected from all selected buttons
        $("button[name=button_text].selected").removeClass("selected");
    }
})

$("form[name=carouselForm]").submit(function(e){
    e.preventDefault();

    //upload form with files
    response = fileUpload($("form[name=carouselForm] input[name=item_img]"), 
    $("form[name=carouselForm]"), $("form[name=carouselForm] button[name=submit]"));

    if(response == true){
        alert("Carousel Was Added Successfully");
        $("#lhs .menu .item.active").click();
    }else if(response == "error"){
        alert("A problem occured on the server. Please try again")
    }
})

//enable the selected button
$("button[name=button_text]").click(function(e){
    e.preventDefault(false);
    //get the text
    text = $(this).html();

    //remove selected from all selected buttons
    $("button[name=button_text].selected").removeClass("selected");

    $(this).addClass("selected");

    if(text != "Enter" && text != "Explore"){
        $("label[for=button_text_input]").removeClass("no_disp");
        $("input[name=button_text").focus();
    }else{
        $("label[for=button_text_input]").addClass("no_disp");

        //transfer value into real deal
        $("input[name=real_button_text]").val($(this).val());
    }
})

$("input[name=button_text]").blur(function(){
    //transfer value into real deal
    $("input[name=real_button_text]").val($(this).val());
})

//click the head of the carousel to show or hide body
$(".section_main_block .head").click(function(){
    if($(this).siblings(".body").hasClass("no_disp")){
        $(".section_main_block>.body").addClass("no_disp");
        $(".section_main_block .head .close.clicked").removeClass("clicked");
        
        $(this).siblings(".body").removeClass("no_disp");
        $(this).children(".close").addClass("clicked");
    }else{
        $(this).siblings(".body").addClass("no_disp");
        $(this).children(".close").removeClass("clicked");
    }
})

//open or close the contents of a windowed tab
$(".item .title_bar.top").click(function(){
    //close all other and reset all item buttons
    $(".section_main_block .body .item .middle").slideUp();
    $(".section_main_block .body .item .edit").addClass("no_disp");

    if($(this).parents(".item").hasClass("active")){
        //mark parents as inactive
        $(this).parents(".body").children(".item").removeClass("active");

        //send close to plus
        $(this).children(".close").removeClass("clicked");
    }else{
        //remove active from open tabs
        $(".section_main_block .body .item.active").removeClass("active");

        //mark parent as active
        $(this).parents(".item").addClass("active");

        //send close to minus and declare this as opened
        $(".item .title_bar .close.clicked").removeClass("clicked");
        $(this).children(".close").addClass("clicked");

        //get the middle and edit elements
        desc = $(this).siblings(".middle");
        edit = $(this).siblings(".edit");

        //display the middle class
        $(desc).slideDown();

        //switch the title of the close button
        title = $(this).children(".close").attr("title");
        if(title == "Hide Details"){
            title = "Show Details";
        }else{
            title = "Hide Details";
        }

        $(this).children(".close").prop("title", title);

        //show or hide the edit menu
        $(edit).toggleClass("no_disp");
    }
})

//edit a content
$(".span_edit").click(function(){
    //check its html name and work with it
    html = $(this).html();

    if(html == "Edit"){
        //change to cancel
        $(this).html("Cancel");

        //add the edit feature to the description element
        $(this).parent().siblings(".middle").children(".desc").prop("contenteditable",true).focus();
    }else if(html == "Save"){
        //step into the parent element
        parent = $(this).parents(".item");

        //when it is clicked as save, save everything and remake changes
        $(this).html("Edit");

        //remove the edit feature to the description element
        $(this).parent().siblings(".middle").children(".desc").prop("contenteditable",false);
    }else{
        //when it is clicked as cancel
        $(this).html("Edit");

        //remove the edit feature to the description element
        $(this).parent().siblings(".middle").children(".desc").prop("contenteditable",false);
    }
})

$(".middle .desc").keypress(function(){
    //when a key is pressed, change the edit to save
    $(this).parent().siblings(".edit").children(".span_edit").html("Save");
})

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
$("input[name=item_img]").change(function(){
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

//making edits to a carousel
$("span.item-event").click(function(){
    item_id = $(this).attr("data-item-id");
    item_event = $(this).attr("data-item-event");
    table = "pageItemDisplays";

    if(item_event == "edit"){

    }else if(item_event == "delete"){
        //display yes no modal box
        $("#modal_yes_no").removeClass("no_disp");

        //message to display
        item_header = $(this).parents(".item").children(".top").children(".flex").children(".content_title").children("h4").html();
        $("#modal_yes_no p#warning_content").html("Do you want to delete block titled \"<b>" + 
        item_header + "</b>\"");

        //fill form with needed details
        $("#modal_yes_no input[name=sid]").val(item_id);
        $("#modal_yes_no input[name=mode]").val(item_event);
        $("#modal_yes_no input[name=table]").val(table);
    }else if(item_event == "activate" || item_event == "deactivate"){
        //display yes no modal box
        $("#modal_yes_no").removeClass("no_disp");

        //message to display
        item_header = $(this).parents(".item").children(".top").children(".flex").children(".content_title").children("h4").html();
        $("#modal_yes_no p#warning_content").html("Do you want to " + item_event + " block titled \"<b>" + 
        item_header + "</b>\"");

        //fill form with needed details
        $("#modal_yes_no input[name=sid]").val(item_id);
        $("#modal_yes_no input[name=mode]").val(item_event);
        $("#modal_yes_no input[name=table]").val(table);
    }
})