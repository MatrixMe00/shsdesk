<?php include_once("../includes/session.php"); 
    if(isset($_SESSION["teacher_id"]) && !is_null($_SESSION["teacher_id"])) :
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles/general.min.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/styles/main.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/styles/nav.css?v=<?= time() ?>">
    <script src="assets/scripts/jquery/compressed_jquery.js"></script>
    <title>Update User Data</title>
</head>
<?php if(fetchData1("LOWER(user_username) AS username", "teacher_login", "user_id=".$_SESSION["teacher_id"])["username"] !== "new user") : ?>
<body class="flex">
    <?php include_once("blocks/nav.php") ?>
    <main id="rhs" class="sp-med light">
    </main>

    <script src="assets/scripts/functions.min.js?v=<?php echo time()?>"></script>
    <script src="assets/scripts/nav.min.js?v=<?= time() ?>"></script>
</body>
<?php else: ?>
<body>
    <?php include("pages/update_stat.php") ?>
</body>
<?php endif; ?>
</html>
<?php
        else:
            header("location: ./");
    endif ?>