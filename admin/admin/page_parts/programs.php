<?php 
    include_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");

    //set nav_point session
    $_SESSION["nav_point"] = "programs";

    //retrieve programs
    $programs = decimalIndexArray(fetchData1("*","program","school_id=$user_school_id", 0));
    $resultPending = decimalIndexArray(fetchData1(
        "DISTINCT r.result_token, r.semester, r.academic_year, re.exam_year, r.submission_date, t.lname, t.oname, p.program_name, p.short_form, c.course_name, c.short_form as short_form_c",
        "recordapproval r JOIN program p ON p.program_id = r.program_id
        JOIN teachers t ON t.teacher_id = r.teacher_id JOIN courses c ON c.course_id=r.course_id
        JOIN results re ON re.result_token=r.result_token",
        "r.school_id=$user_school_id AND r.result_status='pending' ORDER BY r.submission_date DESC", 0
    ));
    $resultAttended = decimalIndexArray(fetchData1(
        "DISTINCT r.result_token, r.semester, r.academic_year, re.exam_year, r.result_status, r.submission_date, t.lname, t.oname, p.program_name, p.short_form, c.course_name, c.short_form as short_form_c",
        "recordapproval r JOIN program p ON p.program_id = r.program_id
        JOIN teachers t ON t.teacher_id = r.teacher_id JOIN courses c ON c.course_id=r.course_id
        JOIN results re ON re.result_token=r.result_token",
        "r.school_id=$user_school_id AND r.result_status != 'pending' ORDER BY r.submission_date DESC", 0
    ));
    $brokenResults = decimalIndexArray(fetchData1(
        "DISTINCT r.result_token, r.semester, r.academic_year, r.exam_year, r.semester, r.academic_year, t.lname, t.oname, p.program_name, p.short_form, c.course_name, c.short_form as short_form_c, MIN(r.date) as date",
        "results r JOIN teachers t ON t.teacher_id = r.teacher_id
        JOIN program p ON p.program_id = r.program_id
        JOIN courses c ON c.course_id = r.course_id
        LEFT JOIN recordapproval re ON r.result_token=re.result_token",
        "re.result_token IS NULL AND r.school_id=$user_school_id", 0, join_type:"LEFT", 
        group_by: "r.result_token, r.exam_year, r.semester, r.academic_year, t.lname, t.oname, p.program_name, p.short_form, c.course_name, c.short_form"
    ));
?>

<section class="section_container">
    <div class="content orange">
        <div class="head">
            <h2>
                <?= is_array($programs) ? (isset($programs[0]) ? count($programs) : 1) : 0; ?>
            </h2>
        </div>
        <div class="body">
            <span>Total Classes</span>
        </div>
    </div>

    <div class="content <?= !$resultPending ? "teal" : "pink" ?>">
        <div class="head">
            <h2>
                <?= is_array($resultPending) ? (isset($resultPending[0]) ? count($resultPending) : 1) : 0; ?>
            </h2>
        </div>
        <div class="body">
            <span>Pending Result Approvals</span>
        </div>
    </div>

    <div class="content <?= !$brokenResults ? "secondary" : "red" ?>">
        <div class="head">
            <h2>
                <?= is_array($brokenResults) ? (isset($brokenResults[0]) ? count($brokenResults) : 1) : 0; ?>
            </h2>
        </div>
        <div class="body">
            <span>Broken Results</span>
        </div>
    </div>
</section>

<section class="flex-column flex-all-center">
    <h1 class="txt-primary">Controls</h1>
    <div class="body btn flex flex-wrap gap-md">
        <button class="control_btn sp-lg xs-rnd primary" data-section="allPrograms" data-refresh="false" id="viewAll">View All Classes</button>
        <button class="control_btn sp-lg xs-rnd plain secondary" data-section="newProgram">Add new Class</button>
        <button class="control_btn sp-lg xs-rnd plain yellow color-dark" data-section="pendingResults">Pending results</button>
        <button class="control_btn sp-lg xs-rnd plain teal" data-section="reviewedResults">Reviewed results</button>
        <button class="control_btn sp-lg xs-rnd plain red" data-section="brokenResults">Broken results</button>
    </div>
