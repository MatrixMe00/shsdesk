<section id="carousel">
    <div class="block">
        <!-- slider images -->
        <div class="img_container img-bright-50">
            <?php 
                $carousels = [
                    "columns" => ["item_img", "image_alt", "item_head", "item_desc", "item_button", "button_text"],
                    "table" => "pageitemdisplays",
                    "where" => ["active=TRUE","item_type='carousel'", "item_page='home'"],
                    "limit" => 0,
                    "where_binds" => "and"
                ];
                $carousels = decimalIndexArray(fetchData(...$carousels));

                if(is_array($carousels)):
                    foreach($carousels as $carousel) :
            ?>
                <img src="<?= "$url/{$carousel['item_img']}" ?>" alt="<?= $carousel["image_alt"] ?>" loading="lazy">
            <?php   endforeach; ?>

            <?php else: ?>
                <img src="<?= "$url/assets/images/default/thought-catalog-xHaZ5BW9AY0-unsplash.jpg" ?>" alt="woman writing" />
            <?php endif; ?>
        </div>

        <!-- slider descriptions -->
        <div class="description">
            <?php
                if(is_array($carousels)):
                    foreach($carousels as $carousel) :
            ?>
            <div class="detail">
                <div class="head">
                    <h1><?= $carousel['item_head']?></h1>
                </div>
                <div class="body">
                    <span class="text txt-fl">
                        <?= html_entity_decode($carousel['item_desc'])?>
                    </span>
                    <?php if(intval($carousel['item_button']) === 1){?>
                    <div class="btn sml-unset spl-unset">
                        <button type="button" class="sp-med"><?= $carousel['button_text']?></button>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php endforeach;
                else: ?>
                <h1 style="font-size: xx-large;">WELCOME TO SHSDESK</h1>
                <div class="body" style="margin-top: 3vh">
                    <span class="text" style="font-size: larger; padding: 2vh 0;">
                        This is your number one web application to make your Senior High School admission easy and safe
                    </span>
                </div>
                ";
            <?php endif; ?>
        </div>
        
        <!-- next and previous buttons -->
        <div class="prev">
            <span>&leftarrow;</span>
        </div>
        <div class="next">
            <span>&bkarow;</span>
        </div>
        
        <!-- slider footer -->
        <div class="footer">
            <div class="slider_counter">
                <span class="active"></span>
            </div>
        </div>
    </div>
</section>