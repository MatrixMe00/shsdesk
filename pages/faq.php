<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHSDESK | FAQ</title>

    <!--Script-->
    <script src="../assets/scripts/jquery/uncompressed_jquery.js"></script>

    <!--Styles-->
    <link rel="stylesheet" href="../assets/styles/head_foot.css">
    <link rel="stylesheet" href="../assets/styles/admin/admin_form.css">
    <link rel="stylesheet" href="../assets/styles/faq.css">
    <link rel="stylesheet" href="../assets/styles/general.css">
</head>
<body>
    <nav>
        <div id="logo">
            <div id="name">
                <span id="first">ONLINE</span>
                <span id="last">admission</span>
            </div>
        </div>
        <div id="buttons">
            <a href="http://localhost/shsdesk/index.php" class="button">
                <span>Home</span>
            </a>
            <a href="http://localhost/shsdesk/pages/about.php" class="button">
                <span>About</span>
            </a>
            <a href="http://localhost/shsdesk/pages/school.php" class="button">
                <span>Schools</span>
            </a>
            <a href="http://localhost/shsdesk/pages/faq.php" class="button">
                <span>FAQ</span>
            </a>
            <a href="http://localhost/shsdesk/pages/contact.php" class="button">
                <span>Contact Us</span>
            </a>
        </div>
        <div id="ham_button">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <main>
        <div id="intro_image" class="flex flex-center-content flex-center-align">
            <img src="../assets/images/backgrounds/HAN4890-web-sq_fb2957a9-958f-47c8-aeb1-8bf211bfbbfb.jpg" alt="intro_image">
            <div class="form">
                <h1>Have a question in mind?</h1>
                <div class="flex flex-wrap" role="form">
                    <label for="search">
                        <input type="search" name="search" id="search" placeholder="Ask a question..." autocomplete="off">
                    </label>
                    <label for="submit">
                        <button name="searchButton" id="searchButton">Search</button>
                    </label>
                </div>
            </div>
            <div class="shadow"></div>
        </div>

        <h2 style="padding: 1.5vh 1vw">Frequently Asked Questions</h2>
        <div class="flex flex-wrap flex-center-align">
            <section class="faq_block">
                <header class="question_head flex flex-column">
                    <h3>Is this system reliable?</h3>
                    <span class="writer">Asked by John Doe</span>
                    <span class="date">02/09/2021</span>
                </header>
                <article class="answers">
                    <div class="block">
                        <h4>Answer</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt aspernatur culpa voluptatibus, corporis repudiandae illum? This is an answer.</p>
                    </div>
                </article>
            </section>
            <section class="faq_block">
                <header class="question_head flex flex-column">
                    <h3>Does the system support the free education system?</h3>
                    <span class="writer">Asked by Emma Watson</span>
                    <span class="date">02/09/2021</span>
                </header>
                <article class="answers">
                    <div class="block">
                        <h4>Answer</h4>
                        <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ducimus quis eligendi nesciunt consequatur eos iste, eum doloremque. Voluptas pariatur sit repudiandae sapiente minus, dolorum cupiditate? This is an answer.</p>
                    </div>
                </article>
            </section>
            <section class="faq_block">
                <header class="question_head flex flex-column">
                    <h3>What are some benefits of this system?</h3>
                    <span class="writer">Asked by Evans Greenwood</span>
                    <span class="date">02/09/2021</span>
                </header>
                <article class="answers">
                    <div class="block">
                        <h4>Answer</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt aspernatur culpa voluptatibus, corporis repudiandae illum?</p>
                    </div>
                </article>
            </section>
            <section class="faq_block">
                <header class="question_head flex flex-column">
                    <h3>Who is eligible to use this system?</h3>
                    <span class="writer">Asked by John Doe</span>
                    <span class="date">02/09/2021</span>
                </header>
                <article class="answers">
                    <div class="block">
                        <h4>Answer</h4>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt aspernatur culpa voluptatibus, corporis repudiandae illum?</p>
                    </div>
                </article>
            </section>
        </div>

        <hr style="margin: 3vh 1vw">
        <form action="" method="post">
            <div class="head">
                <h2>Send Us A Question</h2>
            </div>
            <div class="body">
                <div id="message_box" class="success">
                    <span class="message">Here is a test message</span>
                    <div class="close"><span>&cross;</span></div>
                </div>
                <label for="fullname">
                    <span class="label_image">
                        <img src="../assets/images/icons/user.png" alt="fullname_logo">
                    </span>
                    <input type="text" name="fullname" id="fullname" class="text_input" placeholder="Your Fullname" autocomplete="off" pattern="[a-zA-Z\s]{6,}" title="Provide your full name" required>
                </label>
                <label for="email">
                    <span class="label_image">
                        <img src="../assets/images/icons/mail-outline.svg" alt="username_logo">
                    </span>
                    <input type="email" name="email" id="email" class="text_input" placeholder="Your Email" autocomplete="off" title="Provide you valid email address" required>
                </label>
                <label for="phone">
                    <span class="label_image">
                        <img src="../assets/images/icons/phone-portrait-outline.svg" alt="username_logo">
                    </span>
                    <input type="tel" name="phone" id="phone" class="text_input" placeholder="Your Phone Number" autocomplete="off" maxlength="10">
                </label>
                <label for="message" class="textarea">
                    <span class="label_image">
                        <img src="../assets/images/icons/chatbox-outline.svg" alt="chatbox_icon">
                    </span>
                    <textarea name="message" id="message" placeholder="Type your question here"></textarea>
                </label>
                <label for="submit" class="btn_label">
                    <button type="submit" name="submit" value="login" class="img_btn">
                        <img src="../assets/images/icons/send-outline.svg" alt="send">
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
        
    <footer>
        <span>&copy; Copyright 2021</span>
    </footer>

    <script src="../assets/scripts/head_foot.js"></script>
    <script>
        //alert("This page will be added to the project only if you want it to be added");

        //form settings
        $("form").submit(function(e){
            e.preventDefault();
        })
    </script>
</body>
</html>