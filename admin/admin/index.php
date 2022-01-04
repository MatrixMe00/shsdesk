<?php 
    //depending on where the page is being called from
    $this_url = $_SERVER["REQUEST_URI"];
    $this_url = explode("/", $this_url);
    $this_url = $this_url[count($this_url) - 2];
    
    if($this_url == "admin"){
        include_once("../includes/session.php");
    }else{
        include_once("../../includes/session.php");
    }

if(isset($_SESSION['user_login_id'])){
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/admin/generalHead.php')?>
    <title>Welcome Admin</title>
</head>

<body ng-app="index_application">
    <?php
        //check if this is a new user
        if(checkNewUser($_SESSION["user_login_id"]) == TRUE){
            require_once($rootPath."/admin/admin/page_parts/update_stat.php");
        }elseif(checkNewUser($_SESSION["user_login_id"]) == "invalid-user"){
            echo "User cannot be found! Please speak to the administrator";
        }else{
    ?>
    <nav>
        <div id="nav_holder">
            <div id="logo">
                <div id="name">
                    <span id="first">ONLINE</span>
                    <span id="last">admission</span>
                </div>
            </div>
            <div id="right_nav">
                <!--The hamburgar-->
                <div id="ham">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div id="user_control">
                    <div id="user_img">
                        <img src="<?php echo $url?>/assets/images/icons/person-circle-outline.svg" alt="">
                    </div>
                    <span id="user_name">
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

                            echo $greet;
                            ?>
                        </span>, <?php echo $user_username ?>
                        <div id="logout">
                            <span>Logout</span>
                        </div>
                    </span>
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <section id="lhs">
            <div class="menu">
                <div class="head active">
                    <span>Dashboard</span>
                </div>
                <div class="item active" name="Dashboard" title="Dashboard" data-url="<?php echo $url?>/admin/admin/page_parts/dashboard.php">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/speedometer-outline.svg" alt="Dashboard" />
                    </div>
                    <div class="menu_name">
                        <span>Dashboard</span>
                    </div>
                </div>
                <div class="item relative" name="Notification" title="Notification" data-url="<?php echo $url?>/admin/admin/page_parts/notification.php">
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
                        $result = $connect->query("SELECT DISTINCT n.*
                            FROM notification n JOIN reply r
                            ON n.ID = r.Comment_ID
                            WHERE n.Read_by NOT LIKE '$user_username'");
                        $total += $result->num_rows;

                        //new replies
                        $result = $connect->query("SELECT DISTINCT n.* 
                            FROM notification n JOIN reply r 
                            ON n.ID = r.Comment_id 
                            WHERE r.Read_by NOT LIKE'$user_username'
                            AND n.Read_by LIKE '$user_username'
                            ORDER BY ID DESC");
                        $total += $result->num_rows;

                        if($total > 0){
                    ?>
                    <div class="news_number absolute danger flex flex-center-align flex-center-content">
                        <span><?php echo $total;?></span>
                    </div>
                    <?php }?>
                </div>
            </div>
            <div class="menu">
                <div class="head">
                    <span>Manage</span>
                </div>
                <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/placement.php" name="Placement" title="Placement List">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/people-outline.svg" alt="Placement" />
                    </div>
                    <div class="menu_name">
                        <span>Placement List</span>
                    </div>
                </div>
                <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/enrol.php" name="Enrol" title="Enrolled Students">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/enter.png" alt="Enrol" />
                    </div>
                    <div class="menu_name">
                        <span>Enrolled Students</span>
                    </div>
                </div>
                <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/houses.php" name="House" title="Houses/Bed Capacity">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/bed-outline.svg" alt="Placement" />
                    </div>
                    <div class="menu_name">
                        <span>Houses/Bed Capacity</span>
                    </div>
                </div>
            </div>
            <div class="menu">
                <div class="head">
                    <span>House Allocation</span>
                </div>
                <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/allocation.php" name="student" title="Student House Allocation">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/home-outline.svg" alt="Home" />
                    </div>
                    <div class="menu_name">
                        <span>Student House Allocation</span>
                    </div>
                </div>
            </div>
            <div class="menu">
                <div class="head active">
                    <span>Documents</span>
                </div>
                <div class="item active" name="Request" title="Request Document" data-url="<?php echo $url?>/admin/admin/page_parts/request.php">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/hand-right-outline.svg" alt="request" />
                    </div>
                    <div class="menu_name">
                        <span>Request Document</span>
                    </div>
                </div>
                <div class="item active" name="Report" title="Make Report" data-url="<?php echo $url?>/admin/admin/page_parts/report.php">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/information-outline.svg" alt="request" />
                    </div>
                    <div class="menu_name">
                        <span>Make a Report</span>
                    </div>
                </div>
            </div>
            <div class="menu">
                <div class="head">
                    <span>Settings</span>
                </div>
                <div class="item" name="account" title="Personal Account" data-url="<?php echo $url?>/admin/admin/page_parts/person.php">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="" />
                    </div>
                    <div class="menu_name">
                        <span>Personal Account</span>
                    </div>
                </div>
                <div class="item" name="password" title="Change Password" data-url="<?php echo $url?>/admin/admin/page_parts/change_password.php">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/key-outline.svg" alt="" />
                    </div>
                    <div class="menu_name">
                        <span>Change Password</span>
                    </div>
                </div>
                <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/admission.php" name="admission" title="Admission Details">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/receipt-outline.svg" alt="" />
                    </div>
                    <div class="menu_name">
                        <span>Admission Details</span>
                    </div>
                </div>
                <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/exeat.php" name="exeat" title="Exeat">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/logout.png" alt="exeat">
                    </div>
                    <div class="menu_name">
                        <span>Exeat</span>
                    </div>
                </div>
            </div>
        </section>
        <section id="rhs">
            <div class="head">
                <h3 id="title">Dashboard</h3>
            </div>
            <div class="body"></div>
        </section>
    </div>

    <div id="modal" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
        <?php @include_once("<?php echo $url?>/admin/admin/page_parts/newStudent.php")?>
    </div>

    <div id="modal_3" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
        <?php include_once("<?php echo $url?>/admin/admin/page_parts/add_house.php")?>
    </div>

    <div id="modal_yes_no" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
        <div class="yes_no_container">
            <div class="body">
                <p id="warning_content">Do you want to delete?</p>
            </div>

            <form action="<?php echo $url?>/admin/admin/submit.php" class="no_disp" name="yes_no_form" id="yes_no_form">
                <input type="hidden" name="sid">
                <input type="hidden" name="mode">
                <input type="hidden" name="table">
                <input type="hidden" name="submit" value="yes_no_submit">
            </form>

            <div class="foot btn flex flex-center-content flex-center-align">
                <button type="button" name="yes_button" class="success" onclick="$('#yes_no_form').submit()">Yes</button>
                <button type="button" name="no_button" class="red" onclick="$('#modal_yes_no').addClass('no_disp')">No</button>
            </div>
        </div>
    </div>

    <script src="<?php echo $url?>/admin/assets/scripts/angular_index.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/index.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/admissionForm.js?v=<?php echo time(); ?>"></script>
    <script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>"></script>
    
    <script>
        $(document).ready(function() {
            nav_point = "<?php
                if(isset($_GET["nav_point"]) && $_GET["nav_point"] != null){
                    echo $_GET["nav_point"];
                }else{
                    echo "Dashboard";
                }
                ?>";
            $("div[name=" + nav_point + "]").click();
        })
        //$("#rhs .body").load("<?php echo $url?>/admin/admin/page_parts/change_password.html");
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