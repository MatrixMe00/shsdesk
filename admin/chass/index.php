<?php
    //depending on where the page is being called from

    $this_url = $_SERVER["REQUEST_URI"];

    if($this_url == "/admin/chass/"){
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
        if(checkNewUser($_SESSION["user_login_id"]) === TRUE){
            require_once($rootPath."/admin/admin/page_parts/update_stat.php");
        }elseif(checkNewUser($_SESSION["user_login_id"]) === "invalid-user"){
            echo "User cannot be found! Please speak to the administrator";
            session_destroy();
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
                        $greet = showGreeting();

                        echo "$greet <strong>".strtoupper($user_details["fullname"])."</strong>";
                    ?>
                    </span>
                </div>
                <div class="status">
                    <span>Status: Active</span>
                </div>
            </div>
            <?php include_once("page_parts/nav.php"); ?>
        </div>
    </nav>
    <section id="rhs">
        <div class="head">
            <h3 id="title">CHASS / <span id="head"></span></h3>
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
                <button type="button" name="yes_button" class="plain-r green" onclick="$('#yes_no_form').submit()">Yes</button>
                <button type="button" name="no_button" class="plain-r red" onclick="$('#gen_del').addClass('no_disp')">No</button>
            </div>
        </div>
    </div>

    <script src="<?php echo $url?>/admin/assets/scripts/index.min.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>"></script>
    
    <script>
        $(document).ready(function() {
            const nav_point = "<?= get_nav_point() ?>";
            const nav_light = "<?= get_nav_mode() ?>";

            $("div[name=" + nav_point + "]").click();

            $("nav").addClass(nav_light);
            
            $("#admin_head_title").click(function(){
                $(this).siblings("select, p").toggleClass("no_disp")
                $(this).toggleClass("sm-med-b")
                $(this).parent().toggleClass("dark")
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