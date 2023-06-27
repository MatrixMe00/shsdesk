$("table tbody").click(function(){
    parent = $(this).parent();
    parent_id = $(parent).attr("id");
    thead = $(parent).children("thead");
    tbody = $(parent).children("tbody");
    td_count = $(tbody).children("tr").length;

    highlight_count = parseInt($("tr.highlight").attr("data-row-count"));
    new_tbody = "";

    if(highlight_count == td_count && td_count > 1){
        if(td_count > 2){
            init = 2;
        }else{
            init = 1;
        }

        for(var i = init; i >= 0; i--){
            data = $("#" + parent_id + " tr[data-row-count=" + (td_count - i) + "]").html();
            tr_class = $("#" + parent_id + " tr[data-row-count=" + (td_count - i) + "]").prop("class");
            new_tbody += "<tr class='" + tr_class + "'>" + data + "</tr>";
        }
    }else if(td_count > 1){
        if(highlight_count != 1){
            if(td_count > 2){
                max = 1;
            }else{
                max = 0;
            }
            min = -1;
        }else{
            min = 0;
            if(td_count > 2){
                max = 2;
            }else{
                max = 1;
            }
        }
        
        for(var i = min; i <= max; i++){
            data = $("#" + parent_id + " tr[data-row-count=" + (highlight_count + i) + "]").html();
            tr_class = $("#" + parent_id + " tr[data-row-count=" + (highlight_count + i) + "]").prop("class");
            
            new_tbody += "<tr class='" + tr_class + "'>" + data + "</tr>";
        }
    }else{
        new_tbody = $(tbody).html();
    }

    $("#tables #table_view table thead").html($(thead).html());
    $("#tables #table_view table tbody").html(new_tbody);
    $("#tables").removeClass("no_disp");
})

$("table tbody tr").click(function(){
    $(this).addClass("highlight");
    recepient_name = $(this).children("td.td_name").html();

    if(recepient_name == "-" || recepient_name.toLowerCase() == "not set"){
        recepient_name = "Receipt Number " + $(this).attr("data-row-id");
    }
    $("#data_details #send_name").html(recepient_name);

    //parse details into form
    $("#data_details input[name=trans_ref]").val($(this).children("td:first-child").html());
    $("#data_details input[name=channel]").val($(this).children("td.td_channel").html());
    $("#data_details input[name=amount]").val($(this).children("td.td_amount").html());
    $("#data_details input[name=date]").val($(this).children("td.td_date").html());
    $("#data_details input[name=deduction]").val($(this).children("td.td_deduction").html());
    $("#data_details input[name=send_name]").val($(this).children("td.td_name").html());
    $("#data_details input[name=send_phone]").val($(this).children("td.td_number").html());
    $("#data_details input[name=student_count]").val("Money below is a collective for " + $(this).children("td.td_student").html() + " enroled students");

    
    if($(this).children("td:last-child").html().toLocaleLowerCase() == "sent"){
        $("#data_details input[name=status]").val("Transaction was successful");
    }else{
        $("#data_details input[name=status]").val("Transaction is pending send or approval");
    }
})

$("span#change_status").click(function(){
    $("#data_details label[for=update_status]").toggleClass("no_disp");

    if($(this).html().toLowerCase().includes("change")){
        $(this).html("Retain Status");
    }else{
        $(this).html("Change Status");
    }
})

$("span#change_channel").click(function(){
    $("#data_details label[for=update_channel]").toggleClass("no_disp");

    if($(this).html().toLowerCase().includes("update")){
        $(this).html("Retain Channel");
    }else{
        $(this).html("Update Channel");
    }
})

$("span#change_date").click(function(){
    $("#data_details label[for=update_date]").toggleClass("no_disp");

    if($(this).html().toLowerCase().includes("update")){
        $(this).html("Retain Date");
    }else{
        $(this).html("Update Date");
    }
})

$("#tables .btn .red").click(function(){
    $("#tables").addClass("no_disp");
    $("#tables_view thead, #tables_view tbody").html("");
    $("table tbody tr").removeClass("highlight");

    $("#data_details label[for=update_status]").addClass("no_disp");
    $("span#change_status").html("Change Status");
    $("span#change_status").html("Update Channel");
})

