<?php

@include_once("functions.php");

$host = "localhost";
$hostname = "root";
$host_password = "";
$dbname = "shsdesk";
$dbname2 = "shsdesk2";

@$connect = new mysqli($host,$hostname,$host_password, $dbname);
@$connect2 = new mysqli($host,$hostname,$host_password, $dbname2);

if($connect->connect_error){
    die("Connection failed -> Port 1...".$connect->connect_error);
    exit(1);
}

if($connect2->connect_error){
    die("Connection failed -> Port 2...".$connect2->connect_error);
    exit(1);
}

//creating a default root path for finding php documents
$rootPath = $_SERVER["DOCUMENT_ROOT"];

//creating a default url for folder files
//grabbing protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off" || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

//adding the domain name
$domain_name = $_SERVER['HTTP_HOST'];

//$url = $protocol.$domain_name;
$url = $protocol.$domain_name;

//start a session
if(!session_start())
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

    //check for admission mode and results mode
    if(!isset($_SESSION["admin_mode"])){
        $_SESSION["admin_mode"] = "admission";
    }
}elseif(isset($_SESSION["student_id"]) && !is_null($_SESSION["student_id"])){
    $student = fetchData1("*","students_table","indexNumber='".$_SESSION["student_id"]."'");
    
    if(!is_array($student)){
        echo "Sorry, the account was not found. Contact the administrator for help.";
        exit();
    }
}elseif(isset($_SESSION["teacher_id"]) && !is_null($_SESSION["teacher_id"])){
    $teacher = fetchData1("*","teachers","teacher_id=".$_SESSION["teacher_id"]);

    if(!is_array($teacher)){
        echo "Your account could not be found or was just deleted. Please contact your admin for help";
        exit(1);
    }
}else{
    $user_role = "guest";
}

?>