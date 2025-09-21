<?php
    $rootpath = dirname(__DIR__, 2); 
    require_once($rootpath.'/includes/session.php');

    header("Content-type: application/json");

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $index_number = $_REQUEST["index_number"] ?? null;

        if(empty($index_number)){
            $code = 400;
            $message = "Bad Request";
        }else{
            $sql = "UPDATE affiliate_cssps SET enroled = TRUE WHERE index_number = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("s", $index_number);
            if($stmt->execute()){
                $code = 200;
                $message = "Success";
            }else{
                $code = 500;
                $message = "Internal Server Error";
            }
        }    
    }else{
        $code = 403;
        $message = "Forbidden";
    }

    http_response_code($code);
    echo json_encode(array("status" => $code, "message" => $message));