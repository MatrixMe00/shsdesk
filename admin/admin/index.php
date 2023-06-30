<?php
    //depending on where the page is being called from

    $this_url = $_SERVER["REQUEST_URI"];

    if($this_url == "/admin/admin/"){
        header("Location: ./admin");
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
                <div class="admin_mode">
                    <span class="txt-fs"><?= "[".ucwords($_SESSION["admin_mode"])." Mode]" ?></span>
                </div>
            </div>
            <?php include_once("page_parts/nav.php"); ?>
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
        <div class="yes_no_container white sp-xlg-lr sp-xxlg-tp sm-rnd wmax-sm wmin-unset w-full sm-auto">
            <div class="body txt-al-c sp-xxlg-tp">
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
                <button type="button" name="yes_button" class="plain primary" onclick="$('#yes_no_form').submit()">Yes</button>
                <button type="button" name="no_button" class="plain red" onclick="$('#gen_del').addClass('no_disp')">No</button>
            </div>
        </div>
    </div>

    <div style="transition: unset !important; max-width: 250px; width: 100%; cursor: move" 
        class="fixed dark lt-shade sp-med top-right light sm-lg-t sm-xlg-r" id="admin_mode">
        <p class="txt-al-c txt-fs" style="cursor: pointer" id="admin_head_title">Change Interface Mode</p>
        <select name="admin_mode_select" style="cursor: default" class="p-med sp-med wmin-unset w-full no_disp" id="admin_mode_select">
            <option value="admission"<?= $_SESSION["admin_mode"] == "admission" ? " selected" : "" ?>>Admission</option>
            <option value="records"<?= $_SESSION["admin_mode"] == "records" ? " selected" : "" ?>>Records</option>
        </select>
        <p class="txt-fs2 sm-med-t txt-al-c no_disp">This box can be dragged</p>
    </div>

    <script src="<?php echo $url?>/admin/assets/scripts/index.min.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/admissionForm.min.js?v=<?php echo time(); ?>"></script>
    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>"></script>
    
    <script>
        $(document).ready(function() {
            dragElement($("#admin_mode"))
            touchDragElement($("#admin_mode"))

            nav_point = "<?php
                if((isset($_GET["nav_point"]) && $_GET["nav_point"] != null) || (isset($_SESSION["nav_point"]) && !empty($_SESSION["nav_point"]))){
                    if(isset($_GET["nav_point"]))
                        echo $_GET["nav_point"];
                    else
                        echo $_SESSION["nav_point"];
                }else{
                    echo "dashboard";
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
            
            $("#admin_head_title").click(function(){
                $(this).siblings("select, p").toggleClass("no_disp")
                $(this).toggleClass("sm-med-b")
                $(this).parent().toggleClass("dark")
            })

            $("#admin_mode select").change(function(){
                const val = $(this).val()

                $.ajax({
                    url: "admin/submit.php",
                    data: {
                        submit: "change_admin_mode", admin_mode: val
                    },
                    timeout: 30000,
                    success: function(response){
                        if(response === "true"){
                            alert_box("Mode changed to " + val, "teal")

                            setTimeout(()=>{
                                location.reload()
                            },1000)
                        }else{
                            alert_box(response, "warning color-dark")
                        }
                    },
                    error: function(xhr, textStatus){
                        if(textStatus === "timeout"){
                            alert_box("Connection was timed out. Please check your internet connection", "danger", 7)
                        }else{
                            alert_box(xhr.responseText)
                        }
                    }
                })
            })
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
        include_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");
        
        header("location: $url/admin");
    }
?>