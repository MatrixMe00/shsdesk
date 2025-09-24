<?php include_once("session.php");
    $system_price = fetchData("price","roles","title='system'")["price"] / 100;
    $this_year = date("Y");
    $academic_year = getAcademicYear(now(), false);

    //add nav point session
    $_SESSION["nav_point"] = "Dashboard";

    $main_total_students = fetchData("COUNT(indexNumber) as total","enrol_table")["total"];

    $schools = fetchData([
        "COUNT(CASE WHEN Active=TRUE THEN 1 ELSE 0 END) as active",
        "COUNT(CASE WHEN Active=FALSE THEN 1 ELSE 0 END) as inactive",
        "COUNT(id) as total",
    ], "schools");

    $students = fetchData([
        "COUNT(CASE WHEN academic_year='$academic_year' THEN 1 END) AS current",
        "COUNT(CASE WHEN academic_year='$academic_year' AND DATE(enrolDate) = CURDATE() THEN 1 END) AS enroled_today",
        "COUNT(CASE WHEN academic_year = '$academic_year' AND YEARWEEK(enrolDate, 1) = YEARWEEK(CURDATE(), 1) THEN 1 END) AS enroled_this_week",
        "COUNT(CASE WHEN academic_year = '$academic_year' AND YEARWEEK(enrolDate, 1) = YEARWEEK(CURDATE(), 1) - 1 THEN 1 END) AS enroled_last_week",
        "COUNT(indexNumber) AS total",
    ], "enrol_table");

?>

<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2><?= $schools["total"] ?></h2>
        </div>
        <div class="body">
            <span><?= $schools["total"] != 1 ? "Schools":"School" ?> Registered</span>
        </div>
    </div>

    <div class="content teal" title="<?= number_format($students["current"]) ?>">
        <div class="head">
            <h2><?= numberShortner($students["current"]) ?></h2>
        </div>
        <div class="body">
            <span><?= $students["current"] != 1 ? "Students":"Student" ?> on System [Current]</span>
        </div>
    </div>

    <div class="content dark" title="<?= number_format($students["total"]) ?>">
        <div class="head">
            <h2><?= numberShortner($students["total"]) ?></h2>
        </div>
        <div class="body">
            <span><?= $students["total"] != 1 ? "Students":"Student" ?> on System [Total]</span>
        </div>
    </div>

    <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2><?= $students["enroled_today"] ?></h2>
        </div>
        <div class="body">
            <span>Application<?= $students["enroled_today"] != 1 ? "s":"" ?> today</span>
        </div>
    </div>

    <div class="content" style="background-color: #dc3545">
        <div class="head">
            <h2><?= $total_schools = fetchData("COUNT(id) as total","schools","Active=FALSE")["total"] ?></h2>
        </div>
        <div class="body">
            <span><?= $total_schools != 1 ? "Schools":"School" ?> Deactivated</span>
        </div>
    </div>

    <div class="content light">
        <div class="head">
            <h2><?= $academic_year ?></h2>
        </div>
        <div class="body">
            <span>Academic Year [current]</span>
        </div>
    </div>
</section>

<?php if($admin_access > 3) : ?>
<section class="section_container">
    <div class="content secondary">
        <div class="head">
            <h2>GHC
                <?php
                    //get current sum of prices
                    // $amount = $role_price * fetchData("SUM(t.amountPaid) as ttl",
                    //     ["join" => "transaction enrol_table", "on" => "transactionID transactionID", "alias" => "t e"], 
                    //     "t.academic_year='$academic_year' AND t.Transaction_Expired=TRUE"
                    // )["ttl"];
                    // $amount = number_format(round($amount,2), 2);
                    // echo $amount;
                    $role_price = calculate_actual_price($role_price);
                    echo number_format($students["current"] * $role_price, 2)
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
                    //     ["t.academic_year = '$academic_year'", "YEARWEEK(Transaction_Date, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)", "Transaction_Expired = TRUE"], where_binds: "AND"
                    // )["total"];
                    
                    // $amount = $role_price * $wk_amt;
                    // $amount = number_format(round($amount,2), 2);
                    // echo $amount;
                    echo number_format($students["enroled_last_week"] * $role_price, 2);
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
                    //     ["t.academic_year = '$academic_year'", "YEARWEEK(Transaction_Date, 1) = YEARWEEK(CURDATE(), 1)", "Transaction_Expired = TRUE"], where_binds: "AND"
                    // )["total"];
                    
                    // $amount = $role_price * $wk_amt;
                    // $amount = number_format(round($amount,2), 2);
                    // echo $amount;
                    echo number_format($students["enroled_this_week"] * $role_price, 2);
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
                    // $amount = fetchData("SUM(amountPaid) as total, SUM(deduction) as deduction",
                    // ["join" => "transaction enrol_table", "on" => "transactionID transactionID", "alias" => "t e"], 
                    // "t.academic_year='$academic_year' AND Transaction_Expired = TRUE"
                    // );
                    // $amount = ($system_price * $amount["total"]) - $amount["deduction"];
                    // $amount = number_format(round($amount,2), 2);
                    // echo $amount;
                    $system_price = calculate_actual_price($system_price);
                    echo number_format($system_price * $students["current"],2)
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Made by System This Year</span>
        </div>
    </div>
</section>

<?php endif; close_connections() ?>