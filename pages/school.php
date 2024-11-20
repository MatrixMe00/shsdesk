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
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/school/school.css?v=<?php echo time()?>">
</head>
<body class="light">
    <?php $start_sticky = true; @include_once($rootPath.'/blocks/nav.php')?>

    <main class="sp-xlg wmax-lg sm-auto flex gap-lg flex-eq-3xs flex-wrap">
        <?php 
            $schools = decimalIndexArray(fetchData("s.*, c.title as type","schools s JOIN school_category c ON c.id=s.category","",0));
            if(is_array($schools)):
                foreach($schools as $key=>$school): ?>
        <div class="flex white school-card flex-column wmax-xs sm-auto lt-shade-h">
            <div class="img" style="height: 195px; min-width: 195px;">
                <img src="<?= "$url/{$school["logoPath"]}"?>" loading="lazy" alt="<?= $school["schoolName"] ?>" style="min-width: inherit; height: inherit">
            </div>
            <div class="content sp-lg flex flex-column gap-lg">
                <h5 class="color-secondary flex flex-space-content">
                    <span><?= strtoupper($school["type"]) ?></span>
                    <span><?= ucwords($school["residence_status"],"/") ?></span>
                </h5>
                <h3 class="flex-all-center gap-sm txt-al-c txt-fn flex-column">
                    <span class="school_name"><?= $school["schoolName"] ?></span>
                    <span class="txt-fs color-orange"><?= $school["abbr"] ?></span>
                </h3>
                <hr>
                <p><?php 
                        $add_btn = false;
                        $sub = substr($school["description"], 0, 200);
                        $sub = strip_tags(html_entity_decode($sub));

                        echo $sub;
                        if(intval(strlen($school["description"])) > 200){
                            $add_btn = true;
                            echo "...";
                        }
                ?></p>
                <div class="no_disp full-content">
                    <?php 
                        $content = html_entity_decode($school["description"]);

                        //remove visible escape characters
                        $content = str_replace("\\r", "", $content);
                        $content = str_replace("\\n", "", $content);
                        $content = str_replace("\\", "", $content);

                        echo $content;
                    ?>
                </div>
                <div class="btn p-med w-full">
                    <button <?= !$add_btn ? "disabled":"name='read_more'" ?> class="plain-r <?= $add_btn ? "teal":"red" ?> sm-auto wmax-sm w-full">Continue Reading</button>
                </div>
            </div>
        </div>
        <?php endforeach;
            else: ?>
        <div class="border-secondary border white txt-al-c sp-lg">
            <p>No schools have been uploaded yet. Please try again later</p>
        </div>
        <?php endif; ?>
    </main>

    <?php @include_once($rootPath.'/blocks/footer.php')?>

    <div class="fixed flex flex-center-content wmax-med flex-center-align form_modal_box no_disp" id="more_detail">
        <form class="form">
            <div class="head">
                <h2>School Name</h2>
            </div>
            <div class="body">
                <div id="detail">
                </div>
            </div>
            <div class="foot">
                <div class="btn p-lg wmax-sm sm-auto w-full">
                    <button name="cancel" class="plain-r red w-full">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!--Document scripts-->
    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/index.min.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/head_foot.min.js?v=<?php echo time()?>"></script>

    <script>
        //display details of school
        $("button[name=read_more]").click(function(){
            //insert school name
            const parent = $(this).parents(".school-card");
            s_name = parent.find("span.school_name");
            $("#more_detail .head h2").html(s_name);

            //grab full detail
            detail = parent.find(".full-content").html();

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
<?php close_connections() ?>