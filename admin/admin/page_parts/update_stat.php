    <div class="form_modal_box flex flex-center-align flex-center-content">
        <form action="" method="post" class="" style="width: 80vw; top: 10vw;">
            <div class="head">
                <h2>Change Username and Password</h2>
            </div>
            <p style="margin: 10px auto; padding: 5px; border: thin solid #eee; text-align: center;">You are seeing this form because you are currently registered as a new user</p>
            <div class="body">
                <label class="flex-wrap flex-column" style="align-items: flex-start;" for="new_username">
                    <span class="label_title" style="margin-right: 5px;">Please provide a new username</span>
                    <input style="width: 100%" type="text" name="new_username" id="new_username" required title="Enter a new username to log in with"
                    placeholder="New Username">
                </label>
                <label class="flex-wrap flex-column" style="justify-content: flex-start; align-items: flex-start;" for="new_password">
                    <span class="label_title" style="margin-right: 5px;">Please provide a new password</span>
                    <input style="width: 100%" type="text" name="new_password" id="new_password" required title="Enter a new password for yourself"
                    placeholder="New Password">
                </label>
                <label class="flex-wrap flex-column" style="justify-content: flex-start; align-items: flex-start;" for="email">
                    <span class="label_title" style="margin-right: 5px;">Please enter your email</span>
                    <input style="width: 100%" type="email" name="email" id="email" title="Please enter your email address" required
                    placeholder="Email Address">
                </label>
                <div class="flex">
                    <label for="submit" class="btn">
                        <button type="submit" name="submit" value="new_user_update">Submit</button>
                    </label>
                    <label for="cancel" class="btn">
                        <button type="button" name="cancel" value="cancel">Cancel</button>
                    </label>
                </div>
            </div>
        </form>
    </div>