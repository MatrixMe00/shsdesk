    <div class="form_modal_box flex flex-center-align flex-center-content">
        <form action="<?php echo $url?>/admin/admin/submit.php" method="post" 
        style="width: 80vw; max-height: 80vh; overflow: auto" name="update_new_user">
            <div class="head">
                <h2>Change Username and Password</h2>
            </div>
            <p style="margin: 10px auto; padding: 5px; border: thin solid #eee; text-align: center;" id="pMessage">You are seeing this form because you are currently registered as a new user</p>
            <div class="body">
                <label class="flex-wrap flex-column" style="align-items: flex-start;" for="fullname">
                    <span class="label_title" style="margin-right: 5px;">Please provide your full name</span>
                    <input style="width: 100%" type="text" name="fullname" id="fullname" required title="Enter your full name you used for registration"
                    placeholder="Registered Full Name">
                </label>
                <label class="flex-wrap flex-column" style="justify-content: flex-start; align-items: flex-start;" for="email">
                    <span class="label_title" style="margin-right: 5px;">Please enter your email</span>
                    <input style="width: 100%" type="email" name="email" id="email" title="Please enter your email address" required
                    placeholder="Email Address">
                </label>
                <label class="flex-wrap flex-column" style="align-items: flex-start;" for="new_username">
                    <span class="label_title" style="margin-right: 5px;">Please provide a new username</span>
                    <input style="width: 100%" type="text" name="new_username" id="new_username" required title="Enter a new username to log in with"
                    placeholder="New Username">
                </label>
                <label class="flex-wrap flex-column" style="justify-content: flex-start; align-items: flex-start;" for="new_password">
                    <span class="label_title" style="margin-right: 5px;">Please provide a new password</span>
                    <input style="width: 100%" type="password" name="new_password" id="new_password" required title="Enter a new password for yourself"
                    placeholder="New Password">
                </label>

                <div class="flex">
                    <label for="submit" class="btn">
                        <button type="submit" name="submit" value="new_user_update">Submit</button>
                    </label>
                    <label for="cancel" class="btn">
                        <button type="button" name="cancel" value="cancel" onclick="location.href='<?php echo $url?>/admin/logout.php'">Cancel</button>
                    </label>
                </div>
            </div>
        </form>
    </div>

    <script>
        $("form").submit(function(e){
            e.preventDefault();

            result = formSubmit($(this), $("form[name=update_new_user] button[name=submit]"), false);

            if(result == true){
                $("#pMessage").html("Update Successful. Preparing dashboard...");

                //refresh in 3seconds
                setTimeout(function(){
                    $("#pMessage").html("Welcome " + $("#new_username").val());
                },3000);

                setTimeout(function(){
                    location.href = location.href;
                },4000);
            }else{
                html = $("#pMessage").html();

                if(result == "wrong-email-fullname"){
                   message = "Email or fullname provided is wrong. Please check and try again";
                }else if(result == "same-username"){
                    message = "You cannot use the same username";
                }else if(result == "same-password"){
                    message = "You cannot use the same password";
                }else if(result == "update-error"){
                    message = "Your data could not be updated. Please try again later or contact the admin";
                }

                $("#pMessage").html(message);

                setTimeout(function(){
                    $("#pMessage").html(html);
                },5000);
            }
        })
    </script>