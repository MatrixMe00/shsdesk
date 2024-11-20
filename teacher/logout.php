<?php
include_once("../includes/session.php");

//destroy the session
session_destroy();

close_connections();

//move to index page
header("location:$url");

?>