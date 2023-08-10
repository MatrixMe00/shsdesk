<nav class="sp-xlg-tp <?= isset($start_sticky) && $start_sticky ? "sticky":"absolute no-sticky" ?> top">
        <div id="logo">
            <div id="name">
                <span id="first">SHS</span>
                <span id="last">Desk</span>
            </div>
        </div>
        <?php 
            $links = array();
            $ur = $_SERVER["REQUEST_URI"];

            if(strpos($ur, "shsdesk")){
                $links = array(
                    [ "url" => "/", "name" => "Home"],
                    [ "url" => "/pages/about.php", "name" => "About"],
                    [ "url" => "/pages/school.php", "name" => "Schools"],
                    // [ "url" => "/pages/faq.php", "name" => "FAQ"],
                    [ "url" => "/pages/contact.php", "name" => "Contact"]
                );
            }else{
                $links = array(
                    [ "url" => "/", "name" => "Home"],
                    [ "url" => "/about", "name" => "About"],
                    [ "url" => "/school", "name" => "Schools"],
                    // [ "url" => "/faq", "name" => "FAQ"],
                    [ "url" => "/contact", "name" => "Contact"]
                );
            }
            
        ?>
        <div id="buttons">
            <?php foreach($links as $link) : ?>
            <a href="<?php echo $url.$link["url"]?>" class="button">
                <span><?= $link["name"] ?></span>
            </a>
            <?php endforeach; ?>
            <a href="<?php echo $url?>/student/" class="button">
                <span>Student Menu</span>
            </a>
        </div>
        <div id="ham_button">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <?php $show = false ?>

