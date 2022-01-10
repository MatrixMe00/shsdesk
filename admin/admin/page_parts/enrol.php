<?php include_once("../../../includes/session.php");

    //set nav_point session
    $_SESSION["nav_point"] = "Enrol";
?>
<section class="section_container">
    <div class="content primary">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber 
                    FROM cssps 
                    WHERE enroled = TRUE AND schoolID = $user_school_id");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Enroled Online on System</span>
        </div>
    </div>

    <div class="content secondary">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber 
                    FROM cssps 
                    WHERE enroled = FALSE AND schoolID = $user_school_id");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Not Enroled Online</span>
        </div>
    </div>
</section>

<section>
    <?php 
        $sql = "SELECT DISTINCT c.*, e.enrolDate, e.enrolCode
            FROM cssps c JOIN enrol_table e
            ON c.indexNumber = e.indexNumber
            WHERE c.schoolID = $user_school_id AND e.shsID = $user_school_id AND c.enroled = TRUE";

        $res = $connect->query($sql);

        if($res->num_rows > 0){
    ?>
    <div class="head">
        <div class="btn">
            <button name="submit" value="request_enrol" class="request_btn">Request Report</button>
        </div>
        <div class="form search" role="form" data-action="<?php echo $url?>/admin/admin/submit.php">
            <div class="flex flex-center-align">
                <label for="search" style="width: 80%">
                    <input type="search" name="search" id="search"
                     title="Search a name or index number here" placeholder="Search by index number or name...">
                </label>
                    
                <div class="btn">
                    <button name="search_submit" value="enrol_register">Search</button>
                </div>
            </div>
        </div>
    </div>
    <div class="body">
        <table>
            <tfoot>
                <tr>
                    <td colspan="7"></td>
                </tr>
            </tfoot>
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Lastname</td>
                    <td>Othernames</td>
                    <td>Enrolement Code</td>
                    <td>Programme</td>
                    <td>Residence Status</td>
                    <td>Date Registered</td>
                </tr>
            </thead>
            <tbody>
                <?php while($row=$res->fetch_assoc()){ ?>
                <tr>
                    <td><?php echo $row["indexNumber"] ?></td>
                    <td><?php echo $row["Lastname"] ?></td>
                    <td><?php echo $row["Othernames"] ?></td>
                    <td><?php echo $row["enrolCode"] ?></td>
                    <td><?php echo $row["programme"] ?></td>
                    <td><?php echo $row["boardingStatus"] ?></td>
                    <td><?php echo $row["enrolDate"] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php }else{ ?>
    <div class="body empty">
        <p>No student has enroled the system</p>
    </div>
    <?php } ?>
</section>

<script src="<?php echo $url?>/admin/admin/assets/scripts/placement.js?v=<?php echo time()?>"></script>