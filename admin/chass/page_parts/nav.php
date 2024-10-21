<?php 
    // $admin_mode = $_SESSION["admin_mode"];

    $navMiddle = [
        "Dashboard" => [
            [
                "item_class"=> "active",
                "name" => "dashboard",
                "title" => "Dashboard",
                "data-url" => "/admin/chass/page_parts/dashboard.php",
                "imgSrc" => "/assets/images/icons/speedometer-outline.svg",
                "imgAlt" => "Dashboard",
                "display_title" => "Dashboard"
            ]
        ],
    ];

    $navFoot = [
        /*[
            "name" => "payment",
            "title" => "Payments",
            "data-url" => "/admin/trans.php",
            "imgSrc" => "/assets/images/icons/cash-outline.svg",
            "imgAlt" => "payment",
            "display_title" => "Payments",
            "admin_mode" => "admission records"
        ],*/
        /*[
            "name" => "account",
            "title" => "Account Update",
            "data-url" => "/admin/admin/page_parts/person.php",
            "imgSrc" => "/assets/images/icons/person-outline.svg",
            "imgAlt" => "person",
            "display_title" => "Account Update [Personal]",
            // "admin_mode" => "admission records"
        ],*/
        [
            "name" => "password",
            "title" => "Change Password",
            "data-url" => "/admin/chass/page_parts/change_password.php",
            "imgSrc" => "/assets/images/icons/key-outline.svg",
            "imgAlt" => "password",
            "display_title" => "Change Password",
            // "admin_mode" => "admission records"
        ],
        [
            "item_id" => "logout",
            "name" => "",
            "title" => "Logout",
            "data-url" => "",
            "imgSrc" => "/assets/images/icons/logout.png",
            "imgAlt" => "logout",
            "display_title" => "Logout",
            // "admin_mode" => "admission records"
        ]
    ];
?>

<div id="middle">
    <?php foreach($navMiddle as $key => $items) : ?>
    <?= "<!-- ".str_replace("_"," ",$key)." -->" ?>
    <div class="menu">
        <?php foreach($items as $item) : 
            if(isset($item["item_child"]) && $item["item_child"] === true) :
        ?>
        <div class="item<?= !empty($item["item_class"]) ? " ".$item["item_class"] : ""?>" 
            <?php if(!empty($item["name"])) : ?>
            name="<?= $item["name"] ?>" 
            <?php endif; ?>

            title="<?= $item["title"] ?>" 

            <?php if(!empty($item["data-url"])) : ?>
            data-url="<?= $url.$item["data-url"] ?>"
            <?php endif; ?>

            <?= isset($item["item_id"]) ? "id='".$item["item_id"]."'" : "" ?>>
            <div class="icon">
                <img src="<?= $url.$item["imgSrc"] ?>" alt="<?= $item["imgAlt"] ?>">
            </div>
            <div class="menu_name <?= $item["menu_class"] ?? "" ?>">
                <span><?= $item["display_title"] ?></span>
            </div>
        </div>
        <?php 
            switch($item["name"]){
                case "notification":
                    //count notifications
                    $response = 0;

                    //unread notifications
                    $result = $connect->query("SELECT *
                        FROM notification
                        WHERE (Read_by NOT LIKE '%$user_username%' AND Audience='all')
                        OR (Audience LIKE '%$user_username%' AND Read_by NOT LIKE '%$user_username%')
                        AND '{$user_details['adYear']}' <= DATE
                        ORDER BY ID DESC");
                    $response += $result->num_rows;

                    //new replies
                    $result = $connect->query("SELECT DISTINCT n.* 
                        FROM notification n JOIN reply r 
                        ON n.ID = r.Comment_id 
                        WHERE r.Read_by NOT LIKE '%$user_username%'
                        AND n.Read_by LIKE '%$user_username%'
                        ORDER BY ID DESC");
                    $response += $result->num_rows;
                    break;
            }
            if((intval($response) && $response > 0) || (!is_null($response) && !empty($response))) :
        ?>
        <div class="news_number absolte danger flex-all-center">
            <span><?= $response ?></span>
        </div>
        <?php endif; ?>

        <?php elseif((isset($item["if-condition"]) && $item["if-condition"] === true) && $item["condition"]) : ?>
        <div class="item<?= !empty($item["item_class"]) ? " ".$item["item_class"] : ""?>" 
            <?php if(!empty($item["name"])) : ?>
            name="<?= $item["name"] ?>" 
            <?php endif; ?>

            title="<?= $item["title"] ?>" 

            <?php if(!empty($item["data-url"])) : ?>
            data-url="<?= $url.$item["data-url"] ?>"
            <?php endif; ?>

            <?= isset($item["item_id"]) ? "id='".$item["item_id"]."'" : "" ?>>
            <div class="icon">
                <img src="<?= $url.$item["imgSrc"] ?>" alt="<?= $item["imgAlt"] ?>">
            </div>
            <div class="menu_name <?= $item["menu_class"] ?? "" ?>">
                <span><?= $item["display_title"] ?></span>
            </div>
        </div>

        <?php else : ?>
        <div class="item<?= !empty($item["item_class"]) ? " ".$item["item_class"] : ""?>" 
            <?php if(!empty($item["name"])) : ?>
            name="<?= $item["name"] ?>" 
            <?php endif; ?>

            title="<?= $item["title"] ?>" 

            <?php if(!empty($item["data-url"])) : ?>
            data-url="<?= $url.$item["data-url"] ?>"
            <?php endif; ?>

            <?= isset($item["item_id"]) ? "id='".$item["item_id"]."'" : "" ?>>
            <div class="icon">
                <img src="<?= $url.$item["imgSrc"] ?>" alt="<?= $item["imgAlt"] ?>">
            </div>
            <div class="menu_name <?= $item["menu_class"] ?? "" ?>">
                <span><?= $item["display_title"] ?></span>
            </div>
        </div>
        <?php endif; endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<div id="foot">
    <div class="menu">
    <?php foreach($navFoot as $item) :
    ?>
        <div class="item<?= !empty($item["item_class"]) ? " ".$item["item_class"] : ""?>" 
            <?php if(!empty($item["name"])) : ?>
            name="<?= $item["name"] ?>" 
            <?php endif; ?>

            title="<?= $item["title"] ?>" 

            <?php if(!empty($item["data-url"])) : ?>
            data-url="<?= $url.$item["data-url"] ?>"
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