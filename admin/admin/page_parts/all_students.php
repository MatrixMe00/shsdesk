<?php   
    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
        
        include_once("../../includes/session.php");
    }else{
        include_once("../../../includes/session.php");
    
        //set nav_point session
        $_SESSION["nav_point"] = "students";
    }
?>

<section class="section_container">
    <div class="content purple">
        <div class="head">
            <h2>
                <?php
                $head_total = 0;
                $res = $connect2->query("SELECT indexNumber FROM students_table WHERE school_id = $user_school_id AND studentYear=1");

                echo $res->num_rows;
                $head_total += $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>First Years</span>
        </div>
    </div>

    <div class="content purple">
        <div class="head">
            <h2>
                <?php
                $res = $connect2->query("SELECT indexNumber FROM students_table WHERE school_id = $user_school_id AND studentYear=2");

                echo $res->num_rows;
                $head_total += $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Second Years</span>
        </div>
    </div>

    <div class="content purple">
        <div class="head">
            <h2>
                <?php
                $res = $connect2->query("SELECT indexNumber FROM students_table WHERE school_id = $user_school_id AND studentYear=3");

                echo $res->num_rows;
                $head_total += $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Third Years</span>
        </div>
    </div>

    <div class="content secondary">
        <div class="head">
            <h2>
                <?php
                echo $head_total;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Total Students</span>
        </div>
    </div>
</section>

<section>
    <div class="display light">
        <p style="text-align: center; padding: 1em">This section holds data on all students in the system. You can manually 
        add a student using the "Add New Student" button or import a list of placed students using the "Import From Excel" button</p>
    </div>
</section>

<section>
    <div id="action">
        <div class="head">
            <h2>Student Controls</h2>
        </div>
        <div class="body flex flex-wrap">
            <div class="btn">
                <button onclick="$('#modal').removeClass('no_disp')" class="cyan">Add New Student</button>
            </div>
            <?php if($user_details["role"] != 2 && $user_details["role"] <= 5){?>
            <div class="btn">
                <button onclick="$('#lhs .menu .item.active').click()" class="secondary">Refresh</button>
            </div>
            <?php } ?>
            <div class="btn">
                <button type="button" onclick="$('#modal_2').removeClass('no_disp')" class="teal">Import From Excel</button>
            </div>
            <?php 
                $res = $connect->query("SELECT COUNT(indexNumber) AS total FROM cssps WHERE schoolID = $user_school_id")->fetch_assoc()["total"];
                // $res = $connect2->query("SELECT COUNT(indexNumber) AS total FROM students_table WHERE school_id = $user_school_id")->fetch_assoc()["total"];
                if(intval($res) > 0){
                    if($user_details["role"] != 2 && $user_details["role"] <= 5){ 
            ?>
            <div class="btn">
                <button id="del_all" title="This clears all third years from the system, and in turn promote all students in the system currently to the next class";
                class="red studs">Clean Records</button>
            </div>
            <?php   } ?>
            <div class="btn">
                <button type="button" class="" id="addFirstYears">Transfer First Years</button>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<section id="students_section" class="table_section">
    <div class="head">
        <h2>Student Year</h2>
        <div class="flex">
            <div class="btn">
                <button class="primary year_btn" data-year="1" data-break-point="10">Year One</button>
            </div>
            <div class="btn">
                <button class="light year_btn" data-year="2" data-break-point="10">Year Two</button>
            </div>
            <div class="btn">
                <button class="light year_btn" data-year="3" data-break-point="10">Year Three</button>
            </div>
        </div>
    </div>
    <div class="body">
        <?php $i=1; while($i <= 3) : ?>
        <div id="year<?= $i ?>" class="year">
            <table class="full">
                <thead>
                    <tr>
                        <td>Index Number</td>
                        <td>Lastname</td>
                        <td>Othernames</td>
                        <td>House</td>
                        <td>Boarding Status</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $query = $connect2->query("SELECT * FROM students_table WHERE school_id = $user_school_id AND studentYear = $i");
                        if($query->num_rows > 0){
                            while($row=$query->fetch_assoc()){
                    ?>
                    <tr data-index="<?php echo $row["indexNumber"] ?>">
                        <td class="index"><?php echo $row["indexNumber"] ?></td>
                        <td class="lname"><?php echo $row["Lastname"] ?></td>
                        <td class="oname"><?php echo $row["Othernames"]?></td>
                        <td class="house"><?php echo fetchData("title", "houses", "id=".$row["houseID"])["title"]?></td>
                        <td class="board_stat"><?php echo $row["boardingStatus"]?></td>
                        <td>
                            <span class="item-event edit studs">Edit</span>
                            <span class="item-event delete studs">Delete</span>
                        </td>
                    </tr>
                    <?php }
                        }else{
                    ?>
                    <tr class="empty">
                        <td colspan="5">No results to show</td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="pages" colspan="2">
                            <div class="flex">
                                <div class="pagination">
                                    Page <span class="current"></span>  <strong>of</strong> <span class="last"></span>
                                </div>
                                <?php if($query->num_rows > 0) : ?>
                                <div class="navs">
                                    <span class="item-event prev" data-break-point="10">Prev</span>
                                    <span class="item-event next" data-break-point="10">Next</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td colspan="3">
                            <?= $query->num_rows ?> results were returned
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php $i++; endwhile; ?>
    </div>
</section>

<div id="modal_2" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/file_upload1.php"); ?>
</div>

<div id="table_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/table_del.php") ?>
</div>

<div id="modal" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/newStudent.php")?>
</div>

<div id="updateStudent" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/update_student.php")?>
</div>

<script src="<?php echo $url?>/admin/admin/assets/scripts/placement.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/newstudent.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/table.js?v=<?php echo time()?>" async></script>
<script>
    $(document).ready(function(){
        $("#students_section .head .btn:first-child button").click();
    })

    $("#addFirstYears").click(function(){
        item_id = "all";
        fullname = "all records".toUpperCase(), message = "";

        //display yes no modal box
        $("#table_del").removeClass("no_disp");

        message = "This will transfer data of your enroled first years to be prepared for other management features on the system<br>" + 
                "Do you want to proceed?";
        
        $("form[name=table_yes_no_form] input[name=addFirstYears]").val("true");

        $("#table_del p#warning_content").html(message);

        //fill form with needed details
        $("#table_del input[name=indexNumber]").val(item_id);
    })
</script>