<?php 
    require "session.php";

    $today = date('Y-m-d H:i:s');
    $sql = "UPDATE accesstable SET status=0 WHERE expiryDate < STR_TO_DATE('$today', '%Y-%m-%d %H:%i:%s')";
    $connect2->query($sql);

    echo "Update affected $connect2->affected_rows rows";

    $connect2->close();
?>