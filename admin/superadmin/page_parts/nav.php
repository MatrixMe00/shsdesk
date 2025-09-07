<?php
$user_role = $user_details["role"] ?? 0;

$navMiddle = [
    "Dashboard" => [
        [
            "item_class" => "active",
            "name" => "Dashboard",
            "title" => "Dashboard",
            "data-url" => "/admin/superadmin/page_parts/dashboard.php",
            "imgSrc" => "/assets/images/icons/speedometer-outline.svg",
            "imgAlt" => "Dashboard",
            "display_title" => "Dashboard"
        ],
        [
            "item_class" => "relative",
            "name" => "Notification",
            "title" => "Notification",
            "data-url" => "/admin/superadmin/page_parts/notification.php",
            "imgSrc" => "/assets/images/icons/notifications-circle-outline.svg",
            "imgAlt" => "Notification",
            "menu_class" => "relative",
            "display_title" => "Notification <span class='news_number absolute danger flex-all-center' id='notification_element'></span>"
        ],
        [
            "name" => "ussd",
            "title" => "USSD Management",
            "data-url" => "/admin/superadmin/page_parts/ussd.php",
            "imgSrc" => "/assets/images/icons/chatbox-outline.svg",
            "imgAlt" => "ussd",
            "display_title" => "USSD Management",
            "if-condition" => true,
            "condition" => $role_id <= 2
        ],
        [
            "name" => "broadcast",
            "title" => "Messaging",
            "data-url" => "/admin/superadmin/page_parts/broadcast.php",
            "imgSrc" => "/assets/images/icons/megaphone-outline.svg",
            "imgAlt" => "broadcast",
            "display_title" => "Messaging"
        ]
    ],
    "Management" => [
        [
            "name" => "Schools",
            "title" => "Schools List",
            "data-url" => "/admin/superadmin/page_parts/schools.php",
            "imgSrc" => "/assets/images/icons/city hall.png",
            "imgAlt" => "Schools",
            "display_title" => "Schools List"
        ],
        [
            "name" => "students",
            "title" => "Student Management",
            "data-url" => "/admin/superadmin/page_parts/students.php",
            "imgSrc" => "/assets/images/icons/student.svg",
            "imgAlt" => "students",
            "display_title" => "Student Management"
        ],
        [
            "name" => "create_access",
            "title" => "Access Code",
            "data-url" => "/admin/superadmin/page_parts/access_code.php",
            "imgSrc" => "/assets/images/icons/code-outline.svg",
            "imgAlt" => "access code",
            "display_title" => "Access Code"
        ],
        [
            "name" => "Transactions",
            "title" => "Track Transactions",
            "data-url" => "/admin/superadmin/page_parts/transaction.php",
            "imgSrc" => "/assets/images/icons/receipt-outline.svg",
            "imgAlt" => "transaction",
            "display_title" => "Track Transactions"
        ],
        [
            "name" => "payment",
            "title" => "Payments",
            "data-url" => "/admin/trans.php",
            "imgSrc" => "/assets/images/icons/cash-outline.svg",
            "imgAlt" => "payment",
            "display_title" => "Payments",
            "if-condition" => true,
            "condition" => $admin_access > 3
        ],
        [
            "name" => "trans_splits",
            "title" => "Transaction Splits",
            "data-url" => "/admin/superadmin/page_parts/trans_split.php",
            "imgSrc" => "/assets/images/icons/receipt-outline.svg",
            "imgAlt" => "transaction split",
            "display_title" => "Transaction Splits",
            "if-condition" => true,
            "condition" => $admin_access > 3
        ]
    ],
    "Page Setups" => [
        [
            "name" => "Index",
            "title" => "Home Page",
            "data-url" => "/admin/superadmin/page_parts/home.php",
            "imgSrc" => "/assets/images/icons/home.png",
            "imgAlt" => "Home",
            "display_title" => "Home Page",
            "if-condition" => true,
            "condition" => $admin_access > 3
        ],
        [
            "name" => "about",
            "title" => "About Page",
            "data-url" => "/admin/superadmin/page_parts/about.php",
            "imgSrc" => "/assets/images/icons/receipt-outline.svg",
            "imgAlt" => "about",
            "display_title" => "About Page",
            "if-condition" => true,
            "condition" => $admin_access > 3
        ],
        [
            "name" => "faq",
            "title" => "Frequently Asked Questions",
            "data-url" => "/admin/superadmin/page_parts/faq.php",
            "imgSrc" => "/assets/images/icons/information-circle-outline.svg",
            "imgAlt" => "faq",
            "display_title" => "Frequently Asked Questions",
            "if-condition" => true,
            "condition" => $admin_access > 3
        ]
    ]
];

