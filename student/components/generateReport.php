<?php
    include_once("compSession.php");
    require("$mainRoot/mpdf/vendor/autoload.php");
    require("$mainRoot/mpdf/qr/vendor/autoload.php");

    if(isset($_REQUEST["index_number"])){

    }else{
        echo [
            "status" => false,
            "message" => "No index number specified"
        ];
    }
?>