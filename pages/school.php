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
    <title>SHSDesk - Our Schools</title>

    <!--Page Meta data-->
    <meta name="description" content="Get in touch with our registered schools. Search your placed school and register with ease.
    SHSDesk makes it possible to attain admission resources at a go">
    <meta name="keywords" content="school, shs, shsdesk, placed, register, admission, prospectus, code">

    <!--Stylesheets-->
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/school/school.min.css?v=<?php echo time()?>">

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

    <main class="flex flex-wrap">
        <?php
            $result = $connect->query("SELECT * FROM schools");

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
        ?>
        <div class="school flex flex-center-content flex-center-align">
            <div class="head">
                <div class="image_div">
                    <img src="<?php echo $url?>/<?php echo $row["logoPath"]?>" loading="lazy" alt="School Name">
                </div>
                <div class="name">
                    <h3><?php echo $row["schoolName"]?></h3>
                    <?php
                        if($row["abbr"] != null)
                    ?>
                    <h5 style="color:#aaa">(<?php echo strtoupper($row["abbr"])?>)</h5>
                </div>
            </div>
            <div class="body flex flex-column">
                <div class="desc">
                    <div class="content">
                        <p><?php 
                            $sub = substr($row["description"], 0, 300);
                            $sub = strip_tags($sub);

                            //remove visible escape characters
                            $sub = str_replace("\\r", "", $sub);
                            $sub = str_replace("\\n", "", $sub);
                            $sub = str_replace("\\", "", $sub);

                            echo $sub;
                            if(intval(strlen($row["description"])) > 300){
                                echo "...";
                            }
                        ?></p>
                    </div>        
                    <div class="no_disp full-content">
                        <?php 
                            $content = html_entity_decode($row["description"]);

                            //remove visible escape characters
                            $content = str_replace("\\r", "", $content);
                            $content = str_replace("\\n", "", $content);
                            $content = str_replace("\\", "", $content);

                            echo $content;
                        ?>
                    </div>
                </div>
                <div class="button flex flex-content-end">
                    <?php if($show){?><div class="btn">
                        <button class="enrol_button">Enrol</button>
                    </div><?php }
                    if(intval(strlen($row["description"])) > 300){
                    ?>
                    <div class="btn">
                        <button name="read_more">Read More</button>
                    </div><?php } ?>
                </div>
            </div>
        </div>
        <?php
                }
            }else{
        ?>
        <div class="school no_data flex flex-center-content flex-center-align">
            <div class="body flex flex-column">
                <div class="desc">
                    <p>No School Has been Uploaded yet</p>
                    <p>Come back later!</p>
                </div>
            </div>
        </div>
        <?php } ?>
        
    </main>

    <?php @include_once($rootPath.'/blocks/footer.php')?>

    <div class="fixed flex flex-center-content flex-center-align form_modal_box no_disp" id="more_detail">
        <form class="form">
            <div class="head">
                <h2>School Name</h2>
            </div>
            <div class="body">
                <div id="detail">
                </div>
            </div>
            <div class="foot">
                <div class="btn">
                    <button name="cancel">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!--Document scripts-->
    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
    <script src="<?php echo $url?>/assets/scripts/index.min.js?v=<?php echo time()?>" async></script>
    <script src="<?php echo $url?>/assets/scripts/head_foot.min.js?v=<?php echo time()?>" async></script>

    <!--Angular scripts-->
    <script src="<?php echo $url?>/assets/scripts/angular_index.min.js?v=<?php echo time()?>" async></script>

    <script>
        nav_height = $("nav").height();
        $("main").css("margin-top", nav_height);

        //display details of school
        $("button[name=read_more]").click(function(){
            //insert school name
            s_name = $(this).parents(".body").siblings(".head").children(".name").children("h3").html();
            $("#more_detail .head h2").html(s_name);

            //grab full detail
            detail = $(this).parents(".button").siblings(".desc").children(".full-content").html();

            //parse data
            $("#more_detail #detail").html(detail);

            //show modal
            $("#more_detail").removeClass("no_disp");
        })

        //close modal
        $("#more_detail button[name=cancel]").click(function(){
            //hide modal
            $("#more_detail").addClass("no_disp");

            //remove data
            $("#more_detail #detail").html("");
            
            //reset header
            $("#more_detail .head h2").html("School Name");
        })
    </script>
</body>
</html>