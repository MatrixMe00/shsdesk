<?php require_once("../../../includes/session.php");

    //set nav_point session
    $_SESSION["nav_point"] = "Notification";
?>

<section>
    <p>Notifications will be displayed here</p>
</section>

<?php 
    $result = $connect->query("SELECT DISTINCT n.* 
        FROM notification n JOIN reply r 
        ON n.ID = r.Comment_id 
        WHERE r.Read_by NOT LIKE '%$user_username%'
        AND n.Read_by LIKE '%$user_username%'
        ORDER BY ID DESC");

    if($result->num_rows > 0){
?>
<section class="page_setup">
    <div class="head">
        <h2>New Replies</h2>
    </div>
    <?php
        if($result->num_rows > 0){
    ?>
    <div class="body">
        <?php while($row = $result->fetch_assoc()){ ?>
        <div data-box-number="<?php echo $row["ID"] ?>" class="notif_box flex flex-column relative unread">
            <div class="top">
                <h4><?php echo $row["Title"]?></h4>
                <span class="username"><?php echo getUserDetails($row["Sender_id"])["username"]?></span>
                <span class="date"><?php echo date("jS F, Y",strtotime($row["Date"]))?></span>
            </div>
            <div class="middle">
                <p><?php echo html_entity_decode($row["Description"])?></p>
            </div>
            <div class="foot">
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
                        <p><?php echo html_entity_decode($rep["Message"], ENT_QUOTES)?></p>
                    </div>
                    <div class="foot">
                        <?php if($rep["Sender_id"] == $user_id && date("d-m-Y",strtotime($rep["Date"])) == date("d-m-Y")){?>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>">Edit</span>
                        <?php }?>
                        <span class="item-event reply-reply" data-sender-id="<?php echo $rep["Sender_id"]?>" data-sender-name="<?php echo $rep["username"]?>">Reply</span>
                        <?php if($rep["Sender_id"] == $user_id){ ?>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>" data-item-event="delete">Delete</span>
                        <?php } ?>
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
                <div class="reply_tab flex" role="form" data-action="<?php echo $url?>/admin/submit.php" method="POST">
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
                <span class="load_message" style="display:none; font-size: small; padding: 0.3em 0.5em; margin-left: 0.4em"></span>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php }?>
</section>
<?php } ?>

<?php
    $result = $connect->query("SELECT *
        FROM notification
        WHERE (Read_by NOT LIKE '%$user_username%' AND Audience='all')
        OR (Audience LIKE '%$user_username%' AND Read_by NOT LIKE '%$user_username%')
        ORDER BY ID DESC");

    if($result->num_rows > 0){
?>
<section class="page_setup">
    <div class="head">
        <h2>Unread Notifications</h2>
    </div>
    <?php
        if($result->num_rows > 0){
    ?>
    <div class="body">
        <?php while($row = $result->fetch_assoc()){ ?>
        <div data-box-number="<?php echo $row["ID"] ?>" class="notif_box flex flex-column relative unread">
            <div class="top">
                <h4><?php echo $row["Title"]?></h4>
                <span class="username"><?php echo getUserDetails($row["Sender_id"])["username"]?></span>
                <span class="date"><?php echo date("jS F, Y",strtotime($row["Date"]))?></span>
            </div>
            <div class="middle">
                <p><?php echo html_entity_decode($row["Description"])?></p>
            </div>
            <div class="foot">
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
                        <p><?php echo html_entity_decode($rep["Message"], ENT_QUOTES)?></p>
                    </div>
                    <div class="foot">
                        <?php if($rep["Sender_id"] == $user_id && date("d-m-Y",strtotime($rep["Date"])) == date("d-m-Y")){?>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>">Edit</span>
                        <?php }?>
                        <span class="item-event reply-reply" data-sender-id="<?php echo $rep["Sender_id"]?>" data-sender-name="<?php echo $rep["username"]?>">Reply</span>
                        <?php if($rep["Sender_id"] == $user_id){ ?>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>" data-item-event="delete">Delete</span>
                        <?php } ?>
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
                <div class="reply_tab flex" role="form" data-action="<?php echo $url?>/admin/submit.php" method="POST">
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
                <span class="load_message" style="display:none; font-size: small; padding: 0.3em 0.5em; margin-left: 0.4em"></span>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php }?>
</section>
<?php } ?>

<section class="page_setup">
    <div class="head">
        <h2>Your Notifications</h2>
    </div>
    <?php
        $result = $connect->query("SELECT DISTINCT n.* 
            FROM notification n JOIN reply r 
            ON n.ID = r.Comment_id 
            WHERE r.Read_by LIKE '%$user_username%'
            AND n.Read_by LIKE '%$user_username%'
            ORDER BY ID DESC");
        if($result->num_rows > 0){
            
    ?>
    <div class="body">
        <?php while($row = $result->fetch_assoc()){ ?>
        <div data-box-number="<?php echo $row["ID"] ?>" class="notif_box flex flex-column relative">
            <div class="top">
                <h4><?php echo $row["Title"]?></h4>
                <span class="username"><?php echo getUserDetails($row["Sender_id"])["username"]?></span>
                <span class="date"><?php echo date("jS F, Y",strtotime($row["Date"]))?></span>
            </div>
            <div class="middle">
                <p><?php echo html_entity_decode($row["Description"])?></p>
            </div>
            <div class="foot">
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
                        <p><?php echo html_entity_decode($rep["Message"], ENT_QUOTES)?></p>
                    </div>
                    <div class="foot">
                        <?php if($rep["Sender_id"] == $user_id && date("d-m-Y",strtotime($rep["Date"])) == date("d-m-Y")){?>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>">Edit</span>
                        <?php }?>
                        <span class="item-event reply-reply" data-sender-id="<?php echo $rep["Sender_id"]?>" data-sender-name="<?php echo $rep["username"]?>">Reply</span>
                        <?php if($rep["Sender_id"] == $user_id){ ?>
                        <span class="item-event" data-item-id="<?php echo $rep["ID"]?>" data-item-event="delete">Delete</span>
                        <?php } ?>
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
                <div class="reply_tab flex" role="form" data-action="<?php echo $url?>/admin/submit.php" method="POST">
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
                <span class="load_message" style="display:none; font-size: small; padding: 0.3em 0.5em; margin-left: 0.4em"></span>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>There are no notifications to display yet</p>
    </div>
    <?php } ?>
</section>

<script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>">
</script><script src="<?php echo $url?>/admin/assets/scripts/notification.js?v=<?php echo time()?>"></script>