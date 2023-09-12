<?php
    include_once("auth.php");

    //set nav_point session
    $_SESSION["nav_point"] = "announcement";
?>

<section class="txt-al-c sp-xlg">
    <p>All announcements sent to your school would be displayed here</p>
</section>

<section class="btn w-full sp-xlg-tp p-lg">
    <button name="btn_announce">Make Announcement</button>
</section>

<section class="page_setup">
    <div class="head">
        <h2>Your Announcements</h2>
    </div>
    <div class="body hmax-unset flex flex-eq-4xs flex-wrap gap-sm">
        <?php $announcements = decimalIndexArray(fetchData1("*","announcement","school_id=$user_school_id",0));
            if(is_array($announcements)) :
                foreach($announcements as $announcement) :
        ?>
        <div data-box-number="<?= $announcement["id"] ?>" class="flex flex-column border b-teal p-med announcement">
            <div class="top">
                <h4><?= $announcement["heading"] ?></h4>
                <span class="item-event info">To: <?= ucfirst($announcement["audience"]) ?></span>
                | <span class="item-event date"><?= date("M d, Y H:i:s", strtotime($announcement["date"])) ?></span>
            </div>
            <div class="middle">
                <p>
                    <?= html_entity_decode($announcement["body"]) ?>
                </p>
            </div>
            <div class="foot">
                <span class="item-event" data-item-id="<?= $announcement["id"] ?>" data-item-event="delete">Delete</span>
            </div>
        </div>
        <?php endforeach;?>
        <?php else: ?>
        <div class="empty lt-shade p-xlg txt-al-c">
            <p>No announcements made yet. Please make one to see your announcements</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<div id="announcement" class="fixed form_modal_box flex flex-center-align flex-center-content no_disp">
    <form action="<?php echo $url?>/admin/admin/submit.php" name="announcementForm" method="post">
        <div class="head">
            <h2>Make an Announcement</h2>
        </div>
        <div class="body">
            <div class="message_box no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <label for="title">
                <span class="load_message" style="display: none; font-size: small; padding: 0.3em 0.5em; margin-left: 0.4em"></span>
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/easel-outline.svg" alt="title">
                </span>
                <input type="text" name="title" placeholder="Enter title of notification or announcement" maxlength="60" required>
            </label>
            <label for="message">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/megaphone-outline.svg" alt="message">
                </span>
                <textarea name="message" maxlength="400" placeholder="Enter your announcement in this space. You should not exceed 400 characters" class="admin_tinymce"></textarea>
            </label>
            <div id="#aud">
                <p style="padding-left: 12px">Select your audience</p>
                <div class="flex">
                    <label for="audience_all" class="radio">
                        <input type="radio" name="audience" id="audience_all" value="All" checked>
                        <span class="label_title">All</span>
                    </label>
                    <label for="audience_others" class="radio">
                        <input type="radio" name="audience" id="audience_others" value="teachers">
                        <span class="label_title">Teachers</span>
                    </label>
                    <label for="audience_students" class="radio">
                        <input type="radio" name="audience" id="audience_students" value="students">
                        <span class="label_title">Students</span>
                    </label>
                </div>
            </div>
            <div class="flex flex-wrap btn p-lg gap-md sm-auto">
                    <button type="submit" name="submit" class="primary" value="make_announcement">Make Announcement</button>
                    <button type="reset" name="cancel" class="danger">Cancel</button>
            </div>
        </div>
    </form>
</div>

<script src="<?= "$url/admin/assets/scripts/notification.js?v=".time() ?>"></script>
<script src="<?php echo $url?>/admin/assets/scripts/tinymce/jquery.tinymce.min.js" async></script>
<script src="<?php echo $url?>/admin/assets/scripts/tinymce/tinymce.min.js" async></script>
<script src="<?php echo $url?>/admin/assets/scripts/tinymce.min.js" async></script>
<script>
    /*$("#announce_btn").click(function(){
        $("#announcement").removeClass("no_disp")
    })

    $("#announcement button[name=cancel]").click(function(){
        $("#announcement").addClass("no_disp")
    })*/
</script>