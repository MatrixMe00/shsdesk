<?php include_once("../../../includes/session.php");

    //add nav point session
    $_SESSION["nav_point"] = "account";
?>
<section class="page_setup" id="users">
    <div class="head">
        <h3>Users on System</h3>
    </div>
    <?php 
        $result = $connect->query("SELECT a.*, r.title AS roleTitle 
        FROM admins_table a JOIN roles r
        ON a.role = r.id 
        WHERE r.access = TRUE") or die($connect->error);

        if($result->num_rows > 0){
    ?>
    <div class="middle">
        <?php while($row = $result->fetch_assoc()){?>
        <div class="user_container flex flex-column">
            <div class="top">
                <h4><?php echo $row["fullname"] ?> (<span class="username"><?php echo $row["username"] ?></span>)</h4>
            </div>
            <div class="desc flex flex-wrap">
                <?php if($row["school_id"] != null){ ?>
                <div class="school_name">
                    <span>
                        <?php echo getSchoolDetail($row["school_id"])[0] ?>
                    </span>
                </div>
                <?php } ?>
                <div class="role">
                    <span><?php echo $row["roleTitle"]?></span>
                </div>
                <div class="member_since">
                    <span>Since <?php echo date("dS F, Y", strtotime($row["adYear"]))?></span>
                </div>
            </div>
            <div class="foot">
                <?php if($user_details["role"] == 1 || $row["user_id"] == $user_id){?>
                <span class="item-event" data-user-id="<?php echo $row["user_id"]?>">Edit</span>
                <?php }?>
                <span class="item-event" data-user-id="<?php echo $row["user_id"]?>">Activate</span>
                <span class="item-event" data-user-id="<?php echo $row["user_id"]?>">Delete</span>
                <?php if($user_details["role"] == 1){?>
                <span class="item-event" data-user-id="<?php echo $row["user_id"]?>">Promote</span>
                <?php }?>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php }else{?>
    <div class="body empty" style="margin-top: 10px">
        <p>No users were found in the system</p>
    </div>
    <?php }?>
    <div class="base-foot">
        <div class="btn">
            <button>Add a new user</button>
        </div>
    </div>
</section>

<form action="<?php echo $url?>/admin/submit.php" method="post" class="" name="user_account_form">
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
                        <img src="<?php echo $url?>/assets/images/icons/id card.png" alt="fullname">
                    </span>
                    <input type="text" name="fullname" id="fullname" class="text_input" placeholder="Full Name" autocomplete="off"
                    title="Enter your full name" value="<?php echo $user_details["fullname"] ?>">
                </label>
                <label for="email">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/mail-outline.svg" alt="email">
                    </span>
                    <input type="email" name="email" id="email" class="text_input" placeholder="Email Address" autocomplete="off"
                    title="Provide your email address" value="<?php echo $user_details["email"] ?>">
                </label>
                <label for="contact">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="contact">
                    </span>
                    <input type="tel" name="contact" id="contact" class="text_input" placeholder="Provide your contact" autocomplete="off"
                    value="<?php echo $user_details["contact"] ?>">
                </label>
                <label for="username">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="username">
                    </span>
                    <input type="text" name="username" id="username" class="text_input" placeholder="Enter your username" minlength="8" 
                    autocomplete="off" value="<?php echo $user_details["username"] ?>">
                </label>
            </div>
            <div class="flex">
                <label for="submit" class="btn">
                    <button type="submit" name="submit" value="user_detail_update">Update</button>
                </label>
            </div>
        </div>
    </form>

    <script type="text/javascript" src="<?php echo $url?>/admin/assets/scripts/person.js?v=<?php echo time()?>"></script>