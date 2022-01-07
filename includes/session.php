<?php

@include_once("functions.php");

$host = "localhost";
$hostname = "root";
$host_password = "";
$dbname = "shsdesk";
/*$host = "sql301.epizy.com";
$hostname = "epiz_29441826";
$host_password = "alnYq8eIOGeiyxD";
$dbname = "epiz_29441826_shsdesk";*/

@$connect = new mysqli($host,$hostname,$host_password, $dbname);

if($connect->connect_error){
    die("Connection failed...".$connect->connect_error);
    exit(1);
}
//creating a default root path for finding php documents
$rootPath = $_SERVER["DOCUMENT_ROOT"]."/shsdesk";

//creating a default url for folder files
//grabbing protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off" || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

//adding the domain name
$domain_name = $_SERVER['HTTP_HOST'];

//$url = $protocol.$domain_name;
$url = $protocol.$domain_name."/shsdesk";

//start a session
session_start();

date_default_timezone_set("Africa/Accra");

//create a session variable
if(isset($_SESSION['user_login_id']) && $_SESSION['user_login_id'] > 0){
    $user_details = getUserDetails($_SESSION['user_login_id']);

    //retrieve all details
    $user_id = $user_details['user_id'];
    $user_username = $user_details['username'];
    $user_school_id = $user_details['school_id'];
    $user_role = getRole($user_details['role']);
    $user_email = $user_details['email'];
}else{
    $user_role = "guest";
}

?>