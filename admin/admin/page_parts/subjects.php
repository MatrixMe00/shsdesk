<?php 
    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
        
        include("../../includes/session.php");
    }else{
        include("../../../includes/session.php");
    
        //set nav_point session
        $_SESSION["nav_point"] = "subjects";
    }

    $courses = fetchData1("*","courses","school_id=$user_school_id", 0);
    $teachers = fetchData1("*","teachers","school_id=$user_school_id", 0);
?>

<section class="section_container">
    <div class="content orange">
        <div class="head">
            <h2>
            <?= is_array($courses) ? (isset($courses[0]) ? count($courses) : 1) : 0 ?>
            </h2>
        </div>
        <div class="body">
            <span>Total Courses</span>
        </div>
    </div>

    <div class="content purple">
        <div class="head">
            <h2>
            <?= is_array($teachers) ? (isset($teachers[0]) ? count($teachers) : 1) : 0 ?>
            </h2>
        </div>
        <div class="body">
            <span>Total Teachers</span>
        </div>
    </div>
</section>

<section class="flex-column flex-all-center">
    <div class="head">
        <h1 class="txt-primary">Controls</h1>
    </div>    
    <div class="body control btn flex flex-wrap gap-md">
        <button class="sp-lg xs-rnd primary" data-refresh="false" data-section="courses">View All Courses</button>
        <?php if(is_array($courses)) :?>
        <button class="sp-lg xs-rnd plain indigo" data-refresh="false" data-section="teachers">View All Teachers</button>
        <?php endif; ?>
        <button class="sp-lg xs-rnd plain dark" data-section="add_course">Add new Course</button>
        <?php if (is_array($courses)) : ?>
        <button class="sp-lg xs-rnd plain cyan" data-section="add_teacher">Add new Teacher</button>
        <?php endif; ?>
    </div>
</section>

<section class="section_box sp-xlg-tp hmax-unset-child" id="courses" data-table="courses" data-table-col="course_id">
    <?php if(is_array($courses)) : ?>
    <div class="head">
        <h1 class="txt-al-c">Courses</h1>
    </div>
    <div class="body">
        <table>
            <thead>
                <td>Course ID</td>
                <td>Course Name</td>
                <td>Course Alias</td>
                <td>Programs Offering</td>
                <td>Credit Hours</td>
            </thead>
            <tbody>
                <?php for($counter = 0; $counter < (isset($courses[0]) ? count($courses) : 1); $counter++) : $course = isset($courses[0]) ? $courses[$counter] : $courses ?>
                <tr>
                    <td><?= formatItemId($course["course_id"], "CID") ?></td>
                    <td><?= $course["course_name"] ?></td>
                    <td><?= $course["short_form"] ?></td>
                    <td><?= fetchData1("COUNT(*) AS total","program","course_ids LIKE '%".$course["course_id"]."% '")["total"] ?></td>
                    <td><?= is_null($course["credit_hours"]) ? "Not Set" : $course["credit_hours"] ?></td>
                    <td>
                        <span class="item-event edit" data-item-id="<?= $course["course_id"] ?>">Edit</span>
                        <span class="item-event delete" data-item-id="<?= $course["course_id"] ?>">Delete</span>
                    </td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="txt-al-c">No Courses has been uploaded</p>
    <?php endif; ?>
</section>

<section class="sp-xlg-tp hmax-unset-child section_box no_disp" id="add_course">
    <div class="body">
    <?php eval("?>".file_get_contents("$url/admin/admin/page_parts/subTeaForms.php?form_type=course_add&school_id=$user_school_id")) ?>
    </div>
</section>

<?php if(is_array($courses)) : ?>
    <section class="sp-xlg-tp hmax-unset-child section_box no_disp" id="teachers" data-table="teachers" data-table-col="teacher_id">
    <?php if(is_array($teachers)) : ?>
    <div class="head">
        <h2 class="txt-al-c">Teachers</h2>
    </div>
    <div class="body">
        <table>
            <thead>
                <td>Teacher ID</td>
                <td>Teacher Name</td>
                <td>Classes Teaching (No.)</td>
                <td>Subjects Teaching (No.)</td>
            </thead>
            <tbody>
                <?php 
                    for($counter = 0; $counter < (isset($teachers[0]) ? count($teachers) : 1); $counter++) : $teacher = isset($teachers[0]) ? $teachers[$counter] : $teachers ?>
                <tr>
                   <td><?= formatItemId($teacher["teacher_id"], "TID") ?></td>
                    <td><?= strtoupper($teacher["lname"])." ".ucwords($teacher["oname"]) ?></td>
                    <td><?= fetchData1("COUNT(DISTINCT program_id) as total","teacher_classes","teacher_id={$teacher['teacher_id']}")["total"] ?></td>
                    <td><?= fetchData1("COUNT(DISTINCT course_id) as total","teacher_classes","teacher_id={$teacher['teacher_id']}")["total"] ?></td>
                    <td>
                        <span class="item-event edit" data-item-id="<?= $teacher["teacher_id"] ?>">Edit</span>
                        <span class="item-event delete" data-item-id="<?= $teacher["teacher_id"] ?>">Delete</span>
                    </td> 
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <?php else : ?>
    <p class="txt-al-c">No Teachers have been added to the system</p>
    <?php endif; ?>
</section>

<section class="sp-xlg-tp hmax-unset-child section_box no_disp" id="add_teacher">
    <div class="body">
    <?php eval("?>".file_get_contents("$url/admin/admin/page_parts/subTeaForms.php?form_type=teacher_add&school_id=$user_school_id")) ?>
    </div>
</section>
<?php endif; ?>

<div id="table_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php require_once("$rootPath/admin/admin/page_parts/item_del.php") ?>
</div>

<div id="updateItem" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <div id="updateLoader" class="no_disp flex-column flex-center-align flex-center-content">
        <div id="getLoader"></div>
        <span class="item-event" id="cancelUpdate" style="color: white; margin-top: 10px; padding-left: 10px; text-align: center">Cancel</span>
    </div>
    <?php eval("?>".file_get_contents("$url/admin/admin/page_parts/subTeaForms.php?form_type=course_update&school_id=$user_school_id")) ?>
    <?php eval("?>".file_get_contents("$url/admin/admin/page_parts/subTeaForms.php?form_type=teacher_update&school_id=$user_school_id")) ?>
</div>

<script src="<?= "$url/admin/admin/assets/scripts/subjects.min.js?v=".time() ?>"></script>