    <div class="form_modal_box flex flex-center-align flex-center-content">
        <form action="<?php echo "$url/submit.php" ?>" method="post" 
        style="max-height: 90vh; overflow: auto" name="update_new_user"
        class="light sm-rnd wmax-md sm-sm-lr sp-lg-lr sp-xlg-tp" >
            <div class="head">
                <h2>Change Username and Password</h2>
            </div>
            <p style="margin: 10px auto; padding: 5px; border: thin solid #eee; text-align: center;" id="pMessage" class="sticky light top">You are seeing this form because you are currently registered as a new user</p>
            <div class="body flex flex-column gap-sm" style="overflow: auto">
                <div class="joint gap-sm">
                    <label class="flex gap-xsm flex-column" for="teacher_id">
                        <span class="label_title">Your Teacher ID</span>
                        <input type="text" name="teacher_id" id="teacher_id" readonly title="Your Teacher ID" value="<?= formatItemId($teacher["teacher_id"], "TID") ?>"
                        placeholder="Your Teacher ID">
                    </label>
                    <label class="flex gap-xsm flex-column" for="lname">
                        <span class="label_title">Your Lastname</span>
                        <input type="text" name="lname" id="lname" title="Provide your lastname" required value="<?= ucfirst($teacher["lname"]) ?>"
                        placeholder="Lastname">
                    </label>
                    <label class="flex gap-xsm flex-column" for="oname">
                        <span class="label_title">Your Othername(s)</span>
                        <input type="text" name="oname" id="oname" title="Provide your Othername(s)" required value="<?= ucwords($teacher["oname"]) ?>"
                        placeholder="Othername(s)">
                    </label>
                    <input type="hidden" name="old_email" value="<?= $teacher["email"] ?>">
                    <label class="flex gap-xsm flex-column" for="email">
                        <span class="label_title">Please enter your email</span>
                        <input type="email" name="email" id="email" title="Please enter your email address" required value="<?= $teacher["email"] ?>"
                        placeholder="Email Address">
                    </label>
                    <label class="flex gap-xsm flex-column" for="phone_number">
                        <span class="label_title">Please provide your phone number</span>
                        <input type="tel" name="phone_number" id="phone_number" title="Please provide your phone number" required value="<?= $teacher["phone_number"] ?>"
                        placeholder="Phone Number">
                    </label>
                    <label class="flex gap-xsm flex-column" for="gender">
                        <span class="label_title">Please select your gender</span>
                        <select name="gender" id="gender">
                            <option value="">Select a gender</option>
                            <option value="male" <?= strtolower($teacher["gender"]) == "male" ? "selected" : "" ?>>Male</option>
                            <option value="female" <?= strtolower($teacher["gender"]) == "female" ? "selected" : "" ?>>Female</option>
                        </select>
                    </label>
                </div>
                <div class="joint gap-sm">
                    <label class="flex gap-xsm flex-column" for="new_username">
                        <span class="label_title">Please provide a new username</span>
                        <input type="text" name="new_username" id="new_username" required title="Enter a new username to log in with"
                        placeholder="New Username">
                    </label>
                    <label class="flex gap-xsm flex-column" for="new_password">
                        <span class="label_title">Please provide a new password</span>
                        <input type="password" name="new_password" id="new_password" required title="Enter a new password for yourself"
                        placeholder="New Password">
                    </label>
                </div>
            </div>
            <div class="foot sm-lg-t flex-all-center w-full-child">
                <div class="flex flex-wrap w-full-child flex-eq gap-sm wmax-xs">
                    <label for="submit" class="btn sm-unset sp-unset w-full-child">
                        <button type="submit" name="submit" id="submit" class="primary sp-med" value="new_user_update">Submit</button>
                    </label>
                    <label for="close" class="btn sm-unset sp-unset w-full-child">
                        <button type="reset" name="cancel" id="close" value="cancel" onclick="location.href='<?php echo $url?>/logout.php'" class="secondary sp-med">Cancel</button>
                    </label>
                </div>
            </div>
        </form>
    </div>

    <script src="<?= "$url/assets/scripts/functions.min.js" ?>"></script>
    <script src="<?php echo "$url/assets/scripts/updateStat.min.js?v=".time() ?>"></script>