</section>

<section id="allPrograms" class="sp-xlg-tp section_box">
    <?php if($programs) : ?>
    <div class="head">
        <h2 class="txt-al-c">Your Classes</h2>
    </div>
    <div class="body">
        <div class="form sm-lg-tp">
            <label for="search_classes" class="flex-column gap-sm search-label" data-table="classes_table">
                <span class="title_label">Search for any data in the table below</span>
                <input type="search" name="search" id="search_classes" placeholder="Type your search here...">
            </label>
        </div>

        <!-- multiple action buttons -->
        <div class="multi_actions btn">
            <button class="multi_button delete" disabled>Delete [<span class="selected_rows">0</span>]</button>
            <button class="de_select no_disp">Deselect All</button>
        </div>

        <table id="classes_table">
            <thead>
                <td>Class ID</td>
                <td>Class Name</td>
                <td>Alias Name</td>
                <td>Class Subject Count</td>
            </thead>
            <tbody>
                <?php foreach($programs as $program) : ?>
                <tr>
                    <td><?= formatItemId($program["program_id"], "PID") ?></td>
                    <td><?= $program["program_name"] ?></td>
                    <td><?= is_null($program["short_form"]) ? "Not Set" : $program["short_form"] ?></td>
                    <td><?= count(explode(" ", $program["course_ids"])) - 1 ?></td>
                    <td>
                        <span class="item-event edit" data-item-id="<?= $program["program_id"] ?>">Edit</span>
                        <span class="item-event delete" data-item-id="<?= $program["program_id"] ?>">Delete</span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else : ?>
    <p class="txt-al-c">No Programs have been added yet</p>
    <?php endif; ?>
</section>

<!-- pending results -->
<section id="pendingResults" class="section_box no_disp">
    <?php
        if($resultPending) :
    ?>
    <div class="form sm-lg-tp">
        <label for="search_pending" class="flex-column gap-sm search-label" data-table="pending_table">
            <span class="title_label">Search for any data in the table below</span>
            <input type="search" name="search" id="search_pending" placeholder="Type your search here...">
        </label>
    </div>

    <!-- multiple action buttons -->
    <div class="multi_actions btn">
        <button class="multi_button approve" disabled>Approve [<span class="selected_rows">0</span>]</button>
        <button class="multi_button reject" disabled>Reject [<span class="selected_rows">0</span>]</button>
        <button class="multi_button delete" disabled>Delete [<span class="selected_rows">0</span>]</button>
        <button class="de_select no_disp">Deselect All</button>
    </div>

    <table class="relative" id="pending_table">
        <thead>
            <td>No.</td>
            <td>Class</td>
            <td>Form Year</td>
            <td>Semester</td>
            <td>Subject</td>
            <td>Teacher</td>
            <td>Academic Year</td>
            <td>Submission Date</td>
        </thead>
        <tbody>
        <?php foreach($resultPending as $counter => $result) : ?>
            <tr>
                <td><?= ($counter + 1) ?></td>
                <td class="program_name"><?= empty($result["short_form"]) ? $result["program_name"] : $result["short_form"] ?></td>
                <td class="exam_year"><?= $result["exam_year"] ?></td>
                <td class="semester"><?= $result["semester"] ?></td>
                <td class="course_name"><?= empty($result["short_form_c"]) ? $result["course_name"] : $result["short_form_c"] ?></td>
                <td class="teacher_name"><?= $result["lname"]." ".$result["oname"] ?></td>
                <td class="academic_year"><?= $result["academic_year"] ?></td>
                <td class="result_date"><?= date("M d, Y H:i:s", strtotime($result["submission_date"])) ?></td>
                <td class="options">
                    <span class="item-event view" data-item-id="<?= $result["result_token"] ?>">View</span>
                    <span class="item-event approve" data-item-id="<?= $result["result_token"] ?>">Approve</span>
                    <span class="item-event reject" data-item-id="<?= $result["result_token"] ?>">Reject</span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <td colspan="5" class="res_stat">Status: </td>
        </tfoot>
    </table>
    <?php else : ?>
    <div class="empty txt-al-c p-xxlg p-med">
        <p class="border b-secondary">There are no pending results requiring approval yet</p>
    </div>
    <?php endif; ?>
