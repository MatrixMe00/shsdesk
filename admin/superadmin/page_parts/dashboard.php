<?php include_once("session.php");
    $system_price = fetchData("price","roles","title='system'")["price"] / 100;
    $this_year = date("Y");
    $academic_year = getAcademicYear(now(), false);

    //add nav point session
    $_SESSION["nav_point"] = "Dashboard";
?>

<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2><?= $total_schools = fetchData("COUNT(id) as total", "schools")["total"] ?></h2>
        </div>
        <div class="body">
            <span><?= $total_schools != 1 ? "Schools":"School" ?> Registered</span>
        </div>
    </div>

    <div class="content teal">
        <div class="head">
            <h2><?= $total_students = fetchData("COUNT(indexNumber) as total","enrol_table","current_data=TRUE AND academic_year = '$academic_year'")["total"] ?></h2>
        </div>
        <div class="body">
            <span><?= $total_schools != 1 ? "Students":"Student" ?> on System [Current]</span>
        </div>
    </div>

    <div class="content dark">
        <div class="head">
            <h2><?= $total_students = fetchData("COUNT(indexNumber) as total","enrol_table")["total"] ?></h2>
        </div>
        <div class="body">
            <span><?= $total_schools != 1 ? "Students":"Student" ?> on System [Total]</span>
        </div>
    </div>

    <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2><?= $ttl = fetchData("count(indexNumber) AS total", "enrol_table", 
                ["enrolDate LIKE '".date('Y-m-d')."%'", "current_data=TRUE"], where_binds: "AND")["total"];?></h2>
        </div>
        <div class="body">
            <span>Application<?= $ttl != 1 ? "s":"" ?> today</span>
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
                    $amount = $role_price * fetchData("SUM(amountPaid) as ttl",
                        "transaction", "current_data=TRUE AND Transaction_Expired=TRUE"
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
                    $wk_amt = $connect->query("SELECT Transaction_Date as enrolDate, amountPaid FROM transaction WHERE current_data=TRUE AND Transaction_Expired = TRUE");
                    $amount = 0;
                    if($wk_amt->num_rows > 0){
                        while($row = $wk_amt->fetch_array()){
                            if(date("W",strtotime($row["enrolDate"]) - 1) == date("W") - 1){
                                $amount += ($role_price * $row["amountPaid"]);
                            }else{
                                continue;
                            }
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
                    if($wk_amt->num_rows > 0){
                        while($row = $wk_amt->fetch_array()){
                            if(date("W",strtotime($row["enrolDate"])) == date("W")){
                                $amount += ($role_price * $row["amountPaid"]);
                            }else{
                                continue;
                            }
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
                    $amount = fetchData("SUM(amountPaid) as total, SUM(deduction) as deduction",
                        "transaction", "current_data=TRUE AND Transaction_Expired = TRUE"
                    );
                    $amount = ($system_price * $amount["total"]) - $amount["deduction"];
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

<section class="section_container">
    <div class="content secondary">
        <div class="head">
            <h2>GHC
                <?php
                    $amt = fetchData("SUM(amountPaid) as total, SUM(Deduction) AS deduction", "transaction", "academic_year ='$academic_year' AND Transaction_Expired=TRUE");
                    $amount = $role_price * $amt["total"];
                    $amount = number_format(round($amount,2), 2);
                    echo $amount;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Made in <?= $this_year ?></span>
        </div>
    </div>
    <div class="content dark">
        <div class="head">
            <h2>GHC
                <?php
                    // $amount = fetchData("SUM(Deduction) as deduction, COUNT(Deduction) as total", "transaction", "Transaction_Expired=TRUE AND DATE_FORMAT(Transaction_Date, '%Y')=$this_year");
                    $amount = ($system_price * $amt["total"]) - $amt["deduction"];
                    $amount = number_format(round($amount,2), 2);
                    echo "$amount";
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Made by System in <?= $this_year ?></span>
        </div>
    </div>
</section>
<?php endif; ?>