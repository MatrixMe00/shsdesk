<?php
    include_once("auth.php");

if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
    $user_school_id = $_REQUEST["school_id"];
    $user_details = getUserDetails($_REQUEST["user_id"]);
}else{
    //set nav_point session
    $_SESSION["nav_point"] = "cssps";
    }
?>
<section class="section_container">
    <div class="content primary cssps">
        <div class="head">
            <h2>
                <?= $students_placed = (int) fetchData("COUNT(indexNumber) AS total", "cssps", ["schoolID=$user_school_id","current_data=TRUE"], where_binds: "AND")["total"]; ?>
            </h2>
        </div>
        <div class="body">
            <span>Placed by CSSPS</span>
        </div>
    </div>

    <div class="content teal reg_comp">
        <div class="head">
            <h2>
                <?= $students_enroled = (int) fetchData("COUNT(indexNumber) AS total", "cssps", ["schoolID=$user_school_id","current_data=TRUE", "enroled=TRUE"], where_binds: "AND")["total"]; ?>
            </h2>
        </div>
        <div class="body">
            <span>Completed Online Admission Form</span>
        </div>
    </div>

    <div class="content danger reg_uncomp">
        <div class="head">
            <h2>
                <?= $students_placed - $students_enroled ?>
            </h2>
        </div>
        <div class="body">
            <span>Yet to Perform Online Admission</span>
        </div>
    </div>
</section>

<section>
    <div class="display light">
        <p style="text-align: center; padding: 1em">This section holds data on students who have been placed into your school. You can manually 
        add a student using the "Add New Student" button or import a list of placed students using the "Import From Excel" button</p>
    </div>
</section>

<section>
    <div id="action">
        <div class="head txt-al-c">
            <h3>Placement Actions</h3>
        </div>
        <div class="body btn flex flex-wrap wrap-half w-full-child w-full wmax-med sm-auto gap-sm p-med flex-eq">
                <button onclick="$('#modal').removeClass('no_disp')" class="cyan">Add New Student</button>
            <?php if($user_details["role"] != 2 && ($user_details["role"] <= 5 || str_contains(strtolower(getRole($user_details["role"])), "admin"))){?>
                <button onclick="$('#lhs .menu .item.active').click()" class="secondary">Refresh</button>
            <?php } ?>
                <button type="button" onclick="$('#modal_2').removeClass('no_disp')" class="teal">Import From Excel</button>
                <button type="button" onclick="$('#lhs .admission_settings').click()" class="orange">New Admission Phase</button>
            <?php 
                $res = $connect->query("SELECT COUNT(indexNumber) AS total FROM cssps WHERE schoolID = $user_school_id")->fetch_assoc()["total"];
                if(intval($res) > 0){
            ?>
            <?php if($user_details["role"] != 2 && ($user_details["role"] <= 5 || str_contains(strtolower(getRole($user_details["role"])), "admin"))){ ?>
                <button id="del_all" title="This deletes all saved data from your records. Data would need to be reuploaded again"
                class="red">Delete All Record
            </div>
            <?php } ?>
            <?php } ?>
        </div>
    </div>
</section>

