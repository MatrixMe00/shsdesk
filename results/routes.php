<?php 
    $url_requested = $_SERVER['REQUEST_URI'];
    $url_len = strlen($url_requested);
    
    $actual_path = substr($url_requested,strpos($url_requested,'/'),$url_len); 

    if($actual_path == "/"){
        $page_title = "SHSDesk | Student Login";
        include_once('./index.php');
    }elseif($actual_path == "/main"){
        include_once("./main.php");
    }elseif($actual_path == "/logout"){
        include_once("./logout.php");
    }elseif(str_contains($actual_path, "shutdown")){
        include_once("./shutdown.php");
    }else {
        $page_title = "error 404 not found!";
        include_once('./404.php');
    }
?>