$navFoot = [
    [
        "name" => "account",
        "title" => "Personal Account",
        "data-url" => "/admin/superadmin/page_parts/person.php",
        "imgSrc" => "/assets/images/icons/person-outline.svg",
        "imgAlt" => "account",
        "display_title" => "Personal Account"
    ],
    [
        "name" => "user_roles",
        "title" => "User Roles",
        "data-url" => "/admin/superadmin/page_parts/roles.php",
        "imgSrc" => "/assets/images/icons/key-outline.svg",
        "imgAlt" => "roles",
        "display_title" => "User Roles"
    ],
    [
        "name" => "password",
        "title" => "Change Password",
        "data-url" => "/admin/superadmin/page_parts/change_password.php",
        "imgSrc" => "/assets/images/icons/key-outline.svg",
        "imgAlt" => "password",
        "display_title" => "Change Password"
    ],
    [
        "name" => "database",
        "title" => "Database Queries",
        "data-url" => "/admin/superadmin/page_parts/database.php",
        "imgSrc" => "/assets/images/icons/server-outline.svg",
        "imgAlt" => "database",
        "display_title" => "Database Queries",
        "if-condition" => true,
        "condition" => $user_role == 1
    ],
    [
        "item_id" => "logout",
        "title" => "Logout",
        "imgSrc" => "/assets/images/icons/logout.png",
        "imgAlt" => "logout",
        "display_title" => "Logout"
    ]
];
?>

<!-- MIDDLE MENU -->
<div id="middle">
    <?php foreach ($navMiddle as $section => $items): ?>
        <?= "<!-- " . htmlspecialchars($section) . " -->" ?>
        <div class="menu">
            <?php foreach ($items as $item): 
                $condition = $item["if-condition"] ?? false ? $item["condition"] : true;
                if ($condition): ?>
                <div class="item<?= !empty($item["item_class"]) ? " {$item["item_class"]}" : "" ?>"
                    <?= !empty($item["name"]) ? "name='{$item["name"]}'" : "" ?>
                    <?= !empty($item["title"]) ? "title='{$item["title"]}'" : "" ?>
                    <?= !empty($item["data-url"]) ? "data-url='{$url}{$item["data-url"]}'" : "" ?>
                    <?= isset($item["item_id"]) ? "id='{$item["item_id"]}'" : "" ?>>
                    <div class="icon">
                        <img src="<?= $url . $item["imgSrc"] ?>" alt="<?= $item["imgAlt"] ?>" />
                    </div>
                    <div class="menu_name <?= $item["menu_class"] ?? "" ?>">
                        <span><?= $item["display_title"] ?></span>
                    </div>
                </div>
            <?php endif; endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- FOOTER MENU -->
<div id="foot">
    <div class="menu">
        <?php foreach ($navFoot as $item): 
            $condition = $item["if-condition"] ?? false ? $item["condition"] : true;
            if ($condition): ?>
            <div class="item<?= !empty($item["item_class"]) ? " {$item["item_class"]}" : "" ?>"
                <?= !empty($item["name"]) ? "name='{$item["name"]}'" : "" ?>
                <?= !empty($item["title"]) ? "title='{$item["title"]}'" : "" ?>
                <?= !empty($item["data-url"]) ? "data-url='{$url}{$item["data-url"]}'" : "" ?>
                <?= isset($item["item_id"]) ? "id='{$item["item_id"]}'" : "" ?>>
                <div class="icon">
                    <img src="<?= $url . $item["imgSrc"] ?>" alt="<?= $item["imgAlt"] ?>" />
                </div>
                <div class="menu_name <?= $item["menu_class"] ?? "" ?>">
                    <span><?= $item["display_title"] ?></span>
                </div>
            </div>
        <?php endif; endforeach; ?>
    </div>
</div>

