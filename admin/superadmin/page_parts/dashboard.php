<?php include_once("../../../includes/session.php");
    $price = fetchData("price","roles","id=".$user_details["role"])["price"];
    $system_price = fetchData("price","roles","title='system'")["price"];

    //add nav point session
    $_SESSION["nav_point"] = "Dashboard";
?>

<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT id FROM schools");
                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span><?php 
                if($res->num_rows > 1){
                    echo "Schools";
                }else{
                    echo "School";
                }
            ?> Registered</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT indexNumber FROM enrol_table");
                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span><?php 
                if($res->num_rows > 1){
                    echo "Students";
                }else{
                    echo "Student";
                }
            ?> on System</span>
        </div>
    </div>

    <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2>0</h2>
        </div>
        <div class="body">
            <span>Vistors today</span>
        </div>
    </div>

    <div class="content" style="background-color: #dc3545">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT id FROM schools WHERE Active = FALSE");
                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span><?php 
                if($res->num_rows > 1){
                    echo "Schools";
                }else{
                    echo "School";
                }
            ?> Deactivated</span>
        </div>
    </div>
</section>

<section class="section_container">
    <div class="content secondary">
        <div class="head">
            <h2>GHC
                <?php
                    $res = $connect->query("SELECT transactionID, Transaction_Date, amountPaid, Deduction 
                        FROM transaction
                        WHERE Transaction_Expired=TRUE");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("Y",strtotime($row["Transaction_Date"])) == date("Y")){
                            $amount += $price;
                        }else{
                            continue;
                        }
                    }
                    $amount = number_format(round($amount,2), 2);
                    echo $amount;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Made This Year</span>
        </div>
    </div>

    <div class="content pink">
        <div class="head">
            <h2>GHC
            <?php
                    $res = $connect->query("SELECT transactionID, Transaction_Date, amountPaid, Deduction 
                        FROM transaction
                        WHERE Transaction_Expired=TRUE");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("W",strtotime($row["Transaction_Date"]) - 1) == date("W") - 1){
                            $amount += $price;
                        }else{
                            continue;
                        }
                    }
                    $amount = number_format(round($amount,2), 2);
                    echo $amount;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Made Last Week</span>
        </div>
    </div>

    <div class="content purple">
        <div class="head">
            <h2>GHC
                <?php
                    $res = $connect->query("SELECT transactionID, Transaction_Date, amountPaid, Deduction 
                        FROM transaction
                        WHERE Transaction_Expired=TRUE");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("W",strtotime($row["Transaction_Date"])) == date("W")){
                            $amount += $price;
                        }else{
                            continue;
                        }
                    }
                    $amount = number_format(round($amount,2), 2);
                    echo $amount;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Made This Week</span>
        </div>
    </div>

    <div class="content dark">
        <div class="head">
            <h2>GHC
                <?php
                    $res = $connect->query("SELECT transactionID, Transaction_Date, amountPaid, Deduction 
                        FROM transaction
                        WHERE Transaction_Expired=TRUE");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("Y",strtotime($row["Transaction_Date"])) == date("Y")){
                            $amount += $system_price - $row["Deduction"];
                        }else{
                            continue;
                        }
                    }
                    $amount = number_format(round($amount,2), 2);
                    echo $amount;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Made by System This Year</span>
        </div>
    </div>
</section>