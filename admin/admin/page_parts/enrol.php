<?php include_once("../../../includes/session.php");

    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
    }else{
        //set nav_point session
        $_SESSION["nav_point"] = "Enrol";
    }
?>
<section class="section_container">
    <div class="content primary reg_comp">
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

    <div class="content secondary reg_uncomp">
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

<?php if(isset($_SESSION["real_status"]) && $_SESSION["real_status"]){?>
<section class="flex flex-wrap flex-center-align"> 
    <div class="btn">
        <button name="submit" value="enrolment" class="request_btn cyan">Generate Report</button>
    </div>
</section>
 <?php } ?>

<section id="content">
    <?php 
        $sql = "SELECT DISTINCT c.*, e.enrolDate, e.enrolCode
            FROM cssps c JOIN enrol_table e
            ON c.indexNumber = e.indexNumber
            WHERE c.schoolID = $user_school_id AND e.shsID = $user_school_id AND c.enroled = TRUE";

        $res = $connect->query($sql);

        if($res->num_rows > 0){
    ?>
    <div class="head">
        <div class="form search" role="form" data-action="<?php echo $url?>/admin/admin/submit.php">
            <div class="flex flex-center-align">
                <label for="search" style="width: 80%">
                    <input type="search" name="search"
                    title="Enter a search here. It could be from any column of the table" placeholder="Search by any value in the table below..."
                    autocomplete="off">
                </label>
                   
                <div class="btn">
                    <button name="search_submit" value="register">Search</button>
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
                <tr data-index="<?php echo $row["indexNumber"] ?>" data-register="true">
                    <td><?php echo $row["indexNumber"] ?></td>
                    <td><?php echo $row["Lastname"] ?></td>
                    <td><?php echo $row["Othernames"] ?></td>
                    <td><?php echo $row["enrolCode"] ?></td>
                    <td><?php echo $row["programme"] ?></td>
                    <td><?php echo $row["boardingStatus"] ?></td>
                    <td><?php echo $row["enrolDate"] ?></td>
                    <td class="flex flex-wrap">
                        <span class="item-event edit">Edit</span>
                        <span class="item-event delete">Delete</span>
                    </td>
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

<div id="updateStudent" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/update_student.php")?>
</div>

<div id="table_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <div class="yes_no_container">
        <div class="body">
            <p id="warning_content">Do you want to delete?</p>
        </div>

        <form action="<?php echo $url?>/admin/admin/submit.php" class="no_disp" name="table_yes_no_form" id="table_yes_no_form">
            <input type="hidden" name="indexNumber">
            <input type="hidden" name="school_id" value="<?php echo $user_school_id?>">
            <input type="hidden" name="submit" value="table_yes_no_submit">
        </form>

        <div class="foot btn flex flex-center-content flex-center-align">
            <button type="button" name="yes_button" class="success" onclick="$('#table_yes_no_form').submit();">Yes</button>
            <button type="button" name="no_button" class="red" onclick="$('#table_del').addClass('no_disp')">No</button>
        </div>
    </div>
</div>

<script src="<?php echo $url?>/admin/admin/assets/scripts/placement.js?v=<?php echo time()?>"></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/newstudent.js"></script>
<script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>"></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/table.js?v=<?php echo time()?>"></script>