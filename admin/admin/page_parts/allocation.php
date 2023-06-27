<?php   
    include_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");

    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
    }else{    
        //set nav_point session
        $_SESSION["nav_point"] = "allocation";
    }
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
    <?php $not_allocated_houses = fetchData("COUNT(indexNumber) as total","house_allocation","houseID IS NULL AND schoolID=$user_school_id")["total"];
    if($not_allocated_houses > 0){
    ?>
    <div class="content orange">
        <div class="head">
            <h2>
                <?= $not_allocated_houses ?>
            </h2>
        </div>
        <div class="body">
            <span>Students without House</span>
        </div>
    </div><?php } ?>
</section>

<?php if($_SESSION["real_status"]){?>
<section class="flex flex-wrap flex-center-align"> 
    <div class="btn">
        <button name="submit" value="houses" class="request_btn cyan sp-med">Generate Report</button>
    </div>
    <div class="btn">
        <button onclick="$('#modal_2').removeClass('no_disp')" <?php
            $autoHousePlace = getSchoolDetail($user_school_id, true)["autoHousePlace"];
            if($autoHousePlace)
                echo "disabled";
        ?> class="orange sp-med">Import Allocation List</button>
    </div>
</section>
 <?php } ?>

 <section>
     <div class="display light">
        <p style="text-align: center; padding: 1em">In this section you are allowed to upload students who are to be
        manually uploaded by you. Manual house allocation will have to upload their document via </p>
    </div>
 </section>
 
 <?php if($not_allocated_houses > 0){ ?>
 <section>
     <div class="head" style="align-self: center">
        <h2>Students Not Allocated Houses</h2>
        <div class="btn no_disp">
            <button data-year="1" data-break-point="10"></button>
        </div>
     </div>
     <div class="form search sm-med-tp" role="form" data-action="<?php echo $url?>/admin/admin/submit.php">
        <div class="flex flex-center-align">
            <label for="search" style="width: 80%">
                <input type="search" name="search" data-max-break-point="<?= fetchData("COUNT(indexNumber) as total","cssps","schoolID=$user_school_id")["total"] ?>"
                 title="Enter a search here. It could be from any column of the table" placeholder="Search by any value in the table below..."
                 autocomplete="off" style="border: 1px solid lightgrey;" data-search-value="register">
            </label>
            <label for="row_display">
                <input type="number" name="row_display" id="row_display" class="light" value="10" max="100" min="5">
            </label>
        </div>
     </div>
     <?php 
        $res = $connect->query("SELECT DISTINCT (a.indexNumber), a.studentLname, a.studentOname, c.programme 
            FROM house_allocation a JOIN cssps c 
            ON a.indexNumber = c.indexNumber
            WHERE a.schoolID = $user_school_id AND a.houseID IS NULL");
     ?>
     <div class="body year" id="year1">
        <table class="sm-full">
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Full name</td>
                    <td>Program</td>
                </tr>
            </thead>
            <tbody>
            <?php
                while($row = $res->fetch_assoc()){
            ?>
                <tr data-index="<?php echo $row["indexNumber"] ?>" data-register="true">
                    <td><?php echo $row["indexNumber"] ?></td>
                    <td class="fullname"><?php echo $row["studentLname"]." ".$row["studentOname"] ?></td>
                    <td><?php echo $row["programme"] ?></td>
                    <td class="flex flex-wrap">
                        <span class="item-event edit cssps">Edit</span>
                        <span class="item-event delete cssps">Delete</span>
                    </td>
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
                            <?php if($res->num_rows > 0) : ?>
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
 </section>
 <?php } ?>


<section class="section_container allocation flex-column table_section">
    <div class="head" style="align-self: center">
        <h2>Boarding Students</h2>
        <div class="btn no_disp">
            <button data-year="1" data-break-point="10"></button>
        </div>
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
    <div class="form search sm-med-tp" role="form" data-action="<?php echo $url?>/admin/admin/submit.php">
        <div class="flex flex-center-align">
            <label for="search" style="width: 80%">
                <input type="search" name="search" data-max-break-point="<?= fetchData("COUNT(indexNumber) as total","cssps","schoolID=$user_school_id")["total"] ?>"
                 title="Enter a search here. It could be from any column of the table" placeholder="Search by any value in the table below..."
                 autocomplete="off" style="border: 1px solid lightgrey;" data-search-value="register">
            </label>
            <label for="row_display">
                <input type="number" name="row_display" id="row_display" class="light" value="10" max="100" min="5">
            </label>
        </div>
    </div>
    <div class="body year" id="year1">
        <table class="sm-full">
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
                <tr data-index="<?php echo $row["indexNumber"] ?>" data-register="true">
                    <td><?php echo $row["indexNumber"] ?></td>
                    <td class="fullname"><?php echo $row["studentLname"]." ".$row["studentOname"] ?></td>
                    <td><?php echo $row["title"] ?></td>
                    <td><?php echo $row["programme"] ?></td>
                    <td class="flex flex-wrap">
                        <span class="item-event edit cssps">Edit</span>
                        <span class="item-event delete cssps">Delete</span>
                    </td>
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
                            <?php if($res->num_rows > 0) : ?>
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
    <?php }else{
        echo "
            <div class=\"body empty\">
                <p>No results were found. Try adding some more data</p>
            </div>";
        }
    ?>
</section>

<section class="section_container allocation flex-column table_section">
    <div class="head" style="align-self: center">
        <h2>Day Students</h2>
        <div class="btn no_disp">
            <button data-year="2" data-break-point="10"></button>
        </div>
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
    <div class="form search sm-med-tp" role="form" data-action="<?php echo $url?>/admin/admin/submit.php">
        <div class="flex flex-center-align">
            <label for="search" style="width: 80%">
                <input type="search" name="search" data-max-break-point="<?= fetchData("COUNT(indexNumber) as total","cssps","schoolID=$user_school_id")["total"] ?>"
                 title="Enter a search here. It could be from any column of the table" placeholder="Search by any value in the table below..."
                 autocomplete="off" style="border: 1px solid lightgrey;" data-search-value="register">
            </label>
            <label for="row_display">
                <input type="number" name="row_display" id="row_display" class="light" value="10" max="100" min="5">
            </label>
        </div>
    </div>
    <div class="body year" id="year2">
        <table class="sm-full">
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
                <tr data-index="<?php echo $row["indexNumber"] ?>" data-register="true">
                    <td><?php echo $row["indexNumber"] ?></td>
                    <td class="fullname"><?php echo $row["studentLname"]." ".$row["studentOname"] ?></td>
                    <td><?php echo $row["title"] ?></td>
                    <td><?php echo $row["programme"] ?></td>
                    <td class="flex flex-wrap">
                        <span class="item-event edit cssps">Edit</span>
                        <span class="item-event delete cssps">Delete</span>
                    </td>
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
                            <?php if($res->num_rows > 0) : ?>
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
    <?php }else{
        echo "
            <div class=\"body empty\">
                <p>No results were found. Try adding some more data</p>
            </div>";
        }
    ?>
