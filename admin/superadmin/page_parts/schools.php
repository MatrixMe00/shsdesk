<?php include_once("../../../includes/session.php")?>
<section class="section_container">
<div class="content" style="background-color: #007bff;">
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

    <div class="content" style="background-color: #dc3545">
        <div class="head">
            <h2>0</h2>
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
                    <button>Deactivate</button>
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
                <div class="deactivate btn">
                    <button>Deactivate</button>
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

<script>
    $(".menu_bar .name").click(function(){
        sibling = $(this).parent().siblings(".window_space");
        $(sibling).toggleClass("no_disp");
    })
</script>