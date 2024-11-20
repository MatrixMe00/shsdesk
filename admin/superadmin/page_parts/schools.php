<?php include_once("session.php");

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

<section class="page_setup">
    <div class="head">
        <h2>Active Schools</h2>
    </div>
    <div class="body">
        <p>These are the schools which have been activated on the system</p>
    </div>
    <div class="middle">
        <?php
            $res = $connect->query("SELECT * FROM schools WHERE Active = TRUE");

            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
        ?>
        <div class="user_container">
            <div class="top">
                <h3><?php echo $row['schoolName'];?></h3>
            </div>
            <div class="desc flex flex-wrap">
                <div class="school_admin"><?php echo $row['techName']?></div>
                <div class="head_master"><?php echo $row['headName']?></div>
                <?php 
                    $reg = fetchData("COUNT(indexNumber) AS total","cssps","schoolID=".$row["id"]." AND enroled=TRUE AND current_data=TRUE", 0)["total"];
                    $unreg = fetchData("COUNT(indexNumber) AS total","cssps","schoolID=".$row["id"]." AND enroled=FALSE AND current_data=TRUE", 0)["total"];
                ?>
                <div class="reg_studs">Registered Students: <?php echo $reg?></div>
                <div class="unreg_studs">Unregistered Students: <?php echo $unreg?></div>
            </div>
            <?php if($admin_access > 3): ?>
            <div class="foot flex flex-wrap">
                <div class="item-event edit" data-school-id="<?php echo $row['id'] ?>">Edit</div>
                <div class="item-event deactivate" data-school-id="<?php echo $row['id']?>">Deactivate</div>
                <div class="item-event delete" data-school-id="<?php echo $row['id'] ?>">Delete</div>
                <div class="item-event clear" data-school-id="<?php echo $row['id'] ?>">Clear Records</div>
            </div>
            <?php endif; ?>
        </div>
        <?php }
            }else{ 
                echo '<div class="school_container empty" style="text-align: center;">
                <p>No active schools were found.</p>
                </div>';
        } ?>
    </div>
</section>

<section class="page_setup">
    <div class="head">
        <h2>Deactivated Schools</h2>
    </div>
    <div class="body">
        <p>These are the schools which have been deactivated on the system</p>
    </div>
    <div class="middle">
        <?php
            $res = $connect->query("SELECT * FROM schools WHERE Active = FALSE");

            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
        ?>
        <div class="user_container">
            <div class="top">
                <h3><?php echo $row['schoolName'];?></h3>
            </div>
            <div class="desc flex flex-wrap">
                <div class="school_admin"><?php echo $row['techName']?></div>
                <div class="head_master"><?php echo $row['headName']?></div>
                <?php 
                    $reg = fetchData("COUNT(indexNumber) AS total","cssps","schoolID=".$row["id"]." AND enroled=TRUE AND current_data = TRUE", 0)["total"];
                    $unreg = fetchData("COUNT(indexNumber) AS total","cssps","schoolID=".$row["id"]." AND enroled=FALSE AND current_data = TRUE", 0)["total"];
                ?>
                <div class="reg_studs">Registered Students: <?php echo $reg?></div>
                <div class="unreg_studs">Unregistered Students: <?php echo $unreg?></div>
            </div>
            <?php if($admin_access > 3): ?>
            <div class="foot flex flex-wrap">
                <div class="item-event edit" data-school-id="<?php echo $row['id'] ?>">Edit</div>
                <div class="item-event activate" data-school-id="<?php echo $row['id']?>">Activate</div>
                <div class="item-event delete" data-school-id="<?php echo $row['id'] ?>">Delete</div>
                <div class="item-event clear" data-school-id="<?php echo $row['id'] ?>">Clear Records</div>
            </div>
            <?php endif; ?>
        </div>
        <?php }
            }else{ 
                echo '<div class="school_container empty" style="text-align: center;">
                <p>No deactived schools were found.</p>
                </div>';
        } ?>
    </div>
</section>

<!--Edit form-->
<div id="edit_modal" class="fixed flex flex-center-content flex-center-align form_modal_box flex-column no_disp">
    <span class="no_disp" id="school_select"></span>
    <div id="wrapper">
        <div class="tabs flex flex-center-align flex-center-content">
            <span data-det-id="details" class="tab_btn active">Edit Details</span>
            <span data-det-id="add_student" class="tab_btn">Add Student</span>
            <span data-det-id="houses" class="tab_btn">Edit Houses</span>
            <?php if($user_details["role"] == 1){ ?>
            <span class="tab_btn" data-det-id="alloc">Allocation</span>
            <?php } ?>
        </div>
        <div class="edit_detail">
            <p id="details">Use the form below to update the details about a school</p>
            <p id="add_student" class="no_disp">Use this menu to add a new student to the records of a school</p>
            <p id="houses" class="no_disp">This menu displays a list of houses. Select a house to edit it or add a new house</p>
            <p id="alloc" class="no_disp">This menu shows a list of allocated houses for the students registered to the system</p>
        </div>
        <div class="content">
            <div class="content_box details"></div>
            <div class="content_box add_student no_disp"></div>
            <div class="content_box houses no_disp"></div>
            <div class="content_box alloc no_disp"></div>
        </div>
        <div class="foot flex">
            <div class="btn close">
                <button class="red">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo $url?>/admin/superadmin/assets/scripts/schools.min.js?v=<?php echo time()?>" async></script>
<?php close_connections() ?>