</section>

<?php if(!$autoHousePlace){ ?>
<div id="modal_2" class="fixed no_disp form_modal_form">
    <form action="<?php echo $url?>/read_excel.php" name="importForm" enctype="multipart/form-data" method="POST">
        <h5>NB:</h5>
        <ol>
            <li>Your file should be a spreadsheet file</li>
            <li>Spreadsheet files with .xls or .xlsx as extensions are acceptable</li>
            <li>Your data should have headings for easy entry into the database</li>
            <li>Make sure you have uploaded all your houses and their required details</li>
            <li>If you could not download the file for manual house allocation, download it <a href="<?php echo $url?>/admin/admin/assets/files/default files/house_allocation.xlsx">here</a></li>
        </ol>
        <br>
        <div class="message_box no_disp">
            <span class="message">Here is a test message</span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <label for="import" class="file_label">
            <span class="label_title">Upload your file here</span>
            <div class="fore_file_display">
                <input type="file" name="import" id="import" accept=".xls, .xlsx">
                <span id="plus">+</span>
                <span id="display_file_name">Choose or drag your file here</span>
            </div>
        </label>
        <div class="flex">
            <label for="submit" class="btn">
                <button type="submit" name="submit" value="upload">Upload</button>
            </label>
            <label for="close" class="btn">
                <button type="reset" name="close">Close</button>
            </label>
        </div>
    </form>
</div>
<?php } ?>

<div id="updateStudent" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/update_student.php")?>
</div>

<div id="table_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/table_del.php") ?>
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
        $(this).parents(".table_section").children(".head").children(".btn").children("button").attr("data-break-point", myval);
        $(this).parents(".table_section").find(".navs").children("span").attr("data-break-point", myval);
        $(this).parents(".table_section").children(".head").children(".btn").children("button").click();
    })
</script>