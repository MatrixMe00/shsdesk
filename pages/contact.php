<?php include_once("../includes/session.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/blocks/generalHead.php')?>

    <!--Document title-->
    <title>Contact Us</title>
    <!--Stylesheets-->
    <link rel="stylesheet" href="../assets/styles/contact.css?v=<?php echo time()?>">
</head>
<body>
    <div id="cover"></div>
    <?php @include_once($rootPath.'/blocks/nav.php')?>
    <main>
        <form action="../submit.php" method="post">
            <div class="head">
                <h2>Contact Us</h2>
            </div>
            <div class="body">
                <div class="message_box no_disp">
                    <span class="message">Here is a test message</span>
                    <div class="close"><span>&cross;</span></div>
                </div>
                <label for="username">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/user.png" alt="fullname_logo">
                    </span>
                    <input type="text" name="fullname" id="fullname" class="text_input" placeholder="Your Full Name" autocomplete="off">
                </label>
                <label for="email">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/mail-outline.svg" alt="email_logo">
                    </span>
                    <input type="email" name="email" id="email" class="text_input" placeholder="Your Email" autocomplete="off">
                </label>
                <label for="message" class="textarea">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/chatbox-outline.svg" alt="chatbox_icon">
                    </span>
                    <textarea name="message" id="message" placeholder="Type your message here"></textarea>
                </label>
                <label for="submit" class="btn_label">
                    <button type="submit" name="submit" value="send_contact" class="img_btn">
                        <img src="<?php echo $url?>/assets/images/icons/send-outline.svg" alt="send">
                        <span>Send</span>
                    </button>
                </label>
            </div>
            <div class="foot">
                <p>
                    @2021 shsdesk.com
                </p>
            </div>
        </form>
    </main>

    <?php @include_once($rootPath.'/blocks/footer.php')?>

    <script src="<?php echo $url?>/assets/scripts/form/general.js"></script>
    <script src="<?php echo $url?>/assets/scripts/head_foot.js?v=<?php echo time()?>"></script>
</body>

<script>
    function messageBoxTimeout(message, message_type, time=5){
        //change the time to miliseconds
        time = time * 1000;

        $(".message_box").removeClass("error success load").addClass(message_type).show();
        $(".message_box .message").html(message)

        //prevent the timeout function if the time is set to 0
        if(time > 0){
            setTimeout(function(){
                $(".message_box").removeClass("error success load");
                $(".message_box").slideUp();
                $(".message_box .message").html('');
            }, time);
        }
    }

    $("form").submit(function(e){
        e.preventDefault();

        dataString = $(this).serialize() + "&submit=" + $("button[name=submit]").val();

        $.ajax({
            url: $(this).attr("action"),
            data: dataString,
            dataType: "html",
            cache: true,
            type: "POST",

            beforeSend: function(){
                message = "Please wait...";
                message_type = "load";
                time = 0;

                messageBoxTimeout(message, message_type, time);
            },

            success: function(html){
                message = "";
                message_type = "";
                time = 5;

                if(html == "fname_long"){
                    message = "Your fullname is too long";
                    message_type = "error";
                }else if(html == "fname_short"){
                    message = "Your fullname is too short or empty";
                    message_type = "error";
                }else if(html == "email_long"){
                    message = "Your email is too long";
                    message_type = "error";
                }else if(html == "email_short"){
                    message = "Your email is too short or empty";
                    message_type = "error";
                }else if(html == "eformat"){
                    message = "You have provided a wrong email format";
                    message_type = "error";
                }else if(html == "message_long"){
                    message = "Your message is too long. Limit it to 500 characters";
                    message_type = "error";
                }else if(html == "message_short"){
                    message = "Your message is too short or empty. It should not be less than 10 characters.";
                    message_type = "error";
                }else if(html == "true"){
                    message = "Message was sent successfully";
                    message_type = "success";
                }else{
                    message = html;
                    message_type = "error";
                    time = 10;
                }

                messageBoxTimeout(message, message_type, time);
            },

            error: function(){
                message = "Communication could not be established! Please check your connection and try again later";
                message_type = "error";
                time = 5;

                messageBoxTimeout(message, message_type, time);
            }
        })
    })
</script>
</html>