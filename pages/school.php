<?php include_once("../includes/session.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/blocks/generalHead.php')?>

    <!--Document title-->
    <title>Schools</title>

    <!--Stylesheets-->
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/school/school.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/admissionForm.css?v=<?php echo time()?>">
</head>
<body>
    <?php @include_once($rootPath.'/blocks/nav.php')?>

    <main class="flex flex-wrap">
        <?php
            $result = $connect->query("SELECT * FROM schools");

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
        ?>
        <div class="school flex flex-center-content flex-center-align flex-column">
            <div class="head">
                <div class="image_div">
                    <img src="<?php echo $url?>/<?php echo $row["logoPath"]?>" alt="School Name">
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
                    <p><?php echo html_entity_decode($row["description"])?></p>
                </div>
                <!-- <div class="button flex flex-content-end">
                    <div class="btn">
                        <button class="enrol_button">Enrol</button>
                    </div>
                    <div class="btn">
                        <button>Read More</button>
                    </div>                 
                </div> -->
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

    <div id="payment_form" class="form_modal_box no_disp">
        <?php include_once($rootPath."/blocks/admissionPaymentForm.php");?>
    </div>

    <div id="admission" class="form_modal_box flex no_disp">
        <?php include_once($rootPath.'/blocks/admissionForm.php')?>
    </div>

    <!--Document scripts-->
    <script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/index.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/head_foot.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/admissionForm.js?v=<?php echo time(); ?>"></script>

    <!--Angular scripts-->
    <script src="<?php echo $url?>/assets/scripts/angular_index.js?v=<?php echo time()?>"></script>

    <!--Payment scripts-->
    <script src="<?php echo $url?>/assets/scripts/form/payForm.js?v=<?php echo time();?>"></script>

    <script>
        nav_height = $("nav").height();
        $("main").css("margin-top", nav_height);
    </script>
</body>
</html>