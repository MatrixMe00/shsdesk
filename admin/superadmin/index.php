<?php
    //depending on where the page is being called from
    $this_url = $_SERVER["REQUEST_URI"];

    if($this_url == "/admin/superadmin/"){
        header("Location: ./admin");
    }
?>

<?php if(isset($_SESSION['user_login_id']) && $_SESSION['user_login_id'] > 0){?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once($rootPath.'/admin/generalHead.php')?>
    <title>Welcome SuperAdmin | <?php echo $user_details["username"] ?></title>
</head>

<body>
<div id="container" class="flex">
    <?php
        //check if this is a new user
        if(checkNewUser($_SESSION["user_login_id"]) == TRUE){
            require_once($rootPath."/admin/admin/page_parts/update_stat.php");
        }elseif(checkNewUser($_SESSION["user_login_id"]) == "invalid-user"){
            echo "User cannot be found! Please speak to the administrator";
        }else{
    ?>
    <nav>
    <div id="ham" class="">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div id="lhs">
            <div id="top" class="flex flex-column flex-center-align flex-center-content">
                <div class="img">
                    <img src="<?php echo $url?>/assets/images/icons/person-circle-outline.svg" alt="user logo">
                </div>
                <div class="name">
                    <span id="greeting">
                    <?php
                        $time = date("H");

                        if($time < 12){
                            $greet = "Good Morning";
                        }elseif($time < 16){
                            $greet = "Good Afternoon";
                        }else{
                            $greet = "Good Evening";
                        }

                        echo "$greet <strong>".strtoupper($user_details["fullname"])."</strong>";
                    ?>
                    </span>
                </div>
            </div>
            <div id="middle">
                <!--Dashboard-->
                <div class="menu">
                    <div class="item active" name="Dashboard" title="Dashboard" data-url="<?php echo $url?>/admin/superadmin/page_parts/dashboard.php">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/speedometer-outline.svg" alt="Dashboard" />
                        </div>
                        <div class="menu_name">
                            <span>Dashboard</span>
                        </div>
                    </div>
                    <div class="item relative" name="Notification" title="Notification" data-url="<?php echo $url?>/admin/superadmin/page_parts/notification.php">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/notifications-circle-outline.svg" alt="Dashboard" />
                        </div>
                        <div class="menu_name relative">
                            <span>Notification</span>
                        </div>
                        <?php 
                            //count notifications
                            $total = 0;

                            //unread notifications
                            $result = $connect->query("SELECT *
                                FROM notification
                                WHERE Read_by NOT LIKE '%$user_username%'
                                OR (Audience LIKE '%$user_username%' AND Read_by NOT LIKE '%$user_username%')
                                AND '{$user_details['adYear']}' <= DATE");
                            $total += $result->num_rows;

                            //new replies
                            $result = $connect->query("SELECT DISTINCT n.* 
                                FROM notification n JOIN reply r 
                                ON n.ID = r.Comment_id 
                                WHERE r.Read_by NOT LIKE '%$user_username%'
                                AND n.Read_by LIKE '%$user_username%'
                                ORDER BY ID DESC");
                            $total += $result->num_rows;

                            if($total > 0){
                        ?>
                        <div class="news_number absolute danger flex flex-center-align flex-center-content">
                            <span><?php echo $total;?></span>
                        </div>
                        <?php }?>
                    </div>
                    <?php if($role_id <= 2) : ?>
                    <div class="item" name="ussd" title="USSD" data-url="<?php echo $url?>/admin/superadmin/page_parts/ussd.php" data-current-tab="pending">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/chatbox-outline.svg" alt="ussd" />
                        </div>
                        <div class="menu_name">
                            <span>USSD Management</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="item" name="broadcast" title="Messaging" data-url="<?php echo $url?>/admin/superadmin/page_parts/broadcast.php">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/megaphone-outline.svg" alt="broadcast" />
                        </div>
                        <div class="menu_name">
                            <span>Messaging</span>
                        </div>
                    </div>
                </div>
    
                <!--Management-->
                <div class="menu">
                    <div class="item" data-url="<?php echo $url?>/admin/superadmin/page_parts/schools.php" name="Schools" title="Schools List">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/city hall.png" alt="Schools" />
                        </div>
                        <div class="menu_name">
                            <span>Schools List</span>
                        </div>
                    </div>
                    <div class="item" data-url="<?php echo $url?>/admin/superadmin/page_parts/students.php" name="students" title="Student Management">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/student.svg" alt="students" style="filter:grayscale(1)" />
                        </div>
                        <div class="menu_name">
                            <span>Student Management</span>
                        </div>
                    </div>
                    <div class="item" data-url="<?php echo $url?>/admin/superadmin/page_parts/transaction.php" name="Transactions" title="Track Transactions">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/receipt-outline.svg" alt="transaction" />
                        </div>
                        <div class="menu_name">
                            <span>Track Transactions</span>
                        </div>
                    </div>
                    <?php if($admin_access > 3) : ?>
                    <div class="item" data-url="<?php echo $url?>/admin/trans.php" name="payment" title="Payments">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/cash-outline.svg" alt="payment" />
                        </div>
                        <div class="menu_name">
                            <span>Payments</span>
                        </div>
                    </div>
                    <div class="item" data-url="<?php echo $url?>/admin/superadmin/page_parts/trans_split.php" name="trans_splits" title="Transaction Splits">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/receipt-outline.svg" alt="transaction split" />
                        </div>
                        <div class="menu_name">
                            <span>Transaction Splits</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
    
                <!--Page Setups-->
                <?php if($admin_access > 3) : ?>
                <div class="menu">
                    <div class="item" data-url="<?php echo $url?>/admin/superadmin/page_parts/home.php" name="Index" title="Home Page">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/home.png" alt="Home" />
                        </div>
                        <div class="menu_name">
                            <span>Home Page</span>
                        </div>
                    </div>
                    <div class="item" data-url="<?php echo $url?>/admin/superadmin/page_parts/about.php" name="about" title="About Page">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/receipt-outline.svg" alt="about" />
                        </div>
                        <div class="menu_name">
                            <span>About Page</span>
                        </div>
                    </div>
                    <div class="item" data-url="<?php echo $url?>/admin/superadmin/page_parts/faq.php" name="faq" title="Frequently Asked Questions">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/information-circle-outline.svg" alt="faq" />
                        </div>
                        <div class="menu_name">
                            <span>Frequently Asked Questions</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div id="foot">
                <div class="menu">
                    <div class="item" name="account" title="Personal Account" data-url="<?php echo $url?>/admin/superadmin/page_parts/person.php">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="" />
                        </div>
                        <div class="menu_name">
                            <span>Personal Account</span>
                        </div>
                    </div>
                    <div class="item" name="password" title="Change Password" data-url="<?php echo $url?>/admin/superadmin/page_parts/change_password.php">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/key-outline.svg" alt="" />
                        </div>
                        <div class="menu_name">
                            <span>Change Password</span>
                        </div>
                    </div>
                    <?php if($user_details["role"] == 1){?>
                    <div class="item" name="database" title="Query Database" data-url="<?php echo $url?>/admin/superadmin/page_parts/database.php">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/server-outline.svg" alt="" />
                        </div>
                        <div class="menu_name">
                            <span>Database Queries</span>
                        </div>
                    </div>
                    <?php }?>
                    <div class="item" id="logout" title="Logout">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/logout.png" alt="" />
                        </div>
                        <div class="menu_name">
                            <span>Logout</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <section id="rhs">
        <div class="head">
        <h3 id="title">SHSDesk / <span id="head"></span></h3>
        </div>
        <div class="body"></div>
    </section>
</div>

    <div id="gen_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
        <div class="yes_no_container txt-al-c white sp-xlg-lr sp-xxlg-tp sm-rnd wmax-sm wmin-unset w-full sm-auto">
            <div class="body">
                <p id="warning_content">Do you want to delete?</p>
            </div>

            <form action="<?php echo $url?>/admin/submit.php" class="no_disp" name="yes_no_form" id="yes_no_form">
                <input type="hidden" name="sid">
                <input type="hidden" name="mode">
                <input type="hidden" name="table">
                <input type="hidden" name="key_column">
                <input type="hidden" name="db">
                <input type="hidden" name="submit" value="yes_no_submit">
            </form>

            <div class="foot btn p-lg flex-all-center w-full flex-eq sm-xlg-t gap-md">
                <button type="button" name="yes_button" class="plain-r green" onclick="$('#yes_no_form').submit()">Yes</button>
                <button type="button" name="no_button" class="plain-r red" onclick="$('#gen_del').addClass('no_disp')">No</button>
            </div>
        </div>
    </div>

    <script src="<?php echo $url?>/assets/scripts/admissionForm.min.js?v=<?php echo time(); ?>"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/index.min.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>"></script>

    <script>
        $(document).ready(function() {
            const nav_point = "<?= get_nav_point() ?>";
            const nav_mode = "<?= get_nav_mode() ?>";
            $("div[name=" + nav_point + "]").click();

            $("nav").addClass(nav_mode);
        })
    </script>
    <?php }
        
        //close connection
        $connect->close();
    ?>
</body>
</html>
<?php 
        
    }else{
    header("location: $url/admin");
}
?>