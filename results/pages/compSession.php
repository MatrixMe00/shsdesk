<?php 
    include_once("../../includes/session.php");
    $mainRoot = "../../";

    /*if(!isset($_SESSION["student_id"])){
        echo "<script>alert('Your session has terminated. Please reload page to login again')</script>"; 
        echo "Current session has been terminated. Please reload page";
        exit(1);
    }*/

    if(access_without_payment($student["school_id"])){
        $code = "free_access";
    }else{
        $code = fetchData1("accessToken","accesstable","indexNumber='{$student['indexNumber']}' AND status=1 ORDER BY expiryDate DESC"); 
    }
?>