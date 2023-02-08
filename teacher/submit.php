<?php
    include_once("../includes/session.php");

    if(!isset($_REQUEST["submit"])){
        $submit = $_REQUEST["submit"];
    }else{
        echo "No submit request delivered. No operation is performed.";
    }
?>