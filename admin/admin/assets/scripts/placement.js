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
var importSuccess = false;
$("form[name=importForm").submit(function(e){
    e.preventDefault();
    $("form[name=importForm] .message_box").css("overflow","auto");
    $("form[name=importForm] .message_box").css("max-height","300px");

    if($("input#import").val() == ""){
        messageBoxTimeout("importForm","No file has been chosen", "error");
    }else{
        //submit the form
        response = fileUpload($("form[name=importForm] input#import"), $(this), $("form[name=importForm] button[name=submit]"));

        if(response == true){
            message = "Data has been recorded successfully";
            type = "success";

            importSuccess = true;
        }else{
            type = "error";
            importSuccess = false;

            if(response == "no-file"){
                message = "No file has been chosen";
            }else if(response.includes("Upload failed")){
                message = "File could not be uploaded";
            }else if(response == "extension-error"){
                message = "Incorrect file type sent, please send correct file format"
            }else{
                message = response;

                if(response.toLowerCase().includes("column")){
                    type = "primary";
                }else if(response.toLowerCase().includes("candidate")){
                    type = "warning";
                }
            }
        }

        messageBoxTimeout("importForm", message, type, 0);
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
    $("form[name=importForm] .message_box").hide();

    //reload page
    if(importSuccess){
        $("#lhs .item.active").click();
    }
})

//search field workout
$("label[for=search] input[name=search]").keyup(function(){
    search_value = $(this).val();

    table_foot = $(this).parents("#content").children(".body").children("table").children("tfoot");
    table_body = $(this).parents("#content").children(".body").children("table").children("tbody");

    //grab portion for displaying result counts
    td = $(table_foot).children("tr").children("td.result");

    if(search_value == ""){
        $(td).html("");
        $(table_foot).children("tr").children("td:first-child").attr("hidden", false);
    }else{
        $(table_foot).children("tr").children("td:first-child").attr("hidden", true);
        //covert search value to lower case
        search_value = search_value.toLowerCase();

        //get parent table row
        tr = $(this).parents(".form, .head").siblings(".body").children("table").children("tbody").children("tr");
        $(tr).filter(function(){
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(search_value) > -1
            );
        })

        //count number of visible results
        tr = $(this).parents(".form, .head").siblings(".body").children("table").children("tbody").children("tr:visible");

        if(tr.length > 0)
            $(td).html(tr.length + " results returned");
        else
            $(td).html("No results were returned");

        $(table_foot).removeClass("no_disp");
    }
})