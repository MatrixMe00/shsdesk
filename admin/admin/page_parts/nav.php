<?php 
    $admin_mode = $_SESSION["admin_mode"];
    $navMiddle = [
        "Dashboard" => [
            [
                "item_class"=> "active",
                "name" => "dashboard",
                "title" => "Dashboard",
                "data-url" => "/admin/admin/page_parts/dashboard.php",
                "imgSrc" => "/assets/images/icons/speedometer-outline.svg",
                "imgAlt" => "Dashboard",
                "menu_class" => "",
                "display_title" => "Dashboard",
                "admin_mode" => "admission"
            ],
            [
                "item_class"=> "relative",
                "name" => "notification",
                "title" => "Notification",
                "data-url" => "/admin/admin/page_parts/notification.php",
                "imgSrc" => "/assets/images/icons/notifications-circle-outline.svg",
                "imgAlt" => "notification",
                "menu_class" => "relative",
                "display_title" => "Notification",
                "admin_mode" => "admission",
                "item_child" => true
            ],
            [
                "item_class"=> "",
                "name" => "students",
                "title" => "Students List",
                "data-url" => "/admin/admin/page_parts/all_students.php",
                "imgSrc" => "/assets/images/icons/people-outline.svg",
                "imgAlt" => "students",
                "menu_class" => "",
                "display_title" => "Students List",
                "admin_mode" => " records"
            ],
            [
                "item_class"=> "",
                "name" => "subjects",
                "title" => "Subjects & Teachers",
                "data-url" => "/admin/admin/page_parts/subjects.php",
                "imgSrc" => "/assets/images/icons/book-outline.svg",
                "imgAlt" => "subjects n teachers",
                "menu_class" => "",
                "display_title" => "Subjects & Teachers",
                "admin_mode" => "records",
                "data-tab" => "courses"
            ],
            [
                "item_class"=> "",
                "name" => "programs",
                "title" => "Classes & Results",
                "data-url" => "/admin/admin/page_parts/programs.php",
                "imgSrc" => "/assets/images/icons/list-outline.svg",
                "imgAlt" => "classes n results",
                "menu_class" => "",
                "display_title" => "Classes & Results",
                "data-tab" => "allPrograms",
                "admin_mode" => "records",
                "if-condition" => true,
                "condition" => 'fetchData1("COUNT(*) as total","courses","school_id = $user_school_id")["total"] > 0'
            ]
        ],
        "Management_of_students" => [
            [
                "item_class"=> "",
                "name" => "cssps",
                "title" => "CSSPS List",
                "data-url" => "/admin/admin/page_parts/cssps.php",
                "imgSrc" => "/assets/images/icons/people-outline.svg",
                "imgAlt" => "cssps",
                "menu_class" => "",
                "display_title" => "CSSPS List",
                "admin_mode" => "admission"
            ],
            [
                "item_class"=> "",
                "name" => "enrol",
                "title" => "Enrolment Data",
                "data-url" => "/admin/admin/page_parts/enrol.php",
                "imgSrc" => "/assets/images/icons/enter.png",
                "imgAlt" => "enrol data",
                "menu_class" => "",
                "display_title" => "Enrolment Data",
                "admin_mode" => "admission"
            ],
            [
                "item_class"=> "",
                "name" => "house",
                "title" => "House and Bed Declarations",
                "data-url" => "/admin/admin/page_parts/houses.php",
                "imgSrc" => "/assets/images/icons/bed-outline.svg",
                "imgAlt" => "house n bed",
                "menu_class" => "",
                "display_title" => "House & Bed Declarations",
                "admin_mode" => "admission"
            ]
        ],
        "House_allocation" => [
            [
                "item_class"=> "",
                "name" => "allocation",
                "title" => "House Allocation",
                "data-url" => "/admin/admin/page_parts/allocation.php",
                "imgSrc" => "/assets/images/icons/home-outline.svg",
                "imgAlt" => "house allocation",
                "menu_class" => "",
                "display_title" => "House Allocation",
                "admin_mode" => "admission"
            ],
            [
                "item_class"=> "",
                "name" => "announcement",
                "title" => "Announcements",
                "data-url" => "/admin/admin/page_parts/announcement.php",
                "imgSrc" => "/assets/images/icons/notifications-circle-outline.svg",
                "imgAlt" => "announcement",
                "menu_class" => "",
                "display_title" => "Announcements",
                "admin_mode" => "records"
            ],
            [
                "item_class"=> "",
                "name" => "sms",
                "title" => "Messaging",
                "data-url" => "/admin/admin/page_parts/messaging.php",
                "imgSrc" => "/assets/images/icons/megaphone-outline.svg",
                "imgAlt" => "message",
                "menu_class" => "",
                "display_title" => "SMS Messaging",
                "admin_mode" => "records"
            ],
            [
                "item_class"=> "",
                "name" => "access",
                "title" => "Access Code",
                "data-url" => "/admin/admin/page_parts/accessCode.php",
                "imgSrc" => "/assets/images/icons/code-outline.svg",
                "imgAlt" => "accesscode",
                "menu_class" => "",
                "display_title" => "Access Code",
                "admin_mode" => "records"
            ]
        ],
        "Admission_and_Exeat" => [
            [
                "item_class"=> "",
                "name" => "admission",
                "title" => "School Details",
                "data-url" => "/admin/admin/page_parts/admission.php",
                "imgSrc" => "/assets/images/icons/receipt-outline.svg",
                "imgAlt" => "admissions",
                "menu_class" => "",
                "display_title" => "School Details",
                "admin_mode" => "admission"
            ],
            [
                "item_class"=> "",
                "name" => "admission",
                "title" => "School Details",
                "data-url" => "/admin/admin/page_parts/admission.php",
                "imgSrc" => "/assets/images/icons/receipt-outline.svg",
                "imgAlt" => "admissions",
                "menu_class" => "",
                "display_title" => "School Settings",
                "admin_mode" => "records"
            ],
            [
                "item_class"=> "",
                "name" => "exeat",
                "title" => "Exeat",
                "data-url" => "/admin/admin/page_parts/exeat.php",
                "imgSrc" => "/assets/images/icons/exit-outline.svg",
                "imgAlt" => "exeat",
                "menu_class" => "",
                "display_title" => "Exeat",
                "admin_mode" => "records"
            ],
            [
                "item_class"=> "",
                "name" => "documents",
                "title" => "Document Templates",
                "data-url" => "/admin/admin/page_parts/documents.php",
                "imgSrc" => "/assets/images/icons/documents-outline.svg",
                "imgAlt" => "exeat",
                "menu_class" => "",
                "display_title" => "My Document Templates",
                "admin_mode" => "records"
            ]
        ]
    ];

    $navFoot = [
        [
            "item_class"=> "",
            "name" => "payment",
            "title" => "Payments",
            "data-url" => "/admin/trans.php",
            "imgSrc" => "/assets/images/icons/cash-outline.svg",
            "imgAlt" => "payment",
            "menu_class" => "",
            "display_title" => "Payments",
            "admin_mode" => "admission records"
        ],
        [
            "item_class"=> "",
            "name" => "account",
            "title" => "Account Update",
            "data-url" => "/admin/admin/page_parts/person.php",
            "imgSrc" => "/assets/images/icons/person-outline.svg",
            "imgAlt" => "person",
            "menu_class" => "",
            "display_title" => "Account Update [Personal]",
            "admin_mode" => "admission records"
        ],
        [
            "item_class"=> "",
            "name" => "password",
            "title" => "Change Password",
            "data-url" => "/admin/admin/page_parts/change_password.php",
            "imgSrc" => "/assets/images/icons/key-outline.svg",
            "imgAlt" => "password",
            "menu_class" => "",
            "display_title" => "Change Password",
            "admin_mode" => "admission records"
        ],
        [
            "item_class"=> "",
            "item_id" => "logout",
            "name" => "",
            "title" => "Logout",
            "data-url" => "",
            "imgSrc" => "/assets/images/icons/logout.png",
            "imgAlt" => "logout",
            "menu_class" => "",
            "display_title" => "Logout",
            "admin_mode" => "admission records"
        ]
    ];
?>

<div id="middle">
    <?php foreach($navMiddle as $key => $items) : ?>
    <?= "<!-- ".str_replace("_"," ",$key)." -->" ?>
    <div class="menu">
        <?php foreach($items as $item) : 
            if(str_contains($item["admin_mode"], $admin_mode)) :
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
            <div class="menu_name <?= $item["menu_class"] ?>">
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
            <div class="menu_name <?= $item["menu_class"] ?>">
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
            <div class="menu_name <?= $item["menu_class"] ?>">
                <span><?= $item["display_title"] ?></span>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; 
            endforeach; 
        ?>
    </div>
    <?php endforeach; ?>
</div>

<div id="foot">
    <div class="menu">
    <?php foreach($navFoot as $item) : 
        if(str_contains($item["admin_mode"], $admin_mode)) :
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
            <div class="menu_name <?= $item["menu_class"] ?>">
                <span><?= $item["display_title"] ?></span>
            </div>
        </div>
    <?php endif;
        endforeach; ?>
    </div>
</div>

