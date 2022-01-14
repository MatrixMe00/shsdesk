<?php include_once("../../../includes/session.php");

    //set nav_point session
    $_SESSION["nav_point"] = "Placement";
?>
<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber 
                    FROM cssps 
                    WHERE schoolID = $user_school_id");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Placed by CSSPS</span>
        </div>
    </div>

    <div class="content teal">
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
            <span>Completed Online Admission Form</span>
        </div>
    </div>

    <div class="content danger">
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
            <span>Yet to Perform Online Admission</span>
        </div>
    </div>
</section>

<section id="placement_search">
    <div id="action">
        <div class="head">
            <h2>Placement Actions</h2>
        </div>
        <div class="body flex flex-wrap">
            <div class="btn">
                <button onclick="$('#modal').removeClass('no_disp')">Add New Student</button>
            </div>
            <div class="btn">
                <button onclick="location.reload()">Refresh</button>
            </div>
            <div class="btn">
                <button type="button" onclick="$('#modal_2').removeClass('no_disp')">Import From Excel</button>
            </div>
        </div>
    </div>
    <div class="display secondary">
        <p style="text-align: center;">Click on a row to make changes to student details [Test Version]</p>
    </div>
    <div class="display">
        <div class="title_bar flex flex-space-content flex-center-align teal">
            <div id="title">Registered Students</div>
            <div id="close">
                <span></span>
                <span></span>
            </div>
        </div>
        <div id="content">
            <?php 
                $sql = "SELECT c.*, e.enrolCode 
                    FROM cssps c JOIN enrol_table e
                    ON c.indexNumber = e.indexNumber
                    WHERE c.enroled=TRUE AND c.schoolID = $user_school_id";
                $res = $connect->query($sql);

                if($res->num_rows > 0){
            ?>
            <div class="form search" role="form" data-action="<?php echo $url?>/admin/admin/submit.php">
                <div class="flex flex-center-align">
                    <label for="search" style="width: 80%">
                        <input type="search" name="search"
                         title="Search a name or index number here" placeholder="Search by index number or name...">
                    </label>
                    
                    <div class="btn">
                        <button name="search_submit" value="register">Search</button>
                    </div>
                </div>
            </div>
            <div class="body">
                <table>
                    <thead>
                        <tr>
                            <td>Index Number</td>
                            <td>Enrol Code</td>
                            <td>Fullname</td>
                            <td>Boarding Status</td>
                            <td>Program</td>
                            <td>Gender</td>
                            <td>Track Id</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $res->fetch_assoc()){?>
                        <tr data-index="<?php echo $row["indexNumber"] ?>" data-register="true">
                            <td><?php echo $row["indexNumber"] ?></td>
                            <td><?php echo $row["enrolCode"] ?></td>
                            <td><?php echo $row["Lastname"]." ".$row["Othernames"] ?></td>
                            <td><?php echo $row["boardingStatus"] ?></td>
                            <td><?php echo $row["programme"] ?></td>
                            <td><?php echo $row["Gender"] ?></td>
                            <td><?php echo $row["trackID"] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot class="no_disp">
                        <tr>
                            <td colspan="6">No Results returned</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php }else{ ?>
            <div class="empty body" style="margin-top: 0.3em; padding-top: 1.3em; padding-bottom: 1.3em">
                Nothing to show. Click the "Refresh" button to refresh the page
            </div>
            <?php } ?> 
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
                $sql = "SELECT * FROM cssps WHERE enroled=FALSE AND schoolID = $user_school_id";
                $res = $connect->query($sql);

                if($res->num_rows > 0){
            ?>
            <div class="form search" role="form" data-action="<?php echo $url?>/admin/admin/submit.php">
                <div class="flex flex-center-align">
                    <label for="search" style="width: 80%">
                        <input type="search" name="search"
                         title="Search a name or index number here" placeholder="Search by index number or name...">
                    </label>
                    
                    <div class="btn">
                        <button name="search_submit" value="unregister">Search</button>
                    </div>
                </div>
            </div>
            <div class="body">
                <table>
                    <thead>
                        <tr>
                            <td>Index Number</td>
                            <td>Fullname</td>
                            <td>Boarding Status</td>
                            <td>Program</td>
                            <td>Gender</td>
                            <td>Track Id</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $res->fetch_assoc()){?>
                        <tr data-index="<?php echo $row["indexNumber"] ?>" data-register="false">
                            <td><?php echo $row["indexNumber"] ?></td>
                            <td><?php echo $row["Lastname"]." ".$row["Othernames"] ?></td>
                            <td><?php echo $row["boardingStatus"] ?></td>
                            <td><?php echo $row["programme"] ?></td>
                            <td><?php echo $row["Gender"] ?></td>
                            <td><?php echo $row["trackID"] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot class="no_disp">
                        <tr>
                            <td colspan="6">No Results returned</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php }else{ ?>
            <div class="empty body" style="margin-top: 0.3em; padding-top: 1.3em; padding-bottom: 1.3em">
                Nothing to show. Click the "Refresh" button to refresh this box
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<div id="modal_2" class="fixed no_disp form_modal_form">
    <form action="<?php echo $url?>/read_excel.php" name="importForm" enctype="multipart/form-data" method="POST">
        <h5>NB:</h5>
        <ol>
            <li>Your file should be a spreadsheet file</li>
            <li>Spreadsheet files with .xls or .xlsx as extensions are acceptable</li>
            <li>Your data should have headings for easy entry into the database</li>
            <li>Make sure you have uploaded all your houses and their required details</li>
            <li>If you could not download the file for manual house allocation, download it <a href="<?php echo $url?>/admin/admin/assets/files/default files/house_allocation.xlsx">here</a></li>
            <li>If you do not have the default spreadsheet file for upload, please click <a href="<?php echo $url?>/admin/admin/assets/files/default files/enrolment_template.xlsx">here</a> to download</li>
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

<div id="modal" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/newStudent.php")?>
</div>

<div id="updateStudent" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php @include_once($rootPath."/admin/admin/page_parts/update_student.php")?>
</div>

<script src="<?php echo $url?>/admin/admin/assets/scripts/placement.js?v=<?php echo time()?>"></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/newstudent.js"></script>
<script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>"></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/table.js?v=<?php echo time()?>"></script>