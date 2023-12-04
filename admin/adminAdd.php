<?php include_once("../includes/session.php"); ?>

<form action="<?php echo $url?>/admin/submit.php" method="post" class="fixed" style="overflow: hidden" name="addAdmin">
    <div class="head">
        <h2>Add New User</h2>
    </div>
    <p style="margin: 10px auto; padding: 5px; border: thin solid #eee; text-align: center;" id="pMessage">Please fill out the spaces below</p>
    <div class="body" style="max-height: 50vh; overflow: auto">
        <label class="flex-wrap flex-column" for="fullname">
            <span class="label_title" style="margin-right: 5px;">Provide full name</span>
            <input style="width: 100%" type="text" name="fullname" id="fullname" required title="Enter full name that the individual should use for registration"
            placeholder="Full Name*">
        </label>
        <label class="flex-wrap flex-column" for="email">
            <span class="label_title" style="margin-right: 5px;">Please enter your email</span>
            <input style="width: 100%" type="email" name="email" id="email" title="Please enter your email address" required
            placeholder="Email Address*">
        </label>
        <?php if($user_details["role"] <= 2){ ?>
        <label class="flex-wrap flex-column" for="new_username">
            <span class="label_title" style="margin-right: 5px;">Please provide a new username</span>
            <input style="width: 100%" type="text" name="new_username" id="new_username" title="Enter a username to log in with. No username defaults to 'New User'"
            placeholder="Username">
        </label>
        <label class="flex-wrap flex-column" for="new_password">
            <span class="label_title" style="margin-right: 5px;">Please provide a new password</span>
            <input style="width: 100%" type="password" name="new_password" id="new_password" title="Enter a password for user. No password defaults to Password@1"
            placeholder="Password">
        </label>

        <label for="school" class="flex-wrap flex-column">
            <span class="label_title" style="margin-right: 5px;">Select User's School</span>
            <select name="school" id="school" style="width: 100%">
                <option value="">Select A School</option>
                <option value="NULL">No School</option>
                <?php 
                    $sql = "SELECT id, schoolName FROM schools";
                    $result = $connect->query($sql);

                    if($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                ?>
                <option value="<?php echo $row["id"] ?>"><?php echo formatName($row["schoolName"])?></option>
                <?php 
                        }
                    }
                ?>
            </select>
        </label>
        <?php } ?>

        <label for="role" class="flex-wrap flex-column">
            <span class="label_title" style="margin-right: 5px;">Select A Role for user</span>
            <select name="role" id="role" style="width: 100%" required>
                <option value="">Select A Role</option>
                <?php 
                    if($user_school_id != null){
                        //check if there is a headmaster account
                        $has_head = fetchData("COUNT(user_id) AS total","admins_table","role=".($role_id+1)." AND school_id=$user_school_id")["total"];
                        if($has_head == 0){
                            $sql = "SELECT id, title FROM roles WHERE id=".($role_id+1);
                        }else{
                            $sql = "SELECT id, title FROM roles WHERE id > 2 AND school_id = $user_school_id";
                        }
                    }else{
                        $sql = "SELECT id, title FROM roles WHERE id > 2 AND school_id = 0 AND title != 'system'";
                    }
                    
                    $result = $connect->query($sql);

                    if($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                ?>
                <option value="<?php echo $row["id"] ?>"><?php 
                    if($admin_access >= 3){
                        echo formatName($row["title"]);
                    }else{
                        echo str_contains(strtolower($row["title"]), "head") ? "School Head" : formatName($row["title"]);
                    }
                ?></option>
                <?php 
                        }
                    }
                ?>
                <option value="Others">Others</option>
            </select>
        </label>
        <label class="flex-wrap flex-column no_disp" for="other_role">
            <span class="label_title" style="margin-right: 5px;">Please assign a custom role</span>
            <input style="width: 100%" type="text" name="other_role" id="other_role" title="Provide a custom role to user"
            placeholder="Custom Role">
            <span class="item-event info">
                What you should know about custom roles is that they will not receive any monetery benefits from the system. 
                They will have access to information like the admin and school head
            </span>
        </label>
        <label class="flex-wrap flex-column" for="user_contact">
            <span class="label_title" style="margin-right: 5px;">Enter the contact number of new user</span>
            <input style="width: 100%" type="text" name="user_contact" id="user_contact" title="Enter the contact number for user"
            placeholder="Contact Number">
        </label>
    </div>
    <div class="foot">
        <div class="flex flex-wrap flex-eq wmax-xs">
            <label for="submit" class="btn w-full w-full-child">
                <button type="submit" name="submit" class="primary sp-med" value="addAdmin">Submit</button>
            </label>
            <label for="cancel" class="btn w-full w-full-child">
                <button type="button" name="cancel" value="cancel" class="red sp-med" onclick="$('#adminAdd').addClass('no_disp')">Cancel</button>
        </label>
        </div>
    </div>
</form>