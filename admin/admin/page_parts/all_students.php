<?php   
    include_once("auth.php");

    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
    }else{    
        //set nav_point session
        $_SESSION["nav_point"] = "students";
    }

    // retrieve all houses
    $houses = decimalIndexArray(fetchData(["id","title"],"houses","schoolId=$user_school_id", 0));
    $year1 = decimalIndexArray(fetchData1(
        ["s.*, p.program_name"],
        ["join" => "students_table program", "alias" => "s p", "on" => "program_id program_id"], 
        ["s.school_id=$user_school_id", "s.studentYear=1"], 0, "AND", "LEFT"
    ));
    $year2 = decimalIndexArray(fetchData1(
        ["s.*, p.program_name"],
        ["join" => "students_table program", "alias" => "s p", "on" => "program_id program_id"], 
        ["s.school_id=$user_school_id", "s.studentYear=2"], 0, "AND", "LEFT"
    ));
    $year3 = decimalIndexArray(fetchData1(
        ["s.*, p.program_name"],
        ["join" => "students_table program", "alias" => "s p", "on" => "program_id program_id"], 
        ["s.school_id=$user_school_id", "s.studentYear=3"], 0, "AND", "LEFT"
    ));
?>

<section class="section_container">
    <div class="content purple">
        <div class="head">
            <h2>
                <?= $y1 = is_array($year1) ? count($year1) : 0 ?>
            </h2>
        </div>
        <div class="body">
            <span>First Years</span>
        </div>
    </div>

    <div class="content purple">
        <div class="head">
            <h2>
                <?= $y2 = is_array($year2) ? count($year2) : 0 ?>
            </h2>
        </div>
        <div class="body">
            <span>Second Years</span>
        </div>
    </div>

    <div class="content purple">
        <div class="head">
            <h2>
                <?= $y3 = is_array($year3) ? count($year3) : 0 ?>
            </h2>
        </div>
        <div class="body">
            <span>Third Years</span>
        </div>
    </div>

    <div class="content secondary">
        <div class="head">
            <h2>
                <?= $y1 + $y2 + $y3 ?>
            </h2>
        </div>
        <div class="body">
            <span>Total Students</span>
        </div>
    </div>
</section>

<?php if(is_array($houses)): ?>
<section>
    <div class="display light">
        <p style="text-align: center; padding: 1em">This section holds data on all students in the system. You can manually 
        add a student using the "Add New Student" button or import a list of placed students using the "Import From Excel" button</p>
    </div>
</section>

<section>
    <div id="action">
        <div class="head">
            <h3>Student Controls</h3>
        </div>
        <div class="body flex-all-center flex-wrap btn w-fit-child w-full gap-sm p-med">
                <button onclick="$('#modal').removeClass('no_disp')" class="cyan">Add New Student</button>
            <?php if($user_details["role"] != 2 && ($user_details["role"] <= 5 || str_contains(strtolower(getRole($user_details["role"])), "admin"))){?>
                <button onclick="$('#lhs .menu .item.active').click()" class="secondary">Refresh</button>
            <?php } ?>
                <button type="button" onclick="$('#modal_2').removeClass('no_disp')" class="teal">Import From Excel</button>
            <?php 
                $res = $connect->query("SELECT COUNT(indexNumber) AS total FROM cssps WHERE schoolID = $user_school_id")->fetch_assoc()["total"];
                // $res = $connect2->query("SELECT COUNT(indexNumber) AS total FROM students_table WHERE school_id = $user_school_id")->fetch_assoc()["total"];
                if(intval($res) > 0){
                    if($user_details["role"] != 2 && ($user_details["role"] <= 5 || str_contains(strtolower(getRole($user_details["role"])), "admin"))){ 
            ?>
                <button id="del_all" title="This clears all third years from the system, and in turn promote all students in the system currently to the next class";
                class="red studs">Promote Students</button>
            <?php   } ?>
                <button type="button" class="" id="addFirstYears">Transfer First Years</button>
            <?php } ?>
        </div>
    </div>
