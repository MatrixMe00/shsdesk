<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- favicon -->
    <?php 
        if(!empty($admin_access) && $admin_access < 3){
            $logo = fetchData("logoPath","schools","id=$user_school_id")["logoPath"] ?? "";
        }

        if(empty($logo) || !is_file("$rootPath/$logo")){
            $logo = "assets/favicon.png";
        }
    ?>
    <link rel="shortcut icon" href="<?= "$url/$logo" ?>" type="image/x-icon">

    <!--Scripts-->
    <script src="<?php echo $url?>/assets/scripts/jquery/uncompressed_jquery.js"></script>
    <script src="<?php echo $url?>/assets/scripts/functions.min.js?v=<?php echo time()?>"></script>

    <!--Other Styles-->
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/general.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/admin/admin_form.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/loader.min.css?v=<?php echo time()?>">

    <!--Document Style-->
    <link rel="stylesheet" href="<?php echo $url?>/admin/assets/styles/general.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/admin_index_page.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/admin/assets/styles/index.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/admin/assets/styles/notification.min.css?v=<?php echo time()?>">

    <!--Tiny MCE-->
    <link rel="stylesheet" href="<?php echo $url?>/admin/assets/styles/tinymce.min.css?v=<?php echo time()?>">