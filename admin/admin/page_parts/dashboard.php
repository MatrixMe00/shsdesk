<?php include_once("auth.php");
    //set nav_point session
    $_SESSION["nav_point"] = "dashboard";
?>
<section class="section_container">
    <div class="content blue">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT e.enrolDate 
                        FROM enrol_table e JOIN cssps c ON e.indexNumber=c.indexNumber
                        WHERE shsID=$user_school_id AND c.current_data=TRUE");
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
            <h2><?= $ttl = fetchData("COUNT(indexNumber) AS total", "cssps", ["schoolID=$user_school_id", "current_data=TRUE", "enroled=TRUE"], where_binds: "AND")["total"] ?></h2>
        </div>
        <div class="body">
            <span><?= $ttl > 1 ? "Students" : "Student" ?> Registered</span>
        </div>
    </div>

    <div class="content red">
        <div class="head">
            <h2><?= $ttl = fetchData("COUNT(indexNumber) AS total", "cssps", ["schoolID=$user_school_id", "current_data=TRUE", "enroled=FALSE"], where_binds: "AND")["total"] ?></h2>
        </div>
        <div class="body">
            <span><?= $ttl > 1 ? "Students" : "Student" ?> left to register</span>
        </div>
    </div>
</section>

<section class="section_container">
    <div class="content" style="background-color: #17a2b8;">
        <div class="head">
            <h2><?= fetchData("COUNT(e.indexNumber) AS total", [
                "join" => "cssps enrol_table",
                "alias" => "c e", "on" => "indexNumber indexNumber"
                ], 
                ["c.schoolID=$user_school_id", "e.interest LIKE '%Athletics%'", "c.current_data=TRUE"], 
                where_binds: "AND")["total"] ?>
            </h2>
        </div>
        <div class="body">
            <span>Interested in Athletics</span>
        </div>
    </div>

    <div class="content" style="background-color: #28a745">
        <div class="head">
            <h2><?= fetchData("COUNT(e.indexNumber) AS total", [
                "join" => "cssps enrol_table",
                "alias" => "c e", "on" => "indexNumber indexNumber"
                ], 
                ["c.schoolID=$user_school_id", "e.interest LIKE '%Football%'", "c.current_data=TRUE"], 
                where_binds: "AND")["total"] ?></h2>
        </div>
        <div class="body">
            <span>Interested in Football</span>
        </div>
    </div>

    <div class="content" style="background-color: #fd7e14">
        <div class="head">
            <h2>
                <?= fetchData("COUNT(e.indexNumber) AS total", [
                    "join" => "cssps enrol_table",
                    "alias" => "c e", "on" => "indexNumber indexNumber"
                    ], 
                    ["c.schoolID=$user_school_id", "e.interest LIKE '%Debating Club%'", "c.current_data=TRUE"], 
                    where_binds: "AND")["total"] ?>
            </h2>
        </div>
        <div class="body">
            <span>Interested in Debate Club</span>
        </div>
    </div>

    <div class="content" style="background-color: #6610f2">
        <div class="head">
            <h2>
                <?= fetchData("COUNT(e.indexNumber) AS total", [
                    "join" => "cssps enrol_table",
                    "alias" => "c e", "on" => "indexNumber indexNumber"
                    ], 
                    ["c.schoolID=$user_school_id", "e.interest LIKE '%Others%'", "c.current_data=TRUE"], 
                    where_binds: "AND")["total"] ?>
            </h2>
        </div>
        <div class="body">
            <span>Have other interests</span>
        </div>
    </div>
</section>

<?php if($admin_access < 3): ?>
<section class="section_container">
    <?php if(floatval($role_price) > 0): ?>
    <div class="content secondary">
        <div class="head">
            <h2>GHC
                <?php
                    //get current sum of prices
                    $amount = $role_price * fetchData("SUM(amountPaid) as ttl",
                        "transaction", "current_data=TRUE AND schoolBought=$user_school_id AND Transaction_Expired=TRUE"
                    )["ttl"];
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
                    $wk_amt = $connect->query("SELECT Transaction_Date as enrolDate, amountPaid 
                        FROM transaction WHERE current_data=TRUE AND Transaction_Expired = TRUE
                        AND schoolBought=$user_school_id AND DATE_FORMAT(Transaction_Date, '%Y-%m')");
                    
                    $amount = 0;
                    while($wk_amt = $res->fetch_array()){
                        if(date("W",strtotime($wk_amt["enrolDate"]) - 1) == date("W") - 1){
                            $amount += $role_price;
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
                    $amount = 0;
                    while($wk_amt = $res->fetch_array()){
                        if(date("W",strtotime($wk_amt["enrolDate"])) == date("W")){
                            $amount += $role_price;
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
    <?php endif; ?>
    
    <?php if($admin_access >= 2) :?>
    <div class="content dark">
        <div class="head">
            <h2>GHC
                <?php
                    $amount = fetchData("SUM(amountPaid) as total",
                        "transaction", "current_data=TRUE AND schoolBought=$user_school_id AND Transaction_Expired = TRUE"
                    );
                    
                    $head_id = (int) $role_id + 1;
                    $head_price = fetchData("price","roles","id=$head_id")["price"];
                    $amount = $amount["total"] * ($head_price / 100);
                    
                    echo number_format(round($amount,2), 2);
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Made By School This Year</span>
        </div>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>