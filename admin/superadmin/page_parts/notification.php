<?php require_once("../../../includes/session.php");?>

<section>
    <p>Notifications will be displayed here</p>
</section>

<section class="page_setup">
    <div class="head">
        <h2>New Replies</h2>
    </div>
    <?php 
        $audience = "all";
        $type = "notice";
        if(notificationCounter($audience, $type) > 0){
            $result = $connect->query("SELECT * 
                    FROM notification
                    WHERE Read_by IS NOT LIKE '%$user_username%'");
    ?>
    <div class="body">
        <div class="notif_box flex flex-column relative">
            <div class="top">
                <h4>Title</h4>
                <span class="username">Username</span>
                <span class="date"><?php echo date("dS F, Y")?></span>
            </div>
            <div class="middle">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit odit laboriosam doloremque exercitationem, quia eos tempora enim esse, aliquid alias dolores provident explicabo? Voluptas non magni, quos corporis ratione earum.</p>
            </div>
            <div class="foot">
                <span class="item-event">Edit</span>
                <span class="item-event flex-center-align replies">
                    <?php if(replyCounter(1) > 0){?>
                    <span><?php echo replyCounter(1)?></span>
                    <span class="num_dot"></span>
                    <?php }elseif(replyCounter(1) >= 2){?>
                    <span>Replies</span>
                    <?php }else{?>
                    <span>Reply</span>
                    <?php }?>
                </span>
                <span class="item-event close_reply no_disp">Close Reply</span>
                <span class="item-event">Delete</span>
            </div>
            <div class="reply_container no_disp">
                <?php if(replyCounter(1) > 0){?>
                <div class="reply_box">
                    <div class="top">
                        <h5>Username to Username2</h5>
                    </div>
                    <div class="middle">
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quaerat, facere?</p>
                    </div>
                    <div class="foot">
                        <span class="item-event">Edit</span>
                        <span class="item-event">Reply</span>
                        <span class="item-event">Delete</span>
                    </div>
                </div>
                <?php }else{?>
                <div class="body empty">
                    <p>No replies are available for this comment. Be the first to reply</p>
                </div>
                <?php } ?>
                <div class="reply_tab flex" role="form">
                    <label for="reply">
                        <input type="text" name="reply" id="reply" placeholder="Reply Username...">
                    </label>
                    <label for="submit" class="btn">
                        <button name="submit" value="btn_reply">Reply</button>
                    </label>
                    <input type="hidden" name="comment_id">
                    <input type="hidden" name="reply_id">
                    <input type="hidden" name="user_id" value="<?php echo $user_id?>">
                    <input type="hidden" name="school_id" value="<?php echo $school_id?>">
                </div>
            </div>
        </div>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>You have no unread replies on any notification</p>
    </div>
    <?php } ?>
</section>

<section class="page_setup">
    <div class="head">
        <h2>Your Notifications</h2>
    </div>
    <?php
        $read = true;
        if(notificationCounter($audience, $type, $read) > 0){
    ?>
    <div class="body">
        <div class="notif_box flex flex-column relative">
            <div class="top">
                <h4>Title</h4>
                <span class="username">Username</span>
                <span class="date"><?php echo date("dS F, Y")?></span>
            </div>
            <div class="middle">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit odit laboriosam doloremque exercitationem, quia eos tempora enim esse, aliquid alias dolores provident explicabo? Voluptas non magni, quos corporis ratione earum.</p>
            </div>
            <div class="foot">
                <span class="item-event">Edit</span>
                <span class="item-event flex-center-align replies">
                    <?php if(replyCounter(1) > 0){?>
                    <span><?php echo replyCounter(1)?></span>
                    <span class="num_dot"></span>
                    <?php }elseif(replyCounter(1) >= 2){?>
                    <span>Replies</span>
                    <?php }else{?>
                    <span>Reply</span>
                    <?php }?>
                </span>
                <span class="item-event close_reply no_disp">Close Reply</span>
                <span class="item-event">Delete</span>
            </div>
            <div class="reply_container no_disp">
                <?php if(replyCounter(1) > 0){?>
                <div class="reply_box">
                    <div class="top">
                        <h5>Username to Username2</h5>
                    </div>
                    <div class="middle">
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quaerat, facere?</p>
                    </div>
                    <div class="foot">
                        <span class="item-event">Edit</span>
                        <span class="item-event">Reply</span>
                        <span class="item-event">Delete</span>
                    </div>
                </div>
                <?php }else{?>
                <div class="body empty">
                    <p>No replies are available for this notification. Be the first to reply</p>
                </div>
                <?php } ?>
                <div class="reply_tab flex" role="form">
                    <label for="reply">
                        <input type="text" name="reply" id="reply" placeholder="Reply Username...">
                    </label>
                    <label for="submit" class="btn">
                        <button name="submit" value="btn_reply">Reply</button>
                    </label>
                    <input type="hidden" name="comment_id">
                    <input type="hidden" name="reply_id">
                    <input type="hidden" name="user_id" value="<?php echo $user_id?>">
                    <input type="hidden" name="school_id" value="<?php echo $school_id?>">
                </div>
            </div>
        </div>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>No notifications have been made. Make an announcement or notification</p>
    </div>
    <?php } ?>
    <div class="foot">
        <div class="btn">
            <button name="btn_announce">Make Announcement</button>
        </div>
    </div>
</section>

<div id="announcement" class="fixed form_modal_box flex flex-center-align flex-center-content no_disp">
    <form action="<?php echo $url?>/admin/superadmin/submit.php" name="announcementForm" method="post">
        <div class="head">
            <h2>Make an Announcement</h2>
        </div>
        <div class="body">
            <div id="message_box" class="no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <input type="hidden" name="notification_type" value="notice">
            <label for="title">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/easel-outline.svg" alt="title">
                </span>
                <input type="text" name="title" id="title" placeholder="Enter title of notification or announcement" maxlength="60">
            </label>
            <label for="message">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/megaphone-outline.svg" alt="message">
                </span>
                <textarea name="message" id="message" maxlength="400" placeholder="Enter your announcement in this space. You should not exceed 400 characters"></textarea>
            </label>
            <div id="#aud">
                <p style="padding-left: 12px">Select your audience</p>
                <div class="flex">
                    <label for="audience" class="radio">
                        <input type="radio" name="audience" id="audience_all" value="All" checked>
                        <span class="label_title">All</span>
                    </label>
                    <label for="audience" class="radio">
                        <input type="radio" name="audience" id="audience_others" value="Others">
                        <span class="label_title">Others</span>
                    </label>
                </div>
            </div>
            <label for="custom_audience" class="no_disp" id="aud_ot_label">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/people-outline.svg" alt="custom audience">
                </span>
                <input type="text" name="custom_audience" id="custom_audience" placeholder="Enter usernames here, separate with commas">
            </label>
            <div class="flex flex-wrap">
                <label for="submit" class="btn">
                    <button type="submit" name="submit" value="make_announcement">Make Announcement</button>
                </label>
                <label for="cancel" class="btn">
                    <button type="reset" name="cancel">Cancel</button>
                </label>
            </div>
        </div>
    </form>
</div>

<section id="requests" class="page_setup">
    <div class="head">
        <h3>Document Requests</h3>
    </div>
    <?php if(notificationCounter("all","request") > 0){?>
    <div class="body">
        <div class="user_container" style="text-align: center;">
            <div class="top">
                <h4>
                    <span>5</span>
                    <span>Schools have made a request</span>
                </h4>
            </div>
            <div class="desc flex flex-wrap flex-center-content">
                <div>
                    <span>School 1</span>
                </div>
                <div>
                    <span>School 2</span>
                </div>
                <div>
                    <span>School 3</span>
                </div>
            </div>
            <div class="foot btn flex flex-center-align flex-center-content" style="width: 100%;">
                <button name="fetch_requests">Open Requests Tab</button>
            </div>
        </div>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>No school has parsed a request for a document</p>
    </div>
    <?php } ?>
</section>

<script>
    //when a reply button is clicked
    $(".replies").click(function(){
        //get its parent notification box
        parent = $(this).parents(".notif_box");
        reply_container = $(parent).children(".reply_container").removeClass("no_disp");

        $(this).hide();
        $(this).siblings(".close_reply").removeClass("no_disp");
    })

    $(".close_reply").click(function(){
        //get its parent notification box
        parent = $(this).parents(".notif_box");
        reply_container = $(parent).children(".reply_container").addClass("no_disp");

        $(this).addClass("no_disp");
        $(this).siblings(".replies").show(function(){
            $(this).siblings(".replies").css("display","inline-flex");
        });
    })

    $("#audience_others, #audience_all").change(function(){
        check = $("#audience_others").prop("checked");

        if(check == true){
            $("#aud_ot_label").removeClass("no_disp");
        }else{
            $("#aud_ot_label").addClass("no_disp");
        }
    })

    //mark radio button when whole label is clicked
    $("label[for=audience]").click(function(){
        radio = $(this).children("input[type=radio]");
        check = $(radio).prop("checked");

        if(!check){
            $(radio).prop("checked", true);
            $("#audience_others, #audience_all").change();
        }
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

    //make an announcment
    $("form[name=announcementForm]").submit(function(e){
        e.preventDefault();

        //submit form
        reply = formSubmit($(this), $("form[name=announcementForm] button[name=submit]"));

        alert(reply);
    })
</script>

<script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>"></script>