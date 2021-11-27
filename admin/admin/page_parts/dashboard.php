<?php require_once("../../../includes/session.php")?>
<section class="section_container">
    <div class="content blue">
        <div class="head">
            <h2>
                <?php
                $shsID = 3;
                    $res = $connect->query("SELECT enrolDate FROM enrol_table WHERE shsID=$shsID");
                    $i = 0;

                    if($res->num_rows > 0){
                        while($row = $res->fetch_assoc()){
                            if(date("W",strtotime($row['enrolDate'])) == date("W")){
                                $i += 1;
                            }else{
                                continue;
                            }
                        }
                    }

                    echo $i;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>New Registers this week</span>
        </div>
    </div>

    <div class="content teal">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM enrol_table WHERE shsID=$shsID");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Students Registered</span>
        </div>
    </div>

    <div class="content yellow">
        <div class="head">
            <h2>0</h2>
        </div>
        <div class="body">
            <span>Vistors today</span>
        </div>
    </div>

    <div class="content red">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM cssps WHERE enroled = FALSE");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Students left to register</span>
        </div>
    </div>
</section>

<section class="section_container">
    <div class="content" style="background-color: #17a2b8;">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM enrol_table WHERE interest = 'Athletics' AND shsID=$shsID");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Interested in Athletics</span>
        </div>
    </div>

    <div class="content" style="background-color: #28a745">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM enrol_table WHERE interest = 'Football' AND shsID=$shsID");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Interested in Football</span>
        </div>
    </div>

    <div class="content" style="background-color: #fd7e14">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM enrol_table WHERE interest = 'Debate Club' AND shsID=$shsID");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Interested in Debate Club</span>
        </div>
    </div>

    <div class="content" style="background-color: #6610f2">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM enrol_table WHERE interest = 'Others' AND shsID=$shsID");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Have other interests</span>
        </div>
    </div>
</section>

<section class="section_container">
    <div class="content secondary">
        <div class="head">
            <h2>GHC
                <?php
                    $res = $connect->query("SELECT transactionID, Transaction_Date, amountPaid, Deduction FROM transaction WHERE schoolBought=$shsID");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("Y",strtotime($row["Transaction_Date"])) == date("Y")){
                            $amount += ($row["amountPaid"] - $row["Deduction"]) * 0.1;
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
                    $res = $connect->query("SELECT transactionID, Transaction_Date, amountPaid, Deduction FROM transaction WHERE schoolBought=$shsID");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("W",strtotime($row["Transaction_Date"]) - 1) == date("W") - 1){
                            $amount += ($row["amountPaid"] - $row["Deduction"]) * 0.1;
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
                    $res = $connect->query("SELECT transactionID, Transaction_Date, amountPaid, Deduction FROM transaction WHERE schoolBought=$shsID");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("W",strtotime($row["Transaction_Date"])) == date("W")){
                            $amount += ($row["amountPaid"] - $row["Deduction"]) * 0.1;
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