<?php include_once("includes/session.php")?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($rootPath.'/blocks/generalHead.php')?>

    <!--Document title-->
    <title>SHSDesk - Home Page</title>

    <!--Page Meta data-->
    <meta name="description" content="Your number one platform for online admission in Ghana. 
    This system makes the process easier working your admissions right in your comfort zone">
    <meta name="keywords" content="shs, desk, shsdesk, school, online registration, online, registration, registration in ghana, senior high school,
    senior, high, technical school, technical, secondary, secondary school, student admission, student, admission">

    <!--Stylesheets-->
    <link rel="stylesheet" href="assets/styles/index_page.min.css?v=<?= time()?>">
    <link rel="stylesheet" href="assets/styles/admissionForm.min.css?v=<?= time()?>">

    <!--Payment and angular script-->
    <script src="https://js.paystack.co/v1/inline.js" defer></script>
    <script src="assets/scripts/angular/angular.min.js?v=<?= time()?>"></script>

    <style>
        @media print{
            body *{
                visibility: hidden;
            }
            #sumView, #sumView *{
                visibility: visible;
            }
            #sumView{
                position: absolute;
                left: 0; right: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body ng-app="index_application" id="index_main">
    <!-- navigation bar -->
    <?php require_once($rootPath.'/blocks/nav.php')?>
    
    <!-- page main content -->
    <main>
        <!-- Carousel / Slider -->
        <?php include_once("./blocks/index/carousel.php") ?>

        <!-- About section -->
        <?php include_once("./blocks/index/about.php") ?>

        <!-- Student admission section -->
        <?php include_once("./blocks/index/admission.php") ?>
        
        <div class="flex flex-wrap flex-center-content">
            <!-- assistance section for admin contact numbers -->
            <?php include_once("./blocks/index/assistance.php") ?>
            
            <!-- section for video -->
            <?php include_once("./blocks/index/video.php") ?>
        </div>
    </main>
    
    <!-- message us button -->
    <a href="https://wa.me/233247552852">
        <span id="message_us" class="primary">
            Message Us
        </span>
    </a>

    <!-- page footer -->
    <?php require_once($rootPath.'/blocks/footer.php')?>

    <!--Document scripts-->
    <script src="assets/scripts/form/general.min.js?v=<?= time()?>"></script>
    <script src="assets/scripts/index.min.js?v=<?= time()?>"></script>
    <script src="assets/scripts/head_foot.min.js?v=<?= time()?>"></script>
    <script src="assets/scripts/admissionForm.min.js?v=<?= time(); ?>"></script>

    <!--Angular scripts-->
    <script src="assets/scripts/angular_index.js?v=<?= time()?>"></script>

    <!--Payment scripts-->
    <script src="assets/scripts/form/payForm.min.js?v=<?= time();?>"></script>
</body>
</html>