</section>
<!-- end of pending results -->

<!-- reviewed results -->
<section id="reviewedResults" class="section_box no_disp">
    <?php
        if($resultAttended) :
    ?>
    <div class="form sm-lg-tp">
        <label for="search_attended" class="flex-column gap-sm search-label" data-table="reviewed_table">
            <span class="title_label">Search for any data in the table below</span>
            <input type="search" name="search" id="search_attended" placeholder="Type your search here...">
        </label>
    </div>

    <!-- multiple action buttons -->
    <div class="multi_actions btn">
        <button class="multi_button approve" disabled>Approve [<span class="selected_rows">0</span>]</button>
        <button class="multi_button reject" disabled>Reject [<span class="selected_rows">0</span>]</button>
        <button class="multi_button delete" disabled>Delete [<span class="selected_rows">0</span>]</button>
        <button class="de_select no_disp">Deselect All</button>
    </div>

    <!-- academic years -->
     <div id="academic_years">
        <?php $academic_years = array_unique(array_values(array_column($resultAttended, "academic_year"))) ?>
        <label for="academic_year_select">
            <select name="" id="academic_year_select" data-table-id="reviewed_table">
                <option value="">Filter By Academic Year</option>
                <?php foreach($academic_years as $option): ?>
                    <option value="<?= $option ?>"><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </label>
     </div>

    <table class="relative full" id="reviewed_table">
        <thead>
            <td>No.</td>
            <td>Class</td>
            <td>Form Year</td>
            <td>Semester</td>
            <td>Subject</td>
            <td>Teacher</td>
            <td>Academic Year</td>
            <td>Submission Date</td>
        </thead>
        <tbody>
            <?php foreach($resultAttended as $counter => $result) : ?>
            <tr class="<?= $result["result_status"] === "rejected" ? "red-i" : "" ?>">
                <td><?= ($counter+1) ?></td>
                <td class="program_name"><?= $result["short_form"] ?? $result["program_name"] ?></td>
                <td class="exam_year"><?= $result["exam_year"] ?></td>
                <td class="semester"><?= $result["semester"] ?></td>
                <td class="course_name"><?= empty($result["short_form_c"]) ? $result["course_name"] : $result["short_form_c"] ?></td>
                <td class="teacher_name"><?= $result["lname"]." ".$result["oname"] ?></td>
                <td class="academic_year"><?= $result["academic_year"] ?></td>
                <td class="result_date"><?= date("M d, Y H:i:s", strtotime($result["submission_date"])) ?></td>
                <td class="options">
                    <span class="item-event view" data-item-id="<?= $result["result_token"] ?>">View</span>
                    <?php if($result["result_status"] === "rejected") : ?>
                        <span class="item-event approve" data-item-id="<?= $result["result_token"] ?>">Approve</span>
                        <span class="item-event remove" data-item-id="<?= $result["result_token"] ?>">Delete</span>
                    <?php else : ?>
                        <span class="item-event reject" data-item-id="<?= $result["result_token"] ?>">Reject</span>                    
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <td colspan="5" class="res_stat">Status: </td>
        </tfoot>
    </table>
    <?php else : ?>
    <div class="empty txt-al-c p-xxlg p-med">
        <p class="border b-secondary">There are no reviewed results yet</p>
    </div>
    <?php endif; ?>
</section>
<!-- end of reviewed -->

