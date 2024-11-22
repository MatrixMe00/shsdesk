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
                "display_title" => "Dashboard",
                "admin_mode" => "admission"
            ],
            [
                "item_class"=> "relative",
                "name" => "issues",
                "title" => "Admission Issues",
                "data-url" => "/admin/admin/page_parts/issues.php",
                "imgSrc" => "/assets/images/icons/person-outline.svg",
                "imgAlt" => "issues",
                "display_title" => "Admission Issues <span class='absolute right txt-bold sm-med-r rounded hmax-fit' id='issues_element'></span>",
                "admin_mode" => "admission"
            ],
            [
                "item_class"=> "relative",
                "name" => "transfers",
                "title" => "Student Transfers",
                "data-url" => "/admin/admin/page_parts/transfers.php",
                "imgSrc" => "/assets/images/icons/person-outline.svg",
                "imgAlt" => "transfers",
                "display_title" => "Student Transfers <span class='absolute right txt-bold sm-med-r rounded hmax-fit' id='transfers_element'></span>",
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
                "display_title" => "Notification <span class='absolute right txt-bold sm-med-r rounded hmax-fit' id='notification_element'></span>",
                "admin_mode" => "admission",
                "item_child" => true
            ],
            [

                "name" => "students",
                "title" => "Students List",
                "data-url" => "/admin/admin/page_parts/all_students.php",
                "imgSrc" => "/assets/images/icons/people-outline.svg",
                "imgAlt" => "students",
                "display_title" => "Students List",
                "admin_mode" => " records"
            ],
            [

                "name" => "subjects",
                "title" => "Subjects & Teachers",
                "data-url" => "/admin/admin/page_parts/subjects.php",
                "imgSrc" => "/assets/images/icons/book-outline.svg",
                "imgAlt" => "subjects n teachers",
                "display_title" => "Subjects & Teachers",
                "admin_mode" => "records",
                "data-tab" => "courses"
            ],
            [

                "name" => "programs",
                "title" => "Classes & Results",
                "data-url" => "/admin/admin/page_parts/programs.php",
                "imgSrc" => "/assets/images/icons/list-outline.svg",
                "imgAlt" => "classes n results",
                "display_title" => "Classes & Results",
                "data-tab" => "allPrograms",
                "admin_mode" => "records",
                "if-condition" => true,
                "condition" => 'fetchData1("COUNT(*) as total","courses","school_id = $user_school_id")["total"] > 0'
            ]
        ],
        "Management_of_students" => [
            [

                "name" => "cssps",
                "title" => "CSSPS List",
                "data-url" => "/admin/admin/page_parts/cssps.php",
                "imgSrc" => "/assets/images/icons/people-outline.svg",
                "imgAlt" => "cssps",
                "display_title" => "CSSPS List",
                "admin_mode" => "admission"
            ],
            [

                "name" => "enrol",
                "title" => "Enrolment Data",
                "data-url" => "/admin/admin/page_parts/enrol.php",
                "imgSrc" => "/assets/images/icons/enter.png",
                "imgAlt" => "enrol data",
                "display_title" => "Enrolment Data",
                "admin_mode" => "admission"
            ],
            [
                "item_class"=> "relative",
                "name" => "house",
                "title" => "House and Bed Declarations",
                "data-url" => "/admin/admin/page_parts/houses.php",
                "imgSrc" => "/assets/images/icons/bed-outline.svg",
                "imgAlt" => "house n bed",
                "display_title" => "House & Bed Declarations <span class='absolute right color-red txt-bold sm-med-r rounded hmax-fit' id='displaced_element'></span>",
                "admin_mode" => "admission"
            ]
        ],
        "House_allocation" => [
            [

                "name" => "allocation",
                "title" => "House Allocation",
                "data-url" => "/admin/admin/page_parts/allocation.php",
                "imgSrc" => "/assets/images/icons/home-outline.svg",
                "imgAlt" => "house allocation",
                "display_title" => "House Allocation",
                "admin_mode" => "admission"
            ],
            [

                "name" => "announcement",
                "title" => "Announcements",
                "data-url" => "/admin/admin/page_parts/announcement.php",
                "imgSrc" => "/assets/images/icons/notifications-circle-outline.svg",
                "imgAlt" => "announcement",
                "display_title" => "Announcements",
                "admin_mode" => "records"
            ],
            [

                "name" => "sms",
                "title" => "Messaging",
                "data-url" => "/admin/admin/page_parts/messaging.php",
                "imgSrc" => "/assets/images/icons/megaphone-outline.svg",
                "imgAlt" => "message",
                "display_title" => "SMS Messaging",
                "admin_mode" => "records"
            ],
            [

                "name" => "access",
                "title" => "Access Code",
                "data-url" => "/admin/admin/page_parts/accessCode.php",
                "imgSrc" => "/assets/images/icons/code-outline.svg",
                "imgAlt" => "accesscode",
                "display_title" => "Access Code",
                "admin_mode" => "records"
            ]
        ],
        "Admission_and_Exeat" => [
            [

                "name" => "admission",
                "title" => "School Details",
                "data-url" => "/admin/admin/page_parts/admission.php",
                "imgSrc" => "/assets/images/icons/receipt-outline.svg",
                "imgAlt" => "admissions",
                "display_title" => "School Details",
                "admin_mode" => "admission"
            ],
            [

                "name" => "admission-settings",
                "title" => "History and Settings",
                "data-url" => "/admin/admin/page_parts/admission-settings.php",
                "imgSrc" => "/assets/images/icons/folder-outline.svg",
                "imgAlt" => "admission settings",
                "menu_class" => "admission_settings",
                "display_title" => "History and Settings",
                "admin_mode" => "admission"
            ],
            [

                "name" => "admission",
                "title" => "School Details",
                "data-url" => "/admin/admin/page_parts/admission.php",
                "imgSrc" => "/assets/images/icons/receipt-outline.svg",
                "imgAlt" => "admissions",
                "display_title" => "School Settings",
                "admin_mode" => "records"
            ],
            [

                "name" => "exeat",
                "title" => "Exeat",
                "data-url" => "/admin/admin/page_parts/exeat.php",
                "imgSrc" => "/assets/images/icons/exit-outline.svg",
                "imgAlt" => "exeat",
                "display_title" => "Exeat",
                "admin_mode" => "records"
            ],
            [

                "name" => "documents",
                "title" => "Document Templates",
                "data-url" => "/admin/admin/page_parts/documents.php",
                "imgSrc" => "/assets/images/icons/documents-outline.svg",
                "imgAlt" => "documents",
                "display_title" => "My Document Templates",
                "admin_mode" => "records"
            ],
            [

                "name" => "reports",
                "title" => "My Reports",
                "data-url" => "/admin/admin/page_parts/reports.php",
                "imgSrc" => "/assets/images/icons/documents-outline.svg",
                "imgAlt" => "report",
                "display_title" => "My Reports",
                "admin_mode" => "records"
            ]
        ]
    ];

    $navFoot = [
        [
            "name" => "payment",
            "title" => "Payments",
            "data-url" => "/admin/trans.php",
            "imgSrc" => "/assets/images/icons/cash-outline.svg",
            "imgAlt" => "payment",
            "display_title" => "Payments",
            "admin_mode" => "admission records"
        ],
        [
            "name" => "account",
            "title" => "Account Update",
            "data-url" => "/admin/admin/page_parts/person.php",
            "imgSrc" => "/assets/images/icons/person-outline.svg",
            "imgAlt" => "person",
            "display_title" => "Account Update [Personal]",
            "admin_mode" => "admission records"
        ],
        [
            "name" => "password",
            "title" => "Change Password",
            "data-url" => "/admin/admin/page_parts/change_password.php",
            "imgSrc" => "/assets/images/icons/key-outline.svg",
            "imgAlt" => "password",
            "display_title" => "Change Password",
            "admin_mode" => "admission records"
        ],
        [
            "item_id" => "logout",
            "name" => "",
            "title" => "Logout",
            "data-url" => "",
            "imgSrc" => "/assets/images/icons/logout.png",
            "imgAlt" => "logout",
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
            <div class="menu_name <?= $item["menu_class"] ?? "" ?>">
                <span><?= $item["display_title"] ?></span>
            </div>
        </div>

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
            <div class="menu_name <?= $item["menu_class"] ?? "" ?>">
                <span><?= $item["display_title"] ?></span>
            </div>
        </div>
    <?php endif;
        endforeach; ?>
    </div>
</div>