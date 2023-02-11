<?php

// $request = $_SERVER['REQUEST_URI'];

// switch ($request) {
//     case '/' :
//     case '' :
//         require __DIR__ . '/index.php';
//         break;
//     case '/about' :
//         require __DIR__ . 'pages/about.php';
//         break;
//     case '/contact' :
//         require __DIR__ . '/pages/contact.php';
//         break;
//     case '/faq' :
//         require __DIR__ . '/pages/faq.php';
//         break;
//     case '/school' :
//         require __DIR__ . '/pages/school.php';
//         break;
//     default:
//         http_response_code(404);
//         // require __DIR__ . '/views/404.php';
//         break;
// }

?>

<?php
        $url_requested = $_SERVER['REQUEST_URI'];
        $url_len = strlen($url_requested);
        
        $actual_path = substr($url_requested,strpos($url_requested,'/'),$url_len); 
    
        if($actual_path == "/"){
            $page_title = "Php route project - Home";
            include_once('./index.php');
        }    
        else if(preg_match("!/products/[a-z,A-Z,0-9]!",$actual_path)){
            $actual_route = substr($actual_path,(strrpos($actual_path,'/')+1),$url_len);
            $actual_route = str_replace("%20"," ",$actual_route);  
            $data_arr = [
                'content_to_show' => $actual_route
            ];      
            $page_title = "Php route project - ".$actual_route;
            include_once('./index.php');
        }
        else if($actual_path == "/about"){        
            $page_title = "Php route project - Products";
            include_once('./pages/about.php');
        }
        else if($actual_path == "/faq"){
            $page_title = "Php route project - Solutions";
            include_once('./pages/faq.php');
        }
        else if($actual_path == "/school"){
            $page_title = "Php route project - Solutions";
            include_once('./pages/school.php');
        }
        else if($actual_path == "/contact"){
            $page_title = "Php route project - Solutions";
            include_once('./pages/contact.php');
        }
        else {
            $page_title = "error 404 not found!";
            include_once('./404.html');
        }
    ?>