</section>

<section id="students_section" class="table_section">
    <div class="head flex-all-center flex-column gap-md">
        <h2>Student Year</h2>
        <div class="flex flex-wrap wrap-half gap-sm flex-eq wmax-sm">
            <div class="btn w-full sm-unset sp-unset">
                <button class="primary year_btn w-full table_btn" data-year="1" data-break-point="10" data-max-value="<?= $y1 ?>">Year One</button>
            </div>
            <div class="btn w-full sm-unset sp-unset">
                <button class="light year_btn w-full table_btn" data-year="2" data-break-point="10" data-max-value="<?= $y2 ?>">Year Two</button>
            </div>
            <div class="btn w-full sm-unset sp-unset">
                <button class="light year_btn w-full table_btn" data-year="3" data-break-point="10" data-max-value="<?= $y3 ?>">Year Three</button>
            </div>
        </div>
    </div>
    <div class="form sm-lg-tp">
        <label for="search_mul_table" class="flex-column gap-sm">
            <span class="title_label">Search for any data in the table below</span>
            <input type="search" name="search" id="search_mul_table" placeholder="Type your search here..." data-table-parent-id="year" data-active="primary" data-parent-value="1">
        </label>
    </div>
    <div class="body">        
        <?php $i=1; while($i <= 3) : 
            $year = "year$i";
        ?>
        <div id="<?= $year ?>" class="year">
            <table class="full">
                <thead>
                    <tr>
                        <td>Index Number</td>
                        <td>Lastname</td>
                        <td>Othernames</td>
                        <td>House</td>
                        <td>Class</td>
                        <td>Boarding Status</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if(is_array($$year)){
                            foreach($$year as $row){
                    ?>
                    <tr data-index="<?= $row["indexNumber"] ?>">
                        <td class="index"><?= $row["indexNumber"] ?></td>
                        <td class="lname"><?= $row["Lastname"] ?></td>
                        <td class="oname"><?= $row["Othernames"]?></td>
                        <td class="house"><?php 
                            if(($key = array_search($row["houseID"], array_column($houses, "id"))) !== false){
                                echo $houses[$key]["title"];
                            }else{
                                echo "Invalid House Index";
                            }
                        ?></td>
                        <td><?= empty($row["program_name"]) || is_null($row["program_name"]) ? "Not Set" : $row["program_name"] ?></td>
                        <td class="board_stat"><?php echo $row["boardingStatus"]?></td>
                        <td>
                            <span class="item-event edit studs db2">Edit</span>
                            <span class="item-event delete studs db2">Delete</span>
                        </td>
                    </tr>
                    <?php }
                        }else{
                    ?>
                    <tr class="empty">
                        <td colspan="6">No results to show</td>
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
                                <?php if(is_array($$year)) : ?>
                                <div class="navs">
                                    <span class="item-event prev" data-break-point="10">Prev</span>
                                    <span class="item-event next" data-break-point="10">Next</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td colspan="4" class="result">
                            <?= is_array($$year) ? count($$year) : 0 ?> results were returned
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php $i++; endwhile; ?>
    </div>
</section>
<?php else: ?>
<section>
    <p class="txt-al-c sp-xlg">Please upload at least one (1) house in the admission interface to proceed</p>
</section>
<?php endif; ?>

<div id="modal_2" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/file_upload1.php"); ?>
</div>

<div id="table_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/table_del.php") ?>
</div>

<div id="modal" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php eval("?>".file_get_contents($url."/admin/admin/page_parts/newStudent.php?db2=true&sid=$user_school_id"))?>
</div>

<div id="updateStudent" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/update_student.php")?>
</div>

<script src="<?php echo $url?>/admin/admin/assets/scripts/placement.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/newstudent.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/table.min.js?v=<?php echo time()?>" async></script>
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