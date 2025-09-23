<?php include_once("auth.php");
    //set nav_point session
    $_SESSION["nav_point"] = "dashboard";
    $academic_year = getAcademicYear(now(), false);
    $role_price = !$staff_menu ? 0 : $role_price;
    $role_price = round($system_usage_price * $role_price, 2);

    $students = fetchData(
        [
            "COUNT(CASE WHEN enroled = TRUE THEN 1 END) AS enroled",
            "COUNT(CASE WHEN enroled = FALSE THEN 1 END) AS not_enroled"
        ], 
        "cssps", 
        ["schoolID=$user_school_id", "academic_year='$academic_year'"], 
        where_binds: "AND");
    $enroled = fetchData(
        [
            "COUNT(CASE WHEN YEARWEEK(enrolDate, 1) = YEARWEEK(CURDATE(), 1) THEN 1 END) AS this_week_enroled",
            "COUNT(CASE WHEN YEARWEEK(enrolDate, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1) THEN 1 END) AS last_week_enroled" ,
            "COUNT(CASE WHEN interest LIKE '%Athletics%' THEN 1 END) AS athletics_interest",
            "COUNT(CASE WHEN interest LIKE '%Football%' THEN 1 END) AS football_interest",
            "COUNT(CASE WHEN interest LIKE '%Debating Club%' THEN 1 END) AS debating_interest",
            "COUNT(CASE WHEN interest LIKE '%Others%' THEN 1 END) AS other_interest",
            "COUNT(CASE WHEN interest = 'Not Defined' THEN 1 END) AS no_interest",
        ], 
        ["join" => "enrol_table cssps", "alias" => "e c", "on" => "indexNumber indexNumber"], 
        ["shsID=$user_school_id", "e.academic_year='$academic_year'"], 
        where_binds: "AND");
    
?>

<section class="section_container">
    <div class="content blue">
        <div class="head">
            <h2>
                <?= $enroled["this_week_enroled"] ?>
            </h2>
        </div>
        <div class="body">
            <span>New Registers this week</span>
        </div>
    </div>

    <div class="content teal">
        <div class="head">
            <h2><?= $students["enroled"] ?></h2>
        </div>
        <div class="body">
            <span><?= $students["enroled"] > 1 ? "Students" : "Student" ?> Registered</span>
        </div>
    </div>

    <div class="content red">
        <div class="head">
            <h2><?= $students["not_enroled"] ?></h2>
        </div>
        <div class="body">
            <span><?= $students["not_enroled"] > 1 ? "Students" : "Student" ?> left to register</span>
        </div>
    </div>

    <div class="content light">
        <div class="head">
            <h2><?= $academic_year ?></h2>
        </div>
        <div class="body">
            <span>Admission Year [Current]</span>
        </div>
    </div>
</section>

<section class="section_container">
    <div class="content" style="background-color: #17a2b8;">
        <div class="head">
            <h2><?= $enroled["athletics_interest"] ?>
            </h2>
        </div>
        <div class="body">
            <span>Interested in Athletics</span>
        </div>
    </div>

    <div class="content" style="background-color: #28a745">
        <div class="head">
            <h2><?= $enroled["football_interest"] ?></h2>
        </div>
        <div class="body">
            <span>Interested in Football</span>
        </div>
    </div>

    <div class="content" style="background-color: #fd7e14">
        <div class="head">
            <h2>
                <?= $enroled["debating_interest"] ?>
            </h2>
        </div>
        <div class="body">
            <span>Interested in Debate Club</span>
        </div>
    </div>

    <div class="content" style="background-color: #6610f2">
        <div class="head">
            <h2>
                <?= $enroled["other_interest"] ?>
            </h2>
        </div>
        <div class="body">
            <span>Have other interests</span>
        </div>
    </div>

    <div class="content pink">
        <div class="head">
            <h2>
                <?= $enroled["no_interest"] ?>
            </h2>
        </div>
        <div class="body">
            <span>Have no interests</span>
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
                    // $amount = $role_price * fetchData("SUM(amountPaid) as ttl",
                    // ["join" => "transaction enrol_table", "on" => "transactionID transactionID", "alias" => "t e"], 
                    // "t.academic_year = '$academic_year' AND schoolBought=$user_school_id AND Transaction_Expired=TRUE"
                    // )["ttl"];
                    
                    echo number_format($role_price * $students["enroled"], 2);
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
                // $wk_amt = fetchData("SUM(amountPaid) as total", ["join" => "transaction enrol_table", "on" => "transactionID transactionID", "alias" => "t e"], 
                //     ["t.academic_year = '$academic_year'", "schoolBought = $user_school_id", "YEARWEEK(Transaction_Date, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)", "Transaction_Expired = TRUE"], where_binds: "AND"
                // )["total"];
                
                $amount = $role_price * $enroled["last_week_enroled"];
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
                // $wk_amt = fetchData("SUM(amountPaid) as total", ["join" => "transaction enrol_table", "on" => "transactionID transactionID", "alias" => "t e"], 
                //     ["t.academic_year = '$academic_year'", "schoolBought = $user_school_id", "YEARWEEK(Transaction_Date, 1) = YEARWEEK(CURDATE(), 1)", "Transaction_Expired = TRUE"], where_binds: "AND"
                // )["total"];
                
                $amount = $role_price * $enroled["this_week_enroled"];
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
                    // $amount = fetchData("SUM(amountPaid) as total",
                    // ["join" => "transaction enrol_table", "on" => "transactionID transactionID", "alias" => "t e"], 
                    // "t.academic_year='$academic_year' AND schoolBought=$user_school_id AND Transaction_Expired = TRUE"
                    // );
                    
                    $head_id = (int) $role_id + 1;
                    $head_price = calculate_actual_price(getRole($head_id, false)["price"]);
                    $amount = $students["enroled"] * $head_price;
                    
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
<?php close_connections() ?>