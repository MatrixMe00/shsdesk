<?php include_once("auth.php");
    //set nav_point session
    $_SESSION["nav_point"] = "dashboard";
    $academic_year = getAcademicYear(now(), false);
?>
<section class="section_container">
    <div class="content blue">
        <div class="head">
            <h2>
                <?= fetchData("COUNT(enrolDate) as total", "enrol_table", ["shsID=$user_school_id", "current_data = TRUE", "YEAR(NOW()) = YEAR(enrolDate)", "WEEK(NOW()) = WEEK(enrolDate)"], where_binds: "AND")["total"] ?>
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

    <div class="content pink">
        <div class="head">
            <h2>
                <?= fetchData("COUNT(e.indexNumber) AS total", [
                    "join" => "cssps enrol_table",
                    "alias" => "c e", "on" => "indexNumber indexNumber"
                    ], 
                    ["c.schoolID=$user_school_id", "e.interest LIKE '%Not Defined%'", "c.current_data=TRUE"], 
                    where_binds: "AND")["total"] ?>
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
                    $amount = $role_price * fetchData("SUM(amountPaid) as ttl",
                        "transaction", "current_data=TRUE AND academic_year = '$academic_year' AND schoolBought=$user_school_id AND Transaction_Expired=TRUE"
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
                $wk_amt = fetchData("COUNT(amountPaid) as total", "transaction", 
                    ["current_data = TRUE", "academic_year = '$academic_year'", "schoolBought = $user_school_id", "YEAR(NOW()) = YEAR(Transaction_DATE)", "WEEK(NOW()) - 1", "WEEK(Transaction_Date)"], where_binds: "AND"
                )["total"];
                
                $amount = $role_price * $wk_amt;
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
                $wk_amt = fetchData("COUNT(amountPaid) as total", "transaction", 
                    ["current_data = TRUE", "academic_year = '$academic_year'", "schoolBought = $user_school_id", "YEAR(NOW()) = YEAR(Transaction_DATE)", "WEEK(NOW())", "WEEK(Transaction_Date)"], where_binds: "AND"
                )["total"];
                
                $amount = $role_price * $wk_amt;
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
                        "transaction", "current_data=TRUE AND academic_year='$academic_year' AND schoolBought=$user_school_id AND Transaction_Expired = TRUE"
                    );
                    
                    $head_id = (int) $role_id + 1;
                    $head_price = getRole($head_id, false)["price"];
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