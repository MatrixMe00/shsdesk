<?php include_once("../includes/session.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/blocks/generalHead.php')?>

    <!--Document title-->
    <title>About Us</title>

    <!--Stylesheets-->
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/about/about.css">
</head>
<body>
    <?php @include_once($rootPath.'/blocks/nav.php')?>

    <main>
        <section id="intro">
            <div class="head">
                <h1>About ShsDesk</h1>
            </div>
            <div class="desc">
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Tenetur dicta officia, saepe at reiciendis nulla totam. Libero placeat tempora quisquam praesentium odio quidem distinctio perferendis voluptate deserunt. Modi, nihil quaerat?</p>
                <p>This is the about page of the shsdesk website. Lorem ipsum dolor sit amet consectetur adipisicing elit. Unde exercitationem enim eos dolor est consequuntur?Welcome aboard</p>
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
    </main>

    <?php @include_once($rootPath.'/blocks/footer.php')?>

    <!--Document scripts-->
    <script src="<?php echo $url?>/assets/scripts/head_foot.js?v=<?php echo time()?>"></script>

    <!--Angular scripts-->
    <script src="<?php echo $url?>/assets/scripts/angular_index.js"></script>
</body>
</html>