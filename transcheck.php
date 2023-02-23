<?php include_once("includes/session.php")?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Checks</title>
</head>
<body>
    <?php 
        //get data
        /*$transactions = fetchData("transactionID, indexNumber", "transaction", "Transaction_Expired=TRUE", 0);
        $enrolment = fetchData("indexNumber, transactionID", "enrol_table", "", 0);

        if(is_array($transactions) && is_array($enrolment)){
            echo "Total Transactions: ".count($transactions)."<br>";
            echo "Total enrolment: ".count($enrolment)."<br>";
            
            foreach($transactions as $trans){
                //a marker to check if a transaction was found
                $found = false;
                foreach($enrolment as $enrol){
                    if(strtolower($enrol["transactionID"]) === strtolower($trans["transactionID"])){
                        $found = true;
                        break;
                    }
                }

                //print transaction id if it was not found in the enrolment
                if(!$found){
                    echo $trans["transactionID"]." not found<br>";
                }
            }
        }elseif(is_array($transactions) && !is_array($enrolment)){
            echo "no enrolment data found";
        }elseif(!is_array($transactions) && is_array($enrolment)){
            echo "no transaction data found";
        }else{
            echo "transactions and enrolment have no data";
        }        */

        $row["id"] = 3;
        $admin_role_id = fetchData("r.id","roles r JOIN admins_table a ON r.id=a.role", "r.title LIKE 'admin%' AND a.school_id=".$row["id"])["id"];
        $school_role_id = fetchData("r.id","roles r JOIN admins_table a ON r.id=a.role", "r.title LIKE 'school head%' AND a.school_id=".$row["id"])["id"];

        $price_admin = $connect->query("SELECT price FROM roles WHERE id=$admin_role_id")->fetch_assoc()["price"];
        $price_school = $connect->query("SELECT price FROM roles WHERE id=$school_role_id")->fetch_assoc()["price"];

        echo "Admin: $price_admin<br>School: $price_school";
    ?>
</body>
</html>