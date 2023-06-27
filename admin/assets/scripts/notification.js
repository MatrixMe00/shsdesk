//when a reply button is clicked on notification box
$(".replies").click(function(){
    //get its parent notification box
    parent = $(this).parents(".notif_box");

    //unhide parent's reply container
    $(parent).children(".reply_container").removeClass("no_disp");

    //get username
    username = $(this).children(".reply_label").attr("data-sender-name");

    //relay change in the text field
    placeholder = "Reply " + username + "...";

    $(parent).children(".reply_container").children(".reply_tab").children("label").children("input[name=reply]").prop("placeholder", placeholder);
    
    //get and pass sender's id
    id = $(this).attr("data-sender-id");
    $(parent).children(".reply_container").children(".reply_tab").children("input[name=recepient_id]").val(id);

    $(this).hide();
    $(this).siblings(".close_reply").removeClass("no_disp");
})

//close the reply box
$(".close_reply").click(function(){
    //get its parent notification box
    parent = $(this).parents(".notif_box");
    reply_container = $(parent).children(".reply_container").addClass("no_disp");

    $(this).addClass("no_disp");
    $(this).siblings(".replies").show(function(){
        $(this).siblings(".replies").css("display","inline-flex");
    });

    //remove display_now class from any client side new reply
    $(parent).children(".reply_container").children(".reply_box.display_now").removeClass("display_now");
})

$("button[name=cancel]").click(function(){
    fadeOutElement($(this).parents(".form_modal_box"), 0.2);
})

//display the announcment form when make announcment button is clicked
$("button[name=btn_announce]").click(function(){
    $("#announcement").removeClass("no_disp");
})

//go to requests tab upon click of request button
$("button[name=fetch_requests]").click(function(){
    $("#lhs .item[name=Request]").click();
})

//go to reports tab upon click of report button
$("button[name=fetch_reports]").click(function(){
    $("#lhs .item[name=Report]").click();
})

//make an announcment
$("form[name=announcementForm]").submit(function(e){
    e.preventDefault();

    //submit form
    reply = formSubmit($(this), $("form[name=announcementForm] button[name=submit]"));

    if(reply == true){
        //display message
        message = "Announcement Made Successfully";
        type = "success";
        time = 5;
        messageBoxTimeout($(this).prop("name"), message, type, time);

        //close the form after 6 seconds
        setTimeout(function(){
           $("form[name=announcementForm] button[name=cancel]").click();
           
           //refresh the page
           $("#lhs .item.active").click();
        },6000);
    }else{
        type = "error";
        time = 5;

        if(reply == "no-title"){
            message = "Please provide a title for the announcement";
        }else if(reply == "no-message"){
            message = "No description was provided for your announcement";
        }else if(reply == "notification-type-not-set"){
            message = "No announcement type was specified. Contact the super admin";
        }else{
            message = reply;
        }

        messageBoxTimeout($(this).prop("name"), message, type, time);
    }
})

//when a reply span on a reply is clicked
$(".reply-reply").click(function(){
    //get username
    username = $(this).attr("data-sender-name");

    //pass username into reply placeholder
    placeholder = "Reply " + username + "...";
    $(this).parents(".reply_box").siblings(".reply_tab").children("label").children("input[name=reply]").attr("placeholder", placeholder);

    //pass recepient_id
    id = $(this).attr("data-sender-id");
    $(this).parents(".reply_box").siblings(".reply_tab").children("input[name=recepient_id]").val(id);
})