<!-- start of broken -->
<section id="brokenResults" class="section_box no_disp">
    <?php
        if($brokenResults) :
    ?>
    <div class="form sm-lg-tp">
        <label for="search_broken" class="flex-column gap-sm search-label" data-table="broken_table">
            <span class="title_label">Search for any data in the table below</span>
            <input type="search" name="search" id="search_broken" placeholder="Type your search here...">
        </label>
    </div>

    <!-- multiple action buttons -->
    <div class="multi_actions btn">
        <button class="multi_button delete" disabled>Delete [<span class="selected_rows">0</span>]</button>
        <button class="de_select no_disp">Deselect All</button>
    </div>
    
    <table class="relative" id="broken_table">
        <thead>
            <td>No.</td>
            <td>Class</td>
            <td>Form Year</td>
            <td>Semester</td>
            <td>Subject</td>
            <td>Teacher</td>
            <td>Academic Year</td>
            <td>Submission Date</td>
        </thead>
        <tbody>
            <?php foreach($brokenResults as $counter => $result) : ?>
            <tr>
                <td><?= ($counter+1) ?></td>
                <td class="program_name"><?= $result["short_form"] ?? $result["program_name"] ?></td>
                <td class="exam_year"><?= $result["exam_year"] ?></td>
                <td class="semester"><?= $result["semester"] ?></td>
                <td class="course_name"><?= empty($result["short_form_c"]) ? $result["course_name"] : $result["short_form_c"] ?></td>
                <td class="teacher_name"><?= $result["lname"]." ".$result["oname"] ?></td>
                <td class="academic_year"><?= $result["academic_year"] ?></td>
                <td class="result_date"><?= date("M d, Y H:i:s", strtotime($result["date"])) ?></td>
                <td class="options">
                    <span class="item-event view" data-item-id="<?= $result["result_token"] ?>">View</span>
                    <span class="item-event remove" data-item-id="<?= $result["result_token"] ?>">Delete</span>                    
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <td colspan="5" class="res_stat">Status: </td>
        </tfoot>
    </table>
    <?php else : ?>
    <div class="empty txt-al-c p-xxlg p-med">
        <p class="border b-secondary">No broken results found</p>
    </div>
    <?php endif; ?>
</section>
<!-- end of broken table -->

<section id="newProgram" class="sp-xlg-tp section_box no_disp">
    <?php eval("?>".file_get_contents("$url/admin/admin/page_parts/subTeaForms.php?form_type=addProgram&school_id=$user_school_id")) ?>
</section>

<div id="table_del" class="modal_yes_no outmost-modal fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php require_once("$rootPath/admin/admin/page_parts/item_del.php") ?>
</div>

<div id="updateProgram" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <div id="updateLoader" class="flex flex-column flex-center-align flex-center-content">
        <div id="getLoader"></div>
        <span class="item-event" id="cancelUpdate" style="color: white; margin-top: 10px; padding-left: 10px; text-align: center">Cancel</span>
    </div>
    <?php eval("?>".file_get_contents("$url/admin/admin/page_parts/subTeaForms.php?form_type=updateProgram&school_id=$user_school_id")) ?>
</div>

<div id="view_results" class="fixed flex-all-center flex-column form_modal_box no_disp">
    <div class="flex flex-column wmax-lg w-full" style="max-height: 80vh; overflow: auto">
        <h1 id="topic" class="light sp-lg txt-al-c sm-auto wmin-2xs sticky top border">Results</h1>
        <table class="full wmax-lg light">
            <thead>
                <td>Index Number</td>
                <td>Full name</td>
                <td>Class Score</td>
                <td>Exam Score</td>
                <td>Total Score</td>
                <td>Grade</td>
                <td>Position</td>
            </thead>
            <tbody style="max-height: 80vh">
            </tbody>
            <tfoot>
                <tr>
                    <td class="btn p-med w-fluid-child">
                        <button class="pink" onclick="$('#view_results').addClass('no_disp')">Close</button>
                    </td>
                    <td id="year_sem"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script src="<?= "$url/admin/admin/assets/scripts/programs.js?v=".time() ?>"></script>