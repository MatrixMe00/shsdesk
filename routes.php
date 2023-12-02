<?php
        $url_requested = $_SERVER['REQUEST_URI'];
        $url_len = strlen($url_requested);
        
        $actual_path = substr($url_requested,strpos($url_requested,'/'),$url_len); 
    
        if($actual_path == "/"){
            $page_title = "SHSDesk | Home";
            include_once('./index.php');
        }    
        else if(preg_match("!/products/[a-z,A-Z,0-9]!",$actual_path)){
            $actual_route = substr($actual_path,(strrpos($actual_path,'/')+1),$url_len);
            $actual_route = str_replace("%20"," ",$actual_route);  
            $data_arr = [
                'content_to_show' => $actual_route
            ];      
            $page_title = "SHSDesk | ".$actual_route;
            include_once('./index.php');
        }
        else if($actual_path == "/about"){        
            $page_title = "SHSDesk | About Us";
            include_once('./pages/about.php');
        }
        else if($actual_path == "/faq"){
            $page_title = "SHSDesk | FAQ";
            include_once('./pages/faq.php');
        }
        else if($actual_path == "/school"){
            $page_title = "SHSDesk | Schools";
            include_once('./pages/school.php');
        }
        else if($actual_path == "/contact"){
            $page_title = "SHSDesk | Contact Us";
            include_once('./pages/contact.php');
        }elseif(str_contains($actual_path, "shutdown")){
            include_once("shutdown.php");
        }else if(strpos($actual_path, "/admin/admin") || strpos($actual_path, "/admin/superadmin")){
            $page_title = "SHSDesk | Admin";
            include_once('./admin.index.php');
        }elseif($actual_path == "/admin"){
            $page_title = "SHSDesk | Admin";
            include_once('./admin/index.php');
        }elseif($actual_path == "/password-reset"){
            $page_title = "SHSDesk | Admin";
            include_once('./password.php');
        }elseif($actual_path == "/test"){
            include "./test.php";
        }
        else {
            $page_title = "error 404 not found!";
            include_once('./404.html');
        }
    ?>