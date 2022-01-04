<?php
include_once("../includes/session.php");

//update the login table
$now = date('Y-m-d H:i:s');

$sql = "UPDATE login_details SET logout_time = '$now' WHERE id=".$_SESSION["login_id"] or die($connect->error);

if($connect->query($sql)){
    //destroy the session
    session_destroy();

    //move to index page
    header("location:$url/admin");
}else{
    echo "unexpected error occured";
}
?>