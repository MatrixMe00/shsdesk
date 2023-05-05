<?php 
    include_once("../includes/session.php"); 
    if(isset($_SESSION["student_id"]) && !is_null($_SESSION["student_id"])):
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- stylesheets -->
    <link rel="stylesheet" href="assets/styles/general.min.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/styles/main.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/styles/nav.css?v=<?= time() ?>">

    <!-- scripts -->
    <script src="assets/scripts/jquery/compressed_jquery.js"></script>
    <script src="assets/scripts/functions.min.js"></script>

    <!-- document title -->
    <title>Document</title>
</head>
<body>
    <!-- navigation bar -->
    <?php include_once("components/nav.php") ?>

    <!-- main section -->
    <main class="m-lg">
        
    </main>

    <!-- scripts -->
    <script src="assets/scripts/nav.js?v=<?= time() ?>"></script>
</body>
</html>
<?php 
    else:
        header("location: $url/");
    endif;
?>