$("thead td").click(function(){
    table = $(this).parents("thead").siblings("tbody").eq(0);
    rows = table.find("tr").toArray().sort(comparer($(this).index()));
    this.asc = !this.asc;
    if(!this.asc){rows = rows.reverse()}
    for(var i=0; i < rows.length; i++){
        table.append(rows[i]);
    }
})

function comparer(index){
    return function(a, b){
        valA = getCellValue(a, index), valB = getCellValue(b, index);
        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
    }
}

function getCellValue(row, index){
    return $(row).children("td").eq(index).text();
}

$("button#auto_gen").click(function(){
    html = $(this).parent().html();
    $.ajax({
        url: "submit.php",
        data: "submit=updatePayment",
        beforeSend: function(){
            $("#stat").removeClass("no_disp");
            $("#stat span").html("Loading...");
        },
        success: function(data){
            if(data == "success"){
               $("#stat span").html("Updated Successfully! Refreshing");
               
               setTimeout(function(){
                   $("#stat span").html("");
                   $("#stat").addClass("no_disp");

                   $("#lhs .item.active").click();
               }, 3000);
            }else{
                console.log(data);
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert_box(textStatus, "warning", 7);
        }
    })
})

//details for detailed table
$("#data_details button[name=submit]").click(function(){
    trans_ref = $("#data_details input[name=trans_ref]").val();
    update_channel = $("#data_details select[name=update_channel]").val();
    amount = $("#data_details input[name=amount]").val();
    deduction = $("#data_details input[name=deduction]").val();
    update_date = $("#data_details input[name=update_date]").val();
    update_status = $("#data_details select[name=update_status]").val();
    row_id = $("table tbody tr.highlight").attr("data-row-id");
    send_name = $("#data_details input[name=send_name]").val();
    send_phone = $("#data_details input[name=send_phone]").val();
    date = $("#data_details input[name=date]").val();

    dataString = "trans_ref=" + trans_ref + "&update_date=" + update_date + "&update_channel=" + update_channel +
     "&amount=" + amount + "&deduction=" + deduction + "&update_status=" + update_status + "&row_id=" + row_id + "&submit=updateSelectedPayment" + 
     "&send_name=" + send_name + "&send_phone=" + send_phone + "&date=" + date;

    $.ajax({
        url: "submit.php",
        data: dataString,
        beforeSend: function(){
            $("label[for=form_load] span").html("Loading...");
        },
        success: function(data){
            if(data == "success"){
                $("label[for=form_load] span").html("Update successful. Redirecting!");

                setTimeout(function(){
                    $("#lhs .item.active").click();
                }, 3000);
            }else{
                $("label[for=form_load] span").html("Update could not be made. Please try again!");
            }
        },
        error: function(request, status, errorText){
            if(request.readyState == 4){
                $("label[for=form_load] span").html(request.statusText);
            }else{
                $("label[for=form_load] span").html(request.statusText);
            }
            
        }
    })
})

//transaction split controls
$("select.account_type").change(function(){
    const mode = $(this).attr("data-mode");
    const value = $(this).val();
    const parent = $(this).parent();

    $(parent).siblings("label:not(.keep)").addClass("no_disp")
    
    if(value != ""){
        $(parent).siblings("label#"+mode+"_"+value).removeClass("no_disp")
    }
})

$(".bank").change(function(){
    const local_bank = $(this).parents(".joint").find(".bank_name")
    local_bank.val($(this).val())
})

$("form").submit(function(e){
    e.preventDefault();

    let response = formSubmit($(this), $(this).find("button[name=submit]"), true)
    let type = ""; time = 0;
    
    if(response === true){
        type="success";
        response = "Data was added successfully";

        setTimeout(function(){
            $("#lhs .item.active").click()
        },1000)
    }else{
        type="error";
    }
    messageBoxTimeout("add_new_split", response, type, time)
})

$("button#trans_split").click(function(){
    $(".section_block, button#trans_split").addClass("no_disp")
    $(".section_block.trans_split, button#close_trans").removeClass("no_disp")
})

$("button#close_trans").click(function(){
    $(".section_block, button#trans_split").removeClass("no_disp")
    $(".section_block.trans_split, button#close_trans").addClass("no_disp")
})