<?php include_once("../../../includes/session.php")?>

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
            <span>Schools Registered</span>
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
            <span>Students on System</span>
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
            <span>Schools Deactivated</span>
        </div>
    </div>
</section>

<section class="section_container">
    <div class="content secondary">
        <div class="head">
            <h2>GHC
                <?php
                    $res = $connect->query("SELECT transactionID, Transaction_Date, amountPaid, Deduction FROM transaction");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("Y",strtotime($row["Transaction_Date"])) == date("Y")){
                            $amount += ($row["amountPaid"] - $row["Deduction"]) * 0.3 * 725;
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
                    $res = $connect->query("SELECT transactionID, Transaction_Date, amountPaid, Deduction FROM transaction");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("W",strtotime($row["Transaction_Date"]) - 1) == date("W") - 1){
                            $amount += ($row["amountPaid"] - $row["Deduction"]) * 0.9;
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
                    $res = $connect->query("SELECT transactionID, Transaction_Date, amountPaid, Deduction FROM transaction");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("W",strtotime($row["Transaction_Date"])) == date("W")){
                            $amount += ($row["amountPaid"] - $row["Deduction"]) * 0.9;
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
</section>