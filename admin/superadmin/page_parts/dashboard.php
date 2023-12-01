<?php include_once("session.php");
    $system_price = fetchData("price","roles","title='system'")["price"];
    $this_year = date("Y");

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
            <h2><?= $total_students = fetchData("COUNT(indexNumber) as total","enrol_table","current_data=TRUE")["total"] ?></h2>
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
</section>

<?php if($admin_access > 3) : ?>
<section class="section_container">
    <div class="content secondary">
        <div class="head">
            <h2>GHC
                <?php
                    $ttl_this_year = fetchData("COUNT(indexNumber) as total","enrol_table","current_data=TRUE")["total"];
                    
                    $amount = $role_price * $ttl_this_year;
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
                    $res = $connect->query("SELECT enrolDate FROM enrol_table");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("W",strtotime($row["enrolDate"]) - 1) == date("W") - 1){
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
                    $res = $connect->query("SELECT enrolDate FROM enrol_table");
                    
                    $amount = 0;
                    while($row = $res->fetch_array()){
                        if(date("W",strtotime($row["enrolDate"])) == date("W")){
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

    <div class="content dark">
        <div class="head">
            <h2>GHC
                <?php
                    $amount = fetchData("SUM(Deduction) as deduction, COUNT(Deduction) as total", "transaction", "Transaction_Expired=TRUE AND current_data=TRUE AND DATE_FORMAT(Transaction_Date, '%Y')=$this_year");
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
                    $ttl_this_year = fetchData("COUNT(indexNumber) as total","enrol_table","DATE_FORMAT(enrolDate, '%Y')=$this_year")["total"];
                    
                    $amount = $role_price * $ttl_this_year;
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
                    $amount = fetchData("SUM(Deduction) as deduction, COUNT(Deduction) as total", "transaction", "Transaction_Expired=TRUE AND DATE_FORMAT(Transaction_Date, '%Y')=$this_year");
                    $amount = ($system_price * $amount["total"]) - $amount["deduction"];
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