//send a reply_container
$(".reply_tab label[for=submit] button[name=submit]").click(function(){
    parent = $(this).parents(".reply_tab");
    
    reply = $(parent).children("label[for=reply]").children("input[name=reply]").val();
    comment_id = $(parent).children("input[name=comment_id]").val();
    user_id = $(parent).children("input[name=user_id]").val();
    recepient_id = $(parent).children("input[name=recepient_id]").val();
    school_id = $(parent).children("input[name=school_id]").val();
    submit = $(this).val() + "_ajax";

    dataString = "reply=" + reply + "&comment_id=" + comment_id + "&recepient_id=" + recepient_id +
    "&user_id=" + user_id + "&school_id=" + school_id + "&submit=" + submit;

    var data_var;

    if(reply != "" && reply.length >= 2){
        $.ajax({
            url: $(parent).attr("data-action"),
            data: dataString,
            cache: true,
            type: $(parent).attr("method"),
            dataType: "json",
            async: false,
            timeout: 30000,
            beforeSend: function(){
                //disable the reply input
                $(parent).children("label[for=reply]").children("input[name=reply]").prop("disabled",true);
                $(this).prop("disabled", true);

                //show a loading message
                $(parent).siblings(".load_message").fadeIn().addClass("light").html("Loading...");
            },
            success: function(text){
                text = JSON.parse(JSON.stringify(text));
                data_var = text;

                if(text["status"] == "success"){
                    //empty the reply input field
                    $(parent).children("label[for=reply]").children("input[name=reply]").val("");

                    //display a success message
                    $(parent).siblings(".load_message").removeClass("light danger").addClass("success").html("Reply sent");
                    setTimeout(function(){
                        $(parent).siblings(".load_message").removeClass("success").fadeOut().html("");
                    }, 4000);
                }else{
                    message = text["message"];

                    if(message == "no-reply"){
                        message = "No message was detected. Please enter a message";
                    }else if(message == "reply-short"){
                        message = "Message lentgh unacceptable. Enter at least two characters";
                    }else if(message == "no-comment-id"){
                        message = "Notification for this message could not be found. Error!";
                    }else if(message == "no-user-id"){
                        message = "Please reload the page to see if you are logged in";
                    }else if(message == "no-recepient-id"){
                        message = "No recepient was detected";
                    }else if(message == "same-user"){
                        message = "Disallowed! You cannot reply yourself";
                    }

                    $(parent).siblings(".load_message").removeClass("light success").addClass("danger").fadeIn().html(message);

                    setTimeout(function(){
                        $(parent).siblings(".load_message").removeClass("danger").fadeOut().html("");
                    }, 5000);
                }
            },
            complete: function(){
                //enable the reply input
                $(parent).children("label[for=reply]").children("input[name=reply]").prop("disabled",false);
                $(this).prop("disabled", false);
            },
            error: function(t, textStatus){
                t = JSON.stringify(t);
                if(textStatus = "timeout"){
                    t = "Connection was timed out due to slow network. Please try again later.";
                }
                alert_box(t, "warning", 10);
                
                //enable the reply input
                $(parent).children("label[for=reply]").children("input[name=reply]").prop("disabled",false);
                $(this).prop("disabled", false);
            }
        });
    }else{
        $(parent).children("label[for=reply]").children("input[name=reply]").focus();
    }

    if(data_var["status"] == "success"){
        //push a reply in a demo form from here
        box = "<div class=\"reply_box display_now\">\n" + 
                "<div class=\"top\">\n" +
                "   <h5>" + data_var["username"] + " to " + data_var["username1"] + "</h5>\n" + 
                "</div>\n" +
                "<div class=\"middle\">\n" +
                "   <p>" + reply + "</p>\n" + 
                "</div>\n" + 
                "<div class=\"foot\">\n" +
                "   <span class=\"item-event\">Edit</span>\n" +
                "   <span class=\"item-event\">Delete</span>\n" +
                "</div>\n" +
                "</div>";

        //increment the number of containers on client side
        current_number = $(this).parents(".reply_container").children(".reply_box").length;
            
        //immediately update total number of replies
        $(this).parents(".notif_box").children(".foot").children(".replies").children(".reply_counts").html(current_number + 1);

        //make grammatical corrections to reply label span elements
        if(current_number > 1){
            $(this).parents(".notif_box").children(".foot").children(".replies").children(".reply_label").html("Replies");
        }else{
            //remove the body
            $(this).parents(".reply_container").children(".body.empty").remove();
        }

        //push box into view
        $(this).parents(".reply_container").prepend(box);
    }
})

//when an item event is clicked
$("span.item-event").click(function(){
    const item_id = $(this).attr("data-item-id");
    let item_event = $(this).attr("data-item-event");
    let db = 1;
    let key_column = ""
    

    //check if delete clicked is a notification or reply
    if($(this).parents(".reply_container").length > 0){
        table = "reply";
    }else if($(this).parents(".notif_box").length > 0){
        table = "notification";
    }else if($(this).parents(".announcement").length > 0){
        table = "announcement";
        db = 2;
    }
    

    if(item_event == "edit"){

    }else if(item_event == "delete"){
        if($(this).parents(".announcement")){
            item_event = "remove_item"
            key_column = "id"
        }

        //display yes no modal box
        $("#gen_del").removeClass("no_disp");

        //message to display
        $("#gen_del p#warning_content").html("Do you want to delete this message?");
    }else if(item_event == "activate" || item_event == "deactivate"){
        //display yes no modal box
        $("#gen_del").removeClass("no_disp");

        //message to display
        item_header = $(this).parents(".item").children(".top").children(".flex").children(".content_title").children("h4").html();
        $("#gen_del p#warning_content").html("Do you want to " + item_event + " block titled \"<b>" + 
        item_header + "</b>\"");
    }

    //fill form with needed details
    $("#gen_del input[name=sid]").val(item_id);
    $("#gen_del input[name=mode]").val(item_event);
    $("#gen_del input[name=table]").val(table);
    $("#gen_del input[name=key_column]").val(key_column)
    $("#gen_del input[name=db]").val(db)
})

//marking notifications and replies as read upon mouse leave
$(".notif_box.unread").mouseleave(function(){
    //get number of notifications
    var news_number = parseInt($("#lhs .item.active .news_number span").html());

    //mark as read in database
    comment_id = $(this).attr("data-box-number");

    //grab submit url from reply box
    submit_url = $(this).children(".reply_container").children(".reply_tab").attr("data-action");

    //parse a dataString
    dataString = "comment_id=" + comment_id + "&submit=mark_read";

    //mark as read in database
    if($(this).hasClass("unread")){
        // $(this).removeClass("unread").addClass("read");
        $.ajax({
            url: submit_url,
            data: dataString,
            type: "get",
            dataType: "text",
            async: true,
            timeout: 30000,
            success: function (text){
                if(text == "success"){
                    //mark as read box
                    $(this).removeClass("unread").addClass("read");
                    
                    //hide baloon when results reach 0
                    if(news_number - 1 == 0){
                        $("#lhs .item.active .news_number").fadeOut();
                    }

                    //subtract from total
                    $("#lhs .item.active .news_number span").html(news_number - 1);
                }
            },
            error: function(xhr, textStatus){
                if(textStatus == "timeout"){
                    alert_box("Connection was timed out due to slow network. Please try agin later", "danger", 10)
                }
            }
        })        
    }
})