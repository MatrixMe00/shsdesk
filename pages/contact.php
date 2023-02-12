<?php
    //depending on where the page is being called from
    $this_url = $_SERVER["REQUEST_URI"];

    if(strpos($this_url, "shsdesk")){
        include_once($_SERVER["DOCUMENT_ROOT"]."/shsdesk/includes/session.php");
    }else{
        include_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/blocks/generalHead.php')?>

    <!--Document title-->
    <title>SHSDesk - Contact Us</title>

    <!--Page Meta data-->
    <meta name="description" content="Get in touch with us via your email. We are available 24/7 and provide responses in about
    two hours of message send.">
    <meta name="keywords" content="contact, shs, shsdesk, desk, touch, fullname, contact us, available, email">


    <!--Stylesheets-->
    <link rel="stylesheet" href="../assets/styles/contact.min.css?v=<?php echo time()?>">
</head>
<body>
    <div id="cover"></div>
    <?php @include_once($rootPath.'/blocks/nav.php')?>
    <main>
        <form action="../submit.php" method="post" name="contactForm">
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
                    @<?= date("Y") ?> shsdesk.com
                </p>
            </div>
        </form>
    </main>

    <?php @include_once($rootPath.'/blocks/footer.php')?>

    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
    <script src="<?php echo $url?>/assets/scripts/head_foot.min.js?v=<?php echo time()?>" async></script>
</body>

<script src="<?php echo $url?>/assets/scripts/contact.min.js?v=<?php echo time()?>" async></script>
</html>