<?php 
    include_once("auth.php");

    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
    }else{
        //set nav_point session
        $_SESSION["nav_point"] = "enrol";
    }
?>
<section class="section_container">
    <div class="content primary reg_comp">
        <div class="head">
            <h2>
                <?php 
                    $students = decimalIndexArray(fetchData(...[
                        "columns" => ["DISTINCT c.*", "e.enrolDate", "e.enrolCode"],
                        "table" => ["join" => "cssps enrol_table", "alias" => "c e", "on" => "indexNumber indexNumber"],
                        "where" => ["c.schoolID=$user_school_id", "c.current_data=TRUE", "c.enroled=TRUE"],
                        "limit" => 0, "where_binds" => "AND", "order_by" => "e.enrolDate", "asc" => false
                    ]));

                    echo $students ? count($students) : 0;
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
                <?= fetchData("COUNT(indexNumber) AS total", "cssps", 
                    ["enroled=FALSE","schoolID=$user_school_id","current_data=TRUE"], 1, "AND")["total"];
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
    <div class="btn w-full w-full-child" style="max-width: 12rem">
        <button name="submit" value="enrolment" class="request_btn cyan sp-lg">Generate Report</button>
    </div>
</section>
 <?php } ?>

<section id="content" class="table_section">
    <?php if($students): ?>
    <div class="head">
        <div class="form search sm-med-tp" role="form" data-action="<?php echo $url?>/admin/admin/submit.php">
            <div class="flex flex-center-align">
                <label for="search" style="width: 80%">
                    <input type="search" name="search" data-max-break-point="<?= fetchData("COUNT(indexNumber) as total","cssps","schoolID=$user_school_id")["total"] ?>"
                     title="Enter a search here. It could be from any column of the table" placeholder="Search by any value in the table below..."
                     autocomplete="off" style="border: 1px solid lightgrey;" data-search-value="unregister">
                </label>
                <label for="row_display">
                    <input type="number" name="row_display" id="row_display" class="light" value="10" max="100" min="5">
                </label>
            </div>
        </div>
        <div class="head btn no_disp">
            <button data-year="1" data-break-point="10"></button>
        </div>
    </div>
    <div class="body year" id="year1">
        <table class="sm-full">
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
                    <td>Enrolment Code</td>
                    <td>Programme</td>
                    <td>Aggregate</td>
                    <td>Residence Status</td>
                    <td>Date Registered</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $student): ?>
                <tr data-index="<?php echo $student["indexNumber"] ?>" data-register="true">
                    <td><?php echo $student["indexNumber"] ?></td>
                    <td class="lname"><?php echo $student["Lastname"] ?></td>
                    <td class="oname"><?php echo $student["Othernames"] ?></td>
                    <td><?php echo $student["enrolCode"] ?></td>
                    <td><?php echo $student["programme"] ?></td>
                    <td><?php echo $student["aggregate"] ?></td>
                    <td><?php echo $student["boardingStatus"] ?></td>
                    <td><?php echo $student["enrolDate"] ?></td>
                    <td class="flex flex-wrap">
                        <span class="item-event edit cssps">Edit</span>
                        <span class="item-event delete studs">Delete</span>
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
                    <td class="result" colspan="7"><?= count($students) ?> results were returned</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="body empty">
        <p>No student has enroled the system</p>
    </div>
    <?php endif; ?>
</section>

<div id="updateStudent" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php require($rootPath."/admin/admin/page_parts/update_student.php")?>
</div>

<div id="table_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php require($rootPath."/admin/admin/page_parts/table_del.php") ?>
</div>

<script src="<?php echo $url?>/admin/admin/assets/scripts/placement.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/newstudent.min.js" async></script>
<script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/table.min.js?v=<?php echo time()?>" async></script>
<script>
    $(document).ready(function(){
        $(".table_section .head .btn button").click();
    })

    $("input[name=row_display]").change(function(){
        myval = $(this).val();
        $(this).val(myval);
        $(this).parents("#content").children(".head").children(".btn").children("button").attr("data-break-point", myval);
        $(this).parents("#content").find(".navs").children("span").attr("data-break-point", myval);
        $(this).parents("#content").children(".head").children(".btn").children("button").click();
    })
</script>
<?php close_connections() ?>