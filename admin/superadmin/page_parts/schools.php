<?php include_once("../../../includes/session.php");

    //add nav point session
    $_SESSION["nav_point"] = "Schools";
?>
<section class="section_container">
<div class="content primary">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT id FROM schools WHERE Active = TRUE");
                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Active Schools</span>
        </div>
    </div>

    <div class="content danger">
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

<section>
    <div class="head">
        <h2>Active Schools</h2>
        <p>These are the schools which have been activated on the system</p>
    </div>
    <div class="body">
        <?php
            $res = $connect->query("SELECT * FROM schools WHERE Active = TRUE");

            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
        ?>
        <div class="school_container">
            <div class="menu_bar flex flex-center-align flex-space-content">
                <div class="name">
                    <h3><?php echo $row['schoolName'];?></h3>
                </div>
                <div class="deactivate btn">
                    <button data-school-id="<?php echo $row['id']?>">Deactivate</button>
                </div>
            </div>
            <div class="window_space no_disp">
                <div class="headName">
                    <span>Head's Name: <?php echo $row['headName']?></span>
                </div>
                <div class="techName">
                    <span>IT Personnel Name: <?php echo $row['techName']?></span>
                </div>
            </div>
        </div>
        <?php }
            }else{ 
                echo '<div class="school_container empty" style="text-align: center;">
                <p>No active schools were found.</p>
                </div>';
        } ?>
    </div>
</section>

<section>
    <div class="head">
        <h2>Non-Active Schools</h2>
        <p>These are the schools which have been deactivated on the system</p>
    </div>
    <div class="body">
        <?php
            $res = $connect->query("SELECT * FROM schools WHERE Active = FALSE");

            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
        ?>
        <div class="school_container">
            <div class="menu_bar flex flex-center-align flex-space-content">
                <div class="name">
                    <h3><?php echo $row['schoolName'];?></h3>
                </div>
                <div class="activate btn">
                    <button data-school-id="<?php echo $row['id'] ?>">Activate</button>
                </div>
            </div>
            <div class="window_space no_disp">
                <div class="headName">
                    <span>Head's Name: <?php echo $row['headName']?></span>
                </div>
                <div class="techName">
                    <span>IT Personnel Name: <?php echo $row['techName']?></span>
                </div>
            </div>
        </div>
        <?php }
            }else{ 
                echo '<div class="school_container empty" style="text-align: center;">
                <p>No deactived schools were found.</p>
                </div>';
        } ?>
    </div>
</section>

<script src="<?php echo $url?>/admin/superadmin/admin/scripts/schools.js"></script>