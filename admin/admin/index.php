<?php 
    //depending on where the page is being called from
    $this_url = $_SERVER["REQUEST_URI"];

    if(strpos($this_url, "admin/admin/") || strpos($this_url, "admin/superadmin/")){
        include_once("../../includes/session.php");
    }else{
        include_once("../includes/session.php");
    }

if(isset($_SESSION['user_login_id']) && $_SESSION['user_login_id'] > 0){
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/admin/generalHead.php')?>
    <title>Welcome Admin | <?php echo $user_details["username"] ?></title>
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
    <?php 
        //determine if user system is active or not
        $data = getSchoolDetail($user_school_id, true);
        if($data["Active"] == FALSE){
            echo "<nav id='not-active'>
            ";
            $status = "Status: School Disabled";
            $_SESSION["real_status"] = false;
        }else{
            //check if house and students are set
            $house_check = fetchData("COUNT(DISTINCT(title)) AS total", "houses", "schoolID=$user_school_id")["total"];
            if($house_check >= 1){
                //check if there is at least one student uploaded on the system
                $students = fetchData("COUNT(indexNumber) AS total", "cssps", "schoolID=$user_school_id")["total"];
                if($students == 0){
                    echo "<nav id='not-display'>";
                    $status = "Status: Not Active [No Student Uploaded]";
                    $_SESSION["real_status"] = false;
                }else{
                    echo "<nav>";
                    $status = "Status: Active";
                    $_SESSION["real_status"] = true;
                }
            }else{
                echo "<nav id='not-display'>";
                $status = "Status: Not Active [No House Uploaded]";
                $_SESSION["real_status"] = false;
            }
        }
    ?>
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
                <div class="status">
                    <span><?php echo $status ?></span>
                </div>
            </div>
            <div id="middle">
                <!--Dashboard-->
                <div class="menu">
                    <div class="item active" name="Dashboard" title="Dashboard" data-url="<?php echo $url?>/admin/admin/page_parts/dashboard1.php">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/speedometer-outline.svg" alt="Dashboard" />
                        </div>
                        <div class="menu_name">
                            <span>Dashboard</span>
                        </div>
                    </div>
                    <div class="item relative" name="Notification" title="Notification" data-url="<?php echo $url?>/admin/admin/page_parts/notification.php">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/notifications-circle-outline.svg" alt="notification" />
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
                                WHERE (Read_by NOT LIKE '%$user_username%' AND Audience='all')
                                OR (Audience LIKE '%$user_username%' AND Read_by NOT LIKE '%$user_username%')
                                ORDER BY ID DESC");
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
                </div>
                
                <!--Management of students-->
                <div class="menu">
                    <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/cssps.php" name="CSSPS" title="CSSPS List">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/people-outline.svg" alt="cssps" />
                        </div>
                        <div class="menu_name">
                            <span>CSSPS List</span>
                        </div>
                    </div>
                    <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/enrol.php" name="Enrol" title="Enrolment Data">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/enter.png" alt="Enrol" />
                        </div>
                        <div class="menu_name">
                            <span>Enrolment Data</span>
                        </div>
                    </div>
                    <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/houses.php" name="House" title="House and Bed Declarations">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/bed-outline.svg" alt="Placement" />
                        </div>
                        <div class="menu_name">
                            <span>House and Bed Declarations</span>
                        </div>
                    </div>
                </div>
    
                <!--House allocation-->
                <div class="menu">
                    <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/allocation.php" name="student" title="House Allocation">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/home-outline.svg" alt="Home" />
                        </div>
                        <div class="menu_name">
                            <span>House Allocation</span>
                        </div>
                    </div>
                </div>

                <!--Admission and Exeat-->
                <div class="menu">
                    <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/admission.php" name="admission" title="School Details">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/receipt-outline.svg" alt="" />
                        </div>
                        <div class="menu_name">
                            <span>School Details</span>
                        </div>
                    </div>
                    <div class="item" data-url="<?php echo $url?>/admin/admin/page_parts/exeat.php" name="exeat" title="Exeat">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/exit-outline.svg" alt="exeat">
                        </div>
                        <div class="menu_name">
                            <span>Exeat</span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="foot">
                <div class="menu">
                    <div class="item" data-url="<?php echo $url?>/admin/trans.php" name="Payments" title="Payments">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/cash-outline.svg" alt="payment" />
                        </div>
                        <div class="menu_name">
                            <span>Payments</span>
                        </div>
                    </div>
                    <div class="item" name="account" title="Account Update" data-url="<?php echo $url?>/admin/admin/page_parts/person.php">
                        <div class="icon">
                            <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="" />
                        </div>
                        <div class="menu_name">
                            <span>Account Update [Personal]</span>
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
            <h3 id="title"><?php echo getSchoolDetail($user_school_id)["schoolName"] ?> / <span id="head"></span></h3>
        </div>
        <div class="body"></div>
    </section>
    </div>
    <div id="gen_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
        <div class="yes_no_container">
            <div class="body">
                <p id="warning_content">Do you want to delete?</p>
            </div>

            <form action="<?php echo $url?>/admin/submit.php" class="no_disp" name="yes_no_form" id="yes_no_form">
                <input type="hidden" name="sid">
                <input type="hidden" name="mode">
                <input type="hidden" name="table">
                <input type="hidden" name="submit" value="yes_no_submit">
            </form>

            <div class="foot btn flex flex-center-content flex-center-align">
                <button type="button" name="yes_button" class="success" onclick="$('#yes_no_form').submit()">Yes</button>
                <button type="button" name="no_button" class="red" onclick="$('#gen_del').addClass('no_disp')">No</button>
            </div>
        </div>
    </div>

    <script src="<?php echo $url?>/admin/assets/scripts/angular_index.js?v=<?php echo time()?>" async></script>
    <script src="<?php echo $url?>/admin/assets/scripts/index.js?v=<?php echo time()?>" async></script>
    <script src="<?php echo $url?>/assets/scripts/admissionForm.js?v=<?php echo time(); ?>" async></script>
    <script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>" async></script>
    
    <script>
        $(document).ready(function() {
            nav_point = "<?php
                if((isset($_GET["nav_point"]) && $_GET["nav_point"] != null) || (isset($_SESSION["nav_point"]) && !empty($_SESSION["nav_point"]))){
                    if(isset($_GET["nav_point"]))
                        echo $_GET["nav_point"];
                    else
                        echo $_SESSION["nav_point"];
                }else{
                    echo "Dashboard";
                }
                ?>";
            $("div[name=" + nav_point + "]").click();

            <?php 
                if($time < 6){
                    $nav_light = "dark";
                }elseif($time < 18){
                    $nav_light = "light";
                }else{
                    $nav_light = "dark";
                }
            ?>
            $("nav").addClass("<?php echo $nav_light?>");
            <?php if($nav_light == "dark"){?>
            $("nav *").css("color","white");
            <?php } ?>
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
        include_once("../../includes/session.php");
        
        header("location: $url/admin");
    }
?>