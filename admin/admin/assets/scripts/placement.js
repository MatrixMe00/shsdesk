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
$("form[name=importForm").submit(async function(e){
    e.preventDefault();
    $("form[name=importForm] .message_box").css("overflow","auto");
    $("form[name=importForm] .message_box").css("max-height","300px");

    if($("input#import").val() == ""){
        messageBoxTimeout("importForm","No file has been chosen", "error");
    }else{
        //submit the form
        const response = await fileUpload($("form[name=importForm] input#import"), $(this), $("form[name=importForm] button[name=submit]"));

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

//maximize search to all students
$("label[for=search] input[name=search]").focus(function(){
    max_rows = $(this).attr("data-max-break-point")

    //pass the max rows as the breakpoint for all
    $(this).parents(".table_section").find(".navs").children("span").attr("data-break-point", max_rows)
    $(this).parents(".form").siblings(".head").find("button").attr("data-break-point", max_rows)
    $(this).parents(".form").siblings(".head").find("button").click()
})

//reset to state before search
$("label[for=search] input[name=search]").blur(function(){
    if($(this).val() === ""){
        def_ault = 10
        table_foot = $(this).parents(".table_section").find("table").children("tfoot");

        //pass default values into needful elements
        $(this).parents(".table_section").find(".navs").children("span").attr("data-break-point", def_ault)
        $(this).parents(".form").siblings(".head").find("button").attr("data-break-point", def_ault)
        $(this).parents(".table_section").find("table").find("tr").show();
        $(this).parents(".form").siblings(".head").find("button").click()
        $(table_foot).find("td:first-child").attr("hidden", false);
        $(table_foot).find("td:last-child").html("");
    }
})

//search field workout
$("label[for=search] input[name=search]").keyup(function(){
    //search key control
    const searchText = $(this).val().toLowerCase()

    if(searchText !== ""){
        table_foot = $(this).parents("#content").find("tfoot");
        table_body = $(this).parents("#content").find("tbody");

        //grab portion for displaying result counts
        td = $(table_foot).find("td.result");

        
        $(table_body).children("tr").each(function(){
            const rowData = $(this).text().toLowerCase()
            if(rowData.indexOf(searchText) === -1){
                $(this).hide()
            }else{
                $(this).show()
            }
        })
        
        //count number of visible results
        tr = $(this).parents(".form, .head").siblings(".body").children("table").children("tbody").children("tr:visible");
        if(tr.length > 0)
            td.html(tr.length + " results returned");
        else
            td.html("No results were returned");
    }else{
        def_ault = 10
        table_foot = $(this).parents(".table_section").find("table").children("tfoot");

        //pass default values into needful elements
        $(this).parents(".table_section").find(".navs").children("span").attr("data-break-point", def_ault)
        $(this).parents(".form").siblings(".head").find("button").attr("data-break-point", def_ault)
        $(this).parents(".table_section").find("table").find("tr").show();
        $(this).parents(".form").siblings(".head").find("button").click()
        $(table_foot).find("td:first-child").attr("hidden", false);
        $(table_foot).find("td:last-child").html("");
    }
})

$("label[for=search_mul_table] input[name=search]").on({
    focus: function(){
        const table = $("#" + $(this).attr("data-table-parent-id") + $(this).attr("data-parent-id" + " table"));
        const table_foot = table.find("tfoot");
        const table_body = table.find("tbody");
        const activeClass = $(this).attr("data-active");
        const button = $(this).parents("section").find(".table_btn." + activeClass);

        //let new values show the total rows
        const max_break_point = button.attr("data-max-value")
        button.attr("data-break-point",max_break_point).click()

        //pass default values into needful elements
        table_body.find("tr").show();
        $(table_foot).find("td:first-child").attr("hidden", false);
        $(table_foot).find("td:last-child").html("");
    },
    blur: function(){
        const table = $("#" + $(this).attr("data-table-parent-id") + $(this).attr("data-parent-id" + " table"));
        const table_foot = table.find("tfoot");
        const table_body = table.find("tbody");
        const activeClass = $(this).attr("data-active");
        const button = $(this).parents("section").find(".table_btn." + activeClass);

        //let new values show the default rows
        if($(this).val() == ""){
            button.attr("data-break-point",10).click()
        }
    },
    keyup: function(){
        const table = $("#" + $(this).attr("data-table-parent-id") + $(this).attr("data-parent-value") + " table");
        const table_foot = table.find("tfoot");
        const table_body = table.find("tbody");

        //grab portion for displaying result counts
        td = $(table_foot).find("td.result");

        //search key control
        const searchText = $(this).val().toLowerCase()
        $(table_body).children("tr").each(function(){
            const rowData = $(this).text().toLowerCase()
            if(rowData.indexOf(searchText) === -1){
                $(this).hide()
            }else{
                $(this).show()
            }
        })
        
        //count number of visible results
        tr = table_body.children("tr:visible");
        if(tr.length > 0)
            td.html(tr.length + " results returned");
        else
            td.html("No results were returned");
    }
})