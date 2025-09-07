<?php 
    $navMiddle = [
        "Dashboard" => [
            [
                "item_class"=> "active",
                "name" => "dashboard",
                "title" => "Dashboard",
                "data-url" => "/admin/admin/page_parts/dashboard.php",
                "imgSrc" => "/assets/images/icons/speedometer-outline.svg",
                "imgAlt" => "Dashboard",
                "display_title" => "Dashboard",
            ],
            [
                "name" => "payment",
                "title" => "Payments",
                "data-url" => "/admin/trans.php",
                "imgSrc" => "/assets/images/icons/cash-outline.svg",
                "imgAlt" => "payment",
                "display_title" => "Payments",
            ],
        ]
    ];

    $navFoot = [
        [
            "name" => "admin",
            "title" => "Switch to Admin",
            "data-url" => "/earn/page_parts/admin.php",
            "imgSrc" => "/assets/images/icons/key-outline.svg",
            "imgAlt" => "password",
            "display_title" => "Main Portal",
        ],
        [
            "item_id" => "logout",
            "name" => "",
            "title" => "Logout",
            "data-url" => "",
            "imgSrc" => "/assets/images/icons/logout.png",
            "imgAlt" => "logout",
            "display_title" => "Logout",
        ]
    ];
?>

<div id="middle">
    <?php foreach($navMiddle as $key => $items) : ?>
    <?= "<!-- ".str_replace("_"," ",$key)." -->" ?>
    <div class="menu">
        <?php foreach($items as $item) : ?>
        <div class="item<?= !empty($item["item_class"]) ? " ".$item["item_class"] : ""?>" 
            <?php if(!empty($item["name"])) : ?>
            name="<?= $item["name"] ?>" 
            <?php endif; ?>

            title="<?= $item["title"] ?>" 

            <?php if(!empty($item["data-url"])) : ?>
                data-url="<?= $url.$item["data-url"] ?>"
            <?php elseif(!empty($item["url"])) : ?>
                onclick="location.href='<?= $url.$item['url'] ?>'"
            <?php endif; ?>

            <?= isset($item["item_id"]) ? "id='".$item["item_id"]."'" : "" ?>>
            <div class="icon">
                <img src="<?= $url.$item["imgSrc"] ?>" alt="<?= $item["imgAlt"] ?>">
            </div>
            <div class="menu_name <?= $item["menu_class"] ?? "" ?>">
                <span><?= $item["display_title"] ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<div id="foot">
    <div class="menu">
    <?php foreach($navFoot as $item) : ?>
        <div class="item<?= !empty($item["item_class"]) ? " ".$item["item_class"] : ""?>" 
            <?php if(!empty($item["name"])) : ?>
            name="<?= $item["name"] ?>" 
            <?php endif; ?>

            title="<?= $item["title"] ?>" 

            <?php if(!empty($item["data-url"])) : ?>
                data-url="<?= $url.$item["data-url"] ?>"
            <?php elseif(!empty($item["url"])) : ?>
                onclick="location.href='<?= $url.$item['url'] ?>'"
            <?php endif; ?>

            <?= isset($item["item_id"]) ? "id='".$item["item_id"]."'" : "" ?>>
            <div class="icon">
                <img src="<?= $url.$item["imgSrc"] ?>" alt="<?= $item["imgAlt"] ?>">
            </div>
            <div class="menu_name <?= $item["menu_class"] ?? "" ?>">
                <span><?= $item["display_title"] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>
