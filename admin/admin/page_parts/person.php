<?php
    include_once("auth.php");

    //set nav_point session
    $_SESSION["nav_point"] = "account";
?>
<section class="page_setup" id="users">
    <div class="head">
        <h3>Users on your System</h3>
    </div>
    <?php 
        $users = decimalIndexArray(fetchData(...[
            "columns" => ["a.*", "s.schoolName", "r.title as roleTitle"],
            "table" => [
                [
                    "join" => "admins_table schools",
                    "alias" => "a s",
                    "on" => "school_id id"
                ],
                [
                    "join" => "admins_table roles",
                    "alias" => "a r",
                    "on" => "role id"
                ]
            ],
            "where" => ["s.id=$user_school_id", "r.access < 3", "LOWER(a.username) != 'new user'"],
            "where_binds" => "AND",
            "limit" => 0
        ]));

        if($users):
    ?>
    <div class="middle">
        <?php foreach($users as $user): ?>
        <div class="user_container flex flex-column">
            <div class="top">
                <h4><?= $user["fullname"] ?> (<span class="username"><?= $user["username"] ?></span>)</h4>
            </div>
            <div class="desc flex flex-wrap">
                <?php if($user["schoolName"] != null): ?>
                <div class="school_name">
                    <span><?= $user["schoolName"] ?></span>
                </div>
                <?php endif; ?>
                <div class="role">
                    <span><?= formatName($user["roleTitle"])?></span>
                </div>
                <div class="member_since">
                    <span>Added on <?= date("jS F, Y", strtotime($user["adYear"]))?></span>
                </div>
            </div>
            <div class="foot">
                <?php if($user_id == $user["user_id"]): ?>
                <span class="item-event edit">Edit</span>
                <?php endif; ?>
                <?php if($user_details["role"] <= 4): ?>
                <span class="item-event status" data-user-id="<?= $user["user_id"]?>"><?php 
                    if($user["Active"] == true && $user_id != $user["user_id"]){
                        echo "Deactivate";
                    }elseif($user_id != $user["user_id"]){
                        echo "Activate";
                    }
                ?></span>
                <?php endif; ?>
                <?php if($user["user_id"] != $user_id && $admin_access == 2): ?>
                <span class="item-event delete" data-user-id="<?= $user["user_id"]?>">Delete</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else:?>
    <div class="body empty" style="margin-top: 10px">
        <p>No users were found in the system</p>
    </div>
    <?php endif; ?>
</section>

<?php 
    $users = decimalIndexArray(fetchData(...[
        "columns" => ["a.*", "s.schoolName", "r.title as roleTitle"],
        "table" => [
            [
                "join" => "admins_table schools",
                "alias" => "a s",
                "on" => "school_id id"
            ],
            [
                "join" => "admins_table roles",
                "alias" => "a r",
                "on" => "role id"
            ]
        ],
        "where" => ["s.id=$user_school_id", "r.access < 3", "LOWER(a.username) = 'new user'"],
        "where_binds" => "AND",
        "limit" => 0
    ]));

    if($users):
?>
<section class="page_setup" id="users">
    <div class="head">
        <h3>Users yet to register on your System</h3>
    </div>
    <div class="middle">
        <?php foreach($users as $user):?>
        <div class="user_container flex flex-column">
            <div class="top">
                <h4><?= $user["fullname"] ?></h4>
            </div>
            <div class="desc flex flex-wrap">
                <?php if($user["schoolName"] != null): ?>
                <div class="school_name">
                    <span><?= $user["schoolName"] ?></span>
                </div>
                <?php endif; ?>
                <div class="role">
                    <span><?= formatName($user["roleTitle"]) ?></span>
                </div>
                <div class="member_since">
                    <span>Added on <?= date("jS F, Y", strtotime($user["adYear"]))?></span>
                </div>
            </div>
            <div class="foot">
                <?php if($user_id == $user["user_id"]): ?>
                <span class="item-event edit">Edit</span>
                <?php endif; ?>
                <span class="item-event status" data-user-id="<?= $user["user_id"]?>">
                <?php 
                    if($user["Active"] == true && $user_id != $user["user_id"]){
                        echo "Deactivate";
                    }elseif($user_id != $user["user_id"]){
                        echo "Activate";
                    }
                ?></span>
                <?php if($user["user_id"] != $user_id): ?>
                <span class="item-event delete" data-user-id="<?= $user["user_id"]?>">Delete</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if($admin_access === 2): ?>
<section>
    <div class="base-foot">
        <div class="btn w-full w-full-child" style="max-width: 12rem">
            <button onclick="$('#adminAdd').removeClass('no_disp')" class="cyan sp-lg">Add a new user</button>
        </div>
    </div>
</section>
<?php endif; ?>

<div id="editAccount" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <form action="<?= $url?>/admin/submit.php" method="post" class="" name="user_account_form">
        <div class="head">
            <h2>My Account</h2>
        </div>
        <div class="body">
            <div class="message_box success no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <div class="joint">
                <label for="fullname">
                    <span class="label_image">
                        <img src="<?= $url?>/assets/images/icons/id card.png" alt="fullname">
                    </span>
                    <input type="text" name="fullname" id="fullname" class="text_input" placeholder="Full Name" autocomplete="off"
                    title="Enter your full name" value="<?= $user_details["fullname"] ?>">
                </label>
                <label for="email">
                    <span class="label_image">
                        <img src="<?= $url?>/assets/images/icons/mail-outline.svg" alt="email">
                    </span>
                    <input type="email" name="email" id="email" class="text_input" placeholder="Email Address" autocomplete="off"
                    title="Provide your email address" value="<?= $user_details["email"] ?>">
                </label>
                <label for="contact">
                    <span class="label_image">
                        <img src="<?= $url?>/assets/images/icons/phone-portrait-outline.svg" alt="contact">
                    </span>
                    <input type="text" name="contact" id="contact" class="text_input tel" placeholder="Provide your contact" autocomplete="off"
                    value="<?= $user_details["contact"] ?>">
                </label>
                <label for="username">
                    <span class="label_image">
                        <img src="<?= $url?>/assets/images/icons/person-outline.svg" alt="username">
                    </span>
                    <input type="text" name="username" id="username" class="text_input" placeholder="Enter your username" minlength="5" 
                    autocomplete="off" value="<?= $user_details["username"] ?>">
                </label>
            </div>
            <div class="flex">
                <label for="submit" class="btn">
                    <button type="submit" name="submit" value="user_detail_update">Update</button>
                </label>
                <label for="cancel" class="btn">
                    <button type="button" name="cancel" onclick="$('#editAccount').addClass('no_disp')">Cancel</button>
                </label>
            </div>
        </div>
    </form>
</div>
    
    <?php if($admin_access === 2): ?>
    <div id="adminAdd" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
        <?php require_once($rootPath."/admin/adminAdd.php") ?>
    </div>
    <?php endif; close_connections() ?>

    <script type="text/javascript" src="<?= $url?>/admin/assets/scripts/person.min.js?v=<?= time()?>"></script>
    <script src="<?= $url?>/assets/scripts/form/general.min.js?v=<?= time()?>"></script>