<?php require_once("../../../includes/session.php");

    //add nav point session
    $_SESSION["nav_point"] = "Notification";
?>

<section>
    <p>Notifications will be displayed here</p>
</section>

<section class="page_setup">
    <div class="head">
        <h2>New Replies</h2>
    </div>
    <?php 
        $result = $connect->query("SELECT DISTINCT n.* 
        FROM notification n JOIN reply r 
        ON n.ID = r.Comment_id 
        WHERE r.Read_by NOT LIKE'$user_username'
        AND n.Read_by LIKE '$user_username'
        ORDER BY ID DESC");

        if($result->num_rows > 0){
    ?>
    <div class="body">
        <?php while($row = $result->fetch_assoc()){ ?>
        <div class="notif_box flex flex-column relative">
            <div class="top">
                <h4><?php echo $row["Title"]?></h4>
                <span class="username"><?php echo getUserDetails($row["Sender_id"])["username"]?></span>
                <span class="date"><?php echo date("jS F, Y",strtotime($row["Date"]))?></span>
            </div>
            <div class="middle">
                <p><?php echo $row["Description"]?></p>
            </div>
            <div class="foot">
                <span class="item-event" data-item-id="<?php echo $row["ID"]?>">Edit</span>
                <span class="item-event flex-center-align replies">
                    <?php if(replyCounter($row["ID"]) > 0){?>
                    <span class="reply_counts"><?php echo replyCounter($row["ID"])?></span>
                    <span class="num_dot"></span>
                    <?php }if(replyCounter($row["ID"]) >= 2){?>
                    <span class="reply_label" data-sender-name="<?php echo getUserDetails($row["Sender_id"])["username"]?>">Replies</span>
                    <?php }else{?>
                    <span class="reply_label" data-sender-name="<?php echo getUserDetails($row["Sender_id"])["username"]?>">Reply</span>
                    <?php }?>
                </span>
                <span class="item-event close_reply no_disp">Close Reply</span>
                <span class="item-event" data-item-id="<?php echo $row["ID"]?>" data-item-event="delete">Delete</span>
            </div>
            <div class="reply_container no_disp">
                <?php if(replyCounter($row["ID"]) > 0){
                    $sql = "SELECT r.*, a.username, b.username AS username1
                    FROM reply r JOIN admins_table a
                    ON r.Sender_id = a.user_id
                    JOIN admins_table b
                    ON r.Recipient_id = b.user_id
                    WHERE r.Comment_id = ".$row["ID"]."
                    ORDER BY ID DESC";

                    $res = $connect->query($sql);

                    while($rep = $res->fetch_assoc()){
                ?>
                <div class="reply_box">
                    <div class="top">
                        <h5><?php echo $rep["username"]." to ".$rep["username1"] ?></h5>
                    </div>
                    <div class="middle">
                        <p><?php echo $rep["Message"]?></p>
                    </div>
                    <div class="foot">
                        <?php if($rep["Sender_id"] == $user_id && date("d-m-Y",strtotime($rep["Date"])) == date("d-m-Y")){?>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>">Edit</span>
                        <?php }?>
                        <span class="item-event reply-reply" data-sender-id="<?php echo $rep["Sender_id"]?>" data-sender-name="<?php echo $rep["username"]?>">Reply</span>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>" data-item-event="delete">Delete</span>
                        <span class="item-event date"><?php
                            if(date("d-m-y") == date("d-m-y", strtotime($rep["Date"]))){
                                echo "- Today, ".date("h:i:s A", strtotime($rep["Date"]));
                            }else{
                                echo "- ".date("jS M, Y. h:i:s A", strtotime($rep["Date"]));
                            }
                        ?>
                        </span>
                    </div>
                </div>
                <?php }
                    }else{?>
                <div class="body empty">
                    <p>No replies are available for this comment. Be the first to reply</p>
                </div>
                <?php } ?>
                <div class="reply_tab flex" role="form" data-action="<?php echo $url?>/admin/superadmin/submit.php" method="POST">
                    <label for="reply">
                        <input type="text" name="reply" placeholder="Reply Username...">
                    </label>
                    <label for="submit" class="btn">
                        <button name="submit" value="btn_reply">Reply</button>
                    </label>
                    <input type="hidden" name="comment_id" value="<?php echo $row["ID"]?>">
                    <input type="hidden" name="recepient_id" value="<?php echo $row["Sender_id"]?>">
                    <input type="hidden" name="user_id" value="<?php echo $user_id?>">
                    <input type="hidden" name="school_id" value="<?php echo $school_id?>">
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>You have no unread replies on any notification</p>
    </div>
    <?php } ?>
</section>

<section class="page_setup">
    <div class="head">
        <h2>Unread Notifications</h2>
    </div>
    <?php
        $result = $connect->query("SELECT DISTINCT n.*
        FROM notification n JOIN reply r
        ON n.ID = r.Comment_ID
        WHERE n.Read_by NOT LIKE '$user_username'
        ORDER BY ID DESC");

        if($result->num_rows > 0){
    ?>
    <div class="body">
        <?php while($row = $result->fetch_assoc()){ ?>
        <div class="notif_box flex flex-column relative">
            <div class="top">
                <h4><?php echo $row["Title"]?></h4>
                <span class="username"><?php echo getUserDetails($row["Sender_id"])["username"]?></span>
                <span class="date"><?php echo date("jS F, Y",strtotime($row["Date"]))?></span>
            </div>
            <div class="middle">
                <p><?php echo $row["Description"]?></p>
            </div>
            <div class="foot">
                <span class="item-event" data-item-id="<?php echo $row["ID"]?>">Edit</span>
                <span class="item-event flex-center-align replies" data-sender-id="<?php echo $row["Sender_id"]?>">
                    <?php if(replyCounter($row["ID"]) > 0){?>
                    <span class="reply_counts"><?php echo replyCounter($row["ID"])?></span>
                    <span class="num_dot"></span>
                    <?php }if(replyCounter($row["ID"]) >= 2){?>
                    <span class="reply_label" data-sender-name="<?php echo getUserDetails($row["Sender_id"])["username"]?>">Replies</span>
                    <?php }else{?>
                    <span class="reply_label" data-sender-name="<?php echo getUserDetails($row["Sender_id"])["username"]?>">Reply</span>
                    <?php }?>
                </span>
                <span class="item-event close_reply no_disp">Close Reply</span>
                <span class="item-event" data-item-id="<?php echo $row["ID"]?>" data-item-event="delete">Delete</span>
            </div>
            <div class="reply_container no_disp">
                <?php if(replyCounter($row["ID"]) > 0){
                    $sql = "SELECT r.*, a.username, b.username AS username1
                    FROM reply r JOIN admins_table a
                    ON r.Sender_id = a.user_id
                    JOIN admins_table b
                    ON r.Recipient_id = b.user_id
                    WHERE r.Comment_id = ".$row["ID"]."
                    ORDER BY ID DESC";

                    $res = $connect->query($sql);

                    while($rep = $res->fetch_assoc()){
                ?>
                <div class="reply_box">
                    <div class="top">
                        <h5><?php echo $rep["username"]." to ".$rep["username1"] ?></h5>
                    </div>
                    <div class="middle">
                        <p><?php echo $rep["Message"]?></p>
                    </div>
                    <div class="foot">
                        <?php if($rep["Sender_id"] == $user_id && date("d-m-Y",strtotime($rep["Date"])) == date("d-m-Y")){?>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>">Edit</span>
                        <?php }?>
                        <span class="item-event reply-reply" data-sender-id="<?php echo $rep["Sender_id"]?>" data-sender-name="<?php echo $rep["username"]?>">Reply</span>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>" data-item-event="delete">Delete</span>
                        <span class="item-event date"><?php
                            if(date("d-m-y") == date("d-m-y", strtotime($rep["Date"]))){
                                echo "- Today, ".date("h:i:s A", strtotime($rep["Date"]));
                            }else{
                                echo "- ".date("jS M, Y. h:i:s A", strtotime($rep["Date"]));
                            }
                        ?>
                        </span>
                    </div>
                </div>
                <?php }
                    }else{?>
                <div class="body empty">
                    <p>No replies are available for this comment. Be the first to reply</p>
                </div>
                <?php } ?>
                <div class="reply_tab flex" role="form" data-action="<?php echo $url?>/admin/superadmin/submit.php" method="POST">
                    <label for="reply">
                        <input type="text" name="reply" placeholder="Reply Username...">
                    </label>
                    <label for="submit" class="btn">
                        <button name="submit" value="btn_reply">Reply</button>
                    </label>
                    <input type="hidden" name="comment_id" value="<?php echo $row["ID"]?>">
                    <input type="hidden" name="recepient_id">
                    <input type="hidden" name="user_id" value="<?php echo $user_id?>">
                    <input type="hidden" name="school_id" value="<?php echo $school_id?>">
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>No notifications have been made. Make an announcement or notification</p>
    </div>
    <?php } ?>
</section>

<section class="page_setup">
    <div class="head">
        <h2>Your Notifications</h2>
    </div>
    <?php
        $result = $connect->query("SELECT DISTINCT n.* 
                FROM notification n JOIN reply r 
                ON n.ID = r.Comment_id 
                WHERE n.Sender_id = $user_id
                AND r.Read_by LIKE '$user_username'
                ORDER BY ID DESC");
        if($result->num_rows > 0){
            
    ?>
    <div class="body">
        <?php while($row = $result->fetch_assoc()){ ?>
        <div class="notif_box flex flex-column relative">
            <div class="top">
                <h4><?php echo $row["Title"]?></h4>
                <span class="username"><?php echo getUserDetails($row["Sender_id"])["username"]?></span>
                <span class="date"><?php echo date("jS F, Y",strtotime($row["Date"]))?></span>
            </div>
            <div class="middle">
                <p><?php echo $row["Description"]?></p>
            </div>
            <div class="foot">
                <span class="item-event" data-item-id="<?php echo $row["ID"]?>">Edit</span>
                <span class="item-event flex-center-align replies" data-sender-id="<?php echo $row["Sender_id"]?>">
                    <?php if(replyCounter($row["ID"]) > 0){?>
                    <span class="reply_counts"><?php echo replyCounter($row["ID"])?></span>
                    <span class="num_dot"></span>
                    <?php }if(replyCounter($row["ID"]) >= 2){?>
                    <span class="reply_label" data-sender-name="<?php echo getUserDetails($row["Sender_id"])["username"]?>">Replies</span>
                    <?php }else{?>
                    <span class="reply_label" data-sender-name="<?php echo getUserDetails($row["Sender_id"])["username"]?>">Reply</span>
                    <?php }?>
                </span>
                <span class="item-event close_reply no_disp">Close Reply</span>
                <span class="item-event" data-item-id="<?php echo $row["ID"]?>" data-item-event="delete">Delete</span>
            </div>
            <div class="reply_container no_disp">
                <?php if(replyCounter($row["ID"]) > 0){
                    $sql = "SELECT r.*, a.username, b.username AS username1
                    FROM reply r JOIN admins_table a
                    ON r.Sender_id = a.user_id
                    JOIN admins_table b
                    ON r.Recipient_id = b.user_id
                    WHERE r.Comment_id = ".$row["ID"]."
                    ORDER BY ID DESC";

                    $res = $connect->query($sql);

                    while($rep = $res->fetch_assoc()){
                ?>
                <div class="reply_box">
                    <div class="top">
                        <h5><?php echo $rep["username"]." to ".$rep["username1"] ?></h5>
                    </div>
                    <div class="middle">
                        <p><?php echo $rep["Message"]?></p>
                    </div>
                    <div class="foot">
                        <?php if($rep["Sender_id"] == $user_id && date("d-m-Y",strtotime($rep["Date"])) == date("d-m-Y")){?>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>">Edit</span>
                        <?php }?>
                        <span class="item-event reply-reply" data-sender-id="<?php echo $rep["Sender_id"]?>" data-sender-name="<?php echo $rep["username"]?>">Reply</span>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>" data-item-event="delete">Delete</span>
                        <span class="item-event date"><?php
                            if(date("d-m-y") == date("d-m-y", strtotime($rep["Date"]))){
                                echo "- Today, ".date("h:i:s A", strtotime($rep["Date"]));
                            }else{
                                echo "- ".date("jS M, Y. h:i:s A", strtotime($rep["Date"]));
                            }
                        ?>
                        </span>
                    </div>
                </div>
                <?php }
                    }else{?>
                <div class="body empty">
                    <p>No replies are available for this comment. Be the first to reply</p>
                </div>
                <?php } ?>
                <div class="reply_tab flex" role="form" data-action="<?php echo $url?>/admin/superadmin/submit.php" method="POST">
                    <label for="reply">
                        <input type="text" name="reply" placeholder="Reply Username...">
                    </label>
                    <label for="submit" class="btn">
                        <button name="submit" value="btn_reply">Reply</button>
                    </label>
                    <input type="hidden" name="comment_id" value="<?php echo $row["ID"]?>">
                    <input type="hidden" name="recepient_id">
                    <input type="hidden" name="user_id" value="<?php echo $user_id?>">
                    <input type="hidden" name="school_id" value="<?php echo $school_id?>">
                </div>
            </div>
        </div>
        <?php } ?>
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

<section class="page_setup">
    <div class="head">
        <h2>Other Notifications</h2>
    </div>
    <?php
        $result = $connect->query("SELECT DISTINCT n.* 
                FROM notification n JOIN reply r 
                ON n.ID = r.Comment_id 
                WHERE n.Sender_id != $user_id
                AND r.Read_by LIKE '$user_username'
                AND n.Read_by LIKE '$user_username'
                ORDER BY ID DESC");
        if($result->num_rows > 0){
            
    ?>
    <div class="body">
        <?php while($row = $result->fetch_assoc()){ ?>
        <div class="notif_box flex flex-column relative">
            <div class="top">
                <h4><?php echo $row["Title"]?></h4>
                <span class="username"><?php echo getUserDetails($row["Sender_id"])["username"]?></span>
                <span class="date"><?php echo date("jS F, Y",strtotime($row["Date"]))?></span>
            </div>
            <div class="middle">
                <p><?php echo $row["Description"]?></p>
            </div>
            <div class="foot">
                <span class="item-event" data-item-id="<?php echo $row["ID"]?>">Edit</span>
                <span class="item-event flex-center-align replies" data-sender-id="<?php echo $row["Sender_id"]?>">
                    <?php if(replyCounter($row["ID"]) > 0){?>
                    <span class="reply_counts"><?php echo replyCounter($row["ID"])?></span>
                    <span class="num_dot"></span>
                    <?php }if(replyCounter($row["ID"]) >= 2){?>
                    <span class="reply_label" data-sender-name="<?php echo getUserDetails($row["Sender_id"])["username"]?>">Replies</span>
                    <?php }else{?>
                    <span class="reply_label" data-sender-name="<?php echo getUserDetails($row["Sender_id"])["username"]?>">Reply</span>
                    <?php }?>
                </span>
                <span class="item-event close_reply no_disp">Close Reply</span>
                <span class="item-event" data-item-id="<?php echo $row["ID"]?>" data-item-event="delete">Delete</span>
            </div>
            <div class="reply_container no_disp">
                <?php if(replyCounter($row["ID"]) > 0){
                    $sql = "SELECT r.*, a.username, b.username AS username1
                    FROM reply r JOIN admins_table a
                    ON r.Sender_id = a.user_id
                    JOIN admins_table b
                    ON r.Recipient_id = b.user_id
                    WHERE r.Comment_id = ".$row["ID"]."
                    ORDER BY ID DESC";

                    $res = $connect->query($sql);

                    while($rep = $res->fetch_assoc()){
                ?>
                <div class="reply_box">
                    <div class="top">
                        <h5><?php echo $rep["username"]." to ".$rep["username1"] ?></h5>
                    </div>
                    <div class="middle">
                        <p><?php echo $rep["Message"]?></p>
                    </div>
                    <div class="foot">
                        <?php if($rep["Sender_id"] == $user_id && date("d-m-Y",strtotime($rep["Date"])) == date("d-m-Y")){?>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>">Edit</span>
                        <?php }?>
                        <span class="item-event reply-reply" data-sender-id="<?php echo $rep["Sender_id"]?>" data-sender-name="<?php echo $rep["username"]?>">Reply</span>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>" data-item-event="delete">Delete</span>
                        <span class="item-event date"><?php
                            if(date("d-m-y") == date("d-m-y", strtotime($rep["Date"]))){
                                echo "- Today, ".date("h:i:s A", strtotime($rep["Date"]));
                            }else{
                                echo "- ".date("jS M, Y. h:i:s A", strtotime($rep["Date"]));
                            }
                        ?>
                        </span>
                    </div>
                </div>
                <?php }
                    }else{?>
                <div class="body empty">
                    <p>No replies are available for this comment. Be the first to reply</p>
                </div>
                <?php } ?>
                <div class="reply_tab flex" role="form" data-action="<?php echo $url?>/admin/superadmin/submit.php" method="POST">
                    <label for="reply">
                        <input type="text" name="reply" placeholder="Reply Username...">
                    </label>
                    <label for="submit" class="btn">
                        <button name="submit" value="btn_reply">Reply</button>
                    </label>
                    <input type="hidden" name="comment_id" value="<?php echo $row["ID"]?>">
                    <input type="hidden" name="recepient_id">
                    <input type="hidden" name="user_id" value="<?php echo $user_id?>">
                    <input type="hidden" name="school_id" value="<?php echo $school_id?>">
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>No notifications have been made by other superadmins.</p>
    </div>
    <?php } ?>
</section>

<div id="announcement" class="fixed form_modal_box flex flex-center-align flex-center-content no_disp">
    <form action="<?php echo $url?>/admin/superadmin/submit.php" name="announcementForm" method="post">
        <div class="head">
            <h2>Make an Announcement</h2>
        </div>
        <div class="body">
            <div class="message_box no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <input type="hidden" name="notification_type" value="notice">
            <input type="hidden" name="school_id" value="<?php echo $school_id?>">
            <label for="title">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/easel-outline.svg" alt="title">
                </span>
                <input type="text" name="title" placeholder="Enter title of notification or announcement" maxlength="60" required>
            </label>
            <label for="message">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/megaphone-outline.svg" alt="message">
                </span>
                <textarea name="message" maxlength="400" placeholder="Enter your announcement in this space. You should not exceed 400 characters" class="admin_tinymce" required></textarea>
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

<section id="requests" class="page_setup">
    <div class="head">
        <h3>System Reports</h3>
    </div>
    <?php if(notificationCounter("all","report") > 0){?>
    <div class="body">
        <div class="user_container" style="text-align: center;">
            <div class="top">
                <h4>
                    <span>5</span>
                    <span>Schools have made a report</span>
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
                <button name="fetch_reports">Open Reports Tab</button>
            </div>
        </div>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>No school has made a report on the system</p>
    </div>
    <?php } ?>
</section>

<script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>">
</script><script src="<?php echo $url?>/admin/assets/scripts/notification.js?v=<?php echo time()?>"></script>
<script src="<?php echo $url?>/admin/assets/scripts/tinymce/jquery.tinymce.min.js"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce/tinymce.min.js"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce.js?v=<?php echo time()?>"></script>