<?php include_once("../../includes/session.php")?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/admin/generalHead.php')?>
    <title>Welcome SuperAdmin</title>
</head>

<body ng-app="index_application">
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
                        </span>, Admin
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
                <div class="item active" name="Dashboard" title="Dashboard" data-url="page_parts/dashboard.php">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/speedometer-outline.svg" alt="Dashboard" />
                    </div>
                    <div class="menu_name">
                        <span>Dashboard</span>
                    </div>
                </div>
            </div>
            <div class="menu">
                <div class="head">
                    <span>Manage</span>
                </div>
                <div class="item" data-url="page_parts/schools.php" name="Schools" title="Schools List">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/city hall.png" alt="Schools" />
                    </div>
                    <div class="menu_name">
                        <span>Schools List</span>
                    </div>
                </div>
                <!-- <div class="item" data-url="page_parts/enrol.php" name="Enrol" title="Enrolled Students">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/enter.png" alt="Enrol" />
                    </div>
                    <div class="menu_name">
                        <span>Enrolled Students</span>
                    </div>
                </div>
                 -->
            </div>
            <div class="menu">
                <div class="head">
                    <span>Page Setups</span>
                </div>
                <div class="item" data-url="page_parts/home.php" name="Index" title="Home Page">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/home.png" alt="Home" />
                    </div>
                    <div class="menu_name">
                        <span>Home Page</span>
                    </div>
                </div>
                <div class="item" data-url="page_parts/about.php" name="about" title="About Page">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/receipt-outline.svg" alt="about" />
                    </div>
                    <div class="menu_name">
                        <span>About Page</span>
                    </div>
                </div>
                <div class="item" data-url="page_parts/faq.php" name="faq" title="Frequently Asked Questions">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/information-circle-outline.svg" alt="faq" />
                    </div>
                    <div class="menu_name">
                        <span>Frequently Asked Questions</span>
                    </div>
                </div>
            </div>
            <div class="menu">
                <div class="head active">
                    <span>Documents</span>
                </div>
                <div class="item active" name="Request" title="Document Requests" data-url="page_parts/request.php">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/hand-right-outline.svg" alt="request" />
                    </div>
                    <div class="menu_name">
                        <span>Document Requests</span>
                    </div>
                </div>
                <div class="item active" name="Report" title="Admin System Reports" data-url="page_parts/report.php">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/information-outline.svg" alt="request" />
                    </div>
                    <div class="menu_name">
                        <span>Admin System Reports</span>
                    </div>
                </div>
            </div>
            <div class="menu">
                <div class="head">
                    <span>Settings</span>
                </div>
                <div class="item" name="account" title="Personal Account" data-url="page_parts/person.php">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="" />
                    </div>
                    <div class="menu_name">
                        <span>Personal Account</span>
                    </div>
                </div>
                <div class="item" name="password" title="Change Password" data-url="page_parts/change_password.php">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/key-outline.svg" alt="" />
                    </div>
                    <div class="menu_name">
                        <span>Change Password</span>
                    </div>
                </div>
                <!-- <div class="item" data-url="page_parts/admission.php" name="admission" title="Admission Details">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons/receipt-outline.svg" alt="" />
                    </div>
                    <div class="menu_name">
                        <span>Admission Details</span>
                    </div>
                </div>
                <div class="item" data-url="page_parts/exeat.php" name="exeat" title="Exeat">
                    <div class="icon">
                        <img src="<?php echo $url?>/assets/images/icons" alt="exeat">
                    </div>
                    <div class="menu_name">
                        <span>Exeat</span>
                    </div>
                </div> -->
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
        <?php @include_once("page_parts/newStudent.php")?>
    </div>

    <div id="modal_3" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
        <?php include_once("page_parts/add_house.php")?>
    </div>

    <div id="modal_yes_no" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
        <div class="yes_no_container">
            <div class="body">
                <p id="warning_content">Do you want to delete?</p>
            </div>

            <form action="submit.php" class="no_disp" name="yes_no_form" id="yes_no_form">
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
    <script src="<?php echo $url?>/assets/scripts/admissionForm.js?v=<?php echo time(); ?>"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/index.js?v=<?php echo time()?>"></script>

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
        //$("#rhs .body").load("page_parts/change_password.html");
    </script>
</body>
</html>