<?php include_once("../includes/session.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/blocks/generalHead.php')?>

    <!--Document title-->
    <title>SHSDesk - About Us</title>

    <!--Page meta data-->
    <meta name="description" content="You are welcomed to the SHSDesk platform. Developed by S&S Innovative Hub, we provide trusted resources for student management and record keeping.">
    <meta name="keywords" content="shs, desk, shsdesk, s&s, innovative, hub, student, distance, about us,, ceo,
    site, admission, queing, long queing">

    <!--Stylesheets-->
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/about/about.css?v=<?php echo time()?>">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-W7MF3JTHJ1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-W7MF3JTHJ1');
    </script>
</head>
<body>
    <?php @include_once($rootPath.'/blocks/nav.php')?>

    <section id="intro">
        <div id="background">
            <img src="<?php echo "$url/assets/images/default/javier-quesada-qYfwGVNJqSA-unsplash.jpg"?>" alt="Books on table">
        </div>
        <div class="shadow"></div>
        <div class="head">
            <h1>About ShsDesk</h1>
        </div>
        <div class="desc">
            <p>Welcome to <a href="https://www.shsdesk.com">shsdesk.com</a>, your number one platform for online admission for Senior High Schools.</p>
            <p>Our system is designed to prevent long queing and to save one from the hussle of traveling long distances for the purposes of admission.</p>
            <p>The system is designed by S&S Innovative Hub and are ready to serve you.</p>
        </div>
        </section>
    <main class="flex flex-wrap">
        <div id="lhs">   
            <section id="ceo">
                <div class="head">
                    <h1>Message From the CEO</h1>
                    <h5 style="text-align: center">Success Yeboah Wonder [S&S Innovative]</h5>
                </div>
                <div class="desc">
                    <p>
                        I warmly welcome you to the SHSDesk website. I am very confident that you will find our website useful. We are proud of our continued success as the best 
                        platform to make SHS admission simple and easy in Ghana. We can provide your school or organization with the finest web systems to simplify manual processes.
                    </p>
                    <p>
                        We provide a higher level of services and solutions to our clients. With the best programmers and experts in system development, S&S Innovative Hub can provide
                        your school or organization with the best web system and services. Send us a message now through our <a href="https://www.shsdesk.com/pages/contact.php" 
                    >Contact Form</a> or via <a href="https://wa.me/233247552852">WhatsApp</a>
                    </p>
                    <p>We are open 24/7</p>
                </div>
            </section>
            
            <section id="gallery" class="flex flex-wrap flex-center-content">
                <?php
                    $result = $connect->query("SELECT item_img, image_alt, item_head, item_desc
                    FROM pageitemdisplays
                    WHERE active=TRUE AND item_type='gallery' AND item_page='about'");

                    if($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                ?>
                <div class="gallery_box">
                    <div class="image">
                        <img src="<?php echo $url."/".$row['item_img']?>" alt="<?php echo $row['image_alt']?>">
                    </div>
                    <div class="body">
                        <div class="caption">
                            <h3><?php echo $row["item_head"]?></h3>
                        </div>
                        <div class="message">
                            <p><?php echo $row["item_desc"]?></p>
                        </div>
                    </div>
                </div>
                <?php
                        }
                    }else{
                ?>
                <div class="gallery_box">
                    <div class="image">
                        <img src="<?php echo $url?>/assets/images/default/ges.jfif" alt="">
                    </div>
                    <div class="body">
                        <div class="caption">
                            <h3>This is an item</h3>
                        </div>
                        <div class="message">
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Cum rem asperiores, beatae fugiat temporibus fugit, a veniam dolorem non placeat debitis repudiandae quaerat alias aspernatur eligendi iste, amet error dolorum? Odio cumque fuga culpa laborum?</p>
                        </div>
                    </div>
                </div>
                <div class="gallery_box">
                    <div class="image">
                        <img src="<?php echo $url?>/assets/images/default/ghanaian student.jfif" alt="">
                    </div>
                    <div class="body">
                        <div class="caption">
                            <h3>This is an item</h3>
                        </div>
                        <div class="message">
                            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Repudiandae itaque inventore necessitatibus perspiciatis fuga ratione corporis quaerat reprehenderit facere exercitationem?</p>
                        </div>
                    </div>
                </div>
                <div class="gallery_box">
                    <div class="image">
                        <img src="<?php echo $url?>/assets/images/default/shs student 2.jfif" alt="">
                    </div>
                    <div class="body">
                        <div class="caption">
                            <h3>This is an item</h3>
                        </div>
                        <div class="message">
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Itaque beatae inventore magni, mollitia modi illo eum debitis atque eius id, dolores reiciendis? A facilis, alias eveniet quae at laboriosam nisi magnam quod dolorum mollitia laudantium reprehenderit odit ex officia harum!</p>
                        </div>
                    </div>
                </div>
                <div class="gallery_box">
                    <div class="image">
                        <img src="<?php echo $url?>/assets/images/default/shs student.jfif" alt="">
                    </div>
                    <div class="body">
                        <div class="caption">
                            <h3>This is an item</h3>
                        </div>
                        <div class="message">
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Iure ex molestias nam odio in. A molestiae facere aut est! Cupiditate illo voluptate nam natus?</p>
                        </div>
                    </div>
                </div>
                <?php }?>
            </section>
        </div>
        
        <div id="rhs">
            <section id="team">
                <div class="head">
                    <h1>Our Team</h1>
                </div>
                <div class="desc flex flex-wrap flex-center-content flex-center-align">
                    <div class="person">
                        <div class="image">
                            <img src="<?php echo $url?>/assets/images/default/library.jfif" alt="">
                        </div>
                        <div class="body">
                            <span class="name">John Doe</span>
                            <span class="role">CEO</span>
                            <span class="more">Lorem ipsum dolor sit amet consectetur adipisicing elit. Harum quisquam, amet provident animi nemo laboriosam?</span>
                        </div>
                    </div>
                    <div class="person">
                        <div class="image">
                            <img src="<?php echo $url?>/assets/images/default/library.jfif" alt="">
                        </div>
                        <div class="body">
                            <span class="name">Abraham Lincoln</span>
                            <span class="role">CEO</span>
                            <span class="more">Lorem ipsum dolor sit amet consectetur adipisicing elit. Harum quisquam, amet provident animi nemo laboriosam?</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php @include_once($rootPath.'/blocks/footer.php')?>

    <!--Document scripts-->
    <script src="<?php echo $url?>/assets/scripts/head_foot.js?v=<?php echo time()?>"></script>

    <!--Angular scripts-->
    <script src="<?php echo $url?>/assets/scripts/angular_index.js"></script>
</body>
</html>