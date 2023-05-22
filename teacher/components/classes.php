<?php include_once("compSession.php"); $_SESSION["active-page"] = "classes" ?>
<input type="hidden" name="mark_result_type" value="<?= fetchData("school_result","admissiondetails","schoolID={$teacher['school_id']}")["school_result"] ?>" id="mark_result_type">
<h1 class="txt-al-c sm-lg-t no_d" id="page_title">My Classes</h1>
<section id="classes" class="d-section flex flex-center-content flex-wrap gap-md">
    <?php 
        $results = fetchData1(
            "p.program_name, c.course_name, c.course_id, p.program_id",
            "teacher_classes t JOIN courses c ON t.course_id=c.course_id JOIN program p ON p.program_id=t.program_id",
            "teacher_id={$teacher['teacher_id']}",0
        );
        if(is_array($results)) :
            for($i=0; $i < (array_key_exists(0, $results) ? count($results) : 1); $i++) :
                $result = array_key_exists(0,$results) ? $results[$i] : $results;
    ?>
    <div class="card v-card sm-rnd m-med-tp sp-lg">
        <div class="self-align-start flex gap-sm flex-column">
            <h2><?= $result["program_name"] ?></h2>
            <h4><?= $result["course_name"] ?></h4>
            <p class="txt-fs">[<?= fetchData("academicYear","admissiondetails","schoolID={$teacher['school_id']}")["academicYear"] ?> | Semester Number]</p>
        </div>
        <div class="foot btn self-align-end">
            <button class="light plain-r sp-xlg class_single" onclick="pageChange(<?= $result['program_id'].', \''.$result['course_id'].'\', \''.$result['program_name'].'\'' ?>)">View Data</button>
        </div>
    </div>
    <?php endfor; else: ?>
    <div class="lt-shade p-xxlg txt-fl1 w-full txt-al-c">
        <p>No classes has been assigned to you yet. Please contact your administrator for help</p>
    </div>
    <?php endif; ?>
</section>

<?php if(is_array($results)) : ?>
<section id="single_class" class="d-section lt-shade no_disp">
    <div class="head flex flex-space-content sp-med-lr sp-lg-tp">
        <div class="back" onclick="pageChange()">Back</div>
        <div class="title"><span id="single_class_name"></span> Records</div>
    </div>
    <div class="form inline-form gap-sm flex-wrap">
        <label for="class_year">
            <select name="class_year" id="class_year" class="sp-lg">
                <option value="">Select the class year</option>
                <option value="1">Year 1</option>
                <option value="2">Year 2</option>
                <option value="3">Year 3</option>
            </select>
        </label>
        <label for="search" class="no_disp flex-column">
            <span class="label_title">Search for anything in the table</span>
            <input type="search" name="search" id="search" placeholder="Search..." autocomplete="off"
                class="w-full sp-lg">
        </label>
        <div class="btn sp-unset p-lg self-align-end">
            <button type="submit" name="list_search" class="primary" data-pid="" data-cid="">Search</button>
            <button name="data_search" type="button" class="teal no_disp">Search Student</button>
            <button type="reset" name="reset" class="red no_disp">Reset</button>
        </div>
    </div>
    <div class="body sm-xlg-t">
        <table class="full" id="class_list_table">
            <thead>
                <td>Index Number</td>
                <td>Student Name</td>
                <td>Gender</td>
                <td>Marks(Avg)</td>
                <td>Grade</td>
            </thead>
            <tbody>
                <tr class="empty">
                    <td colspan="5">Make a search on your class year to proceed</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section id="single_student" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <div class="wmax-md sp-lg light w-full window txt-fl">
        <div class="head flex flex-space-content light sp-med">
            <div class="title">
                <span class="txt-bold">Student Data</span>
            </div>
            <div class="controls border sp-sm-lr" onclick="$('#single_student').addClass('no_disp')">
                <div class="mini-o" title="Close" >
                    <span></span>
                </div>
            </div>
        </div>
        <div class="body white sp-lg flex flex-column gap-md">
            <div class="flex flex-space-content">
                <span class="txt-bold">Name</span>
                <span class="name"></span>
            </div>
            <div class="flex flex-space-content">
                <span class="txt-bold">Index Number</span>
                <span class="index"></span>
            </div>
            <div class="flex flex-space-content">
                <span class="txt-bold">Mark</span>
                <span class="mark"></span>
            </div>
            <div class="flex flex-space-content">
                <span class="txt-bold">Grade</span>
                <span class="grade"></span>
            </div>
            <div class="flex flex-space-content">
                <span class="txt-bold">Gender</span>
                <span class="gender"></span>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<script src="<?= "$url/assets/scripts/classes.js?v=".time() ?>"></script>