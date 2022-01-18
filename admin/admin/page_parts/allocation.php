<?php include_once("../../../includes/session.php");

    //set nav_point session
    $_SESSION["nav_point"] = "student";
?>
<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT indexNumber FROM cssps WHERE schoolID = $user_school_id AND enroled = TRUE");

                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Registered Students</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT indexNumber FROM cssps WHERE schoolID = $user_school_id AND boardingStatus = 'Boarder' AND enroled = TRUE");

                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Registered Boarding Students</span>
        </div>
    </div>

    <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT indexNumber FROM cssps WHERE schoolID = $user_school_id AND boardingStatus = 'Day' AND enroled = TRUE");

                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Registered Day Students</span>
        </div>
    </div>
</section>

<?php if($_SESSION["real_status"]){?>
<section class="flex flex-wrap flex-center-align"> 
    <div class="btn">
        <button>Generate Report</button>
    </div>
</section>
 <?php } ?>

<section class="section_container allocation flex-column">
    <div class="head" style="align-self: center">
        <h2>Boarding Students</h2>
    </div>
    <?php
        $res = $connect->query("SELECT DISTINCT (a.indexNumber), a.studentLname, a.studentOname, b.title, c.programme 
            FROM house_allocation a JOIN cssps c 
            ON a.indexNumber = c.indexNumber
            JOIN houses b
            ON a.houseID = b.id
            WHERE a.schoolID = $user_school_id AND a.boardingStatus = 'Boarder'");

        if($res->num_rows > 0){
    ?>
    <div class="body">
        <table>
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Full name</td>
                    <td>House</td>
                    <td>Program</td>
                </tr>
            </thead>
            <tbody>
            <?php
                while($row = $res->fetch_assoc()){
            ?>
                <tr>
                    <td><?php echo $row["indexNumber"] ?></td>
                    <td><?php echo $row["studentLname"]." ".$row["studentOname"] ?></td>
                    <td><?php echo $row["title"] ?></td>
                    <td><?php echo $row["programme"] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>  
    </div>
    <?php }else{
        echo "
            <div class=\"body empty\">
                <p>No results were found. Try adding some more data</p>
            </div>";
        }
    ?>
</section>

<section class="section_container allocation flex-column">
    <div class="head" style="align-self: center">
        <h2>Day Students</h2>
    </div>
    <?php
        $res = $connect->query("SELECT DISTINCT (a.indexNumber), a.studentLname, a.studentOname, b.title, c.programme 
            FROM house_allocation a JOIN cssps c 
            ON a.indexNumber = c.indexNumber
            JOIN houses b
            ON a.houseID = b.id
            WHERE a.schoolID = $user_school_id AND a.boardingStatus = 'Day'");

        if($res->num_rows > 0){
    ?>
    <div class="body">
        <table>
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Full name</td>
                    <td>House</td>
                    <td>Program</td>
                </tr>
            </thead>
            <tbody>
            <?php
                while($row = $res->fetch_assoc()){
            ?>
                <tr>
                    <td><?php echo $row["indexNumber"] ?></td>
                    <td><?php echo $row["studentLname"]." ".$row["studentOname"] ?></td>
                    <td><?php echo $row["title"] ?></td>
                    <td><?php echo $row["programme"] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>  
    </div>
    <?php }else{
        echo "
            <div class=\"body empty\">
                <p>No results were found. Try adding some more data</p>
            </div>";
        }
    ?>
</section>