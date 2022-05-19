<nav>
        <div id="logo">
            <div id="name">
                <span id="first">SHS</span>
                <span id="last">Desk</span>
            </div>
        </div>
        <div id="buttons">
            <a href="<?php echo $url?>/index.php" class="button">
                <span>Home</span>
            </a>
            <a href="<?php echo $url?>/pages/about.php" class="button">
                <span>About</span>
            </a>
            <a href="<?php echo $url?>/pages/school.php" class="button">
                <span>Schools</span>
            </a>
            <?php
            $show = false;
            if($show){
            ?>
            <a href="<?php echo $url?>/pages/faq.php" class="button">
                <span>FAQ</span>
            </a>
            <?php } ?>
            <a href="<?php echo $url?>/pages/contact.php" class="button">
                <span>Contact Us</span>
            </a>
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

