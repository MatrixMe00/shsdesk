<?php include_once("compSession.php"); $_SESSION["active-page"] = "classes" ?>
<input type="hidden" name="mark_result_type" value="<?= fetchData("school_result","admissiondetails","schoolID={$teacher['school_id']}")["school_result"] ?>" id="mark_result_type">

<section id="cards-section" class="flex d-section flex-wrap gap-sm p-lg card-section">
    <div class="card v-card gap-lg purple p-med sm-rnd flex-wrap">
        <span class="">Subject Count</span>
        <span class="txt-fl3 txt-bold">
            <?php             
                echo fetchData1("COUNT(DISTINCT(course_id)) as total","teacher_classes","teacher_id={$teacher['teacher_id']}")["total"];
            ?>
        </span>
    </div>
    <div class="card v-card gap-lg orange p-med sm-rnd flex-wrap">
        <span class="">Class Count</span>
        <span class="txt-fl3 txt-bold"><?= fetchData1("COUNT(DISTINCT program_id) as total","teacher_classes","teacher_id={$teacher['teacher_id']}")["total"] ?></span>
    </div>
</section><?php 
    $academicYear = fetchData("academicYear","admissiondetails","schoolID={$teacher['school_id']}")["academicYear"];
?>

<h1 class="txt-al-c sm-lg-t no_d" id="page_title">My Classes</h1>
<section id="classes" class="d-section flex flex-center-content flex-wrap gap-md">
    <?php 
        $results = fetchData1(
            "DISTINCT(t.program_id), p.program_name",
            "teacher_classes t JOIN program p ON t.program_id=p.program_id",
            "t.teacher_id={$teacher['teacher_id']}",
            0
        );
        if(is_array($results)) :
            for($i=0; $i < (array_key_exists(0, $results) ? count($results) : 1); $i++) :
                $result = array_key_exists(0,$results) ? $results[$i] : $results;
    ?>
    <div class="card v-card sm-rnd m-med-tp white sp-lg">
        <div class="self-align-start flex gap-sm flex-column">
            <h2><?= $result["program_name"] ?></h2>
            <h4><?php 
                $classes = fetchData1("c.course_name","teacher_classes t JOIN courses c ON t.course_id = c.course_id","t.teacher_id={$teacher['teacher_id']} AND t.program_id={$result['program_id']}", 0);
                if(is_array($classes)){
                    if(array_key_exists("course_name", $classes)){
                        echo $classes["course_name"];
                    }else{
                        $new_classes = array();
                        foreach($classes as $class){
                            array_push($new_classes, $class["course_name"]); 
                        }
                        echo implode(" | ", $new_classes);
                    }
                }else{
                    echo "Not set";
                }
            ?></h4>
            <p class="txt-fs">[Current Year: <?= $academicYear ?>]</p>
        </div>
        <div class="foot btn self-align-end">
            <button class="light plain-r sp-xlg class_single" onclick="pageChange(<?= $result['program_id'].', \''.$result['program_name'].'\'' ?>)">View Data</button>
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
    <div class="form-element flex-eq sm-auto w-fit flex-all-center gap-sm flex-wrap">
        <label for="result_year" class="w-full w-fluid-child">
            <select name="result_year" id="result_year" class="sp-lg">
                <option value="">Select an academic year</option>
                <?php 
                    $dates = fetchData1("MIN(submission_date) as submission_date","recordapproval","teacher_id={$teacher['teacher_id']} GROUP BY YEAR(submission_date)",0);
                    if(is_array($dates)):
                        if(array_key_exists("submission_date", $dates)){
                            $dates = array_values($dates);
                            print_r($dates);
                        }else{
                            $dates = numericIndexArray($dates);
                        }

                        foreach($dates as $date) :
                ?>
                <option value="<?= date("Y", strtotime($date)) ?>"><?= getAcademicYear(date("d-m-Y", strtotime($date))) ?></option>
                <?php endforeach; else: ?>
                <option value="<?= date("Y") ?>"><?= getAcademicYear(date("d-m-Y")) ?></option>
                <?php endif;?>
            </select>
        </label>
        <label class="w-full w-fluid-child" for="class_year">
            <select name="class_year" id="class_year" class="sp-lg">
                <option value="">Select the class year</option>
                <option value="1">Year 1</option>
                <option value="2">Year 2</option>
                <option value="3">Year 3</option>
            </select>
        </label>
        <label class="w-full w-fluid-child" for="course_id">
            <select name="course_id" id="course_id" class="sp-lg">
                <option value="" data-pid="null">Select the subject</option>
                <?php 
                    $sql = "SELECT t.course_id, t.program_id, c.course_name
                        FROM teacher_classes t JOIN courses c ON t.course_id=c.course_id
                        WHERE t.teacher_id={$teacher['teacher_id']}";
                    $courses = $connect2->query($sql);
                    while($course = $courses->fetch_assoc()):
                ?>
                <option value="<?= $course["course_id"] ?>" data-pid="<?= $course["program_id"] ?>"><?= $course["course_name"] ?></option>
                <?php endwhile ?>
            </select>
        </label>
        <label class="w-full w-fluid-child" for="exam_semester">
            <select name="exam_semester" id="exam_semester" class="sp-lg">
                <option value="">Select Semester Number</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
            </select>
        </label>
        <label for="search" class="w-full w-fluid-child no_disp flex-column">
            <span class="label_title">Search for anything in the table</span>
            <input type="search" name="search" id="search" placeholder="Search..." autocomplete="off"
                class="w-full sp-lg">
        </label>
        <div class="btn sp-unset p-lg wmax-sm flex-eq flex gap-sm flex-wrap self-align-end" style="flex-basis: 320px">
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
                <td>Total Score</td>
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
<?php close_connections() ?>