<section id="placement_search" class="table_section">   
    <div class="display">
        <div class="title_bar flex flex-space-content flex-center-align teal">
            <div id="title">Registered Students [Last 20 Registration]</div>
            <div id="close">
                <span></span>
                <span></span>
            </div>
        </div>
        <div id="content">
            <?php 
                $students = decimalIndexArray(fetchData(["c.*","e.enrolCode"],[
                    "join" => "cssps enrol_table", "on" => "indexNumber indexNumber", "alias" => "c e"
                ], ["c.enroled=TRUE","c.schoolID=$user_school_id", "c.current_data=TRUE"],
                order_by: "e.enrolDate", asc: false, limit: 20, where_binds: "AND"));

                if($students):
            ?>
            <div class="form search sm-med-tp" role="form" data-action="<?php echo $url?>/admin/admin/submit.php">
                <div class="flex flex-center-align">
                    <label for="search" style="width: 80%">
                        <input type="search" name="search" data-max-break-point="<?= fetchData("COUNT(indexNumber) as total","cssps","schoolID=$user_school_id")["total"] ?>"
                         title="Enter a search here. It could be from any column of the table" placeholder="Search by any value in the table below..."
                         autocomplete="off" style="border: 1px solid lightgrey;" data-search-value="register">
                    </label>
                    <label for="row_display">
                        <input type="number" placeholder="Number of rows per view" name="row_display" id="row_display" class="light" value="10" max="100" min="5">
                    </label>
                </div>
            </div>
            <div class="head no_disp">
                <div class="btn">
                    <button data-year="1" data-break-point="10"></button>
                </div>
            </div>
            <div class="body year" id="year1">
                <table class="sm-full">
                    <thead>
                        <tr>
                            <td>Index Number</td>
                            <td>Enrol Code</td>
                            <td>Fullname</td>
                            <td>Boarding Status</td>
                            <td>Program</td>
                            <td>Aggregate</td>
                            <td>Gender</td>
                            <td>Track Id</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $student): ?>
                        <tr data-index="<?php echo $student["indexNumber"] ?>" data-register="true">
                            <td><?php echo $student["indexNumber"] ?></td>
                            <td><?php echo $student["enrolCode"] ?></td>
                            <td class="fullname"><?php echo $student["Lastname"]." ".$student["Othernames"] ?></td>
                            <td><?php echo $student["boardingStatus"] ?></td>
                            <td><?php echo $student["programme"] ?></td>
                            <td><?php echo $student["aggregate"]?></td>
                            <td><?php echo $student["Gender"] ?></td>
                            <td><?php echo $student["trackID"] ?></td>
                            <td class="flex flex-wrap">
                                <span class="item-event edit cssps">Edit</span>
                                <span class="item-event delete cssps">Delete</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="pages" colspan="2">
                                <div class="flex">
                                    <div class="pagination">
                                        Page <span class="current"></span>  <strong>of</strong> <span class="last"></span>
                                    </div>
                                    <?php if($students) : ?>
                                    <div class="navs">
                                        <span class="item-event prev" data-break-point="10">Prev</span>
                                        <span class="item-event next" data-break-point="10">Next</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="result" colspan="7"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <div class="empty body" style="margin-top: 0.3em; padding-top: 1.3em; padding-bottom: 1.3em">
                Nothing to show. Click the "Refresh" button to refresh the page
            </div>
            <?php endif; ?> 
        </div>
    </div>

    <div class="display">
        <div class="title_bar flex flex-space-content flex-center-align red">
            <div id="title">Unregistered Students</div>
            <div id="close">
                <span></span>
                <span></span>
            </div>
        </div>
        <div id="content">
            <?php
                $students = decimalIndexArray(fetchData("*", "cssps", ["enroled=FALSE", "schoolID=$user_school_id", "current_data=TRUE"], 
                    0, "AND", order_by: "indexNumber"));                

                if($students):
            ?>
            <div class="form search sm-med-tp" role="form" data-action="<?php echo $url?>/admin/admin/submit.php">
                <div class="flex flex-center-align">
                    <label for="search" style="width: 80%">
                        <input type="search" name="search" data-max-break-point="<?= fetchData("COUNT(indexNumber) as total","cssps","schoolID=$user_school_id")["total"] ?>"
                         title="Enter a search here. It could be from any column of the table" placeholder="Search by any value in the table below..."
                         autocomplete="off" style="border: 1px solid lightgrey;" data-search-value="unregister">
                    </label>
                    <label for="row_display">
                        <input type="number" placeholder="Number of rows per view" name="row_display" id="row_display" class="light" value="10" max="100" min="5">
                    </label>
                </div>
            </div>
            <div class="head no_disp">
                <div class="btn">
                    <button data-year="2" data-break-point="10"></button>
                </div>
            </div>
            <div class="body year" id="year2">
                <table class="sm-full">
                    <thead>
                        <tr>
                            <td>Index Number</td>
                            <td>Fullname</td>
                            <td>Boarding Status</td>
                            <td>Program</td>
                            <td>Aggregate</td>
                            <td>Gender</td>
                            <td>Track Id</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $student):?>
                        <tr data-index="<?php echo $student["indexNumber"] ?>" data-register="false">
                            <td><?php echo $student["indexNumber"] ?></td>
                            <td class="fullname"><?php echo $student["Lastname"]." ".$student["Othernames"] ?></td>
                            <td><?php echo $student["boardingStatus"] ?></td>
                            <td><?php echo $student["programme"] ?></td>
                            <td><?php echo $student["aggregate"]?></td>
                            <td><?php echo $student["Gender"] ?></td>
                            <td><?php echo $student["trackID"] ?></td>
                            <td class="flex flex-wrap">
                                <span class="item-event edit cssps">Edit</span>
                                <span class="item-event delete cssps">Delete</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="pages" colspan="2">
                                <div class="flex">
                                    <div class="pagination">
                                        Page <span class="current"></span>  <strong>of</strong> <span class="last"></span>
                                    </div>
                                    <?php if($students) : ?>
                                    <div class="navs">
                                        <span class="item-event prev" data-break-point="10">Prev</span>
                                        <span class="item-event next" data-break-point="10">Next</span>
                                    </div>
                                    <?php endif; ?>
                                </div>                                
                            </td>
                            <td class="result" colspan="7"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <div class="empty body" style="margin-top: 0.3em; padding-top: 1.3em; padding-bottom: 1.3em">
                Nothing to show. Click the "Refresh" button to refresh this box
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<div id="modal_2" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/file_upload.php"); ?>
</div>

<div id="table_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/table_del.php"); ?>
</div>

<div id="modal" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/newStudent.php")?>
</div>

<div id="updateStudent" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/update_student.php")?>
</div>

<?php 
    //choose the details to show when a student clicks
    if(isset($_REQUEST["school_id"])){
?>
<script src="<?php echo $url?>/admin/admin/assets/scripts/calledJS/placement.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/calledJS/newstudent.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/calledJS/general.min.js?v=<?php echo time()?>" async></script>
<?php }else{ ?>
<script src="<?php echo $url?>/admin/admin/assets/scripts/placement.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/newstudent.min.js?v=<?php echo time()?>" async></script>
<?php } ?>
<script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/table.min.js?v=<?php echo time()?>" async></script>
<script>
    $(document).ready(function(){
        $(".table_section .head .btn button").click();
    })

    $("input[name=row_display]").change(function(){
        myval = $(this).val();
        $(this).val(myval);
        $(this).parents(".display").children("#content").children(".head").children(".btn").children("button").attr("data-break-point", myval);
        $(this).parents(".display").find(".navs").children("span").attr("data-break-point", myval);
        $(this).parents(".display").children("#content").children(".head").children(".btn").children("button").click();
    })
</script>
<?php close_connections() ?>