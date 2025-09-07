<?php
    require_once "session.php";

    // log out of staff portal, but not session
    $_SESSION["staff_menu"] = false;
    $_SESSION["nav_point"] = "dashboard";
?>

<p>Switching Portals</p>
<script>
    $(document).ready(function(){
        setTimeout(function(){
            location.href = "<?= $url ?>/admin"
        }, 1000)
    })
</script>