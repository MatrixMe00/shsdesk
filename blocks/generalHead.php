    <!--General Meta data-->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--General Scripts-->
    <script src="<?php echo $url?>/assets/scripts/jquery/compressed_jquery.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/functions.min.js?v=<?php echo time()?>"></script>

    <!--General Stylesheets-->
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/head_foot.min.css?v=<?php echo time()?>">
    <?php if(isset($gen1) && $gen1 == true){?>
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/general1.min.css?v=<?php echo time()?>">
    <?php }else{ ?>
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/general.min.css?v=<?php echo time()?>">
    <?php }?>
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/admin/admin_form.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="<?php echo $url?>/assets/styles/loader.min.css?v=<?php echo time()?>">